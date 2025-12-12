@php
    $permissions = [
        'dashboard' => [
            'show_total_stats' => 'Lihat Statistik Total',
            'show_month_overview' => 'Lihat Ringkasan Bulanan',
            'show_weekly_sales_purchases' => 'Lihat Penjualan & Pembelian Mingguan',
            'show_monthly_cashflow' => 'Lihat Arus Kas Bulanan',
            'show_notifications' => 'Lihat Notifikasi',
        ],
        'user_management' => [
            'access_user_management' => 'Akses Manajemen Pengguna',
            'edit_own_profile' => 'Ubah Profil Sendiri',
        ],
        'products' => [
            'access_products' => 'Akses Produk',
            'create_products' => 'Buat Produk',
            'show_products' => 'Lihat Produk',
            'edit_products' => 'Edit Produk',
            'delete_products' => 'Hapus Produk',
            'access_product_categories' => 'Akses Kategori',
            'print_barcodes' => 'Cetak Barcode',
        ],
        'adjustments' => [
            'access_adjustments' => 'Akses Penyesuaian',
            'create_adjustments' => 'Buat Penyesuaian',
            'show_adjustments' => 'Lihat Penyesuaian',
            'edit_adjustments' => 'Edit Penyesuaian',
            'delete_adjustments' => 'Hapus Penyesuaian',
            'approve_adjustments' => 'Approve Penyesuaian',
        ],
        'stock_opname' => [
            'access_stock_opname' => 'Akses Stock Opname',
            'create_stock_opname' => 'Buat Stock Opname',
            'show_stock_opname' => 'Lihat Stock Opname',
            'edit_stock_opname' => 'Edit Stock Opname',
            'delete_stock_opname' => 'Hapus Stock Opname',
        ],
        'quotations' => [
            'access_quotations' => 'Akses Penawaran',
            'create_quotations' => 'Buat Penawaran',
            'show_quotations' => 'Lihat Penawaran',
            'edit_quotations' => 'Edit Penawaran',
            'delete_quotations' => 'Hapus Penawaran',
            'send_quotation_mails' => 'Kirim Email Penawaran',
            'create_quotation_sales' => 'Buat Penjualan dari Penawaran',
        ],
        'expenses' => [
            'access_expenses' => 'Akses Pengeluaran',
            'create_expenses' => 'Buat Pengeluaran',
            'edit_expenses' => 'Edit Pengeluaran',
            'delete_expenses' => 'Hapus Pengeluaran',
            'access_expense_categories' => 'Akses Kategori Pengeluaran',
        ],
        'customers' => [
            'access_customers' => 'Akses Pelanggan',
            'create_customers' => 'Buat Pelanggan',
            'show_customers' => 'Lihat Pelanggan',
            'edit_customers' => 'Edit Pelanggan',
            'delete_customers' => 'Hapus Pelanggan',
        ],
        'suppliers' => [
            'access_suppliers' => 'Akses Pemasok',
            'create_suppliers' => 'Buat Pemasok',
            'show_suppliers' => 'Lihat Pemasok',
            'edit_suppliers' => 'Edit Pemasok',
            'delete_suppliers' => 'Hapus Pemasok',
        ],
        'sales' => [
            'access_sales' => 'Akses Penjualan',
            'create_sales' => 'Buat Penjualan',
            'show_sales' => 'Lihat Penjualan',
            'edit_sales' => 'Edit Penjualan',
            'delete_sales' => 'Hapus Penjualan',
            'create_pos_sales' => 'Akses Sistem POS',
            'access_sale_payments' => 'Akses Pembayaran Penjualan',
            'pos.override_price_limit' => 'POS: Override Limit Harga',
            'pos.approve_discount' => 'POS: Approve Diskon',
            'pos.view_cost_price' => 'POS: Lihat Harga Modal',
        ],
        'sale_returns' => [
            'access_sale_returns' => 'Akses Retur Penjualan',
            'create_sale_returns' => 'Buat Retur Penjualan',
            'show_sale_returns' => 'Lihat Retur Penjualan',
            'edit_sale_returns' => 'Edit Retur Penjualan',
            'delete_sale_returns' => 'Hapus Retur Penjualan',
            'access_sale_return_payments' => 'Akses Pembayaran Retur Penjualan',
        ],
        'purchases' => [
            'access_purchases' => 'Akses Pembelian',
            'create_purchases' => 'Buat Pembelian',
            'show_purchases' => 'Lihat Pembelian',
            'edit_purchases' => 'Edit Pembelian',
            'delete_purchases' => 'Hapus Pembelian',
            'access_purchase_payments' => 'Akses Pembayaran Pembelian',
            'inventory.edit_hpp' => 'Inventory: Edit HPP',
            'inventory.approve_hpp_override' => 'Inventory: Approve Override HPP',
        ],
        'purchase_returns' => [
            'access_purchase_returns' => 'Akses Retur Pembelian',
            'create_purchase_returns' => 'Buat Retur Pembelian',
            'show_purchase_returns' => 'Lihat Retur Pembelian',
            'edit_purchase_returns' => 'Edit Retur Pembelian',
            'delete_purchase_returns' => 'Hapus Retur Pembelian',
            'access_purchase_return_payments' => 'Akses Pembayaran Retur Pembelian',
        ],
        'reports' => [
            'access_reports' => 'Akses Laporan',
            'report.view_deviation' => 'Laporan: Lihat Deviasi',
            'report.view_activity_log' => 'Laporan: Lihat Log Aktivitas',
            'report.export_sensitive' => 'Laporan: Export Data Sensitif',
        ],
        'settings' => [
            'access_settings' => 'Akses Pengaturan',
            'access_currencies' => 'Akses Mata Uang',
            'create_currencies' => 'Buat Mata Uang',
            'edit_currencies' => 'Edit Mata Uang',
            'delete_currencies' => 'Hapus Mata Uang',
            'access_units' => 'Akses Satuan Unit',
            'create_units' => 'Buat Satuan Unit',
            'edit_units' => 'Edit Satuan Unit',
            'delete_units' => 'Hapus Satuan Unit',
            'settings.manage_service_standards' => 'Kelola Standar Servis',
            'settings.view_system_log' => 'Lihat Log Sistem',
        ],
    ];

    $rolePermissions = $rolePermissions ?? [];
@endphp

@if (isset($permissions[$group]))
    <div class="space-y-3">
        @foreach ($permissions[$group] as $key => $label)
            <div class="flex items-center p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <input type="checkbox" id="{{ $key }}" name="permissions[]" value="{{ $key }}"
                    {{ in_array($key, $rolePermissions) ? 'checked' : '' }}
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 cursor-pointer">
                <label for="{{ $key }}" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300 cursor-pointer w-full select-none">
                    {{ $label }}
                </label>
            </div>
        @endforeach
    </div>
@else
    <div class="flex flex-col items-center justify-center py-6 text-gray-400 dark:text-gray-500">
        <i class="bi bi-slash-circle text-3xl mb-2 opacity-50"></i>
        <p class="text-sm">Tidak ada permission</p>
    </div>
@endif
