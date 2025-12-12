<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\OwnerNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class NotificationController extends Controller
{
    /** ===== Helpers ===== */

    /** Boleh lihat jika notifikasi milik user ATAU broadcast (user_id null) */
    protected function authorizeView(OwnerNotification $n): void
    {
        $u = auth()->user();
        abort_unless($n->user_id === $u->id || is_null($n->user_id), 403);
    }

    /** Scope query: notifikasi untuk user saat ini + broadcast */
    protected function scopeForCurrentUser()
    {
        $uid = auth()->id();
        return OwnerNotification::query()->where(fn($q) => $q->where('user_id', $uid)->orWhereNull('user_id'));
    }

    /** Format angka untuk badge supaya tidak overflow */
    protected function shortCount(int $n): string
    {
        if ($n >= 1000) {
            return '999+';
        }
        if ($n >= 100) {
            return '99+';
        }
        if ($n >= 10) {
            return '9+';
        }
        return (string) $n;
    }

    /** ===== Pages ===== */

    public function index(Request $request)
    {
        $query = $this->scopeForCurrentUser()->orderByDesc('created_at');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('is_read') && $request->input('is_read') !== 'all') {
            $query->where('is_read', $request->boolean('is_read'));
        }
        if ($request->filled('is_reviewed') && $request->input('is_reviewed') !== 'all') {
            $query->where('is_reviewed', $request->boolean('is_reviewed'));
        }
        if ($request->filled('severity') && $request->input('severity') !== 'all') {
            $query->where('severity', $request->input('severity'));
        }
        if ($request->filled('type') && $request->input('type') !== 'all') {
            $query->where('notification_type', $request->input('type'));
        }

        $notifications = $query->paginate(10)->withQueryString();

        $stats = [
            'unread_count' => $this->scopeForCurrentUser()->where('is_read', false)->count(),
            'today_count' => $this->scopeForCurrentUser()->whereDate('created_at', now()->toDateString())->count(),
            'unreviewed_count' => $this->scopeForCurrentUser()->where('is_reviewed', false)->count(),
            'critical_count' => $this->scopeForCurrentUser()->where('is_read', false)->where('severity', 'critical')->count(),
        ];

        return view('notifications.index', compact('notifications', 'stats'));
    }

    // data() method removed as it is replaced by DataTable class

    /** Detail */
    public function show(OwnerNotification $notification)
    {
        $this->authorizeView($notification);

        if (method_exists($notification, 'markAsRead')) {
            $notification->markAsRead();
        } else {
            if (!$notification->is_read) {
                $notification->update(['is_read' => true, 'read_at' => now()]);
            }
        }

        return view('notifications.show', compact('notification'));
    }

    /** Mark reviewed */
    public function markAsReviewed(Request $request, OwnerNotification $notification)
    {
        $this->authorizeView($notification);

        $data = $request->validate(['review_notes' => 'nullable|string|max:500']);

        $notification->update([
            'is_reviewed' => true,
            'reviewed_at' => now(),
            'reviewer_id' => auth()->id(),
            'review_notes' => $data['review_notes'] ?? null,
        ]);

        if (!$notification->is_read) {
            $notification->update(['is_read' => true, 'read_at' => now()]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Notifikasi berhasil di-review']);
        }

        return redirect()->route('notifications.index', $notification->id)->with('swal-success', 'Notifikasi berhasil di-review');
    }

    /** Mark read (single) */
    public function markAsRead(Request $request, OwnerNotification $notification)
    {
        $this->authorizeView($notification);

        if (!$notification->is_read) {
            $notification->update(['is_read' => true, 'read_at' => now()]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Notifikasi ditandai sudah dibaca']);
        }

        return redirect()->route('notifications.index', $notification->id)->with('swal-success', 'Notifikasi ditandai sudah dibaca');
    }

    /** Mark all read (WEB) – termasuk broadcast */
    public function markAllAsRead()
    {
        $this->scopeForCurrentUser()
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    /** Mark all read (AJAX) – termasuk broadcast */
    public function markAllAsReadApi()
    {
        $this->scopeForCurrentUser()
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Semua notifikasi berhasil ditandai sebagai dibaca']);
    }

    /** Hapus (batasi hanya milik user agar broadcast aman) */
    public function destroy(OwnerNotification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);
        $notification->delete();
        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    public function destroyApi(OwnerNotification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);
        $notification->delete();
        return response()->json(['success' => true, 'message' => 'Notifikasi berhasil dihapus']);
    }

    /** Navbar badge count */
    public function getUnreadCount(Request $request)
    {
        $count = $this->scopeForCurrentUser()->where('is_read', false)->count();

        return response()->json([
            'success' => true,
            'count' => $count, // nilai asli (untuk logic)
            'display_count' => $this->shortCount($count), // untuk UI badge
            'has_unread' => $count > 0, // memudahkan untuk hide badge saat 0
        ]);
    }

    /** Navbar dropdown items */
    public function getLatest(Request $request)
    {
        $items = $this->scopeForCurrentUser()
            ->latest('created_at')
            ->limit(8)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title ?? 'Notifikasi',
                    'message' => Str::limit(strip_tags((string) $n->message), 180),
                    'severity' => $n->severity ?? 'info',
                    'is_read' => (bool) $n->is_read,
                    'time_ago' => optional($n->created_at)->diffForHumans(),
                    'url' => route('notifications.show', $n->id),
                ];
            });

        $unread = $this->scopeForCurrentUser()->where('is_read', false)->count();

        return response()->json([
            'success' => true,
            'count_unread' => $unread,
            'display_count' => $this->shortCount($unread),
            'has_unread' => $unread > 0,
            'notifications' => $items,
        ]);
    }

    /** Kartu ringkas (AJAX) */
    public function summary()
    {
        $base = $this->scopeForCurrentUser();

        return response()->json([
            'unread' => (clone $base)->where('is_read', false)->count(),
            'unread_display' => $this->shortCount((clone $base)->where('is_read', false)->count()),
            'unreviewed' => (clone $base)->where('is_reviewed', false)->count(),
            'critical' => (clone $base)->where('is_read', false)->where('severity', 'critical')->count(),
        ]);
    }
}
