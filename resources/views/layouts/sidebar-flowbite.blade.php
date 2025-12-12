{{-- Modern Clean Sidebar - White Theme --}}
<aside id="logo-sidebar" 
       class="fixed top-0 left-0 z-40 w-60 h-screen pt-16 transition-transform -translate-x-full bg-white/95 backdrop-blur-xl border-r border-zinc-200/80 sm:translate-x-0 shadow-sm" 
       aria-label="Sidebar">
   
   {{-- Sidebar Inner Container with Custom Scrollbar --}}
   <div class="h-full flex flex-col overflow-hidden">
      
      {{-- Scrollable Menu Area --}}
      <nav class="flex-1 px-3 py-4 overflow-y-auto custom-scrollbar">
         <ul class="space-y-0.5">
            @include('layouts.menu-flowbite')
         </ul>
      </nav>
      
      {{-- Bottom User Section --}}
      <div class="p-3 border-t border-zinc-100">
         <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-zinc-50 transition-colors cursor-pointer">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
               {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
               <p class="text-xs font-semibold text-zinc-800 truncate">{{ Auth::user()->name ?? 'User' }}</p>
               <p class="text-[10px] text-zinc-400 truncate">{{ Auth::user()->email ?? '' }}</p>
            </div>
         </div>
      </div>
   </div>
</aside>

{{-- Custom Scrollbar Styles --}}
<style>
   .custom-scrollbar::-webkit-scrollbar {
      width: 4px;
   }
   .custom-scrollbar::-webkit-scrollbar-track {
      background: transparent;
   }
   .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #e4e4e7;
      border-radius: 4px;
   }
   .custom-scrollbar::-webkit-scrollbar-thumb:hover {
      background: #d4d4d8;
   }
</style>
