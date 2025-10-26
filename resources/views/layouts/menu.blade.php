{{-- Sidebar/Menu Utama --}}

{{-- =======================
     DASHBOARD
======================= --}}
<li class="c-sidebar-nav-item {{ request()->routeIs('home') ? 'c-active' : '' }}">
    <a class="c-sidebar-nav-link" href="{{ route('home') }}">
        <i class="c-sidebar-nav-icon bi bi-house" style="line-height: 1;"></i> Beranda
    </a>
</li>

{{-- =======================
     PRODUK
======================= --}}
<li class="c-sidebar-nav-title">Produk</li>

@can('access_products')
<li class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs(['products.*', 'product-categories.*', 'brands.*', 'products_second.*']) ? 'c-show' : '' }}">
    <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
        <i class="c-sidebar-nav-icon bi bi-journal-bookmark" style="line-height: 1;"></i> Manajemen Produk
    </a>
    <ul class="c-sidebar-nav-dropdown-items">
        @can('access_product_categories')
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ request()->routeIs('product-categories.*') ? 'c-active' : '' }}" href="{{ route('product-categories.index') }}">
                    <i class="c-sidebar-nav-icon bi bi-collection" style="line-height: 1;"></i> Kategori Produk
                </a>
            </li>
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ request()->routeIs('brands.*') ? 'c-active' : '' }}" href="{{ route('brands.index') }}">
                    <i class="c-sidebar-nav-icon bi bi-tags" style="line-height: 1;"></i> Merek Produk
                </a>
            </li>
        @endcan
        @can('create_products')
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ request()->routeIs('products.create') ? 'c-active' : '' }}" href="{{ route('products.create') }}">
                    <i class="c-sidebar-nav-icon bi bi-journal-plus" style="line-height: 1;"></i> Tambah Produk Baru
                </a>
            </li>
        @endcan
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ request()->routeIs('products.index') ? 'c-active' : '' }}" href="{{ route('products.index') }}">
                <i class="c-sidebar-nav-icon bi bi-journals" style="line-height: 1;"></i> Daftar Produk Baru
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ request()->routeIs('products_second.index') ? 'c-active' : '' }}" href="{{ route('products_second.index') }}">
                <i class="c-sidebar-nav-icon bi bi-recycle" style="line-height: 1;"></i> Daftar Produk Bekas
            </a>
        </li>
    </ul>
</li>
@endcan

{{-- =======================
     STOK & PENAWARAN
======================= --}}
<li class="c-sidebar-nav-title">Stok & Penawaran</li>

@can('access_adjustments')
<li class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs('adjustments.*') ? 'c-show' : '' }}">
    <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
        <i class="c-sidebar-nav-icon bi bi-clipboard-check" style="line-height: 1;"></i> Penyesuaian Stok
    </a>
    <ul class="c-sidebar-nav-dropdown-items">
        @can('create_adjustments')
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ request()->routeIs('adjustments.create') ? 'c-active' : '' }}" href="{{ route('adjustments.create') }}">
                    <i class="c-sidebar-nav-icon bi bi-journal-plus" style="line-height: 1;"></i> Buat Penyesuaian
                </a>
            </li>
        @endcan
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ request()->routeIs('adjustments.index') ? 'c-active' : '' }}" href="{{ route('adjustments.index') }}">
                <i class="c-sidebar-nav-icon bi bi-journals" style="line-height: 1;"></i> Semua Penyesuaian
            </a>
        </li>
    </ul>
</li>
@endcan

