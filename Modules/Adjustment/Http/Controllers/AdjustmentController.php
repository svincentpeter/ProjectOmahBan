<?php

namespace Modules\Adjustment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

use Modules\Adjustment\Entities\Adjustment;
use Modules\Adjustment\Entities\AdjustedProduct;
use Modules\Adjustment\Entities\AdjustmentLog;
use Modules\Adjustment\Entities\AdjustmentFile;
use Modules\Product\Entities\Product;
use App\Models\User;
use Modules\Adjustment\Http\Requests\StoreAdjustmentRequest;
use Illuminate\Support\Facades\Storage;
use Modules\Adjustment\DataTables\AdjustmentsDataTable;
use Yajra\DataTables\Facades\DataTables;

class AdjustmentController extends Controller
{
    /**
     * ✅ INDEX: Tampilkan semua adjustment (Kasir lihat miliknya, Admin lihat semua)
     * 📝 Fitur: DataTable dengan filter status & requester
     * 🔐 Permission: access_adjustments
     */
    public function index(AdjustmentsDataTable $dataTable)
    {
        // Cek permission
        abort_if(Gate::denies('access_adjustments'), 403);

        return $dataTable->render('adjustment::index');
    }

    /**
     * ✅ CREATE: Form buat adjustment baru
     * 📝 Fitur: Load daftar produk aktif dengan kategorinya
     * 🔐 Permission: create_adjustments
     */
    public function create()
    {
        abort_if(Gate::denies('create_adjustments'), 403);

        $products = Product::query()
            ->select([
                'id',
                'product_name',
                'product_code', // <-- WAJIB, biar tidak [N/A]
                'product_unit', // <-- WAJIB, biar tidak "undefined"
                'product_quantity', // stok saat ini
                'category_id',
            ])
            ->with(['category:id,category_name'])
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('product_name')
            ->get();

        return view('adjustment::create', compact('products'));
    }

