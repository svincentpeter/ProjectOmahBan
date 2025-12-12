<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Setting\Entities\Unit;

class   UnitsController extends Controller
{

    public function index(Request $request) {
        $query = Unit::query()->orderBy('name');

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_name', 'like', "%{$search}%");
            });
        }

        $units = $query->paginate(15);

        return view('setting::units.index', compact('units'));
    }

    public function create() {
        return view('setting::units.create');
    }

    public function store(Request $request) {
        $request->validate([
            'name'       => 'required|string|max:255',
            'short_name' => 'required|string|max:255'
        ]);

        Unit::create([
            'name'            => $request->name,
            'short_name'      => $request->short_name,
            'operator'        => $request->operator,
            'operation_value' => $request->operation_value,
        ]);

        session()->flash('swal-success', 'Satuan berhasil dibuat!');

        return redirect()->route('units.index');
    }

    public function edit(Unit $unit) {
        return view('setting::units.edit', [
            'unit' => $unit
        ]);
    }

    public function update(Request $request, Unit $unit) {
        $request->validate([
            'name'       => 'required|string|max:255',
            'short_name' => 'required|string|max:255'
        ]);

        $unit->update([
            'name'            => $request->name,
            'short_name'      => $request->short_name,
            'operator'        => $request->operator,
            'operation_value' => $request->operation_value,
        ]);

        session()->flash('swal-success', 'Satuan berhasil diperbarui!');

        return redirect()->route('units.index');
    }

    public function destroy(Unit $unit) {
        $unit->delete();

        session()->flash('swal-warning', 'Satuan berhasil dihapus!');

        return redirect()->route('units.index');
    }
}
