<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind & Flowbite -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {"50":"#eff6ff","100":"#dbeafe","200":"#bfdbfe","300":"#93c5fd","400":"#60a5fa","500":"#3b82f6","600":"#2563eb","700":"#1d4ed8","800":"#1e40af","900":"#1e3a8a","950":"#172554"},
                        zinc: { 50: '#fafafa', 100: '#f4f4f5', 200: '#e4e4e7', 300: '#d4d4d8', 400: '#a1a1aa', 500: '#71717a', 600: '#52525b', 700: '#3f3f46', 800: '#27272a', 900: '#18181b', 950: '#09090b' }
                    },
                    fontFamily: {
                        'body': ['Inter', 'sans-serif'],
                        'sans': ['Plus Jakarta Sans', 'Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        body { background-color: #f8fafc; }
    </style>

    @stack('page_styles')
</head>
<body class="bg-slate-50 text-zinc-950 antialiased font-sans">
    
    <!-- Navbar -->
    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700 shadow-sm">
      <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
          <div class="flex items-center justify-start rtl:justify-end">
            <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-zinc-600 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                <span class="sr-only">Buka sidebar</span>
                <i class="bi bi-list text-2xl"></i>
             </button>
            <a href="{{ route('home') }}" class="flex ms-2 md:ms-4 items-center">
              <img src="{{ asset('images/logo.png') }}" class="h-9 me-3" alt="Omah Ban Logo" />
            </a>
          </div>
          <div class="flex items-center gap-3">
             <!-- Search Mobile -->
             <button type="button" class="md:hidden text-zinc-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5 me-1">
                <i class="bi bi-search font-bold"></i>
                <span class="sr-only">Cari</span>
            </button>
            
            <!-- Apps Dropdown -->
             <button type="button" class="text-zinc-900 hover:bg-gray-100 dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">    
                <i class="bi bi-grid-3x3-gap-fill text-xl"></i>
            </button>
            
            <!-- User Menu -->
            <div class="flex items-center ms-3">
                <div>
                  <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                    <span class="sr-only">Buka menu pengguna</span>
                    <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                  </button>
                </div>
                <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-lg dark:bg-gray-700 dark:divide-gray-600" id="dropdown-user">
                  <div class="px-4 py-3" role="none">
                    <p class="text-sm text-zinc-900 dark:text-white font-bold" role="none">
                      {{ auth()->user()->name ?? 'Pengguna' }}
                    </p>
                    <p class="text-xs font-medium text-zinc-500 truncate dark:text-gray-300" role="none">
                      {{ auth()->user()->email }}
                    </p>
                  </div>
                  <ul class="py-1" role="none">
                    <li>
                      <a href="{{ route('home') }}" class="block px-4 py-2 text-sm text-zinc-800 hover:bg-slate-50 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Dashboard</a>
                    </li>
                    <li>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Keluar</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </li>
                  </ul>
                </div>
            </div>
          </div>
        </div>
      </div>
    </nav>
  
    <!-- Sidebar -->
    <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
       <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
        
          <h6 class="px-2 mb-2 text-xs font-extrabold text-zinc-400 uppercase tracking-wider">Menu Utama</h6>
          <ul class="space-y-1 font-medium">
             
             <!-- Home Item -->
             <li>
                <a href="{{ route('home') }}" class="flex items-center p-2 text-zinc-900 rounded-lg dark:text-white hover:bg-blue-50 hover:text-blue-700 group transition-all">
                   <div class="w-6 h-6 flex  items-center justify-center text-zinc-400 group-hover:text-blue-600 transition-colors">
                        <i class="bi bi-speedometer2 text-lg"></i>
                   </div>
                   <span class="ms-3 font-semibold">Dashboard</span>
                </a>
             </li>

             <!-- Sales -->
             <li>
                <a href="{{ route('sales.index') }}" class="flex items-center p-2 text-zinc-900 rounded-lg dark:text-white hover:bg-blue-50 hover:text-blue-700 group transition-all">
                   <div class="w-6 h-6 flex items-center justify-center text-zinc-400 group-hover:text-blue-600 transition-colors">
                        <i class="bi bi-cart3 text-lg"></i>
                   </div>
                   <span class="ms-3 font-semibold">Penjualan</span>
                </a>
             </li>

             <!-- Products -->
             <li>
                <a href="{{ route('products.index') }}" class="flex items-center p-2 text-zinc-900 rounded-lg dark:text-white hover:bg-blue-50 hover:text-blue-700 group transition-all">
                   <div class="w-6 h-6 flex items-center justify-center text-zinc-400 group-hover:text-blue-600 transition-colors">
                        <i class="bi bi-box-seam text-lg"></i>
                   </div>
                   <span class="ms-3 font-semibold">Produk</span>
                </a>
             </li>
             
              <!-- Customers -->
             <li>
                <a href="{{ route('customers.index') }}" class="flex items-center p-2 text-zinc-900 rounded-lg dark:text-white hover:bg-blue-50 hover:text-blue-700 group transition-all">
                   <div class="w-6 h-6 flex items-center justify-center text-zinc-400 group-hover:text-blue-600 transition-colors">
                        <i class="bi bi-people text-lg"></i>
                   </div>
                   <span class="ms-3 font-semibold">Pelanggan</span>
                </a>
             </li>

             <!-- Notifications Item -->
             <li>
                <a href="{{ route('notifications.index') }}" class="flex items-center p-2 rounded-lg group transition-all {{ Request::is('notifications*') ? 'bg-blue-50 text-blue-700 font-bold border-r-4 border-blue-600' : 'text-zinc-900 hover:bg-blue-50 hover:text-blue-700' }}">
                   <div class="w-6 h-6 flex items-center justify-center {{ Request::is('notifications*') ? 'text-blue-700' : 'text-zinc-400 group-hover:text-blue-600' }} transition-colors">
                        <i class="bi bi-bell{{ Request::is('notifications*') ? '-fill' : '' }} text-lg"></i>
                   </div>
                   <span class="ms-3">Notifikasi</span>
                   <span class="inline-flex items-center justify-center w-3 h-3 p-3 ms-3 text-sm font-medium text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-300">3</span>
                </a>
             </li>
          </ul>
       </div>
    </aside>
  
    <div class="p-4 sm:ml-64 relative">
       <div class="p-4 mt-14 max-w-7xl mx-auto">
            @yield('breadcrumb')
            @yield('content')
       </div>
        <footer class="mt-10 text-center text-xs text-zinc-400 pb-5">
            &copy; {{ date('Y') }} ProjectOmahBan system. All rights reserved.
        </footer>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    <script>
        // Initialize tooltips/popovers if needed
    </script>
    @stack('page_scripts')
</body>
</html>
