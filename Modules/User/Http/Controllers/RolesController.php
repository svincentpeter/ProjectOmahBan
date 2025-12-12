<?php

namespace Modules\User\Http\Controllers;

use Modules\User\DataTables\RolesDataTable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function index(Request $request) {
        abort_if(Gate::denies('access_user_management'), 403);

        $query = Role::query()->orderBy('name');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $roles = $query->withCount('users')->paginate(15);

        return view('user::roles.index', compact('roles'));
    }


    public function create() {
        abort_if(Gate::denies('access_user_management'), 403);

        return view('user::roles.create');
    }


    public function store(Request $request) {
        abort_if(Gate::denies('access_user_management'), 403);

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array'
        ]);

        $role = Role::create([
            'name' => $request->name
        ]);

        $role->givePermissionTo($request->permissions);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        toast('Peran berhasil dibuat dengan hak akses yang dipilih!', 'success');

        return redirect()->route('roles.index');
    }


    public function edit(Role $role) {
    abort_if(Gate::denies('access_user_management'), 403);

    // Baris ini mengambil semua nama permission yang dimiliki oleh peran ini
    $rolePermissions = $role->getPermissionNames()->toArray(); 

    // Pastikan 'rolePermissions' ada di dalam compact()
    return view('user::roles.edit', compact('role', 'rolePermissions'));
}


    public function update(Request $request, Role $role) {
        abort_if(Gate::denies('access_user_management'), 403);

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'required|array'
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        toast('Peran berhasil diperbarui dengan hak akses yang dipilih!', 'info');

        return redirect()->route('roles.index');
    }


    public function destroy(Role $role) {
        abort_if(Gate::denies('access_user_management'), 403);

        $role->delete();

        toast('Peran berhasil dihapus!', 'warning');

        return redirect()->route('roles.index');
    }
}