{{-- 
    Modal: Tambah Jasa Baru (Flowbite)
--}}

<div id="modal-add-service" tabindex="-1" aria-hidden="true" 
     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-2xl shadow-xl dark:bg-gray-700">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600 bg-gradient-to-r from-blue-600 to-indigo-600">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="bi bi-plus-circle"></i>
                    Tambah Jasa Baru
                </h3>
                <button type="button" onclick="closeModal('modal-add-service')"
                        class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition-all">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            {{-- Modal Body --}}
            <form id="formAddService" action="{{ route('service-masters.store') }}" method="POST">
                @csrf
                <div class="p-5 space-y-4">
                    
                    {{-- Nama Jasa --}}
                    <div>
                        <label for="addServiceName" class="block mb-1.5 text-sm font-bold text-zinc-700">
                            Nama Jasa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="addServiceName" name="service_name" required maxlength="100"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder:text-gray-400"
                               placeholder="Contoh: Pasang Ban, Balancing">
                        <p class="mt-1 text-xs text-zinc-500">
                            <i class="bi bi-info-circle me-1"></i> Nama jasa harus unik
                        </p>
                    </div>
                    
                    {{-- Harga Standar --}}
                    <div>
                        <label for="addStandardPrice" class="block mb-1.5 text-sm font-bold text-zinc-700">
                            Harga Standar (Rp) <span class="text-red-500">*</span>
                        </label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-lg font-bold">
                                Rp
                            </span>
                            <input type="text" id="addStandardPrice" name="standard_price" required value="0" inputmode="numeric"
                                   class="rounded-none rounded-r-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 block flex-1 min-w-0 w-full text-sm p-2.5 placeholder:text-gray-400"
                                   placeholder="25.000">
                        </div>
                        <p class="mt-1 text-xs text-zinc-500">
                            <i class="bi bi-info-circle me-1"></i> Masukkan 0 jika harga flexible
                        </p>
                    </div>
                    
                    {{-- Kategori --}}
                    <div>
                        <label for="addCategory" class="block mb-1.5 text-sm font-bold text-zinc-700">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select id="addCategory" name="category" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="service">Service (Jasa)</option>
                            <option value="goods">Goods (Barang)</option>
                            <option value="custom">Custom (Khusus)</option>
                        </select>
                    </div>
                    
                    {{-- Deskripsi --}}
                    <div>
                        <label for="addDescription" class="block mb-1.5 text-sm font-bold text-zinc-700">
                            Deskripsi <span class="text-zinc-400">(Opsional)</span>
                        </label>
                        <textarea id="addDescription" name="description" rows="2" maxlength="500"
                                  class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 placeholder:text-gray-400"
                                  placeholder="Deskripsi singkat tentang jasa ini"></textarea>
                        <p class="mt-1 text-xs text-zinc-500">Max 500 karakter</p>
                    </div>
                </div>
                
                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-2 p-5 border-t border-gray-200 rounded-b bg-gray-50">
                    <button type="button" onclick="closeModal('modal-add-service')"
                            class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 transition-all">
                        <i class="bi bi-x me-1"></i> Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-all">
                        <i class="bi bi-check-lg me-1"></i> Simpan Jasa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
