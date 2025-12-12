@extends('layouts.app-flowbite')

@section('title', 'Mata Uang')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'title' => 'Mata Uang',
        'items' => [['text' => 'Home', 'url' => route('home')], ['text' => 'Mata Uang', 'url' => '#']],
    ])
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    {{-- Info Alert --}}
    <div class="mb-4">
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-6 py-4 rounded-xl flex items-start gap-3 dark:bg-blue-900/30 dark:border-blue-800 dark:text-blue-300">
            <i class="bi bi-info-circle text-blue-600 dark:text-blue-400 text-lg mt-0.5"></i>
            <div>
                <p class="text-sm font-medium">
                    <strong>Informasi:</strong> Mata uang digunakan untuk transaksi dengan nilai tukar berbeda. 
                    Anda dapat menambahkan, mengedit, atau menghapus mata uang sesuai kebutuhan bisnis.
                </p>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <x-flowbite-table 
        title="Daftar Mata Uang" 
        description="Kelola mata uang untuk transaksi multi-currency"
        icon="bi-currency-exchange"
        :items="$currencies"
        :addRoute="route('currencies.create')"
        addLabel="Tambah Mata Uang"
        searchPlaceholder="Cari mata uang..."
    >
        {{-- Table Header --}}
        <x-slot name="thead">
            <tr>
                <th scope="col" class="px-6 py-4">No</th>
                <th scope="col" class="px-6 py-4">Nama</th>
                <th scope="col" class="px-6 py-4">Kode</th>
                <th scope="col" class="px-6 py-4">Simbol</th>
                <th scope="col" class="px-6 py-4">Exchange Rate</th>
                <th scope="col" class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </x-slot>

        {{-- Table Body --}}
        @forelse($currencies as $index => $currency)
        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                {{ $currencies->firstItem() + $index }}
            </td>
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                {{ $currency->currency_name }}
            </td>
            <td class="px-6 py-4">
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                    {{ $currency->code }}
                </span>
            </td>
            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                {{ $currency->symbol }}
            </td>
            <td class="px-6 py-4">
                {{ $currency->exchange_rate ?? '-' }}
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-2">
                    {{-- Edit Button --}}
                    @can('edit_currencies')
                    <a href="{{ route('currencies.edit', $currency) }}" 
                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                       title="Edit">
                        <i class="bi bi-pencil-square text-lg"></i>
                    </a>
                    @endcan
                    
                    {{-- Delete Button --}}
                    @can('delete_currencies')
                    <button type="button" 
                            class="btn-delete text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                            data-id="{{ $currency->id }}"
                            data-name="{{ $currency->currency_name }}"
                            title="Hapus">
                        <i class="bi bi-trash text-lg"></i>
                    </button>
                    
                    <form id="delete-form-{{ $currency->id }}" action="{{ route('currencies.destroy', $currency) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center py-8 text-gray-500 dark:text-gray-400">
                <div class="flex flex-col items-center justify-center">
                    <i class="bi bi-currency-exchange text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                    <p class="font-medium">Belum ada mata uang</p>
                    <p class="text-sm">Klik tombol "Tambah Mata Uang" untuk membuat mata uang baru.</p>
                </div>
            </td>
        </tr>
        @endforelse
    </x-flowbite-table>
@endsection

@push('page_scripts')
<script>
$(document).on('click', '.btn-delete', function(e) {
    e.preventDefault();
    const id = $(this).data('id');
    const name = $(this).data('name');
    
    Swal.fire({
        title: 'Hapus Mata Uang?',
        html: `Mata uang <strong>"${name}"</strong> akan dihapus permanen.<br><small class="text-slate-500">Data yang dihapus tidak dapat dikembalikan!</small>`,
        icon: 'warning',
        iconColor: '#EF4444',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menghapus...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            $('#delete-form-' + id).submit();
        }
    });
});
</script>
@endpush
