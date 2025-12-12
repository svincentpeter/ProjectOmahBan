{{-- 
    Modal: Edit Jasa (Flowbite)
--}}

<div id="modal-edit-service" tabindex="-1" aria-hidden="true" 
     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-lg max-h-full">
        <div class="relative bg-white rounded-2xl shadow-xl dark:bg-gray-700">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600 bg-gradient-to-r from-amber-500 to-orange-500">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="bi bi-pencil"></i>
                    Edit Jasa
                </h3>
                <button type="button" onclick="closeModal('modal-edit-service')"
                        class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition-all">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            {{-- Modal Body --}}
            <form id="editServiceForm" method="POST">
                @csrf
                @method('PUT')
                <div class="p-5 space-y-4">
                    
                    {{-- Nama Jasa --}}
                    <div>
                        <label for="editServiceName" class="block mb-1.5 text-sm font-bold text-zinc-700">
                            Nama Jasa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="editServiceName" name="service_name" required maxlength="100"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
                               placeholder="Nama jasa">
                    </div>
                    
                    {{-- Harga Lama Display --}}
                    <div class="p-3 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-blue-700">Harga Lama:</span>
                            <span id="oldPriceDisplay" class="text-lg font-bold text-blue-700">Rp 0</span>
                        </div>
                    </div>
                    
                    {{-- Harga Baru --}}
                    <div>
                        <label for="editStandardPrice" class="block mb-1.5 text-sm font-bold text-zinc-700">
                            Harga Standar Baru (Rp) <span class="text-red-500">*</span>
                        </label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-lg font-bold">
                                Rp
                            </span>
                            <input type="text" id="editStandardPrice" name="standard_price" required value="0" inputmode="numeric"
                                   class="rounded-none rounded-r-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-amber-500 focus:border-amber-500 block flex-1 min-w-0 w-full text-sm p-2.5"
                                   placeholder="25.000">
                        </div>
                        <p class="mt-1 text-xs text-zinc-500">
                            <i class="bi bi-info-circle me-1"></i> Perubahan harga akan dicatat dalam audit log
                        </p>
                    </div>
                    
                    {{-- Price Change Alert --}}
                    <div id="priceChangeAlert" class="hidden p-3 bg-amber-50 rounded-lg border border-amber-200">
                        <div class="flex items-start gap-2">
                            <i class="bi bi-exclamation-triangle text-amber-600 mt-0.5"></i>
                            <div class="text-sm">
                                <p class="font-semibold text-amber-700">Perubahan Harga Terdeteksi</p>
                                <p class="text-amber-600">
                                    <span id="priceChangeOld">Rp 0</span> → <span id="priceChangeNew">Rp 0</span>
                                    (<span id="priceChangePercent" class="font-bold">0%</span>)
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Kategori --}}
                    <div>
                        <label for="editCategory" class="block mb-1.5 text-sm font-bold text-zinc-700">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select id="editCategory" name="category" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
                            <option value="service">Service (Jasa)</option>
                            <option value="goods">Goods (Barang)</option>
                            <option value="custom">Custom (Khusus)</option>
                        </select>
                    </div>
                    
                    {{-- Deskripsi --}}
                    <div>
                        <label for="editDescription" class="block mb-1.5 text-sm font-bold text-zinc-700">
                            Deskripsi <span class="text-zinc-400">(Opsional)</span>
                        </label>
                        <textarea id="editDescription" name="description" rows="2" maxlength="500"
                                  class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-amber-500 focus:border-amber-500"
                                  placeholder="Deskripsi singkat tentang jasa ini"></textarea>
                    </div>
                </div>
                
                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-2 p-5 border-t border-gray-200 rounded-b bg-gray-50">
                    <button type="button" onclick="closeModal('modal-edit-service')"
                            class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 transition-all">
                        <i class="bi bi-x me-1"></i> Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2.5 text-sm font-medium text-white bg-amber-500 rounded-lg hover:bg-amber-600 focus:ring-4 focus:ring-amber-300 transition-all">
                        <i class="bi bi-check-lg me-1"></i> Update Jasa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
