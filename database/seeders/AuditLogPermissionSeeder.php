<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuditLogPermissionSeeder extends Seeder
{
    public function run()
    {
        // Create audit log permissions
        $permissions = [
            'access_audit_log',
            'view_audit_log_details',
            'export_audit_log',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Assign to Owner, Super Admin, and Admin roles
        $roles = ['Owner', 'Super Admin', 'Admin'];
        
        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo($permissions);
            }
        }

        $this->command->info('Audit log permissions created and assigned!');
    }
}
