@extends('layouts.app-flowbite')

@section('title', 'Satuan Produk')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Satuan Produk', 'url' => '#']
    ]])
@endsection

@section('content')
    {{-- Info Alert --}}
    <div class="mb-4">
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-6 py-4 rounded-xl flex items-start gap-3 dark:bg-blue-900/30 dark:border-blue-800 dark:text-blue-300">
            <i class="bi bi-info-circle text-blue-600 dark:text-blue-400 text-lg mt-0.5"></i>
            <div>
                <p class="text-sm font-medium">
                    <strong>Informasi:</strong> Satuan digunakan untuk mengatur unit pengukuran produk. 
                    Operator dan nilai operasi digunakan untuk konversi antar satuan.
                </p>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <x-flowbite-table 
        title="Daftar Satuan" 
        description="Kelola satuan dan konversi unit produk"
        icon="bi-calculator"
        :items="$units"
        :addRoute="route('units.create')"
        addLabel="Tambah Satuan"
        searchPlaceholder="Cari satuan..."
    >
        {{-- Table Header --}}
        <x-slot name="thead">
            <tr>
                <th scope="col" class="px-6 py-4">No</th>
                <th scope="col" class="px-6 py-4">Nama Satuan</th>
                <th scope="col" class="px-6 py-4">Kode</th>
                <th scope="col" class="px-6 py-4">Operator</th>
                <th scope="col" class="px-6 py-4">Nilai Operasi</th>
                <th scope="col" class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </x-slot>

        {{-- Table Body --}}
        @forelse($units as $index => $unit)
        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                {{ $units->firstItem() + $index }}
            </td>
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                {{ $unit->name }}
            </td>
            <td class="px-6 py-4">
                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                    {{ $unit->short_name }}
                </span>
            </td>
            <td class="px-6 py-4">
                @if($unit->operator)
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                        {{ $unit->operator == '*' ? 'Kali (×)' : 'Bagi (÷)' }}
                    </span>
                @else
                    <span class="text-gray-400">-</span>
                @endif
            </td>
            <td class="px-6 py-4">
                {{ $unit->operation_value ?? '-' }}
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-2">
                    {{-- Edit Button --}}
                    <a href="{{ route('units.edit', $unit) }}" 
                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                       title="Edit">
                        <i class="bi bi-pencil-square text-lg"></i>
                    </a>
                    
                    {{-- Delete Button --}}
                    <button type="button" 
                            class="btn-delete text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                            data-id="{{ $unit->id }}"
                            data-name="{{ $unit->name }}"
                            title="Hapus">
                        <i class="bi bi-trash text-lg"></i>
                    </button>
                    
                    {{-- Hidden Delete Form --}}
                    <form id="delete-form-{{ $unit->id }}" action="{{ route('units.destroy', $unit) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center py-8 text-gray-500 dark:text-gray-400">
                <div class="flex flex-col items-center justify-center">
                    <i class="bi bi-calculator text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                    <p class="font-medium">Belum ada satuan</p>
                    <p class="text-sm">Klik tombol "Tambah Satuan" untuk membuat satuan baru.</p>
                </div>
            </td>
        </tr>
        @endforelse
    </x-flowbite-table>
@endsection

@push('page_scripts')
<script>
$(document).ready(function() {
    // Delete confirmation
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        Swal.fire({
            title: 'Hapus Satuan?',
            html: `Satuan <strong>"${name}"</strong> akan dihapus permanen.`,
            icon: 'warning',
            iconColor: '#ef4444',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Hapus!',
            cancelButtonText: '<i class="bi bi-x me-1"></i> Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                $('#delete-form-' + id).submit();
            }
        });
    });
});
</script>
@endpush