@can('access_quotations')
<li class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs('quotations.*') ? 'c-show' : '' }}">
    <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
        <i class="c-sidebar-nav-icon bi bi-cart-check" style="line-height: 1;"></i> Penawaran Harga
    </a>
    <ul class="c-sidebar-nav-dropdown-items">
        @can('create_quotations')
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ request()->routeIs('quotations.create') ? 'c-active' : '' }}" href="{{ route('quotations.create') }}">
                    <i class="c-sidebar-nav-icon bi bi-journal-plus" style="line-height: 1;"></i> Buat Penawaran
                </a>
            </li>
        @endcan
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ request()->routeIs('quotations.index') ? 'c-active' : '' }}" href="{{ route('quotations.index') }}">
                <i class="c-sidebar-nav-icon bi bi-journals" style="line-height: 1;"></i> Semua Penawaran
            </a>
        </li>
    </ul>
</li>
@endcan

{{-- =======================
     PEMBELIAN
======================= --}}
<li class="c-sidebar-nav-title">Pembelian</li>

@can('access_purchases')
<li class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs(['purchases.*', 'purchase-payments.*']) ? 'c-show' : '' }}">
    <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
        <i class="c-sidebar-nav-icon bi bi-bag" style="line-height: 1;"></i> Pembelian
    </a>
    <ul class="c-sidebar-nav-dropdown-items">
        @can('create_purchases')
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ request()->routeIs('purchases.create') ? 'c-active' : '' }}" href="{{ route('purchases.create') }}">
                    <i class="c-sidebar-nav-icon bi bi-journal-plus" style="line-height: 1;"></i> Buat Pembelian
                </a>
            </li>
        @endcan
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ request()->routeIs('purchases.index') ? 'c-active' : '' }}" href="{{ route('purchases.index') }}">
                <i class="c-sidebar-nav-icon bi bi-journals" style="line-height: 1;"></i> Semua Pembelian
            </a>
        </li>
    </ul>
</li>
@endcan

@can('access_purchase_returns')
<li class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs(['purchase-returns.*', 'purchase-return-payments.*']) ? 'c-show' : '' }}">
    <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
        <i class="c-sidebar-nav-icon bi bi-arrow-return-right" style="line-height: 1;"></i> Retur Pembelian
    </a>
    <ul class="c-sidebar-nav-dropdown-items">
        @can('create_purchase_returns')
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ request()->routeIs('purchase-returns.create') ? 'c-active' : '' }}" href="{{ route('purchase-returns.create') }}">
                    <i class="c-sidebar-nav-icon bi bi-journal-plus" style="line-height: 1;"></i> Buat Retur
                </a>
            </li>
        @endcan
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ request()->routeIs('purchase-returns.index') ? 'c-active' : '' }}" href="{{ route('purchase-returns.index') }}">
                <i class="c-sidebar-nav-icon bi bi-journals" style="line-height: 1;"></i> Semua Retur
            </a>
        </li>
    </ul>
</li>
@endcan

{{-- =======================
     PENJUALAN
======================= --}}
<li class="c-sidebar-nav-title">Penjualan</li>

@can('access_sales')
<li class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs(['sales.*', 'sale-payments.*']) ? 'c-show' : '' }}">
    <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
        <i class="c-sidebar-nav-icon bi bi-receipt" style="line-height: 1;"></i> Penjualan
    </a>
    <ul class="c-sidebar-nav-dropdown-items">
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ request()->routeIs('sales.index') ? 'c-active' : '' }}" href="{{ route('sales.index') }}">
                <i class="c-sidebar-nav-icon bi bi-journals" style="line-height: 1;"></i> Semua Penjualan
            </a>
        </li>
    </ul>
</li>
@endcan

@can('access_sale_returns')
<li class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs(['sale-returns.*', 'sale-return-payments.*']) ? 'c-show' : '' }}">
    <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
        <i class="c-sidebar-nav-icon bi bi-arrow-return-left" style="line-height: 1;"></i> Retur Penjualan
    </a>
    <ul class="c-sidebar-nav-dropdown-items">
        @can('create_sale_returns')
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ request()->routeIs('sale-returns.create') ? 'c-active' : '' }}" href="{{ route('sale-returns.create') }}">
                    <i class="c-sidebar-nav-icon bi bi-journal-plus" style="line-height: 1;"></i> Buat Retur
                </a>
            </li>
        @endcan
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ request()->routeIs('sale-returns.index') ? 'c-active' : '' }}" href="{{ route('sale-returns.index') }}">
                <i class="c-sidebar-nav-icon bi bi-journals" style="line-height: 1;"></i> Semua Retur
            </a>
        </li>
    </ul>
