{{-- 
    Modal: Konfirmasi Hapus Jasa (Flowbite)
--}}

<div id="modal-delete-service" tabindex="-1" aria-hidden="true" 
     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-2xl shadow-xl dark:bg-gray-700">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600 bg-gradient-to-r from-red-600 to-rose-600">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="bi bi-trash"></i>
                    Hapus Jasa
                </h3>
                <button type="button" onclick="closeModal('modal-delete-service')"
                        class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition-all">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            {{-- Modal Body --}}
            <div class="p-5 space-y-4">
                
                {{-- Warning Alert --}}
                <div class="flex items-start gap-3 p-4 bg-red-50 rounded-lg border border-red-200">
                    <i class="bi bi-exclamation-triangle-fill text-red-600 text-xl"></i>
                    <div>
                        <h4 class="font-bold text-red-700">Peringatan!</h4>
                        <p class="text-sm text-red-600">Anda akan menghapus jasa berikut. Tindakan ini <strong>tidak dapat dibatalkan</strong>.</p>
                    </div>
                </div>
                
                {{-- Service Info Card --}}
                <div class="p-4 bg-gray-50 rounded-lg border-l-4 border-red-500">
                    <p class="text-xs text-zinc-500 mb-1">Jasa yang akan dihapus:</p>
                    <h4 id="deleteServiceName" class="text-lg font-bold text-red-600 mb-3">-</h4>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <p class="text-zinc-500">Harga Standar:</p>
                            <p id="deleteServicePrice" class="font-semibold">Rp 0</p>
                        </div>
                        <div>
                            <p class="text-zinc-500">Kategori:</p>
                            <p id="deleteServiceCategory" class="font-semibold">-</p>
                        </div>
                    </div>
                </div>
                
                {{-- Consequences --}}
                <div class="text-sm">
                    <p class="font-semibold text-zinc-700 mb-2">Apa yang akan terjadi:</p>
                    <ul class="space-y-1.5 text-zinc-600">
                        <li class="flex items-center gap-2">
                            <i class="bi bi-check-circle text-emerald-500"></i>
                            Jasa <strong>tidak akan</strong> muncul di dropdown POS
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="bi bi-check-circle text-emerald-500"></i>
                            Riwayat penggunaan jasa tetap tersimpan untuk audit
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="bi bi-check-circle text-emerald-500"></i>
                            Tidak ada data yang hilang dari database
                        </li>
                    </ul>
                </div>
                
                {{-- Confirmation Checkbox --}}
                <div class="flex items-center">
                    <input id="confirmDelete" type="checkbox" 
                           class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500 focus:ring-2">
                    <label for="confirmDelete" class="ml-2 text-sm font-medium text-gray-700">
                        Saya yakin ingin menghapus jasa ini
                    </label>
                </div>
            </div>
            
            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-2 p-5 border-t border-gray-200 rounded-b bg-gray-50">
                <button type="button" onclick="closeModal('modal-delete-service')"
                        class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 transition-all">
                    <i class="bi bi-x me-1"></i> Jangan Hapus
                </button>
                <form id="deleteServiceForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" id="btnConfirmDelete" disabled
                            class="px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="bi bi-trash me-1"></i> Ya, Hapus Jasa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
