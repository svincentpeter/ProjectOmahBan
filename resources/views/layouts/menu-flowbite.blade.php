{{-- Modern Clean Menu - White Theme with Smaller Fonts --}}

{{-- =======================
     OPERASIONAL
======================= --}}
<li class="pt-3 pb-2 px-2">
    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Operasional</span>
</li>

@php
    try {
        $uid = auth()->id();
        $base = \App\Models\OwnerNotification::where(fn($q) => $q->where('user_id', $uid)->orWhereNull('user_id'));

        $unread = (clone $base)->where('is_read', false)->count();
        $critical = (clone $base)->where('is_read', false)->where('severity', 'critical')->count();

        $variant = $critical > 0 ? 'critical' : ($unread > 0 ? 'info' : 'none');
        $dispUnread = $unread >= 1000 ? '999+' : ($unread >= 100 ? '99+' : (string) $unread);
        $dispCritical = $critical >= 1000 ? '999+' : ($critical >= 100 ? '99+' : (string) $critical);
    } catch (\Throwable $e) {
        $unread = $critical = 0;
        $variant = 'none';
        $dispUnread = $dispCritical = '0';
    }
@endphp

{{-- Notifikasi --}}
<li class="px-1">
    <a href="{{ route('notifications.index') }}" 
       class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-150 group
              {{ request()->routeIs('notifications.*') 
                 ? 'bg-blue-500 text-white shadow-sm shadow-blue-500/20' 
                 : ($variant === 'critical' 
                    ? 'bg-red-50 text-red-700 hover:bg-red-100' 
                    : ($variant === 'info' 
                       ? 'text-zinc-800 hover:bg-zinc-100' 
                       : 'text-zinc-800 hover:bg-zinc-100')) }}">
        <i class="bi {{ $variant === 'critical' ? 'bi-exclamation-triangle-fill' : 'bi-bell' }} text-base {{ request()->routeIs('notifications.*') ? '' : 'text-zinc-500 group-hover:text-zinc-700' }}"></i>
        <span class="flex-1">Notifikasi</span>
        
        @if($critical > 0)
            <span class="px-1.5 py-0.5 text-[10px] font-bold bg-red-500 text-white rounded-full min-w-[18px] text-center">{{ $dispCritical }}</span>
        @endif
        @if($unread > 0 && $critical == 0)
            <span class="px-1.5 py-0.5 text-[10px] font-bold bg-blue-500 text-white rounded-full min-w-[18px] text-center">{{ $dispUnread }}</span>
        @endif
    </a>
</li>

{{-- Beranda --}}
<li class="px-1">
    <a href="{{ route('home') }}" 
       class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-150 group
              {{ request()->routeIs('home') 
                 ? 'bg-blue-500 text-white shadow-sm shadow-blue-500/20' 
                 : 'text-zinc-800 hover:bg-zinc-100' }}">
        <i class="bi bi-house text-base {{ request()->routeIs('home') ? '' : 'text-zinc-500 group-hover:text-zinc-700' }}"></i>
        <span>Beranda</span>
    </a>
</li>

{{-- =======================
     PRODUK
======================= --}}
<li class="pt-5 pb-2 px-2">
    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Produk</span>
</li>

@can('access_products')
<li class="px-1" x-data="{ open: {{ request()->routeIs(['products.*', 'product-categories.*', 'brands.*', 'products_second.*', 'service-masters.*']) ? 'true' : 'false' }} }">
    <button @click="open = !open" type="button"
            class="flex items-center gap-2.5 w-full px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-150 group text-zinc-800 hover:bg-zinc-100">
        <i class="bi bi-journal-bookmark text-base text-zinc-500 group-hover:text-zinc-700"></i>
        <span class="flex-1 text-left">Manajemen Produk</span>
        <i class="bi bi-chevron-down text-xs text-zinc-500 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
    </button>
    <ul x-show="open" x-collapse class="mt-0.5 space-y-0.5 ml-4 border-l border-zinc-100 pl-3">
        @can('access_product_categories')
        <li>
            <a href="{{ route('product-categories.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('product-categories.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('product-categories.*') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Kategori Produk
            </a>
        </li>
        <li>
            <a href="{{ route('brands.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('brands.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('brands.*') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Merek Produk
            </a>
        </li>
        @endcan
        @can('create_products')
        <li>
            <a href="{{ route('products.create') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('products.create') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('products.create') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Tambah Produk Baru
            </a>
        </li>
        @endcan
        <li>
            <a href="{{ route('products.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('products.index') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('products.index') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Daftar Produk Baru
            </a>
        </li>
        <li>
            <a href="{{ route('products_second.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('products_second.index') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('products_second.index') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Daftar Produk Bekas
            </a>
        </li>
        <li>
            <a href="{{ route('service-masters.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('service-masters.index') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('service-masters.index') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Daftar Jasa
            </a>
        </li>
    </ul>