</li>
@endcan

{{-- =======================
     PENGELUARAN
======================= --}}
<li class="c-sidebar-nav-title">Pengeluaran</li>

<li class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs(['expenses.*', 'expense-categories.*']) ? 'c-show' : '' }}">
    <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
        <i class="c-sidebar-nav-icon bi bi-wallet2" style="line-height: 1;"></i> Pengeluaran
    </a>
    <ul class="c-sidebar-nav-dropdown-items">
        @can('access_expense_categories')
        @if(Route::has('expense-categories.index'))
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ request()->routeIs('expense-categories.*') ? 'c-active' : '' }}" href="{{ route('expense-categories.index') }}">
                    <i class="c-sidebar-nav-icon bi bi-collection" style="line-height: 1;"></i> Kategori Pengeluaran
                </a>
            </li>
        @endif
        @endcan
        @can('create_expenses')
        @if(Route::has('expenses.create'))
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ request()->routeIs('expenses.create') ? 'c-active' : '' }}" href="{{ route('expenses.create') }}">
                    <i class="c-sidebar-nav-icon bi bi-journal-plus" style="line-height: 1;"></i> Input Pengeluaran
                </a>
            </li>
        @endif
        @endcan
        @can('access_expenses')
        @if(Route::has('expenses.index'))
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ request()->routeIs('expenses.index') ? 'c-active' : '' }}" href="{{ route('expenses.index') }}">
                    <i class="c-sidebar-nav-icon bi bi-list-ul" style="line-height: 1;"></i> Daftar Pengeluaran
                </a>
            </li>
        @endif
        @endcan
    </ul>
</li>

{{-- =======================
     KONTAK
======================= --}}
<li class="c-sidebar-nav-title">Kontak</li>

@canany(['access_customers','access_suppliers'])
<li class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs(['customers.*', 'suppliers.*']) ? 'c-show' : '' }}">
    <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
        <i class="c-sidebar-nav-icon bi bi-people" style="line-height: 1;"></i> Manajemen Kontak
    </a>
    <ul class="c-sidebar-nav-dropdown-items">
        @can('access_customers')
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ request()->routeIs('customers.*') ? 'c-active' : '' }}" href="{{ route('customers.index') }}">
                    <i class="c-sidebar-nav-icon bi bi-people-fill" style="line-height: 1;"></i> Pelanggan
                </a>
            </li>
        @endcan
        @can('access_suppliers')
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ request()->routeIs('suppliers.*') ? 'c-active' : '' }}" href="{{ route('suppliers.index') }}">
                    <i class="c-sidebar-nav-icon bi bi-people-fill" style="line-height: 1;"></i> Pemasok
                </a>
            </li>
        @endcan
    </ul>
</li>
@endcanany

{{-- =======================
     LAPORAN
======================= --}}
<li class="c-sidebar-nav-title">Laporan</li>

@can('access_reports')
@php
    $openReports = request()->routeIs(['reports.*', 'ringkas-report.*', '*-report.*']);
    $link = fn(array $names) => collect($names)->map(fn($name) => Route::has($name) ? route($name) : null)->first() ?? '#';
    $isActive = fn(array $patterns) => collect($patterns)->contains(fn($p) => request()->routeIs($p));
@endphp

