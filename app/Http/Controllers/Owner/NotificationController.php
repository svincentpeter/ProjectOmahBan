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

    public function index()
    {
        $base = $this->scopeForCurrentUser();

        $stats = [
            'unread_count' => (clone $base)->where('is_read', false)->count(),
            'today_count' => (clone $base)->whereDate('created_at', now()->toDateString())->count(),
            'unreviewed_count' => (clone $base)->where('is_reviewed', false)->count(),
            'critical_count' => (clone $base)->where('is_read', false)->where('severity', 'critical')->count(),
        ];

        return view('notifications.index', compact('stats'));
    }

    /** DataTable API */
    public function data(Request $request)
    {
        $request->validate([
            'draw' => 'nullable|integer',
            'start' => 'nullable|integer|min:0',
            'length' => 'nullable|integer|min:1|max:500',
        ]);

        $with = [];
        if (method_exists(OwnerNotification::class, 'sale')) {
            $with[] = 'sale';
        }
        if (method_exists(OwnerNotification::class, 'reviewer')) {
            $with[] = 'reviewer';
        }

        $query = $this->scopeForCurrentUser()->when($with, fn($q) => $q->with($with))->orderByDesc('created_at');

        // Filters
        if ($request->filled('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }
        if ($request->filled('is_reviewed')) {
            $query->where('is_reviewed', $request->boolean('is_reviewed'));
        }
        if ($request->filled('severity')) {
            $query->where('severity', (string) $request->input('severity'));
        }
        if ($request->filled('type')) {
            $query->where('notification_type', (string) $request->input('type'));
        }
        if ($request->filled('fontee_status')) {
            $query->where('fontee_status', (string) $request->input('fontee_status'));
        }

        try {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('checkbox', fn() => '')
                ->addColumn('severity_badge', fn($row) => method_exists($row, 'getSeverityBadge') ? $row->getSeverityBadge() : e(strtoupper($row->severity ?? '-')))
                ->addColumn('type_badge', function ($row) {
                    $colorMap = [
                        'manual_input_alert' => 'primary',
                        'price_adjustment' => 'info',
                        'discount_alert' => 'success',
                        'high_value_transaction' => 'danger',
                        'default' => 'secondary',
                    ];
                    $type = (string) ($row->notification_type ?? 'default');
                    $color = $colorMap[$type] ?? $colorMap['default'];
                    $label = ucfirst(str_replace('_', ' ', $type));
                    return '<span class="badge bg-' . e($color) . '">' . e($label) . '</span>';
                })
                ->addColumn('created_at_ts', fn($row) => optional($row->created_at)->timestamp ?? 0)
                ->addColumn('time_ago', fn($row) => optional($row->created_at)->diffForHumans() ?? '-')
                ->editColumn('title', fn($row) => e(Str::limit($row->title ?? '-', 60)))
                ->addColumn('read_status', fn($row) => $row->is_read ? '<span class="badge bg-success"><i class="cil-check-circle"></i> Dibaca</span>' : '<span class="badge bg-primary"><i class="cil-bell"></i> Baru</span>')
                ->addColumn('reviewed_status', fn($row) => $row->is_reviewed ? '<span class="badge bg-success"><i class="cil-task"></i> Direview</span>' : '<span class="badge bg-warning"><i class="cil-clock"></i> Pending</span>')
                ->addColumn('fontee_status_badge', function ($row) {
                    if (!$row->fontee_message_id) {
                        return '<span class="badge bg-secondary">-</span>';
                    }
                    $statusMap = [
                        'sent' => ['color' => 'info', 'icon' => 'cil-check', 'label' => 'Sent'],
                        'read' => ['color' => 'success', 'icon' => 'cil-check-circle', 'label' => 'Read'],
                        'failed' => ['color' => 'danger', 'icon' => 'cil-x', 'label' => 'Failed'],
                        'pending' => ['color' => 'warning', 'icon' => 'cil-clock', 'label' => 'Pending'],
                    ];
                    $s = $statusMap[$row->fontee_status] ?? $statusMap['pending'];
                    return '<span class="badge bg-' . e($s['color']) . '" title="' . e($row->fontee_message_id) . '"><i class="' . e($s['icon']) . '"></i> ' . e($s['label']) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $detailUrl = route('notifications.show', $row->id);
                    return '<a href="' . e($detailUrl) . '" class="btn btn-outline-primary btn-sm" title="Lihat detail"><i class="cil-search"></i></a> ' . '<button type="button" class="btn btn-outline-danger btn-sm delete-notif" data-id="' . e($row->id) . '" title="Hapus"><i class="cil-trash"></i></button>';
                })
                ->rawColumns(['checkbox', 'severity_badge', 'type_badge', 'read_status', 'reviewed_status', 'fontee_status_badge', 'action'])
                ->make(true);
        } catch (\Throwable $e) {
            Log::error('DT OwnerNotification error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return DataTables::of(OwnerNotification::query()->whereRaw('1=0'))->make(true);
        }
    }

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
