@props([
    'action' => '#',
    'title' => 'Filter Data',
    'icon' => 'bi bi-funnel',
    'quickFilters' => [],
    'filters' => []
])

<div class="bg-white border border-slate-100 rounded-2xl shadow-xl shadow-slate-200/50 dark:bg-gray-800 dark:border-gray-700 mb-6 overflow-hidden">
    {{-- Header with Vibrant Gradient --}}
    <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-700 dark:to-indigo-800 flex items-center justify-between">
        <h5 class="text-white font-bold text-lg flex items-center gap-2">
            <i class="{{ $icon }}"></i> {{ $title }}
        </h5>
        
        {{-- Optional: Collapse Toggle if needed in future --}}
    </div>

    <div class="p-6 bg-slate-50/50 dark:bg-gray-900/50">
        {{-- 1. Quick Filters (Pills) --}}
        @if(!empty($quickFilters))
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-3">
                    <i class="bi bi-lightning-charge-fill text-amber-400 text-lg"></i>
                    <h6 class="font-bold text-slate-700 dark:text-gray-200 text-sm uppercase tracking-wider">Filter Cepat</h6>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($quickFilters as $qf)
                        @php
                            $isActive = request($qf['param']) == $qf['value'];
                            $activeClass = 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white border-transparent shadow-lg shadow-blue-500/30 ring-2 ring-blue-500/20';
                            $inactiveClass = 'bg-white text-slate-600 border-slate-200 hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50';
                        @endphp
                        <a href="{{ $qf['url'] }}" 
                           class="px-4 py-2 rounded-full text-sm font-bold border transition-all duration-200 flex items-center gap-2 {{ $isActive ? $activeClass : $inactiveClass }}">
                            @if(isset($qf['icon'])) <i class="{{ $qf['icon'] }}"></i> @endif
                            {{ $qf['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
            
            @if(!empty($filters))
                <hr class="border-slate-200 dark:border-gray-700 my-6">
            @endif
        @endif

        {{-- 2. Advanced Filters (Form) --}}
        @if(!empty($filters))
            <form action="{{ $action }}" method="GET" id="filter-form">
                {{-- Preserve Quick Filter functionality if needed, though they usually reset --}}
                @foreach(request()->only(array_map(fn($q) => $q['param'], $quickFilters)) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($filters as $filter)
                        <div class="relative group">
                            {{-- Label --}}
                            <label for="{{ $filter['name'] }}" class="block mb-1.5 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                {{ $filter['label'] }}
                            </label>
                            
                            {{-- Input/Select --}}
                            @if($filter['type'] === 'select')
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                        <i class="{{ $filter['icon'] ?? 'bi bi-list' }}"></i>
                                    </div>
                                    <select name="{{ $filter['name'] }}" id="{{ $filter['name'] }}" 
                                            class="bg-white border border-slate-300 text-slate-700 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 shadow-sm transition-all hover:border-blue-400">
                                        <option value="">{{ $filter['placeholder'] ?? 'Semua ' . $filter['label'] }}</option>
                                        @foreach($filter['options'] as $optValue => $optLabel)
                                            <option value="{{ $optValue }}" {{ request($filter['name']) == $optValue ? 'selected' : '' }}>
                                                {{ $optLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif($filter['type'] === 'date')
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                        <i class="{{ $filter['icon'] ?? 'bi bi-calendar' }}"></i>
                                    </div>
                                    <input type="date" name="{{ $filter['name'] }}" id="{{ $filter['name'] }}" value="{{ request($filter['name']) }}"
                                           class="bg-white border border-slate-300 text-slate-700 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 shadow-sm transition-all hover:border-blue-400">
                                </div>
                            @else
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                        <i class="{{ $filter['icon'] ?? 'bi bi-input-cursor' }}"></i>
                                    </div>
                                    <input type="text" name="{{ $filter['name'] }}" id="{{ $filter['name'] }}" value="{{ request($filter['name']) }}" placeholder="{{ $filter['placeholder'] ?? '' }}"
                                           class="bg-white border border-slate-300 text-slate-700 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 shadow-sm transition-all hover:border-blue-400">
                                </div>
                            @endif
                        </div>
                    @endforeach

                    {{-- Action Buttons --}}
                    <div class="flex items-end gap-2 lg:col-span-4 justify-end mt-2">
                         <a href="{{ $action }}" class="text-slate-500 bg-white border border-slate-300 hover:bg-slate-50 hover:text-slate-700 font-bold rounded-xl text-sm px-5 py-2.5 focus:outline-none transition-all shadow-sm">
                            <i class="bi bi-arrow-counterclockwise mr-1"></i> Reset
                        </a>
                        <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-bold rounded-xl text-sm px-5 py-2.5 transition-all shadow-lg shadow-blue-500/30 flex items-center gap-2">
                            <i class="bi bi-funnel-fill"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>
