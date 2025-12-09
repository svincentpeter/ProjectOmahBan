<div class="relative w-full">
    {{-- Search Input --}}
    <div class="relative w-full">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <i class="bi bi-search text-ob-primary text-lg"></i>
        </div>
        <input 
            wire:keydown.escape="resetQuery" 
            wire:model.live.debounce.500ms="query" 
            type="text" 
            class="bg-white border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-ob-primary focus:border-ob-primary block w-full pl-10 p-4 shadow-sm transition-all hover:border-ob-primary" 
            placeholder="Ketik nama produk atau kode..."
            autofocus
        >
        {{-- Loading Spinner in right side --}}
        <div wire:loading class="absolute inset-y-0 right-0 flex items-center pr-3">
             <svg aria-hidden="true" class="w-5 h-5 text-gray-200 animate-spin dark:text-gray-600 fill-ob-primary" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
            </svg>
        </div>
    </div>

    {{-- Dropdown Results --}}
    @if(!empty($query))
        <div wire:click="resetQuery" class="fixed inset-0 z-10" aria-hidden="true"></div>
        
        <div class="absolute z-20 w-full mt-2 bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden">
            @if($search_results->isNotEmpty())
                <ul class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                    @foreach($search_results as $result)
                        <li class="hover:bg-slate-50 transition-colors">
                            <a wire:click="resetQuery" wire:click.prevent="selectProduct({{ $result }})" href="#" 
                               class="flex flex-col py-3 px-4 w-full text-left">
                                <span class="font-bold text-slate-800 text-sm">{{ $result->product_name }}</span>
                                <span class="text-xs text-slate-500 font-mono">{{ $result->product_code }}</span>
                            </a>
                        </li>
                    @endforeach
                    
                    @if($search_results->count() >= $how_many)
                         <li class="p-3 bg-slate-50 text-center border-t border-slate-100">
                             <a wire:click.prevent="loadMore" class="inline-flex items-center text-sm font-semibold text-ob-primary hover:text-indigo-800" href="#">
                                 Load More <i class="bi bi-arrow-down-circle ml-1"></i>
                             </a>
                         </li>
                    @endif
                </ul>
            @else
                <div class="p-4 text-center text-slate-500 bg-slate-50">
                    <i class="bi bi-search mb-1 text-lg block opacity-50"></i>
                    <p class="text-sm">Produk tidak ditemukan...</p>
                </div>
            @endif
        </div>
    @endif
</div>
