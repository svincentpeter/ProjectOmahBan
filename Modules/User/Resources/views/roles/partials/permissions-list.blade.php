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
        ],
        'settings' => [
            'access_settings' => 'Akses Pengaturan',
            'access_currencies' => 'Akses Mata Uang',
            'access_units' => 'Akses Satuan Unit',
        ],
    ];

    $rolePermissions = $rolePermissions ?? [];
@endphp

<div class="row">
    @foreach ($permissions[$group] as $key => $label)
        <div class="col-12">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="{{ $key }}" name="permissions[]"
                       value="{{ $key }}" {{ in_array($key, $rolePermissions) ? 'checked' : '' }}>
                <label class="custom-control-label" for="{{ $key }}">{{ $label }}</label>
            </div>
        </div>
    @endforeach
</div>