</li>
@endcan

{{-- =======================
     STOK & GUDANG
======================= --}}
<li class="pt-5 pb-2 px-2">
    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Stok & Gudang</span>
</li>

@canany(['access_adjustments', 'access_stock_opname'])
<li class="px-1" x-data="{ open: {{ request()->routeIs(['adjustments.*', 'stock-opnames.*']) ? 'true' : 'false' }} }">
    <button @click="open = !open" type="button"
            class="flex items-center gap-2.5 w-full px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 group text-zinc-800 hover:bg-zinc-100">
        <i class="bi bi-clipboard-check text-base text-zinc-500 group-hover:text-zinc-700"></i>
        <span class="flex-1 text-left">Penyesuaian Stok</span>
        <i class="bi bi-chevron-down text-xs text-zinc-500 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
    </button>
    <ul x-show="open" x-collapse class="mt-0.5 space-y-0.5 ml-4 border-l border-zinc-100 pl-3">
        @can('access_stock_opname')
        <li>
            <a href="{{ route('stock-opnames.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('stock-opnames.index') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('stock-opnames.index') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Stock Opname
                @php
                    try {
                        $opnameInProgress = \Modules\Adjustment\Entities\StockOpname::where('status', 'in_progress')->count();
                    } catch (\Throwable $e) {
                        $opnameInProgress = 0;
                    }
                @endphp
                @if($opnameInProgress > 0)
                    <span class="ml-auto px-1.5 py-0.5 text-[9px] font-bold bg-orange-500 text-white rounded-full">{{ $opnameInProgress }}</span>
                @endif
            </a>
        </li>
        @can('create_stock_opname')
        <li>
            <a href="{{ route('stock-opnames.create') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('stock-opnames.create') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('stock-opnames.create') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Buat Stock Opname
            </a>
        </li>
        @endcan
        @endcan
        @can('create_adjustments')
        <li>
            <a href="{{ route('adjustments.create') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('adjustments.create') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('adjustments.create') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Buat Penyesuaian Manual
            </a>
        </li>
        @endcan
        @can('access_adjustments')
        <li>
            <a href="{{ route('adjustments.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('adjustments.index') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('adjustments.index') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Semua Penyesuaian
            </a>
        </li>
        @endcan
        @can('approve_adjustments')
        <li>
            <a href="{{ route('adjustments.approvals') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('adjustments.approvals') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('adjustments.approvals') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Approval Penyesuaian
                @php
                    try {
                        $pendingCount = \Modules\Adjustment\Entities\Adjustment::where('status', 'pending')->count();
                    } catch (\Throwable $e) {
                        $pendingCount = 0;
                    }
                @endphp
                @if($pendingCount > 0)
                    <span class="ml-auto px-1.5 py-0.5 text-[9px] font-bold bg-red-500 text-white rounded-full">{{ $pendingCount }}</span>
                @endif
            </a>
        </li>
        @endcan
    </ul>
</li>
@endcanany

{{-- =======================
     RELASI / MASTER DATA
======================= --}}
<li class="pt-5 pb-2 px-2">
    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Relasi</span>
</li>

