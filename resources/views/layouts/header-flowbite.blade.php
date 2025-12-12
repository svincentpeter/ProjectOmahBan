<nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700 shadow-sm">
  <div class="px-3 py-3 lg:px-5 lg:pl-3">
    <div class="flex items-center justify-between">
      
      {{-- Left: Sidebar Toggle & Logo --}}
      <div class="flex items-center justify-start rtl:justify-end">
        <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-zinc-600 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
            <span class="sr-only">Buka sidebar</span>
            <i class="bi bi-list text-2xl"></i>
         </button>
        <a href="{{ route('home') }}" class="flex ms-2 md:ms-4 items-center">
          <img src="{{ asset('images/logo.png') }}" class="h-9 me-3" alt="Omah Ban Logo" />
        </a>
      </div>

      {{-- Right: POS, Low Stock, Search, Dark Mode, Apps, Profile --}}
      <div class="flex items-center gap-2 sm:gap-4">
         
         {{-- POS System Button (Desktop) --}}
         @can('create_pos_sales')
         <a href="{{ route('app.pos.index') }}" class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors {{ request()->routeIs('app.pos.index') ? 'opacity-50 cursor-not-allowed' : '' }}">
            <i class="bi bi-cart-fill me-2"></i> POS System
         </a>
         @endcan

         {{-- Low Stock Indicator --}}
         @can('show_notifications')
            @php
                $lowScokProducts = \Modules\Product\Entities\Product::select('id','product_quantity','product_stock_alert','product_code')
                    ->whereColumn('product_quantity', '<=', 'product_stock_alert')
                    ->get();
                $lowStockCount = $lowScokProducts->count();
            @endphp
            
            <div class="relative">
                <button type="button" data-dropdown-toggle="dropdown-lowstock" class="relative inline-flex items-center p-2 text-sm font-medium text-center text-zinc-500 rounded-lg hover:bg-zinc-100 focus:ring-4 focus:outline-none focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                    <i class="bi bi-exclamation-triangle-fill text-xl {{ $lowStockCount > 0 ? 'text-orange-500' : 'text-zinc-400' }}"></i>
                    <span class="sr-only">Notifikasi Stok</span>
                    @if($lowStockCount > 0)
                        <div class="absolute inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-1 -end-1 dark:border-gray-900">{{ $lowStockCount > 99 ? '99+' : $lowStockCount }}</div>
                    @endif
                </button>

                {{-- Low Stock Dropdown --}}
                <div id="dropdown-lowstock" class="z-50 hidden w-80 max-h-96 overflow-y-auto bg-white divide-y divide-gray-100 rounded-lg shadow-xl dark:bg-gray-700 dark:divide-gray-600">
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-600 rounded-t-lg">
                        <span class="block text-sm font-semibold text-gray-900 dark:text-white">{{ $lowStockCount }} Produk Stok Menipis</span>
                    </div>
                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                        @forelse($lowScokProducts as $product)
                        <li>
                            <a href="{{ route('products.show', $product->id) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                <div class="flex items-start">
                                    <i class="bi bi-exclamation-triangle text-orange-500 me-2 mt-0.5"></i>
                                    <div>
                                        <span class="font-bold">{{ $product->product_code }}</span> stok tinggal <span class="font-bold text-red-600">{{ $product->product_quantity }}</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        @empty
                        <li>
                            <div class="px-4 py-3 text-center text-zinc-500">
                                <i class="bi bi-check-circle text-green-500 text-xl block mb-1"></i>
                                Semua stok aman
                            </div>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
         @endcan

         {{-- Search Mobile --}}
         <button type="button" class="md:hidden text-zinc-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5 me-1">
            <i class="bi bi-search font-bold"></i>
            <span class="sr-only">Cari</span>
        </button>
        
        {{-- Dark Mode Toggle --}}
        <button id="theme-toggle" type="button" class="text-zinc-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
            <i class="bi bi-sun-fill text-xl hidden dark:inline"></i>
            <i class="bi bi-moon-stars-fill text-xl inline dark:hidden"></i>
            <span class="sr-only">Toggle dark mode</span>
        </button>
        
        {{-- Apps Dropdown --}}
         <button type="button" class="text-zinc-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">    
            <i class="bi bi-grid-3x3-gap-fill text-xl"></i>
        </button>
        
        {{-- User Menu --}}
        <div class="flex items-center ms-1">
            <div>
              <button type="button" class="flex items-center gap-2 text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                <span class="sr-only">Buka menu pengguna</span>
                @php $avatar = auth()->user()->getFirstMediaUrl('avatars') ?: asset('images/default-avatar.png'); @endphp
                <img class="w-8 h-8 rounded-full object-cover border-2 border-white shadow-sm" src="{{ $avatar }}" alt="user photo">
              </button>
            </div>
            
            <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-lg dark:bg-gray-700 dark:divide-gray-600 min-w-[200px]" id="dropdown-user">
              <div class="px-4 py-3" role="none">
                <p class="text-sm text-zinc-900 dark:text-white font-bold" role="none">
                    {{ auth()->user()->name ?? 'Pengguna' }}
                </p>
                <div class="flex items-center mt-1">
                    <span class="flex w-2.5 h-2.5 bg-green-500 rounded-full me-1.5 flex-shrink-0"></span>
                    <p class="text-xs font-medium text-zinc-500 truncate dark:text-gray-300">Online</p>
                </div>
              </div>
              <ul class="py-1" role="none">
                <li>
                  <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-zinc-800 hover:bg-slate-50 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">
                    <i class="bi bi-person me-2"></i> Profile
                  </a>
                </li>
                <li>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">
                        <i class="bi bi-box-arrow-right me-2"></i> Keluar
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </li>
              </ul>
            </div>
        </div>
      </div>
    </div>
  </div>
</nav>

{{-- Dark Mode Toggle Script --}}
<script>
// Dark mode toggle
const themeToggleBtn = document.getElementById('theme-toggle');

// Check for saved theme preference or default to 'light' mode
const currentTheme = localStorage.getItem('color-theme') || 'light';

// Set initial theme
if (currentTheme === 'dark' || (!localStorage.getItem('color-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
} else {
    document.documentElement.classList.remove('dark');
}

if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', function() {
        // Toggle dark mode
        document.documentElement.classList.toggle('dark');
        
        // Save preference
        if (document.documentElement.classList.contains('dark')) {
            localStorage.setItem('color-theme', 'dark');
        } else {
            localStorage.setItem('color-theme', 'light');
        }
    });
}
</script>
