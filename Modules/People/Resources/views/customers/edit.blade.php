@extends('layouts.app-flowbite')

@section('title', 'Edit Customer')

@section('content')
    {{-- Breadcrumb --}}
    @include('layouts.breadcrumb-flowbite', [
        'title' => 'Edit Customer',
        'items' => [
            ['text' => 'Home', 'url' => route('home')],
            ['text' => 'Customer', 'url' => route('customers.index')],
            ['text' => 'Edit', 'url' => '#']
        ]
    ])

    <div class="p-4">
        <form action="{{ route('customers.update', $customer->id) }}" method="POST" id="customer-form">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- LEFT COLUMN: Form Input --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Informasi Customer --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-zinc-200">
                        <div class="p-6 border-b border-zinc-100">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-50 rounded-xl text-indigo-600">
                                    <i class="bi bi-person-fill-gear text-xl"></i>
                                </div>
                                <div>
                                    <h5 class="text-lg font-bold text-zinc-800">Edit Informasi Customer</h5>
                                    <p class="text-sm text-zinc-500">Perbarui data customer untuk transaksi penjualan</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Nama Customer --}}
                                <div>
                                    <label for="customer_name" class="block text-sm font-medium text-zinc-700 mb-1">
                                        Nama Customer <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           class="w-full rounded-xl border-zinc-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm placeholder-zinc-400 @error('customer_name') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                                           name="customer_name" 
                                           id="customer_name" 
                                           value="{{ old('customer_name', $customer->customer_name) }}"
                                           placeholder="Contoh: Budi Santoso" 
                                           required>
                                    @error('customer_name')
                                        <p class="mt-1 text-xs text-red-500 flex items-center">
                                            <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div>
                                    <label for="customer_email" class="block text-sm font-medium text-zinc-700 mb-1">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" 
                                           class="w-full rounded-xl border-zinc-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm placeholder-zinc-400 @error('customer_email') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                                           name="customer_email" 
                                           id="customer_email" 
                                           value="{{ old('customer_email', $customer->customer_email) }}" 
                                           placeholder="email@customer.com"
                                           required>
                                    @error('customer_email')
                                        <p class="mt-1 text-xs text-red-500 flex items-center">
                                            <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- No. Telepon --}}
                                <div>
                                    <label for="customer_phone" class="block text-sm font-medium text-zinc-700 mb-1">
                                        No. Telepon <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           class="w-full rounded-xl border-zinc-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm placeholder-zinc-400 @error('customer_phone') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                                           name="customer_phone" 
                                           id="customer_phone" 
                                           value="{{ old('customer_phone', $customer->customer_phone) }}"
                                           placeholder="08123456789" 
                                           required>
                                    @error('customer_phone')
                                        <p class="mt-1 text-xs text-red-500 flex items-center">
                                            <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                                        </p>
                                    @enderror
                                    <p class="mt-1 text-xs text-zinc-500">Format: 08xxx atau +62-xxx</p>
                                </div>

                                {{-- Kota --}}
                                <div>
                                    <label for="city" class="block text-sm font-medium text-zinc-700 mb-1">
                                        Kota <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           class="w-full rounded-xl border-zinc-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm placeholder-zinc-400 @error('city') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                                           name="city" 
                                           id="city" 
                                           value="{{ old('city', $customer->city) }}"
                                           placeholder="Contoh: Jakarta" 
                                           required>
                                    @error('city')
                                        <p class="mt-1 text-xs text-red-500 flex items-center">
                                            <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Negara --}}
                                <div class="md:col-span-2">
                                    <label for="country" class="block text-sm font-medium text-zinc-700 mb-1">
                                        Negara
                                    </label>
                                    <select class="w-full rounded-xl border-zinc-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm @error('country') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                                            name="country" 
                                            id="country">
                                        <option value="Indonesia" {{ old('country', $customer->country) == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                                        <option value="Malaysia" {{ old('country', $customer->country) == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
                                        <option value="Singapura" {{ old('country', $customer->country) == 'Singapura' ? 'selected' : '' }}>Singapura</option>
                                        <option value="Brunei" {{ old('country', $customer->country) == 'Brunei' ? 'selected' : '' }}>Brunei</option>
                                    </select>
                                    @error('country')
                                        <p class="mt-1 text-xs text-red-500 flex items-center">
                                            <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                                        </p>
                                    @enderror
                                    <p class="mt-1 text-xs text-zinc-500">Default: Indonesia (untuk UMKM lokal)</p>
                                </div>

                                {{-- Alamat Lengkap --}}
                                <div class="md:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-zinc-700 mb-1">
                                        Alamat Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <textarea class="w-full rounded-xl border-zinc-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm placeholder-zinc-400 @error('address') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
                                              name="address" 
                                              id="address" 
                                              rows="3"
                                              placeholder="Alamat lengkap customer (minimal 10 karakter)" 
                                              required>{{ old('address', $customer->address) }}</textarea>
                                    @error('address')
                                        <p class="mt-1 text-xs text-red-500 flex items-center">
                                            <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: Info & Action --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-zinc-200 sticky top-24">
                        <div class="p-5 border-b border-zinc-100">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-blue-50 rounded-xl text-blue-600">
                                    <i class="bi bi-info-circle-fill text-xl"></i>
                                </div>
                                <h5 class="text-lg font-bold text-zinc-800">Informasi Customer</h5>
                            </div>
                        </div>

                        <div class="p-5">
                            {{-- Customer Stats --}}
                            <div class="bg-zinc-50 rounded-xl p-4 mb-4 border border-zinc-100">
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-zinc-500 flex items-center"><i class="bi bi-cart me-2"></i> Total Penjualan</span>
                                        <span class="text-sm font-bold text-indigo-600">{{ $customer->sales->count() }} transaksi</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-zinc-500 flex items-center"><i class="bi bi-credit-card me-2"></i> Total Nilai</span>
                                        <span class="text-sm font-bold text-emerald-600">{{ format_currency($customer->sales->sum('total_amount')) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-zinc-500 flex items-center"><i class="bi bi-calendar me-2"></i> Terdaftar</span>
                                        <span class="text-sm font-medium text-zinc-700">{{ $customer->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Warning jika ada sales history --}}
                            @if ($hasSales)
                                <div class="bg-amber-50 rounded-xl p-4 mb-4 border border-amber-100">
                                    <div class="flex items-start gap-3">
                                        <i class="bi bi-exclamation-triangle-fill text-amber-600 mt-1"></i>
                                        <div>
                                            <h6 class="text-sm font-bold text-amber-800 mb-1">Perhatian!</h6>
                                            <p class="text-xs text-amber-700 leading-relaxed">
                                                Customer ini memiliki <strong>{{ $customer->sales->count() }}</strong> riwayat penjualan.
                                                Jika dihapus, data hanya akan di-arsipkan (soft delete).
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Validation Summary --}}
                            @if ($errors->any())
                                <div class="bg-red-50 rounded-xl p-4 mb-4 border border-red-100">
                                    <div class="flex items-start gap-3">
                                        <i class="bi bi-exclamation-triangle-fill text-red-600 mt-1"></i>
                                        <div>
                                            <h6 class="text-sm font-bold text-red-800 mb-1">Terdapat kesalahan:</h6>
                                            <ul class="text-xs text-red-700 list-disc list-inside space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <hr class="border-zinc-100 my-4">

                            {{-- Submit Buttons --}}
                            <div class="space-y-3">
                                <button type="submit" class="w-full flex items-center justify-center px-4 py-3 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 group">
                                    <i class="bi bi-save me-2 group-hover:scale-110 transition-transform"></i> Update Customer
                                </button>
                                <a href="{{ route('customers.show', $customer->id) }}" class="w-full flex items-center justify-center px-4 py-3 bg-sky-100 text-sky-700 text-sm font-medium rounded-xl hover:bg-sky-200 transition-colors">
                                    <i class="bi bi-eye me-2"></i> Lihat Detail
                                </a>
                                <a href="{{ route('customers.index') }}" class="w-full flex items-center justify-center px-4 py-3 bg-zinc-100 text-zinc-700 text-sm font-medium rounded-xl hover:bg-zinc-200 transition-colors">
                                    <i class="bi bi-x-lg me-2"></i> Batal
                                </a>
                            </div>

                            {{-- Last Updated Info --}}
                            <div class="mt-4 p-4 bg-zinc-50 rounded-xl border border-zinc-100">
                                <h6 class="text-xs font-bold text-zinc-600 mb-1 flex items-center">
                                    <i class="bi bi-clock me-1.5 text-zinc-500"></i> Terakhir Diupdate
                                </h6>
                                <p class="text-xs text-zinc-500">
                                    {{ $customer->updated_at->diffForHumans() }}
                                    <br>
                                    <span class="text-[10px] text-zinc-400">({{ $customer->updated_at->format('d M Y, H:i') }})</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            // Track if form has changes
            let formChanged = false;
            const originalFormData = $('#customer-form').serialize();

            $('#customer-form input, #customer-form select, #customer-form textarea').on('change input', function() {
                formChanged = ($('#customer-form').serialize() !== originalFormData);
            });

            // Warn user before leaving if form changed
            $(window).on('beforeunload', function() {
                if (formChanged) {
                    return 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
                }
            });

            // Auto-format phone number
            $('#customer_phone').on('blur', function() {
                let phone = $(this).val().trim();
                const regex = /^[0-9+\-\s\(\)]+$/;

                if (phone && !regex.test(phone)) {
                    $(this).addClass('border-red-500 focus:border-red-500 focus:ring-red-500');
                    if (!$(this).next('.text-red-500').length) {
                        $(this).after('<p class="mt-1 text-xs text-red-500 flex items-center"><i class="bi bi-exclamation-circle me-1"></i> Format nomor telepon tidak valid</p>');
                    }
                } else {
                    $(this).removeClass('border-red-500 focus:border-red-500 focus:ring-red-500');
                    $(this).next('.text-red-500').remove();
                }
            });

            // Auto-capitalize city name
            $('#city').on('blur', function() {
                let city = $(this).val().trim();
                if (city) {
                    city = city.toLowerCase().replace(/\b\w/g, function(char) {
                        return char.toUpperCase();
                    });
                    $(this).val(city);
                }
            });

            // Form validation before submit
            $('#customer-form').on('submit', function(e) {
                let valid = true;

                // Check address length
                const address = $('#address').val().trim();
                if (address.length < 10) {
                    e.preventDefault();
                    $('#address').addClass('border-red-500 focus:border-red-500 focus:ring-red-500');
                    if (!$('#address').next('.text-red-500').length) {
                        $('#address').after('<p class="mt-1 text-xs text-red-500 flex items-center"><i class="bi bi-exclamation-circle me-1"></i> Alamat minimal 10 karakter untuk memastikan kelengkapan</p>');
                    }
                    valid = false;
                }

                if (!valid) {
                    // Scroll to first error
                    $('html, body').animate({
                        scrollTop: $('.border-red-500').first().offset().top - 100
                    }, 500);

                    return false;
                }

                // Disable warning when submitting
                formChanged = false;

                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true);
                submitBtn.html('<span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white me-2"></span>Memperbarui...');

                return true;
            });

            // Disable warning when clicking cancel/view detail
            $('a').on('click', function() {
                // If the link is not # or blank, we might want to disable the warning check if it's an intentional navigation
                // taking advantage of the fact that buttons also exist
                if ($(this).attr('href') && $(this).attr('href').startsWith('/')) {
                     // Optionally disable check? No, standard behavior is to warn.
                     // But if clicking "Batal", we explicitly want to ignore changes.
                     if($(this).text().includes('Batal') || $(this).text().includes('Lihat Detail')) {
                         formChanged = false;
                     }
                }
            });
        });
    </script>
@endpush