<li class="px-1" x-data="{ open: {{ request()->routeIs(['suppliers.*', 'customers.*']) ? 'true' : 'false' }} }">
    <button @click="open = !open" type="button"
            class="flex items-center gap-2.5 w-full px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 group text-zinc-800 hover:bg-zinc-100">
        <i class="bi bi-people text-base text-zinc-500 group-hover:text-zinc-700"></i>
        <span class="flex-1 text-left">Data Relasi</span>
        <i class="bi bi-chevron-down text-xs text-zinc-500 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
    </button>
    <ul x-show="open" x-collapse class="mt-0.5 space-y-0.5 ml-4 border-l border-zinc-100 pl-3">
        @can('access_suppliers')
        <li>
            <a href="{{ route('suppliers.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('suppliers.index') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('suppliers.index') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Daftar Supplier
            </a>
        </li>
        @can('create_suppliers')
        <li>
            <a href="{{ route('suppliers.create') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('suppliers.create') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('suppliers.create') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Tambah Supplier
            </a>
        </li>
        @endcan
        @endcan
        @can('access_customers')
        <li>
            <a href="{{ route('customers.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('customers.index') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('customers.index') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Daftar Customer
            </a>
        </li>
        @can('create_customers')
        <li>
            <a href="{{ route('customers.create') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('customers.create') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('customers.create') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Tambah Customer
            </a>
        </li>
        @endcan
        @endcan
    </ul>
</li>

{{-- =======================
     PEMBELIAN
======================= --}}
<li class="pt-5 pb-2 px-2">
    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Pembelian</span>
</li>

@can('access_purchases')
<li class="px-1" x-data="{ open: {{ request()->routeIs('purchases.*') ? 'true' : 'false' }} }">
    <button @click="open = !open" type="button"
            class="flex items-center gap-2.5 w-full px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 group text-zinc-800 hover:bg-zinc-100">
        <i class="bi bi-cart-plus text-base text-zinc-500 group-hover:text-zinc-700"></i>
        <span class="flex-1 text-left">Pembelian Stok</span>
        <i class="bi bi-chevron-down text-xs text-zinc-500 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
    </button>
    <ul x-show="open" x-collapse class="mt-0.5 space-y-0.5 ml-4 border-l border-zinc-100 pl-3">
        @can('create_purchases')
        <li>
            <a href="{{ route('purchases.create') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('purchases.create') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('purchases.create') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Buat Pembelian
            </a>
        </li>
        @endcan
        <li>
            <a href="{{ route('purchases.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('purchases.index') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('purchases.index') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Daftar Pembelian
            </a>
        </li>
        <li>
            <a href="{{ route('purchases.second.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('purchases.second.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('purchases.second.*') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Daftar Pembelian Bekas
            </a>
        </li>
    </ul>
</li>
@endcan

{{-- =======================
     PENJUALAN
======================= --}}
<li class="pt-5 pb-2 px-2">
    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Penjualan</span>
</li>

@can('access_sales')
<li class="px-1" x-data="{ open: {{ request()->routeIs(['sales.*', 'sale-payments.*', 'sale-returns.*', 'quotations.*']) ? 'true' : 'false' }} }">
    <button @click="open = !open" type="button"
            class="flex items-center gap-2.5 w-full px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 group text-zinc-800 hover:bg-zinc-100">
        <i class="bi bi-receipt text-base text-zinc-500 group-hover:text-zinc-700"></i>
        <span class="flex-1 text-left">Penjualan</span>
        <i class="bi bi-chevron-down text-xs text-zinc-500 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
    </button>
    <ul x-show="open" x-collapse class="mt-0.5 space-y-0.5 ml-4 border-l border-zinc-100 pl-3">
        <li>
            <a href="{{ route('quotations.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('quotations.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('quotations.*') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Penawaran
            </a>
        </li>
        <li>
            <a href="{{ route('sales.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('sales.index') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('sales.index') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Semua Penjualan
            </a>
        </li>
        @can('access_sale_returns')
        <li>
            <a href="{{ route('sale-returns.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('sale-returns.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('sale-returns.*') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Retur Penjualan
            </a>
        </li>
        @endcan
    </ul>
</li>
@endcan

{{-- =======================
     PENGELUARAN
======================= --}}
<li class="pt-5 pb-2 px-2">
    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Pengeluaran</span>
</li>

