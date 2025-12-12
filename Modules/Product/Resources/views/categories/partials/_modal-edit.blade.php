{{-- Modal Edit Category --}}
<div id="modal-edit-category" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-2xl shadow-2xl dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-5 border-b border-zinc-100 rounded-t dark:border-gray-600">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center text-white shadow-md">
                        <i class="bi bi-pencil"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-black dark:text-white">Edit Kategori</h3>
                        <p class="text-xs text-zinc-500">Ubah kode dan nama kategori</p>
                    </div>
                </div>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="modal-edit-category">
                    <i class="bi bi-x-lg"></i>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <form id="form-edit-category" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" id="edit-category-id" name="id">
                <div class="p-5 space-y-4">
                    <div>
                        <label for="edit-category-code" class="block mb-2 text-sm font-bold text-black dark:text-white">
                            Kode Kategori <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="edit-category-code" 
                               name="category_code" 
                               class="bg-white border border-zinc-300 text-black text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white font-medium" 
                               placeholder="Kode kategori..." 
                               required>
                        <p id="edit-category-code-error" class="mt-2 text-sm text-red-600 hidden"></p>
                    </div>
                    <div>
                        <label for="edit-category-name" class="block mb-2 text-sm font-bold text-black dark:text-white">
                            Nama Kategori <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="edit-category-name" 
                               name="category_name" 
                               class="bg-white border border-zinc-300 text-black text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white font-medium" 
                               placeholder="Nama kategori..." 
                               required>
                        <p id="edit-category-name-error" class="mt-2 text-sm text-red-600 hidden"></p>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center justify-end gap-2 p-5 border-t border-zinc-100 rounded-b dark:border-gray-600">
                    <button type="button" data-modal-hide="modal-edit-category" class="px-5 py-2.5 text-sm font-semibold text-zinc-700 bg-white border border-zinc-300 rounded-xl hover:bg-zinc-50 focus:ring-4 focus:outline-none focus:ring-zinc-200 transition-all">
                        <i class="bi bi-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-amber-500 rounded-xl hover:bg-amber-600 focus:ring-4 focus:outline-none focus:ring-amber-300 transition-all shadow-sm">
                        <i class="bi bi-check-lg me-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
