<?php

namespace Modules\People\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\People\DataTables\SuppliersDataTable;
use Modules\People\Entities\Supplier;
use Modules\People\Http\Requests\StoreSupplierRequest;
use Modules\People\Http\Requests\UpdateSupplierRequest;

class SuppliersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param SuppliersDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(SuppliersDataTable $dataTable)
    {
        abort_if(Gate::denies('access_suppliers'), 403);

        return $dataTable->render('people::suppliers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('create_suppliers'), 403);

        // Get list kota untuk dropdown (dari data existing)
        $cities = Supplier::getUniqueCities();

        return view('people::suppliers.create', compact('cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreSupplierRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSupplierRequest $request)
    {
        // Authorization sudah dicek di Request class

        try {
            // Data sudah tervalidasi dan tersanitasi otomatis dari Request
            $validated = $request->validated();

            // Create supplier baru
            $supplier = Supplier::create($validated);

            // Log activity (opsional - jika ada activity log)
            // activity()
            //     ->causedBy(auth()->user())
            //     ->performedOn($supplier)
            //     ->log('Menambahkan supplier baru: ' . $supplier->supplier_name);

            toast('Supplier berhasil ditambahkan!', 'success');

            return redirect()->route('suppliers.index');
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Error creating supplier: ' . $e->getMessage());

            toast('Gagal menambahkan supplier. Silakan coba lagi.', 'error');

            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        abort_if(Gate::denies('show_suppliers'), 403);

        // Load relasi purchases dengan summary
        $supplier->load([
            'purchases' => function ($query) {
                $query->latest()->take(10); // 10 transaksi terakhir
            },
        ]);

        // Hitung statistik supplier
        $stats = [
            'total_purchases' => $supplier->purchases()->count(),
            'total_amount' => $supplier->purchases()->sum('total_amount'),
            'total_paid' => $supplier->purchases()->sum('paid_amount'),
            'total_due' => $supplier->purchases()->sum('due_amount'),
            'last_purchase_date' => $supplier->purchases()->latest('date')->value('date'),
        ];

        return view('people::suppliers.show', compact('supplier', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        abort_if(Gate::denies('edit_suppliers'), 403);

        // Get list kota untuk dropdown
        $cities = Supplier::getUniqueCities();

        // Cek apakah supplier punya purchase history
        $hasPurchases = $supplier->purchases()->exists();

        return view('people::suppliers.edit', compact('supplier', 'cities', 'hasPurchases'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateSupplierRequest $request
     * @param Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        // Authorization sudah dicek di Request class

        try {
            // Data sudah tervalidasi dan tersanitasi otomatis
            $validated = $request->validated();

            // Update supplier
            $supplier->update($validated);

            // Log activity (opsional)
            // activity()
            //     ->causedBy(auth()->user())
            //     ->performedOn($supplier)
            //     ->log('Mengubah data supplier: ' . $supplier->supplier_name);

            toast('Data supplier berhasil diperbarui!', 'success');

            return redirect()->route('suppliers.index');
        } catch (\Exception $e) {
            \Log::error('Error updating supplier: ' . $e->getMessage());

            toast('Gagal memperbarui data supplier. Silakan coba lagi.', 'error');

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     * Menggunakan soft delete untuk keamanan data
     *
     * @param Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        abort_if(Gate::denies('delete_suppliers'), 403);

        try {
            // Cek apakah supplier punya purchase history
            if ($supplier->purchases()->exists()) {
                // TIDAK BOLEH HAPUS - Gunakan soft delete
                $supplier->delete(); // Soft delete

                toast('Supplier diarsipkan karena memiliki riwayat pembelian. Data masih bisa dikembalikan.', 'warning');

                return redirect()->route('suppliers.index');
            }

            // Jika tidak ada purchase history, boleh hard delete
            $supplierName = $supplier->supplier_name;
            $supplier->forceDelete(); // Hard delete

            toast("Supplier '{$supplierName}' berhasil dihapus permanen.", 'success');

            return redirect()->route('suppliers.index');
        } catch (\Exception $e) {
            \Log::error('Error deleting supplier: ' . $e->getMessage());

            toast('Gagal menghapus supplier. Silakan coba lagi.', 'error');

            return redirect()->back();
        }
    }

    /**
     * API Endpoint: Get suppliers list untuk dropdown (Select2)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSuppliers(Request $request)
    {
        // Untuk dropdown di form Purchase
        $query = Supplier::query();

        // Filter by search term (untuk Select2)
        if ($request->has('q')) {
            $query->search($request->q);
        }

        // Ambil hanya yang aktif (tidak soft deleted)
        $query->active();

        // Select kolom yang diperlukan saja
        $suppliers = $query
            ->select('id', 'supplier_name', 'city')
            ->limit(20) // Batasi hasil untuk performa
            ->get()
            ->map(function ($supplier) {
                return [
                    'id' => $supplier->id,
                    'text' => $supplier->full_name, // "Toko ABC - Jakarta"
                ];
            });

        return response()->json($suppliers);
    }

    /**
     * Show archived (soft deleted) suppliers
     *
     * @return \Illuminate\Http\Response
     */
    public function archived()
    {
        abort_if(Gate::denies('access_suppliers'), 403);

        $suppliers = Supplier::onlyTrashed()->with('purchases')->orderBy('deleted_at', 'desc')->paginate(20);

        return view('people::suppliers.archived', compact('suppliers'));
    }

    /**
     * Restore soft deleted supplier
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        abort_if(Gate::denies('edit_suppliers'), 403);

        try {
            $supplier = Supplier::onlyTrashed()->findOrFail($id);
            $supplier->restore();

            toast("Supplier '{$supplier->supplier_name}' berhasil dikembalikan!", 'success');

            return redirect()->route('suppliers.index');
        } catch (\Exception $e) {
            \Log::error('Error restoring supplier: ' . $e->getMessage());

            toast('Gagal mengembalikan supplier. Silakan coba lagi.', 'error');

            return redirect()->back();
        }
    }

    /**
     * Export suppliers to Excel/PDF (opsional - untuk report)
     *
     * @param Request $request
     * @return mixed
     */
    public function export(Request $request)
    {
        abort_if(Gate::denies('access_suppliers'), 403);

        // Implementasi export menggunakan Laravel Excel
        // return Excel::download(new SuppliersExport, 'suppliers.xlsx');

        toast('Fitur export sedang dalam pengembangan.', 'info');
        return redirect()->back();
    }

    /**
     * Get supplier statistics (untuk dashboard)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics()
    {
        abort_if(Gate::denies('access_suppliers'), 403);

        $stats = [
            'total_suppliers' => Supplier::count(),
            'active_suppliers' => Supplier::whereHas('purchases', function ($query) {
                $query->where('date', '>=', now()->subMonths(6));
            })->count(),
            'suppliers_by_city' => Supplier::select('city', DB::raw('count(*) as total'))->groupBy('city')->orderByDesc('total')->limit(5)->get(),
            'top_suppliers' => Supplier::withSum('purchases', 'total_amount')
                ->orderByDesc('purchases_sum_total_amount')
                ->limit(5)
                ->get()
                ->map(function ($supplier) {
                    return [
                        'name' => $supplier->supplier_name,
                        'total' => $supplier->purchases_sum_total_amount ?? 0,
                    ];
                }),
        ];

        return response()->json($stats);
    }
}