    /**
     * ✅ STORE: Simpan adjustment baru ke database
     * 📝 Fitur:
     *    - Upload multiple foto bukti
     *    - Validasi stok jika tipe 'sub' (pengurangan)
     *    - Generate reference number otomatis
     *    - Atomic transaction (semua atau gagal)
     * 🔐 Permission: create_adjustments
     */
    public function store(StoreAdjustmentRequest $request)
    {
        abort_if(Gate::denies('create_adjustments'), 403);

        try {
            DB::transaction(function () use ($request) {
                // Generate reference number: ADJ-YYYYMMDD-XXXXX
                $dateCode = date('Ymd');
                $lastAdjustment = Adjustment::whereDate('created_at', today())->latest('id')->first();
                $sequence = ($lastAdjustment ? intval(substr($lastAdjustment->reference, -5)) : 0) + 1;
                $reference = 'ADJ-' . $dateCode . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);

                // Buat adjustment record
                $adjustment = Adjustment::create([
                    'date' => $request->date ?? now()->toDateString(),
                    'reference' => $reference, // ✅ Reference auto-generated
                    'note' => $request->note,
                    'reason' => $request->reason,
                    'description' => $request->description,
                    'requester_id' => Auth::id(), // ✅ Set requester otomatis
                    'status' => 'pending', // Status awal selalu pending
                ]);

                // Upload foto bukti (NEW FEATURE)
                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $file) {
                        // Store ke storage/public/adjustment_files/
                        $path = $file->store('adjustment_files', 'public');

                        AdjustmentFile::create([
                            'adjustment_id' => $adjustment->id,
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                            'file_size' => $file->getSize(),
                            'mime_type' => $file->getMimeType(),
                        ]);
                    }
                }

                // Loop produk yang akan disesuaikan
                foreach ($request->product_ids as $key => $productId) {
                    $product = Product::findOrFail($productId);
                    $quantity = (int) $request->quantities[$key];
                    $type = $request->types[$key]; // 'add' atau 'sub'

                    // ✅ Validasi: Jika pengurangan, pastikan stok cukup
                    if ($type === 'sub' && $product->current_stock < $quantity) {
                        throw new \Exception("Stok produk '{$product->product_name}' tidak cukup. " . "Sisa stok: {$product->current_stock}, Kurangi: {$quantity}");
                    }

                    // Buat record adjusted_product
                    AdjustedProduct::create([
                        'adjustment_id' => $adjustment->id,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'type' => $type,
                    ]);
                }

                // ✅ Create audit log: Pengajuan baru
                AdjustmentLog::create([
                    'adjustment_id' => $adjustment->id,
                    'user_id' => Auth::id(),
                    'action' => $validated['action'] === 'approve' ? 'approved' : 'rejected',
                    'old_status' => 'pending',
                    'new_status' => $validated['action'] === 'approve' ? 'approved' : 'rejected',
                    'notes' => $validated['approval_notes'] ?? 'Tanpa catatan',
                    'locked' => 1,
                    // ✅ Jangan include created_at
                ]);
            });

            // Success toast
            toast('✅ Pengajuan penyesuaian berhasil! Menunggu persetujuan admin.', 'info');
            return redirect()->route('adjustments.index');
        } catch (\Exception $e) {
            // Error toast
            toast('❌ Error: ' . $e->getMessage(), 'error');
            return back()->withInput();
        }
    }

    /**
     * ✅ SHOW: Tampilkan detail adjustment
     * 📝 Fitur:
     *    - Tampilkan produk detail
     *    - Tampilkan foto bukti
     *    - Tampilkan riwayat approval
     * 🔐 Permission: show_adjustments
     */
    public function show(Adjustment $adjustment)
    {
        abort_if(Gate::denies('show_adjustments'), 403);

        // Load relasi yang diperlukan
        $adjustment->load([
            'adjustedProducts.product.category', // Produk + kategori
            'adjustmentFiles', // Foto bukti
            'logs.user', // Riwayat dengan nama user
            'requester', // Nama pengaju
            'approver', // Nama penyetuju
        ]);

        return view('adjustment::show', compact('adjustment'));
    }

    /**
     * ✅ EDIT: Form edit adjustment (hanya jika pending)
     * 📝 Fitur: Hanya bisa edit jika status masih pending
     * 🔐 Permission: edit_adjustments
     */
    public function edit(Adjustment $adjustment)
    {
        abort_if(Gate::denies('edit_adjustments'), 403);

        if ($adjustment->status !== 'pending') {
            toast('❌ Hanya pengajuan PENDING yang bisa diedit! Status: ' . strtoupper($adjustment->status), 'error');
            return back();
        }

        $products = Product::query()
            ->select([
                'id',
                'product_name',
                'product_code', // <-- penting
                'product_unit', // <-- penting
                'product_quantity', // JANGAN pakai 'current_stock' (itu bukan kolom DB)
                'category_id',
            ])
            ->with(['category:id,category_name'])
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('product_name')
            ->get();

        $adjustment->load(['adjustedProducts.product']);

        return view('adjustment::edit', compact('adjustment', 'products'));
    }

    /**
     * ✅ UPDATE: Update adjustment (hanya jika pending)
     * 📝 Fitur:
     *    - Update produk
     *    - Add foto bukti tambahan
     *    - Log perubahan
     * 🔐 Permission: edit_adjustments
     */
    public function update(StoreAdjustmentRequest $request, Adjustment $adjustment)
    {
        abort_if(Gate::denies('edit_adjustments'), 403);

        // Validasi status
        if ($adjustment->status !== 'pending') {
            toast('❌ Hanya pengajuan PENDING yang bisa diupdate!', 'error');
            return back();
        }

        try {
            DB::transaction(function () use ($request, $adjustment) {
                // Update info dasar
                $adjustment->update([
                    'date' => $request->date,
                    'note' => $request->note,
                    'reason' => $request->reason ?? $adjustment->reason,
                    'description' => $request->description ?? $adjustment->description,
                ]);

                // Upload foto tambahan
                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $file) {
                        $path = $file->store('adjustment_files', 'public');
                        AdjustmentFile::create([
                            'adjustment_id' => $adjustment->id,
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                            'file_size' => $file->getSize(),
                            'mime_type' => $file->getMimeType(),
                        ]);
                    }
                }

                // Hapus produk lama
                $adjustment->adjustedProducts()->delete();

                // Insert produk baru
                foreach ($request->product_ids as $key => $productId) {
                    $product = Product::findOrFail($productId);
                    $quantity = (int) $request->quantities[$key];
                    $type = $request->types[$key];

                    // Validasi stok
                    if ($type === 'sub' && $product->current_stock < $quantity) {
                        throw new \Exception("Stok '{$product->product_name}' tidak cukup!");
                    }

                    AdjustedProduct::create([
                        'adjustment_id' => $adjustment->id,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'type' => $type,
                    ]);
                }

                // Log update
                AdjustmentLog::create([
                    'adjustment_id' => $adjustment->id,
                    'user_id' => Auth::id(),
                    'action' => 'submitted',
                    'old_status' => null,
                    'new_status' => 'pending',
                    'notes' => $request->note ?? 'Pengajuan dibuat',
                    'locked' => 1,
                ]);
            });

            toast('✅ Pengajuan berhasil diperbarui!', 'info');
            return redirect()->route('adjustments.index');
        } catch (\Exception $e) {
            toast('❌ Error: ' . $e->getMessage(), 'error');
            return back()->withInput();
        }
    }

    /**
     * ✅ DESTROY: Hapus adjustment (hanya jika pending)
     * 📝 Fitur: Soft validation, cek stok reversal
     * 🔐 Permission: delete_adjustments
     */
    public function destroy(Adjustment $adjustment)
    {
        abort_if(Gate::denies('delete_adjustments'), 403);

        // Validasi: hanya pending yang bisa dihapus
        if ($adjustment->status !== 'pending') {
            toast('❌ Hanya pengajuan PENDING yang bisa dihapus!', 'error');
            return back();
        }

        try {
            DB::transaction(function () use ($adjustment) {
                // Soft check: jika dihapus, apakah stok akan negatif?
                foreach ($adjustment->adjustedProducts as $adjustedProduct) {
                    $product = Product::findOrFail($adjustedProduct->product_id);
                    $reverseQty = $adjustedProduct->type === 'add' ? -$adjustedProduct->quantity : $adjustedProduct->quantity;

                    if ($product->current_stock + $reverseQty < 0) {
                        throw new \Exception("Tidak dapat menghapus. Stok '{$product->product_name}' akan negatif!");
                    }
                }

                // Hapus associated data
                $adjustment->adjustedProducts()->delete();
                $adjustment->adjustmentFiles()->delete(); // ✅ Hapus file references
                $adjustment->logs()->delete();

                // Hapus adjustment
                $adjustment->delete();

                // Log penghapusan
                AdjustmentLog::create([
                    'adjustment_id' => $adjustment->id,
                    'user_id' => Auth::id(),
                    'action' => 'delete',
                    'notes' => 'Penghapusan pengajuan penyesuaian',
                    'locked' => true,
                ]);
            });

            toast('✅ Pengajuan berhasil dihapus!', 'warning');
            return redirect()->route('adjustments.index');
        } catch (\Exception $e) {
            toast('❌ Error: ' . $e->getMessage(), 'error');
            return back();
        }
    }

    /**
     * ✅ APPROVALS: Halaman list approval (untuk Admin/Supervisor)
     * 📝 Fitur: Tampilkan summary pending/approved/rejected
     * 🔐 Permission: approve_adjustments
     */
    public function approvals()
    {
        abort_if(Gate::denies('approve_adjustments'), 403);

        // Summary counts
        $pendingCount = Adjustment::where('status', 'pending')->count();
        $approvedCount = Adjustment::where('status', 'approved')->count();
        $rejectedCount = Adjustment::where('status', 'rejected')->count();

        return view('adjustment::approvals', compact('pendingCount', 'approvedCount', 'rejectedCount'));
    }

    /**
     * ✅ GET PENDING ADJUSTMENTS: DataTable endpoint untuk approval list
     * 📝 Format: JSON response untuk Yajra DataTables
     * 🔐 Permission: approve_adjustments
     */
    public function getPendingAdjustments()
    {
        abort_if(Gate::denies('approve_adjustments'), 403);

        $adjustments = Adjustment::query()
            ->where('status', 'pending')
            ->with(['requester', 'adjustedProducts.product'])
            ->latest('id')
            ->get();

        return DataTables::of($adjustments)
            ->addIndexColumn()
            ->addColumn('requester_name', fn($row) => $row->requester?->name ?? 'System')
            ->addColumn('product_count', fn($row) => $row->adjustedProducts->count() . ' produk')
            ->addColumn('created_at_formatted', fn($row) => $row->created_at->format('d/m/Y H:i'))
            ->addColumn('actions', function ($row) {
                return view('adjustment::partials.actions-approval', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * ✅ APPROVE/REJECT: Handle approval decision
     * 📝 Fitur:
     *    - Validate action (approve/reject)
     *    - Update status & approver_id
     *    - Log perubahan status
     *    - Return JSON response
     * 🔐 Permission: approve_adjustments
     * 📌 Method: POST /adjustments/{adjustment}/approve?action=approve|reject
     */
    public function approve(Request $request, Adjustment $adjustment)
    {
        abort_if(Gate::denies('approve_adjustments'), 403);

        if ($adjustment->status !== 'pending') {
            $payload = [
                'success' => false,
                'message' => '❌ Status adjustment sudah ' . strtoupper($adjustment->status),
            ];
            // HTML form -> redirect back with flash
            if (!$request->expectsJson() && !$request->ajax()) {
                toast($payload['message'], 'error');
                return back();
            }
            return response()->json($payload, 422);
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'approval_notes' => 'nullable|string|max:500',
        ]);

        try {
            $newStatus = $validated['action'] === 'approve' ? 'approved' : 'rejected';

            DB::transaction(function () use ($adjustment, $validated, $newStatus) {
                $adjustment->update([
                    'status' => $newStatus,
                    'approver_id' => Auth::id(),
                    'approval_notes' => $validated['approval_notes'] ?? '',
                    'approval_date' => now(),
                ]);

                AdjustmentLog::create([
                    'adjustment_id' => $adjustment->id,
                    'user_id' => Auth::id(),
                    'action' => $newStatus,
                    'old_status' => 'pending',
                    'new_status' => $newStatus,
                    'notes' => $validated['approval_notes'] ?? 'Tanpa catatan',
                    'locked' => true,
                ]);
            });

            $message = $validated['action'] === 'approve' ? '✅ Penyesuaian berhasil DISETUJUI!' : '❌ Penyesuaian berhasil DITOLAK';

            // ====== HTML form (non-AJAX) -> redirect normal ======
            if (!$request->expectsJson() && !$request->ajax()) {
                toast($message, $newStatus === 'approved' ? 'success' : 'warning');
                // Balik ke detail atau ke daftar approvals – pilih salah satu:
                return redirect()->route('adjustments.show', $adjustment->id);
                // return redirect()->route('adjustments.approvals');
            }

            // ====== AJAX -> JSON response ======
            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $newStatus,
                'redirect' => route('adjustments.approvals'),
            ]);
        } catch (\Exception $e) {
            if (!$request->expectsJson() && !$request->ajax()) {
                toast('⚠️ Error: ' . $e->getMessage(), 'error');
                return back();
            }
            return response()->json(['success' => false, 'message' => '⚠️ Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * ✅ PDF: Generate laporan PDF adjustment
     * 📝 Fitur: Export ke PDF dengan detail lengkap
     * 🔐 Permission: show_adjustments
     */
    public function pdf(Adjustment $adjustment)
    {
        abort_if(Gate::denies('show_adjustments'), 403);

        // Load data
        $adjustment->load(['adjustedProducts.product.category', 'adjustmentFiles', 'requester', 'approver']);

        // Generate PDF
        $pdf = Pdf::loadView('adjustment::print', compact('adjustment'))
            ->setPaper('a4')
            ->setOrientation('portrait')
            ->setOptions([
                'margin-top' => 10,
                'margin-right' => 10,
                'margin-bottom' => 10,
                'margin-left' => 10,
                'enable-local-file-access' => true,
            ]);

        return $pdf->inline('Penyesuaian_Stok_' . $adjustment->reference . '.pdf');
    }
}
