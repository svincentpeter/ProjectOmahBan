{{-- Modal Create Brand --}}
<div id="modal-create" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-2xl shadow-2xl dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-5 border-b border-zinc-100 rounded-t dark:border-gray-600">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-md">
                        <i class="bi bi-plus-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-black dark:text-white">Tambah Merek Baru</h3>
                        <p class="text-xs text-zinc-500">Masukkan nama merek produk</p>
                    </div>
                </div>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="modal-create">
                    <i class="bi bi-x-lg"></i>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <form id="form-create" action="{{ route('brands.store') }}" method="POST">
                @csrf
                <div class="p-5">
                    <div class="mb-4">
                        <label for="create-name" class="block mb-2 text-sm font-bold text-black dark:text-white">
                            Nama Merek <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="create-name" 
                               name="name" 
                               class="bg-white border border-zinc-300 text-black text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white font-medium" 
                               placeholder="Contoh: Bridgestone, Dunlop, HSR..." 
                               required>
                        <p id="create-error" class="mt-2 text-sm text-red-600 hidden"></p>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center justify-end gap-2 p-5 border-t border-zinc-100 rounded-b dark:border-gray-600">
                    <button type="button" data-modal-hide="modal-create" class="px-5 py-2.5 text-sm font-semibold text-zinc-700 bg-white border border-zinc-300 rounded-xl hover:bg-zinc-50 focus:ring-4 focus:outline-none focus:ring-zinc-200 transition-all">
                        <i class="bi bi-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 transition-all shadow-sm">
                        <i class="bi bi-check-lg me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
