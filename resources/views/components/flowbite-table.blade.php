{{--
    Flowbite Table Component (Premium Redesign)
    ========================
    Komponen tabel manual dengan desain premium Flowbite.
--}}

@props([
    'title' => 'Data',
    'description' => null,
    'icon' => 'bi-table',
    'searchable' => true,
    'searchPlaceholder' => 'Cari data...',
    'addRoute' => null,
    'addLabel' => 'Tambah Data',
    'exportRoute' => null,
    'items' => null,
])

<section class="bg-gray-50 dark:bg-gray-900 pb-12">
    <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
        {{-- Card Container --}}
        <div class="relative bg-white dark:bg-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-gray-900/50 sm:rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700 transition-all duration-300 hover:shadow-2xl hover:shadow-blue-100/50 dark:hover:shadow-none">
            
            {{-- Header dengan Gradient Accent --}}
            <div class="relative px-6 py-6 border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                {{-- Decorative Gradient Line --}}
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 opacity-80"></div>

                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center gap-5">
                        @if($icon)
                            <div class="relative group">
                                <div class="absolute inset-0 bg-blue-500/20 dark:bg-blue-500/10 blur-xl rounded-full group-hover:bg-blue-500/30 transition-all duration-500"></div>
                                <div class="relative w-12 h-12 flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 rounded-xl border border-blue-100 dark:border-gray-600 shadow-sm transition-transform duration-300 group-hover:scale-105">
                                    <i class="{{ $icon }} text-2xl text-blue-600 dark:text-blue-400 drop-shadow-sm"></i>
                                </div>
                            </div>
                        @else
                            <div class="w-1 h-12 bg-blue-500 rounded-full"></div>
                        @endif
                        
                        <div>
                            <h5 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">{{ $title }}</h5>
                            @if($description)
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 font-medium">{{ $description }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                        {{ $actions ?? '' }}
                        
                        @if($exportRoute)
                        <a href="{{ $exportRoute }}" class="inline-flex justify-center items-center px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-blue-600 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 transition-all duration-200">
                            <i class="bi bi-download mr-2 text-lg"></i>
                            Export
                        </a>
                        @endif

                        @if($addRoute)
                        <a href="{{ $addRoute }}" class="inline-flex justify-center items-center px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-0.5 transition-all duration-200">
                            <i class="bi bi-plus-lg mr-2 text-lg"></i>
                            {{ $addLabel }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Toolbar: Search & Filters --}}
            <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                {{-- Search --}}
                @if($searchable)
                <div class="w-full md:w-96">
                    <form method="GET" action="" class="relative">
                        @foreach(request()->except(['search', 'page']) as $key => $value)
                            @if($value) <input type="hidden" name="{{ $key }}" value="{{ $value }}"> @endif
                        @endforeach
                        
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <i class="bi bi-search text-gray-400 text-lg"></i>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               class="bg-white border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block w-full pl-11 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-shadow duration-200 shadow-sm" 
                               placeholder="{{ $searchPlaceholder }}">
                        @if(request('search'))
                            <a href="{{ request()->url() }}" class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <i class="bi bi-x-circle-fill"></i>
                            </a>
                        @endif
                    </form>
                </div>
                @else
                <div class="hidden md:block"></div>
                @endif

                {{-- Filters Slot --}}
                <div class="flex items-center gap-3 w-full md:w-auto overflow-x-auto pb-1 md:pb-0 scrollbar-hide">
                    {{ $filters ?? '' }}
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto relative">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50/80 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 sticky top-0 z-10 backdrop-blur-sm">
                        {{ $thead ?? '' }}
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        {{ $slot }}
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($items && method_exists($items, 'links'))
            <div class="px-6 py-4 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
                @if(isset($pagination))
                    {{ $pagination }}
                @else
                    {{ $items->withQueryString()->links() }}
                @endif
            </div>
            @endif

        </div>
    </div>
</section>

