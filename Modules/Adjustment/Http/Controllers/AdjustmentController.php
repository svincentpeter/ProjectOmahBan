<?php

namespace Modules\Adjustment\Http\Controllers;

// ===== BASE CLASSES =====
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// ===== MODELS & ENTITIES =====
use Modules\Adjustment\Entities\Adjustment;
use Modules\Adjustment\Entities\AdjustedProduct;
use Modules\Adjustment\Entities\AdjustmentFile;
use Modules\Adjustment\Entities\AdjustmentLog;
use Modules\Adjustment\Entities\StockMovement;
use Modules\Product\Entities\Product;

// ===== REQUESTS =====
use Modules\Adjustment\Http\Requests\StoreAdjustmentRequest;

// ===== FACADES =====
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// ===== DATA TABLES =====
use Yajra\DataTables\DataTables;
use Modules\Adjustment\DataTables\AdjustmentsDataTable;

// ===== PDF =====
use Barryvdh\DomPDF\Facade\Pdf;

class AdjustmentController extends Controller
{
    /**
     * INDEX
     */
    public function index(AdjustmentsDataTable $dataTable)
    {
        abort_if(Gate::denies('access_adjustments'), 403);

        // Basis query: hormati soft delete + visibilitas user
        $base = Adjustment::query()->whereNull('deleted_at');

        // Jika user TIDAK punya izin melihat semua, batasi ke dirinya saja
        if (!Auth::user()->can('approve_adjustments') && !Auth::user()->can('view_all_adjustments')) {
            $base->where('requester_id', Auth::id());
        }

        // Hitung kartu dari scope yang sama
        $stats = [
            'total' => (clone $base)->count(),
            'pending' => (clone $base)->where('status', 'pending')->count(),
            'approved' => (clone $base)->where('status', 'approved')->count(),
            'rejected' => (clone $base)->where('status', 'rejected')->count(),
        ];

        // Kirim $stats ke Blade
        return $dataTable->render('adjustment::index', compact('stats'));
    }

