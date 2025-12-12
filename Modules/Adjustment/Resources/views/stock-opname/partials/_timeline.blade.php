{{--
    Timeline Partial (Tailwind Version)
    Variables: $logs (Collection of StockOpnameLog)
--}}

@if($logs->isEmpty())
    <div class="text-center py-12 text-zinc-500">
        <i class="bi bi-clock-history text-4xl"></i>
        <p class="mt-2">Belum ada riwayat aktivitas.</p>
    </div>
@else
    <div class="relative pl-4 border-l border-zinc-200 space-y-8 ml-2 mt-4">
        @foreach($logs as $log)
            <div class="relative">
                <span class="absolute -left-[25px] top-1 flex items-center justify-center w-6 h-6 bg-white rounded-full ring-4 ring-zinc-50 border border-zinc-200">
                    <i class="bi bi-circle-fill text-xs {{ $log->action === 'created' || $log->action === 'started' ? 'text-blue-500' : ($log->action === 'completed' || $log->action === 'approved' ? 'text-emerald-500' : 'text-zinc-500') }}"></i>
                </span>
                <div class="bg-white border border-zinc-100 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-sm font-bold {{ $log->action === 'created' || $log->action === 'started' ? 'text-blue-600' : ($log->action === 'completed' || $log->action === 'approved' ? 'text-emerald-600' : 'text-zinc-700') }}">
                            {{ Str::title(str_replace('_', ' ', $log->action)) }}
                        </span>
                        <span class="text-xs text-zinc-400">
                            {{ $log->created_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                    <p class="text-sm text-zinc-600 mb-2">
                        {{ $log->description }}
                    </p>
                    <div class="flex items-center text-xs text-zinc-500">
                        <div class="w-5 h-5 rounded-full bg-zinc-100 flex items-center justify-center me-2 text-zinc-600">
                            <i class="bi bi-person"></i>
                        </div>
                        {{ $log->user->name ?? 'System' }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