<li class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ $openReports ? 'c-show' : '' }}">
    <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
        <i class="c-sidebar-nav-icon bi bi-graph-up" style="line-height:1;"></i> Laporan
    </a>
    <ul class="c-sidebar-nav-dropdown-items">
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ $isActive(['reports.daily.*']) ? 'c-active' : '' }}" href="{{ $link(['reports.daily.index']) }}">
                <i class="c-sidebar-nav-icon bi bi-journal-text" style="line-height:1;"></i> Laporan Kas Harian
            </a>
        </li>
        @if (Route::has('ringkas-report.cashier'))
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ $isActive(['ringkas-report.*']) ? 'c-active' : '' }}" href="{{ route('ringkas-report.cashier') }}">
                <i class="c-sidebar-nav-icon bi bi-people" style="line-height:1;"></i> Laporan Ringkas Kasir
            </a>
        </li>
        @endif
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ $isActive(['reports.profit_loss.*','profit-loss-report.*']) ? 'c-active' : '' }}" href="{{ $link(['reports.profit_loss.index','profit-loss-report.index']) }}">
                <i class="c-sidebar-nav-icon bi bi-cash-coin" style="line-height:1;"></i> Laporan Laba/Rugi
            </a>
        </li>
        
    </ul>
</li>
@endcan

{{-- =======================
     MANAJEMEN PENGGUNA
======================= --}}
<li class="c-sidebar-nav-title">Pengguna & Akses</li>

@can('access_user_management')
<li class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs(['roles.*', 'users.*']) ? 'c-show' : '' }}">
    <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
        <i class="c-sidebar-nav-icon bi bi-people" style="line-height: 1;"></i> Manajemen Pengguna
    </a>
    <ul class="c-sidebar-nav-dropdown-items">
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ request()->routeIs('users.create') ? 'c-active' : '' }}" href="{{ route('users.create') }}">
                <i class="c-sidebar-nav-icon bi bi-person-plus" style="line-height: 1;"></i> Tambah Pengguna
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ request()->routeIs(['users.index', 'users.edit']) ? 'c-active' : '' }}" href="{{ route('users.index') }}">
                <i class="c-sidebar-nav-icon bi bi-person-lines-fill" style="line-height: 1;"></i> Semua Pengguna
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link {{ request()->routeIs('roles.*') ? 'c-active' : '' }}" href="{{ route('roles.index') }}">
                <i class="c-sidebar-nav-icon bi bi-key" style="line-height: 1;"></i> Peran & Hak Akses
            </a>
        </li>
    </ul>
</li>
@endcan

{{-- =======================
     PENGATURAN
======================= --}}
<li class="c-sidebar-nav-title">Pengaturan</li>

@canany(['access_currencies','access_settings','access_units'])
<li class="c-sidebar-nav-item c-sidebar-nav-dropdown {{ request()->routeIs(['currencies.*', 'units.*', 'settings.*']) ? 'c-show' : '' }}">
    <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
        <i class="c-sidebar-nav-icon bi bi-gear" style="line-height: 1;"></i> Pengaturan Sistem
    </a>
    <ul class="c-sidebar-nav-dropdown-items">
        @can('access_units')
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ request()->routeIs('units.*') ? 'c-active' : '' }}" href="{{ route('units.index') }}">
                    <i class="c-sidebar-nav-icon bi bi-calculator" style="line-height: 1;"></i> Satuan Unit
                </a>
            </li>
        @endcan
        @can('access_currencies')
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ request()->routeIs('currencies.*') ? 'c-active' : '' }}" href="{{ route('currencies.index') }}">
                    <i class="c-sidebar-nav-icon bi bi-cash-stack" style="line-height: 1;"></i> Mata Uang
                </a>
            </li>
        @endcan
        @can('access_settings')
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link {{ request()->routeIs('settings.*') ? 'c-active' : '' }}" href="{{ route('settings.index') }}">
                    <i class="c-sidebar-nav-icon bi bi-sliders" style="line-height: 1;"></i> Pengaturan Umum
                </a>
            </li>
        @endcan
    </ul>
</li>
@endcanany