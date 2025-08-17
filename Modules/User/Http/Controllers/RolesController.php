<?php

namespace Modules\User\Http\Controllers;

use Modules\User\DataTables\RolesDataTable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function index(RolesDataTable $dataTable) {
        abort_if(Gate::denies('access_user_management'), 403);

        return $dataTable->render('user::roles.index');
    }


    public function create() {
        abort_if(Gate::denies('access_user_management'), 403);

        return view('user::roles.create');
    }


    public function store(Request $request) {
        abort_if(Gate::denies('access_user_management'), 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'required|array'
        ]);

        $role = Role::create([
            'name' => $request->name
        ]);

        $role->givePermissionTo($request->permissions);

        toast('Role Created With Selected Permissions!', 'success');

        return redirect()->route('roles.index');
    }


    public function edit(Role $role) {
    abort_if(Gate::denies('access_user_management'), 403);

    $rolePermissions = $role->getPermissionNames()->toArray(); // ['show_total_stats', ...]
    return view('user::roles.edit', compact('role', 'rolePermissions'));
}



    public function update(Request $request, Role $role) {

    abort_if(Gate::denies('access_user_management'), 403);

    $request->validate([
        'name' => 'required|string|max:255',
        'permissions' => 'required|array'
    ]);

    $role->update(['name' => $request->name]);

    // Pastikan semua permission yang dikirim form sudah ada di DB (guard web)
    foreach ($request->permissions as $permName) {
        \Spatie\Permission\Models\Permission::firstOrCreate(
            ['name' => $permName, 'guard_name' => 'web']
        );
    }

    // Sinkronkan
    $role->syncPermissions($request->permissions);

    // Reset cache permission supaya efeknya langsung
    app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

    toast('Role Updated With Selected Permissions!', 'success');
    return redirect()->route('roles.index');
}



    public function destroy(Role $role) {
        abort_if(Gate::denies('access_user_management'), 403);

        $role->delete();

        toast('Role Deleted!', 'success');

        return redirect()->route('roles.index');
    }
}