<li class="px-1" x-data="{ open: {{ request()->routeIs(['expenses.*', 'expense-categories.*']) ? 'true' : 'false' }} }">
    <button @click="open = !open" type="button"
            class="flex items-center gap-2.5 w-full px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 group text-zinc-800 hover:bg-zinc-100">
        <i class="bi bi-wallet2 text-base text-zinc-500 group-hover:text-zinc-700"></i>
        <span class="flex-1 text-left">Pengeluaran</span>
        <i class="bi bi-chevron-down text-xs text-zinc-500 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
    </button>
    <ul x-show="open" x-collapse class="mt-0.5 space-y-0.5 ml-4 border-l border-zinc-100 pl-3">
        @can('access_expense_categories')
        @if(Route::has('expense-categories.index'))
        <li>
            <a href="{{ route('expense-categories.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('expense-categories.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('expense-categories.*') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Kategori Pengeluaran
            </a>
        </li>
        @endif
        @endcan
        @can('create_expenses')
        @if(Route::has('expenses.create'))
        <li>
            <a href="{{ route('expenses.create') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('expenses.create') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('expenses.create') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Input Pengeluaran
            </a>
        </li>
        @endif
        @endcan
        @can('access_expenses')
        @if(Route::has('expenses.index'))
        <li>
            <a href="{{ route('expenses.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('expenses.index') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('expenses.index') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Daftar Pengeluaran
            </a>
        </li>
        @endif
        @endcan
    </ul>
</li>

{{-- =======================
     LAPORAN
======================= --}}
<li class="pt-5 pb-2 px-2">
    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Laporan</span>
</li>

@can('access_reports')
@php
    $openReports = request()->routeIs(['reports.*', 'ringkas-report.*', '*-report.*']);
    $link = fn(array $names) => collect($names)->map(fn($name) => Route::has($name) ? route($name) : null)->first() ?? '#';
    $isActive = fn(array $patterns) => collect($patterns)->contains(fn($p) => request()->routeIs($p));
@endphp

<li class="px-1" x-data="{ open: {{ $openReports ? 'true' : 'false' }} }">
    <button @click="open = !open" type="button"
            class="flex items-center gap-2.5 w-full px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 group text-zinc-800 hover:bg-zinc-100">
        <i class="bi bi-graph-up-arrow text-base text-zinc-500 group-hover:text-zinc-700"></i>
        <span class="flex-1 text-left">Laporan</span>
        <i class="bi bi-chevron-down text-xs text-zinc-500 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
    </button>
    <ul x-show="open" x-collapse class="mt-0.5 space-y-0.5 ml-4 border-l border-zinc-100 pl-3">
        <li>
            <a href="{{ $link(['reports.daily.index']) }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ $isActive(['reports.daily.*']) ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $isActive(['reports.daily.*']) ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Laporan Kas Harian
            </a>
        </li>
        @if(Route::has('ringkas-report.cashier'))
        <li>
            <a href="{{ route('ringkas-report.cashier') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ $isActive(['ringkas-report.*']) ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $isActive(['ringkas-report.*']) ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Laporan Ringkas Kasir
            </a>
        </li>
        @endif
        <li>
            <a href="{{ $link(['reports.profit_loss.index', 'profit-loss-report.index']) }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ $isActive(['reports.profit_loss.*', 'profit-loss-report.*']) ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $isActive(['reports.profit_loss.*', 'profit-loss-report.*']) ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Laporan Laba/Rugi
            </a>
        </li>
    </ul>
</li>
@endcan

{{-- =======================
     PENGGUNA
======================= --}}
<li class="pt-5 pb-2 px-2">
    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Pengguna</span>
</li>