    /**
     * LEGACY: DataTables AJAX endpoint (gunakan AdjustmentsDataTable jika memungkinkan)
     */
    public function getDataTable(Request $request)
    {
        abort_if(Gate::denies('access_adjustments'), 403);

        $query = Adjustment::with(['requester', 'adjustedProducts'])
            ->whereNull('deleted_at')
            ->latest('created_at');

        // Batasi visibilitas bila perlu
        if (!Auth::user()->can('approve_adjustments') && !Auth::user()->can('view_all_adjustments')) {
            $query->where('requester_id', Auth::id());
        }

        // === Filter dari DataTables (sesuai JS preXhr) ===
        $status = $request->input('status'); // '', 'pending', 'approved', 'rejected'
        $type = $request->input('type'); // '', 'add', 'sub' (ada di adjusted_products)
        $requesterId = $request->input('requester_id'); // '', <id>
        $from = $request->input('date_from'); // yyyy-mm-dd
        $to = $request->input('date_to'); // yyyy-mm-dd

        $query->when($status, fn($q) => $q->where('status', $status));
        $query->when($requesterId, fn($q) => $q->where('requester_id', $requesterId));

        // Filter tipe via relasi detail (header tidak punya kolom `type`)
        $query->when($type, function ($q) use ($type) {
            $q->whereHas('adjustedProducts', fn($w) => $w->where('type', $type));
        });

        // Filter tanggal hanya jika ada input (hindari default menyempit)
        $query->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))->when($to, fn($q) => $q->whereDate('created_at', '<=', $to));

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('requester_name', fn($a) => $a->requester->name ?? '-')
            ->addColumn('product_count', fn($a) => $a->adjustedProducts->count() . ' produk')
            ->addColumn('status', fn($a) => $a->status)
            ->addColumn('actions', fn($a) => view('adjustment::partials.actions', compact('a'))->render())
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * CREATE
     */
    public function create()
    {
        abort_if(Gate::denies('create_adjustments'), 403);

        $products = Product::query()
            ->select([
                'id',
                'product_name',
                'product_code',
                'product_unit',
                'product_quantity', // stok kolom yang benar
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
     * STORE
     */
    public function store(StoreAdjustmentRequest $request)
    {
        abort_if(Gate::denies('create_adjustments'), 403);

        try {
            DB::transaction(function () use ($request) {
                // Generate reference yang aman dari race condition
                $reference = $this->generateReference();

                // Buat adjustment
                $adjustment = Adjustment::create([
                    'date' => $request->date ?? now()->toDateString(),
                    'reference' => $reference,
                    'note' => $request->note,
                    'reason' => $request->reason,
                    'description' => $request->description,
                    'requester_id' => Auth::id(),
                    'status' => 'pending',
                ]);

                // Upload file bukti (UI limit 3; back-end tetap aman)
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

                // Detail produk
                foreach ($request->product_ids as $i => $productId) {
                    $product = Product::findOrFail($productId);
                    $quantity = (int) $request->quantities[$i];
                    $type = $request->types[$i]; // 'add' | 'sub'

                    // Validasi stok pakai product_quantity
                    if ($type === 'sub' && $product->product_quantity < $quantity) {
                        throw new \Exception("Stok produk '{$product->product_name}' tidak cukup. Sisa: {$product->product_quantity}, Kurangi: {$quantity}");
                    }

                    AdjustedProduct::create([
                        'adjustment_id' => $adjustment->id,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'type' => $type,
                    ]);
                }

                // Audit log: CREATED
                AdjustmentLog::create([
                    'adjustment_id' => $adjustment->id,
                    'user_id' => Auth::id(),
                    'action' => 'created',
                    'old_status' => null,
                    'new_status' => 'pending',
                    'notes' => $request->note ?? 'Pengajuan baru',
                    'locked' => 1,
                ]);
            });

            toast('✅ Pengajuan penyesuaian berhasil! Menunggu persetujuan.', 'info');
            return redirect()->route('adjustments.index');
        } catch (\Throwable $e) {
            Log::error('Adjustment Store Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            toast('❌ Error: ' . $e->getMessage(), 'error');
            return back()->withInput();
        }
    }

    /**
     * SHOW
     */
    public function show(Adjustment $adjustment)
    {
        abort_if(Gate::denies('show_adjustments'), 403);

        $adjustment->load(['adjustedProducts.product.category', 'adjustmentFiles', 'logs.user', 'requester', 'approver']);

        return view('adjustment::show', compact('adjustment'));
    }

    /**
     * EDIT
     */
    public function edit(Adjustment $adjustment)
    {
        abort_if(Gate::denies('edit_adjustments'), 403);

        if ($adjustment->status !== 'pending') {
            toast('❌ Hanya pengajuan PENDING yang bisa diedit! Status: ' . strtoupper($adjustment->status), 'error');
            return back();
        }

        $products = Product::query()
            ->select(['id', 'product_name', 'product_code', 'product_unit', 'product_quantity', 'category_id'])
            ->with(['category:id,category_name'])
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('product_name')
            ->get();

        $adjustment->load(['adjustedProducts.product']);

        return view('adjustment::edit', compact('adjustment', 'products'));
    }

    /**
     * UPDATE
     */
    public function update(StoreAdjustmentRequest $request, Adjustment $adjustment)
    {
        abort_if(Gate::denies('edit_adjustments'), 403);

        if ($adjustment->status !== 'pending') {
            toast('❌ Hanya pengajuan PENDING yang bisa diupdate!', 'error');
            return back();
        }

        try {
            DB::transaction(function () use ($request, $adjustment) {
                $adjustment->update([
                    'date' => $request->date,
                    'note' => $request->note,
                    'reason' => $request->reason ?? $adjustment->reason,
                    'description' => $request->description ?? $adjustment->description,
                ]);

                // Upload tambahan
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

                // Reset detail lalu isi ulang
                $adjustment->adjustedProducts()->delete();

                foreach ($request->product_ids as $i => $productId) {
                    $product = Product::findOrFail($productId);
                    $quantity = (int) $request->quantities[$i];
                    $type = $request->types[$i];

                    if ($type === 'sub' && $product->product_quantity < $quantity) {
                        throw new \Exception("Stok '{$product->product_name}' tidak cukup!");
                    }

                    AdjustedProduct::create([
                        'adjustment_id' => $adjustment->id,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'type' => $type,
                    ]);
                }

                // Log update (tetap pending)
                AdjustmentLog::create([
                    'adjustment_id' => $adjustment->id,
                    'user_id' => Auth::id(),
                    'action' => 'updated',
                    'old_status' => 'pending',
                    'new_status' => 'pending',
                    'notes' => $request->note ?? 'Perubahan pengajuan',
                    'locked' => 1,
                ]);
            });

            toast('✅ Pengajuan berhasil diperbarui!', 'info');
            return redirect()->route('adjustments.index');
        } catch (\Throwable $e) {
            Log::error('Adjustment Update Error: ' . $e->getMessage());
            toast('❌ Error: ' . $e->getMessage(), 'error');
            return back()->withInput();
        }
    }

    /**
     * DESTROY
     */
    public function destroy(Adjustment $adjustment)
    {
        abort_if(Gate::denies('delete_adjustments'), 403);

        if ($adjustment->status !== 'pending') {
            toast('❌ Hanya pengajuan PENDING yang bisa dihapus!', 'error');
            return back();
        }

        try {
            DB::transaction(function () use ($adjustment) {
                // Tidak perlu cek stok karena PENDING belum memengaruhi stok

                // Hapus file fisik (opsional)
                foreach ($adjustment->adjustmentFiles as $f) {
                    try {
                        Storage::disk('public')->delete($f->file_path);
                    } catch (\Throwable $th) {
                        // abaikan
                    }
                }

                // Hapus relasi
                $adjustment->adjustedProducts()->delete();
                $adjustment->adjustmentFiles()->delete();
                $adjustment->logs()->delete();

                // Hapus adjustment
                $adjustment->delete();
            });

            toast('✅ Pengajuan berhasil dihapus!', 'warning');
            return redirect()->route('adjustments.index');
        } catch (\Throwable $e) {
            Log::error('Adjustment Destroy Error: ' . $e->getMessage());
            toast('❌ Error: ' . $e->getMessage(), 'error');
            return back();
        }
    }

    /**
     * APPROVALS (halaman)
     */
    public function approvals()
    {
        abort_if(Gate::denies('approve_adjustments'), 403);

        $pendingCount = Adjustment::where('status', 'pending')->count();
        $approvedCount = Adjustment::where('status', 'approved')->count();
        $rejectedCount = Adjustment::where('status', 'rejected')->count();
        $urgentCount = Adjustment::where('status', 'pending')
            ->where('created_at', '<', now()->subDays(7))
            ->count();

        return view('adjustment::approvals', compact('pendingCount', 'approvedCount', 'rejectedCount', 'urgentCount'));
    }

    /**
     * DataTables source untuk approvals.blade
     * Route name: adjustments.getPendingAdjustments
     */
    public function pendingDatatable(Request $request)
    {
        abort_if(Gate::denies('approve_adjustments'), 403);

        $query = Adjustment::with(['requester', 'adjustedProducts'])
            ->where('status', 'pending')
            ->latest('created_at');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('requester_name', fn($a) => $a->requester->name ?? '-')
            ->addColumn('product_count', fn($a) => $a->adjustedProducts->count())
            ->addColumn('created_at_formatted', fn($a) => optional($a->created_at)->format('d/m/Y H:i'))
            ->addColumn('actions', function ($row) {
                return view('adjustment::partials.actions-approval', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * APPROVE / REJECT
     * Route: POST /adjustments/{adjustment}/approve
     */
    public function approve(Request $request, Adjustment $adjustment)
    {
        abort_if(Gate::denies('approve_adjustments'), 403);

        if ($adjustment->status !== 'pending') {
            toast('❌ Hanya pengajuan PENDING yang bisa diproses!', 'error');
            return back();
        }

        $validated = $request->validate([
            'action' => ['required', Rule::in(['approve', 'reject'])],
            'approval_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $action = $validated['action'];
        $newStatus = $action === 'approve' ? 'approved' : 'rejected';

        try {
            DB::transaction(function () use ($adjustment, $newStatus, $request) {
                // Pastikan relasi siap
                $adjustment->loadMissing('adjustedProducts');

                // Update header
                $adjustment->update([
                    'status' => $newStatus,
                    'approver_id' => Auth::id(),
                    'approval_date' => now(),
                    'approval_notes' => $request->approval_notes,
                ]);

                // Jika approved: update stok & catat pergerakan
                if ($newStatus === 'approved') {
                    foreach ($adjustment->adjustedProducts as $item) {
                        $product = Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail();

                        $delta = $item->type === 'add' ? $item->quantity : -$item->quantity;
                        $after = $product->product_quantity + $delta;

                        if ($after < 0) {
                            throw new \Exception("Stok '{$product->product_name}' akan negatif! Sisa: {$product->product_quantity}, Perubahan: {$delta}");
                        }

                        $product->update(['product_quantity' => $after]);

                        // === A) Rekomendasi: gunakan polymorphic ref_type/ref_id ===
                        // StockMovement::record([
                        //     'product_id'  => $product->id,
                        //     'type'        => $item->type === 'add' ? 'in' : 'out',
                        //     'quantity'    => $item->quantity,
                        //     'description' => "Adjustment {$adjustment->reference} - {$item->type}",
                        //     'user_id'     => Auth::id(),
                        //     'ref'         => $adjustment, // biar helper set ref_type/ref_id
                        // ]);

                        // === B) Fallback: jika masih memakai kolom adjustment_id ===
                        StockMovement::record([
                            'product_id' => $product->id,
                            'type' => $item->type === 'add' ? 'in' : 'out',
                            'quantity' => $item->quantity,
                            'description' => "Adjustment {$adjustment->reference} - {$item->type}",
                            'user_id' => Auth::id(),
                            'adjustment_id' => $adjustment->id,
                        ]);
                    }
                }

                // Audit log
                AdjustmentLog::create([
                    'adjustment_id' => $adjustment->id,
                    'user_id' => Auth::id(),
                    'action' => $newStatus, // 'approved' / 'rejected'
                    'old_status' => 'pending',
                    'new_status' => $newStatus,
                    'notes' => $request->approval_notes ?? ucfirst($newStatus),
                    'locked' => 1,
                ]);
            });

            if ($newStatus === 'approved') {
                toast('✅ Pengajuan DISETUJUI dan stok telah diperbarui!', 'success');
            } else {
                toast('❌ Pengajuan DITOLAK!', 'warning');
            }

            return redirect()->route('adjustments.approvals');
        } catch (\Throwable $e) {
            Log::error('Adjustment Approve Error: ' . $e->getMessage());
            toast('❌ Error: ' . $e->getMessage(), 'error');
            return back();
        }
    }

    /**
     * PDF
     */
    public function pdf(Adjustment $adjustment)
    {
        abort_if(Gate::denies('show_adjustments'), 403);

        $adjustment->load(['adjustedProducts.product.category', 'adjustmentFiles', 'requester', 'approver', 'logs.user']);

        try {
            $pdf = Pdf::loadView('adjustment::pdf', compact('adjustment'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true, // izinkan asset eksternal (logo, css cdn)
                ]);

            return $pdf->download("Adjustment_{$adjustment->reference}.pdf");
        } catch (\Throwable $e) {
            Log::warning("PDF generation failed: {$e->getMessage()}");
            return view('adjustment::pdf', compact('adjustment'));
        }
    }

    /**
     * EXPORT (CSV)
     * Route: GET /adjustments/export?status=&requester_id=&start_date=&end_date=&q=
     */
    public function export(Request $request)
    {
        abort_if(Gate::denies('access_adjustments'), 403);

        try {
            // DUKUNG KEDUA NAMA PARAM (kompatibel dgn JS sekarang)
            $startDate = $request->input('start_date') ?? ($request->input('date_from') ?? now()->subDays(30)->toDateString());
            $endDate = $request->input('end_date') ?? ($request->input('date_to') ?? now()->toDateString());

            $status = $request->input('status'); // optional
            $requesterId = $request->input('requester_id'); // optional
            $q = $request->input('q'); // optional

            $query = Adjustment::with(['requester', 'adjustedProducts.product', 'approver'])
                ->whereBetween('date', [$startDate, $endDate]) // tetap pakai kolom 'date' untuk export
                ->latest('date');

            if ($status) {
                $query->where('status', $status);
            }
            if ($requesterId) {
                $query->where('requester_id', $requesterId);
            }
            if ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('reference', 'like', "%{$q}%")
                        ->orWhere('reason', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            }

            $adjustments = $query->get();
            $filename = 'adjustments_' . now()->format('Ymd_His') . '.csv';

            return response()->streamDownload(
                function () use ($adjustments) {
                    $out = fopen('php://output', 'w');
                    fputcsv($out, ['Tanggal', 'Reference', 'Requester', 'Produk', 'Alasan', 'Status', 'Approver', 'Tgl Approval']);
                    foreach ($adjustments as $adj) {
                        $products = $adj->adjustedProducts->map(fn($i) => ($i->product->product_name ?? '-') . ' (' . $i->formatted_quantity . ')')->implode(', ');
                        $safe = function ($v) {
                            $v = (string) ($v ?? '-');
                            return preg_match('/^[=+\-@]/', $v) ? "'" . $v : $v;
                        };
                        fputcsv($out, [$adj->date ? $adj->date->format('d/m/Y') : '-', $safe($adj->reference), $safe($adj->requester->name ?? '-'), $safe($products), $safe($adj->reason ?? '-'), strtoupper($adj->status), $safe($adj->approver->name ?? '-'), $adj->approval_date ? $adj->approval_date->format('d/m/Y H:i') : '-']);
                    }
                    fclose($out);
                },
                $filename,
                ['Content-Type' => 'text/csv; charset=UTF-8'],
            );
        } catch (\Throwable $e) {
            Log::error('Adjustment Export Error: ' . $e->getMessage());
            toast('❌ Error export: ' . $e->getMessage(), 'error');
            return back();
        }
    }

    // ==========================================================
    // Helpers
    // ==========================================================

    /**
     * Generate reference ADJ-YYYYMMDD-##### dengan mitigasi race condition.
     * Saran: tambahkan unique index di kolom `reference`.
     */
    private function generateReference(int $maxRetries = 5): string
    {
        $dateCode = now()->format('Ymd');

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            // Kunci baris "hari ini" untuk menghindari duplikasi urutan
            $lastToday = Adjustment::whereDate('created_at', today())->lockForUpdate()->latest('id')->first();

            $lastSeq = 0;
            if ($lastToday && !empty($lastToday->reference)) {
                $tail = substr($lastToday->reference, -5);
                if (ctype_digit($tail)) {
                    $lastSeq = (int) $tail;
                }
            }

            $reference = 'ADJ-' . $dateCode . '-' . str_pad($lastSeq + 1, 5, '0', STR_PAD_LEFT);

            // Jika pakai unique index, kita cek cepat agar early-exit
            $exists = Adjustment::where('reference', $reference)->exists();
            if (!$exists) {
                return $reference;
            }

            // jika tabrakan, coba lagi
        }

        // Fallback ekstrem (timestamp)
        return 'ADJ-' . $dateCode . '-' . substr((string) now()->timestamp, -5);
    }
}
