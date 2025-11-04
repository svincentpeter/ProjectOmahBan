<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Entities\ServiceMaster;
use Modules\Product\Entities\ServiceMasterAudit;
use Modules\Product\DataTables\ServiceMasterDataTable;

class ServiceMasterController extends Controller
{
    /**
     * Halaman utama kelola master jasa
     */
    public function index(ServiceMasterDataTable $dataTable)
    {
        return $dataTable->render('product::service-masters.index');
    }

    /**
     * DataTable Data (API)
     * Dipanggil oleh DataTable AJAX
     */
    public function data(ServiceMasterDataTable $dataTable)
    {
        return $dataTable->render('product::service-masters.index');
    }

    /**
     * Simpan jasa baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:100|unique:service_masters,service_name',
            'standard_price' => 'required|integer|min:0',
            'category' => 'required|in:service,goods,custom',
            'description' => 'nullable|string|max:500',
        ]);

        $service = ServiceMaster::create(array_merge($validated, ['status' => 1]));

        // Return JSON response untuk AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Jasa '{$service->service_name}' berhasil ditambahkan!",
                'data' => $service,
            ]);
        }

        return redirect()
            ->route('service-masters.index')
            ->with('success', "Jasa '{$service->service_name}' berhasil ditambahkan!");
    }

    /**
     * Update jasa
     */
    public function update(Request $request, $id)
    {
        $serviceMaster = ServiceMaster::findOrFail($id);

        $validated = $request->validate([
            'service_name' => 'required|string|max:100|unique:service_masters,service_name,' . $serviceMaster->id,
            'standard_price' => 'required|integer|min:0',
            'category' => 'required|in:service,goods,custom',
            'description' => 'nullable|string|max:500',
        ]);

        $oldPrice = $serviceMaster->standard_price;
        $newPrice = $validated['standard_price'];

        if ($oldPrice !== $newPrice) {
            ServiceMasterAudit::create([
                'service_master_id' => $serviceMaster->id,
                'old_price' => $oldPrice,
                'new_price' => $newPrice,
                'reason' => $request->input('price_change_reason'),
                'changed_by' => auth()->id(),
            ]);

            $serviceMaster->update([
                'price_before' => $oldPrice,
                'price_after' => $newPrice,
                'price_updated_at' => now(),
                'updated_by' => auth()->id(),
            ]);
        }

        $serviceMaster->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jasa berhasil diperbarui!',
                'data' => $serviceMaster,
            ]);
        }

        return redirect()
            ->route('service-masters.index')
            ->with('success', "Jasa '{$serviceMaster->service_name}' berhasil diperbarui!");
    }

    /**
     * Toggle status jasa (aktif/nonaktif)
     */
    public function toggleStatus($id)
    {
        $serviceMaster = ServiceMaster::findOrFail($id);
        $serviceMaster->update(['status' => !$serviceMaster->status]);

        return back()->with('success', 'Status jasa berhasil diubah.');
    }

    /**
     * Hapus jasa
     */
    public function destroy($id)
    {
        $serviceMaster = ServiceMaster::findOrFail($id);
        $name = $serviceMaster->service_name;
        $serviceMaster->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Jasa '{$name}' berhasil dihapus.",
            ]);
        }

        return back()->with('success', "Jasa '{$name}' berhasil dihapus.");
    }

    /**
     * Lihat history perubahan harga jasa
     */
    public function auditLog($id)
    {
        $serviceMaster = ServiceMaster::findOrFail($id);
        $audits = $serviceMaster->audits()->with('changedBy')->orderByDesc('created_at')->paginate(20);

        return view('product::service-masters.audit-log', compact('serviceMaster', 'audits'));
    }
}
