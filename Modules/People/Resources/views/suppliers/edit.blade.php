@extends('layouts.app-flowbite')

@section('title', 'Edit Supplier')

@section('content')
    <!-- Header -->
    <div class="mb-6">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-zinc-700 hover:text-blue-600 dark:text-zinc-400 dark:hover:text-white">
                        <i class="bi bi-house-door me-2"></i> Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="bi bi-chevron-right text-zinc-400 text-xs mx-1"></i>
                        <a href="{{ route('suppliers.index') }}" class="ms-1 text-sm font-medium text-zinc-700 hover:text-blue-600 md:ms-2 dark:text-zinc-400 dark:hover:text-white">
                            Supplier
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="bi bi-chevron-right text-zinc-400 text-xs mx-1"></i>
                        <span class="ms-1 text-sm font-medium text-zinc-500 md:ms-2 dark:text-zinc-400">Edit</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-extrabold text-zinc-900 tracking-tight dark:text-white">Edit Data Supplier</h1>
    </div>

    <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Input Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Card: Informasi Supplier -->
                <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm p-6 dark:bg-zinc-800 dark:border-zinc-700">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-zinc-100 dark:border-zinc-700">
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <i class="bi bi-pencil-square text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Edit Informasi Supplier</h3>
                            <p class="text-sm text-zinc-500">Perbarui data supplier untuk pembelian stok.</p>
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
                                   value="{{ old('supplier_name', $supplier->supplier_name) }}"
                                   class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('supplier_name') border-red-500 bg-red-50 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 @enderror" 
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
                                   value="{{ old('supplier_email', $supplier->supplier_email) }}"
                                   class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('supplier_email') border-red-500 bg-red-50 text-red-900 @enderror" 
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
                                   value="{{ old('supplier_phone', $supplier->supplier_phone) }}"
                                   class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('supplier_phone') border-red-500 bg-red-50 text-red-900 @enderror" 
                                   required>
                            @error('supplier_phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kota -->
                        <div>
                            <label for="city" class="block mb-2 text-sm font-bold text-zinc-700 dark:text-zinc-300">
                                Kota <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city', $supplier->city) }}"
                                   class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('city') border-red-500 bg-red-50 text-red-900 @enderror" 
                                   required>
                            @error('city')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Negara -->
                        <div class="md:col-span-2">
                            <label for="country" class="block mb-2 text-sm font-bold text-zinc-700 dark:text-zinc-300">Negara</label>
                            <select id="country" name="country" class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="Indonesia" {{ old('country', $supplier->country) == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                                <option value="Malaysia" {{ old('country', $supplier->country) == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
                                <option value="Singapura" {{ old('country', $supplier->country) == 'Singapura' ? 'selected' : '' }}>Singapura</option>
                                <option value="Brunei" {{ old('country', $supplier->country) == 'Brunei' ? 'selected' : '' }}>Brunei</option>
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
                                      class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('address') border-red-500 bg-red-50 text-red-900 @enderror" 
                                      required>{{ old('address', $supplier->address) }}</textarea>
                            @error('address')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-zinc-500">Minimal 10 karakter.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Info & Action -->
            <div class="space-y-6">
                <!-- Info Card -->
                <div class="bg-zinc-50 border border-zinc-200 rounded-2xl p-5 shadow-sm">
                    <h5 class="font-bold text-zinc-900 mb-4 border-b border-zinc-200 pb-2">Status Supplier</h5>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-zinc-500"><i class="bi bi-cart me-2"></i>Total Transaksi</span>
                            <span class="font-bold text-zinc-900">{{ $supplier->purchases->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-zinc-500"><i class="bi bi-currency-dollar me-2"></i>Total Nilai</span>
                            <span class="font-bold text-emerald-600">{{ format_currency($supplier->purchases->sum('total_amount')) }}</span>
                        </div>
                         <div class="flex justify-between items-center text-sm">
                            <span class="text-zinc-500"><i class="bi bi-calendar me-2"></i>Terdaftar</span>
                            <span class="font-bold text-zinc-900">{{ $supplier->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="pt-2 text-xs text-zinc-400 text-right">
                           Terakhir update: {{ $supplier->updated_at->diffForHumans() }}
                        </div>
                    </div>

                    @if ($supplier->purchases->count() > 0)
                    <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                        <div class="flex items-start gap-2">
                            <i class="bi bi-exclamation-circle-fill text-amber-500 mt-0.5"></i>
                            <p class="text-xs text-amber-800">
                                Supplier ini memiliki riwayat pembelian. Perubahan pada nama atau alamat mungkin mempengaruhi faktur lama.
                            </p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Action Card -->
                <div class="bg-white border border-zinc-200 rounded-2xl p-5 shadow-sm sticky top-24">
                    <h4 class="font-bold text-zinc-900 mb-4">Simpan Perubahan</h4>
                    <p class="text-sm text-zinc-500 mb-4">Periksa kembali data sebelum menyimpan.</p>
                    
                    <div class="flex flex-col gap-3">
                        <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-bold rounded-xl text-sm px-5 py-2.5 text-center transition-all shadow-md hover:shadow-lg">
                            <i class="bi bi-cloud-arrow-up me-2"></i> Update Data
                        </button>
                        <a href="{{ route('suppliers.show', $supplier->id) }}" class="w-full text-blue-600 bg-blue-50 hover:bg-blue-100 border border-transparent font-bold rounded-xl text-sm px-5 py-2.5 text-center transition-all">
                             <i class="bi bi-eye me-2"></i> Lihat Detail
                        </a>
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
            // Track changes
            const initialData = $('form').serialize();
            
            $(window).on('beforeunload', function() {
                if ($('form').serialize() !== initialData) {
                    return 'Perubahan belum disimpan!';
                }
            });
            
            $('form').on('submit', function() {
                $(window).off('beforeunload');
                $(this).find('button[type="submit"]').prop('disabled', true).html('<span class="inline-block animate-spin mr-2"><i class="bi bi-arrow-repeat"></i></span> Memproses...');
            });
        
            // Same helper scripts
            $('#supplier_phone').on('blur', function() {
                const val = $(this).val();
                if(val && !/^[0-9+\-\s()]+$/.test(val)) {
                    $(this).addClass('border-red-500 bg-red-50');
                } else {
                    $(this).removeClass('border-red-500 bg-red-50');
                }
            });

            $('#city').on('blur', function() {
                let city = $(this).val();
                if(city) {
                    city = city.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
                    $(this).val(city);
                }
            });
        });
    </script>
@endpush
