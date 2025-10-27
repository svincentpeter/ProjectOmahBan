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

@if (isset($permissions[$group]))
    <div class="permissions-list">
        @foreach ($permissions[$group] as $key => $label)
            <div class="permission-item">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input permission-checkbox" id="{{ $key }}"
                        name="permissions[]" value="{{ $key }}"
                        {{ in_array($key, $rolePermissions) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="{{ $key }}">
                        {{ $label }}
                    </label>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center text-muted py-3">
        <i class="cil-ban" style="font-size: 2rem; opacity: 0.3;"></i>
        <p class="mb-0 mt-2 small">Tidak ada permission</p>
    </div>
@endif

<style>
    /* ========== Permissions List Styling ========== */
    .permissions-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .permission-item {
        padding: 0.5rem 0;
        border-bottom: 1px solid #f1f3f5;
        transition: all 0.2s ease;
    }

    .permission-item:last-child {
        border-bottom: none;
    }

    .permission-item:hover {
        background-color: rgba(72, 52, 223, 0.03);
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        border-radius: 6px;
        border-bottom-color: transparent;
    }

    /* ========== Custom Checkbox ========== */
    .custom-checkbox {
        position: relative;
        padding-left: 0;
    }

    .custom-control-input {
        position: absolute;
        left: 0;
        z-index: -1;
        width: 1rem;
        height: 1.25rem;
        opacity: 0;
    }

    .custom-control-label {
        position: relative;
        margin-bottom: 0;
        vertical-align: top;
        padding-left: 2rem;
        cursor: pointer;
        font-size: 0.875rem;
        color: #495057;
        font-weight: 500;
        user-select: none;
        line-height: 1.5;
        display: block;
        transition: color 0.2s ease;
    }

    .custom-control-label::before {
        position: absolute;
        top: 0.125rem;
        left: 0;
        display: block;
        width: 1.25rem;
        height: 1.25rem;
        pointer-events: none;
        content: "";
        background-color: #fff;
        border: 2px solid #adb5bd;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }

    .custom-control-label::after {
        position: absolute;
        top: 0.125rem;
        left: 0;
        display: block;
        width: 1.25rem;
        height: 1.25rem;
        content: "";
        background: no-repeat 50% / 50% 50%;
        transition: all 0.2s ease;
    }

    /* ========== Checked State ========== */
    .custom-control-input:checked~.custom-control-label::before {
        color: #fff;
        border-color: #4834DF;
        background-color: #4834DF;
        box-shadow: 0 2px 4px rgba(72, 52, 223, 0.2);
    }

    .custom-control-input:checked~.custom-control-label::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%23fff' d='M6.564.75l-3.59 3.612-1.538-1.55L0 4.26l2.974 2.99L8 2.193z'/%3e%3c/svg%3e");
    }

    .custom-control-input:checked~.custom-control-label {
        color: #4834DF;
        font-weight: 600;
    }

    /* ========== Focus State ========== */
    .custom-control-input:focus~.custom-control-label::before {
        box-shadow: 0 0 0 0.2rem rgba(72, 52, 223, 0.25);
    }

    /* ========== Hover State ========== */
    .custom-control-label:hover::before {
        border-color: #4834DF;
    }

    /* ========== Disabled State ========== */
    .custom-control-input:disabled~.custom-control-label {
        color: #6c757d;
        cursor: not-allowed;
        opacity: 0.5;
    }

    .custom-control-input:disabled~.custom-control-label::before {
        background-color: #e9ecef;
    }
</style>
