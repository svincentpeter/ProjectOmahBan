<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
{
    $permissions = [
        // User Management
        'edit_own_profile',
        'access_user_management',
        // Dashboard
        'show_total_stats',
        'show_month_overview',
        'show_weekly_sales_purchases',
        'show_monthly_cashflow',
        'show_notifications',
        // Products
        'access_products','create_products','show_products','edit_products','delete_products',
        // Product Categories
        'access_product_categories',
        // Barcode
        'print_barcodes',
        // Adjustments
        'access_adjustments','create_adjustments','show_adjustments','edit_adjustments','delete_adjustments',
        // Quotations
        'access_quotations','create_quotations','show_quotations','edit_quotations','delete_quotations',
        'create_quotation_sales','send_quotation_mails',
        // Expenses
        'access_expenses','create_expenses','edit_expenses','delete_expenses',
        'access_expense_categories',
        // Customers
        'access_customers','create_customers','show_customers','edit_customers','delete_customers',
        // Suppliers
        'access_suppliers','create_suppliers','show_suppliers','edit_suppliers','delete_suppliers',
        // Sales
        'access_sales','create_sales','show_sales','edit_sales','delete_sales',
        'create_pos_sales',
        'access_sale_payments',
        // Sale Returns
        'access_sale_returns','create_sale_returns','show_sale_returns','edit_sale_returns','delete_sale_returns',
        'access_sale_return_payments',
        // Purchases
        'access_purchases','create_purchases','show_purchases','edit_purchases','delete_purchases',
        'access_purchase_payments',
        // Purchase Returns
        'access_purchase_returns','create_purchase_returns','show_purchase_returns','edit_purchase_returns','delete_purchase_returns',
        'access_purchase_return_payments',
        // Reports
        'access_reports',
        // Currencies
        'access_currencies','create_currencies','edit_currencies','delete_currencies',
        // Settings
        'access_settings',
        // Units
        'access_units', 'create_units', 'edit_units', 'delete_units',

        // POS
        'pos.override_price_limit', 'pos.approve_discount', 'pos.view_cost_price',

        // Inventory
        'inventory.edit_hpp', 'inventory.approve_hpp_override',

        // Reports Extra
        'report.view_deviation', 'report.view_activity_log', 'report.export_sensitive',

        // Settings Extra
        'settings.manage_service_standards', 'settings.view_system_log',

        // Adjustments Extra
        'approve_adjustments',

        // Stock Opname
        'access_stock_opname', 'create_stock_opname', 'edit_stock_opname', 'show_stock_opname', 'delete_stock_opname',
    ];

    // Buat permissions (idempotent) dengan guard 'web'
    foreach ($permissions as $name) {
        \Spatie\Permission\Models\Permission::firstOrCreate(
            ['name' => $name, 'guard_name' => 'web']
        );
    }

    // Buat role Admin (idempotent) dan sinkronkan semua permissions di atas
    $role = \Spatie\Permission\Models\Role::firstOrCreate(
        ['name' => 'Admin', 'guard_name' => 'web']
    );
    $role->syncPermissions($permissions);

    // Opsional: pastikan 'access_user_management' tidak ikut jika memang mau dibatasi
    // $role->revokePermissionTo('access_user_management');
}

}
