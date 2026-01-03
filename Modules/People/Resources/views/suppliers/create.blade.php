@extends('layouts.app-flowbite')

@section('title', 'Tambah Supplier')

@section('content')
    {{-- Breadcrumb --}}
    @include('layouts.breadcrumb-flowbite', [
        'title' => 'Tambah Supplier',
        'items' => [
            ['text' => 'Home', 'url' => route('home')],
            ['text' => 'Supplier', 'url' => route('suppliers.index')],
            ['text' => 'Tambah', 'url' => '#']
        ]
    ])

    <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Input Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Card: Informasi Supplier -->
                <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm p-6 dark:bg-zinc-800 dark:border-zinc-700">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-zinc-100 dark:border-zinc-700">
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <i class="bi bi-person-lines-fill text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Informasi Supplier</h3>
                            <p class="text-sm text-zinc-500">Lengkapi data supplier untuk keperluan pembelian stok.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Supplier -->
                        <div>
                            <label for="supplier_name" class="block mb-2 text-sm font-bold text-zinc-700 dark:text-zinc-300">
                                Nama Supplier <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="supplier_name" 
                                   name="supplier_name" 
                                   value="{{ old('supplier_name') }}"
                                   class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-zinc-400 @error('supplier_name') border-red-500 bg-red-50 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   placeholder="Contoh: PT. Ban Maju Jaya" 
                                   required>
                            @error('supplier_name')
                                <p class="mt-2 text-sm text-red-600"><span class="font-medium">Oops!</span> {{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="supplier_email" class="block mb-2 text-sm font-bold text-zinc-700 dark:text-zinc-300">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="supplier_email" 
                                   name="supplier_email" 
                                   value="{{ old('supplier_email') }}"
                                   class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-zinc-400 @error('supplier_email') border-red-500 bg-red-50 text-red-900 @enderror" 
                                   placeholder="email@supplier.com" 
                                   required>
                            @error('supplier_email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- No. Telepon -->
                        <div>
                            <label for="supplier_phone" class="block mb-2 text-sm font-bold text-zinc-700 dark:text-zinc-300">
                                No. Telepon <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="supplier_phone" 
                                   name="supplier_phone" 
                                   value="{{ old('supplier_phone') }}"
                                   class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-zinc-400 @error('supplier_phone') border-red-500 bg-red-50 text-red-900 @enderror" 
                                   placeholder="0812xxxx" 
                                   required>
                            @error('supplier_phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-zinc-500">Gunakan format angka lokal (08xxx).</p>
                        </div>

                        <!-- Kota -->
                        <div>
                            <label for="city" class="block mb-2 text-sm font-bold text-zinc-700 dark:text-zinc-300">
                                Kota <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city') }}"
                                   class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-zinc-400 @error('city') border-red-500 bg-red-50 text-red-900 @enderror" 
                                   placeholder="Jakarta, Surabaya, dll" 
                                   required>
                            @error('city')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Negara -->
                        <div class="md:col-span-2">
                            <label for="country" class="block mb-2 text-sm font-bold text-zinc-700 dark:text-zinc-300">Negara</label>
                            <select id="country" name="country" class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="Indonesia" {{ old('country', 'Indonesia') == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                                <option value="Malaysia" {{ old('country') == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
                                <option value="Singapura" {{ old('country') == 'Singapura' ? 'selected' : '' }}>Singapura</option>
                                <option value="Brunei" {{ old('country') == 'Brunei' ? 'selected' : '' }}>Brunei</option>
                            </select>
                        </div>

                        <!-- Alamat -->
                        <div class="md:col-span-2">
                            <label for="address" class="block mb-2 text-sm font-bold text-zinc-700 dark:text-zinc-300">
                                Alamat Lengkap <span class="text-red-500">*</span>
                            </label>
                            <textarea id="address" 
                                      name="address" 
                                      rows="4" 
                                      class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-zinc-400 @error('address') border-red-500 bg-red-50 text-red-900 @enderror" 
                                      placeholder="Nama jalan, nomor gedung, RT/RW, Kecamatan..." 
                                      required>{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-zinc-500">Minimal 10 karakter untuk alamat yang valid.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Info & Action -->
            <div class="space-y-6">
                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-start mb-3">
                        <i class="bi bi-info-circle-fill text-blue-600 text-xl me-2"></i>
                        <h4 class="font-bold text-blue-900">Tips Pengisian</h4>
                    </div>
                    <ul class="space-y-2 text-sm text-blue-800 list-disc list-inside">
                        <li>Pastikan email aktif untuk keperluan faktur digital.</li>
                        <li>Alamat detail membantu pengiriman retur barang jika diperlukan.</li>
                        <li>Data supplier dapat diubah kapan saja melalui menu Edit.</li>
                    </ul>
                </div>

                <!-- Action Card -->
                <div class="bg-white border border-zinc-200 rounded-2xl p-5 shadow-sm">
                    <h4 class="font-bold text-zinc-900 mb-4">Simpan Data</h4>
                    <p class="text-sm text-zinc-500 mb-4">Pastikan seluruh data yang wajib diisi (bertanda *) sudah lengkap dan benar.</p>
                    
                    <div class="flex flex-col gap-3">
                        <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-bold rounded-xl text-sm px-5 py-2.5 text-center transition-all shadow-md hover:shadow-lg">
                            <i class="bi bi-save me-2"></i> Simpan Supplier
                        </button>
                        <a href="{{ route('suppliers.index') }}" class="w-full text-zinc-700 bg-white border border-zinc-300 hover:bg-zinc-50 focus:ring-4 focus:ring-zinc-100 font-bold rounded-xl text-sm px-5 py-2.5 text-center transition-all">
                            Batal
                        </a>
                    </div>
                </div>
                
                @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center gap-2 mb-2 text-red-800 font-bold">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <h5>Terdapat Kesalahan</h5>
                    </div>
                    <ul class="list-disc list-inside text-sm text-red-700">
                         @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </form>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Auto-format phone (simple validation visuals)
            $('#supplier_phone').on('blur', function() {
                const val = $(this).val();
                if(val && !/^[0-9+\-\s()]+$/.test(val)) {
                    $(this).addClass('border-red-500 bg-red-50');
                } else {
                    $(this).removeClass('border-red-500 bg-red-50');
                }
            });

            // Auto capitalize city
            $('#city').on('blur', function() {
                let city = $(this).val();
                if(city) {
                    city = city.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
                    $(this).val(city);
                }
            });

            // Client-side validation for address length
             $('form').on('submit', function(e) {
                const address = $('#address').val().trim();
                let valid = true;

                if (address.length < 10) {
                    $('#address').addClass('border-red-500 bg-red-50');
                    // Add error message if not exists
                    if($('#address-error').length === 0) {
                        $('#address').after('<p id="address-error" class="mt-2 text-sm text-red-600">Alamat terlalu pendek, minimal 10 karakter.</p>');
                    }
                    valid = false;
                } else {
                    $('#address').removeClass('border-red-500 bg-red-50');
                    $('#address-error').remove();
                }

                if(!valid) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: $("#address").offset().top - 150
                    }, 500);
                } else {
                     // Loading state
                    const btn = $(this).find('button[type="submit"]');
                    btn.prop('disabled', true).html('<span class="inline-block animate-spin mr-2"><i class="bi bi-arrow-repeat"></i></span> Menyimpan...');
                }
             });
        });
    </script>
@endpush
