{{-- Toggler sidebar (mobile & desktop) --}}
<button class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar"
    data-class="c-sidebar-show" aria-label="Toggle sidebar">
    <i class="bi bi-list" style="font-size: 2rem;"></i>
</button>

<button class="c-header-toggler c-class-toggler mfs-3 d-md-down-none" type="button" data-target="#sidebar"
    data-class="c-sidebar-lg-show" responsive="true" aria-label="Toggle sidebar large">
    <i class="bi bi-list" style="font-size: 2rem;"></i>
</button>

<ul class="c-header-nav ml-auto"></ul>

<ul class="c-header-nav ml-auto mr-4">
    @can('create_pos_sales')
        <li class="c-header-nav-item mr-3">
            <a class="btn btn-primary btn-pill {{ request()->routeIs('app.pos.index') ? 'disabled' : '' }}"
                href="{{ route('app.pos.index') }}">
                <i class="bi bi-cart mr-1"></i> POS System
            </a>
        </li>
    @endcan


    {{-- 📉 Low stock indicator (ikon diperjelas) --}}
    @can('show_notifications')
        <li class="c-header-nav-item dropdown d-md-down-none mr-2">
            <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" title="Stok menipis">
                <i class="bi bi-exclamation-triangle" style="font-size: 20px;"></i>
                <span class="badge badge-pill badge-danger">
                    @php
                        $low_quantity_products = \Modules\Product\Entities\Product::select(
                            'id',
                            'product_quantity',
                            'product_stock_alert',
                            'product_code',
                        )
                            ->whereColumn('product_quantity', '<=', 'product_stock_alert')
                            ->get();
                        echo $low_quantity_products->count();
                    @endphp
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg pt-0" style="max-height:420px; overflow-y:auto;">
                <div class="dropdown-header bg-light">
                    <strong>{{ $low_quantity_products->count() }} Stok Menipis</strong>
                </div>
                @forelse($low_quantity_products as $product)
                    <a class="dropdown-item" href="{{ route('products.show', $product->id) }}">
                        <i class="bi bi-exclamation-triangle mr-1 text-warning"></i>
                        Produk <strong>{{ $product->product_code }}</strong> stok tinggal
                        <strong>{{ $product->product_quantity }}</strong>
                    </a>
                    <div class="dropdown-divider m-0"></div>
                @empty
                    <span class="dropdown-item text-muted small">
                        <i class="bi bi-check2-circle mr-2 text-success"></i> Tidak ada produk yang menipis.
                    </span>
                @endforelse
            </div>
        </li>
    @endcan

    {{-- User dropdown --}}
    <li class="c-header-nav-item dropdown">
        <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
            aria-expanded="false">
            <div class="c-avatar mr-2">
                @php $avatar = auth()->user()->getFirstMediaUrl('avatars') ?: asset('images/default-avatar.png'); @endphp
                <img class="c-avatar rounded-circle" src="{{ $avatar }}" alt="Profile Image" width="36"
                    height="36">
            </div>
            <div class="d-flex flex-column text-left">
                <span class="font-weight-bold">{{ auth()->user()->name }}</span>
                <span class="font-italic">Online <i class="bi bi-circle-fill text-success"
                        style="font-size: 11px;"></i></span>
            </div>
        </a>
        <div class="dropdown-menu dropdown-menu-right pt-0">
            <div class="dropdown-header bg-light py-2"><strong>Account</strong></div>
            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                <i class="mfe-2 bi bi-person" style="font-size: 1.2rem;"></i> Profile
            </a>
            <a class="dropdown-item" href="#"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="mfe-2 bi bi-box-arrow-left" style="font-size: 1.2rem;"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </li>
</ul>