@can('access_user_management')
<li class="px-1" x-data="{ open: {{ request()->routeIs(['roles.*', 'users.*']) ? 'true' : 'false' }} }">
    <button @click="open = !open" type="button"
            class="flex items-center gap-2.5 w-full px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 group text-zinc-800 hover:bg-zinc-100">
        <i class="bi bi-person-gear text-base text-zinc-500 group-hover:text-zinc-700"></i>
        <span class="flex-1 text-left">Manajemen User</span>
        <i class="bi bi-chevron-down text-xs text-zinc-500 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
    </button>
    <ul x-show="open" x-collapse class="mt-0.5 space-y-0.5 ml-4 border-l border-zinc-100 pl-3">
        <li>
            <a href="{{ route('users.create') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('users.create') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('users.create') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Tambah Pengguna
            </a>
        </li>
        <li>
            <a href="{{ route('users.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs(['users.index', 'users.edit']) ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs(['users.index', 'users.edit']) ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Semua Pengguna
            </a>
        </li>
        <li>
            <a href="{{ route('roles.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('roles.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('roles.*') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Peran & Hak Akses
            </a>
        </li>
    </ul>
</li>
@endcan

{{-- =======================
     PENGATURAN
======================= --}}
<li class="pt-5 pb-2 px-2">
    <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Pengaturan</span>
</li>

@canany(['access_currencies', 'access_settings', 'access_units'])
<li class="px-1" x-data="{ open: {{ request()->routeIs(['currencies.*', 'units.*', 'settings.*']) ? 'true' : 'false' }} }">
    <button @click="open = !open" type="button"
            class="flex items-center gap-2.5 w-full px-3 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 group text-zinc-800 hover:bg-zinc-100">
        <i class="bi bi-gear text-base text-zinc-500 group-hover:text-zinc-700"></i>
        <span class="flex-1 text-left">Pengaturan Sistem</span>
        <i class="bi bi-chevron-down text-xs text-zinc-500 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
    </button>
    <ul x-show="open" x-collapse class="mt-0.5 space-y-0.5 ml-4 border-l border-zinc-100 pl-3">
        @can('access_units')
        <li>
            <a href="{{ route('units.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('units.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('units.*') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Satuan Unit
            </a>
        </li>
        @endcan
        @can('access_currencies')
        <li>
            <a href="{{ route('currencies.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('currencies.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('currencies.*') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Mata Uang
            </a>
        </li>
        @endcan
        @can('access_settings')
        <li>
            <a href="{{ route('settings.index') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('settings.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('settings.*') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                Pengaturan Umum
            </a>
        </li>
        <li>
            <a href="{{ route('whatsapp.settings') }}" 
               class="flex items-center gap-2 px-2.5 py-2 rounded-md text-xs transition-all {{ request()->routeIs('whatsapp.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('whatsapp.*') ? 'bg-blue-500' : 'bg-zinc-400' }}"></span>
                WhatsApp Settings
                @php
                    try {
                        $waStatus = app(\App\Services\WhatsApp\BaileysNotificationService::class)->getStatus();
                        $isConnected = $waStatus['connected'] ?? false;
                    } catch (\Throwable $e) {
                        $isConnected = false;
                    }
                @endphp
                @if($isConnected)
                    <span class="ml-auto w-2 h-2 rounded-full bg-green-500 animate-pulse" title="WhatsApp Connected"></span>
                @else
                    <span class="ml-auto w-2 h-2 rounded-full bg-red-500" title="WhatsApp Disconnected"></span>
                @endif
            </a>
        </li>
        @endcan
    </ul>
</li>
@endcanany

{{-- Spacer at bottom --}}
<li class="py-6"></li>

@push('scripts')
<script>
(function refreshNotif() {
    fetch('{{ route('notifications.summary') }}')
        .then(r => r.json())
        .then(({ unread = 0, critical = 0 }) => {
            const link = document.querySelector('a[href="{{ route('notifications.index') }}"]');
            if (!link) return;

            // Update badges
            const badges = link.querySelectorAll('span.rounded-full');
            if (badges.length >= 2) {
                badges[0].style.display = critical > 0 ? 'inline-flex' : 'none';
                badges[0].textContent = critical >= 1000 ? '999+' : (critical >= 100 ? '99+' : String(critical));
                badges[1].style.display = unread > 0 ? 'inline-flex' : 'none';
                badges[1].textContent = unread >= 1000 ? '999+' : (unread >= 100 ? '99+' : String(unread));
            }
        })
        .catch(() => {})
        .finally(() => setTimeout(refreshNotif, 30000));
})();
</script>
@endpush
