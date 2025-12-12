<div class="relative overflow-hidden bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-5 shadow-sm mb-6 transition-all duration-300 hover:shadow-md group">
    
    {{-- Decorative Background Blob --}}
    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-gradient-to-br from-blue-500/10 to-purple-500/10 rounded-full blur-2xl pointer-events-none group-hover:scale-150 transition-transform duration-500"></div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
        
        {{-- Breadcrumb Navigation --}}
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                
                {{-- Home --}}
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-semibold text-zinc-500 hover:text-blue-600 dark:text-zinc-400 dark:hover:text-blue-400 transition-colors">
                        <i class="bi bi-house-door-fill text-lg me-2"></i>
                        Beranda
                    </a>
                </li>

                {{-- Dynamic Items --}}
                @if(isset($items))
                    @foreach($items as $item)
                        <li>
                            <div class="flex items-center">
                                <i class="bi bi-chevron-right text-zinc-300 dark:text-zinc-600 text-xs mx-2"></i>
                                
                                {{-- Item Icon (Optional) --}}
                                @if(isset($item['icon']))
                                    <i class="{{ $item['icon'] }} text-zinc-400 me-2 text-sm"></i>
                                @endif

                                @if(isset($item['url']) && $item['url'] != '#')
                                    <a href="{{ $item['url'] }}" class="text-sm font-medium text-zinc-600 hover:text-blue-600 dark:text-zinc-400 dark:hover:text-white transition-colors">
                                        {{ $item['text'] }}
                                    </a>
                                @else
                                    <span class="text-sm font-bold text-zinc-800 dark:text-zinc-200">
                                        {{ $item['text'] }}
                                    </span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                @else
                    {{-- Yield Fallback for Manual Implementation --}}
                    @yield('breadcrumb_items')
                @endif
            </ol>
        </nav>

        {{-- Date Badge --}}
        <div class="flex items-center gap-2 text-xs font-semibold text-zinc-500 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-700/50 px-3 py-1.5 rounded-lg border border-zinc-100 dark:border-zinc-600/50">
            <i class="bi bi-calendar4-week text-blue-500 dark:text-blue-400"></i>
            <span>{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
        </div>
    </div>
</div>

