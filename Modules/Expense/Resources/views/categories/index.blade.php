@extends('layouts.app-flowbite')

@section('title', 'Kategori Pengeluaran')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Pengeluaran', 'url' => route('expenses.index')],
            ['text' => 'Kategori', 'url' => '#', 'icon' => 'bi bi-folder2-open'],
        ]
    ])
@endsection

@section('content')
    {{-- Stats Cards --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Total Kategori --}}
        <div class="relative bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-6 text-white shadow-lg overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-20 group-hover:opacity-30 transition-opacity">
                <i class="bi bi-layers-fill text-9xl"></i>
            </div>
            <div class="relative z-10 flex items-center">
                <div class="p-3 bg-white/20 rounded-xl mr-4 backdrop-blur-sm shadow-inner">
                    <i class="bi bi-tag-fill text-2xl"></i>
                </div>
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1 tracking-wide">Total Kategori</p>
                    <h3 class="text-3xl font-bold">{{ $categories_count }}</h3>
                </div>
            </div>
        </div>

        {{-- Status Sistem --}}
        <div class="bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-xl mr-4">
                    <i class="bi bi-check-circle-fill text-2xl"></i>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-gray-400 text-sm font-medium mb-1">Status Sistem</p>
                    <h3 class="text-xl font-bold text-slate-800 dark:text-white">Aktif</h3>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="px-4 py-1.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-bold uppercase tracking-wider rounded-lg border border-emerald-200 dark:border-emerald-800">
                    Online
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <x-flowbite-table 
        title="Daftar Kategori Pengeluaran" 
        description="Kelola kategori untuk pengelompokan pengeluaran operasional"
        icon="bi-folder2-open"
        :items="$categories"
        :addRoute="route('expense-categories.create')"
        addLabel="Tambah Kategori"
        searchPlaceholder="Cari kategori..."
    >
        {{-- Table Header --}}
        <x-slot name="thead">
            <tr>
                <th scope="col" class="px-6 py-4">No</th>
                <th scope="col" class="px-6 py-4">Nama Kategori</th>
                <th scope="col" class="px-6 py-4">Deskripsi</th>
                <th scope="col" class="px-6 py-4">Jumlah Pengeluaran</th>
                <th scope="col" class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </x-slot>

        {{-- Table Body --}}
        @forelse($categories as $index => $category)
        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                {{ $categories->firstItem() + $index }}
            </td>
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                {{ $category->category_name }}
            </td>
            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 max-w-xs truncate">
                {{ $category->category_description ?? '-' }}
            </td>
            <td class="px-6 py-4">
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                    {{ $category->expenses_count ?? 0 }} transaksi
                </span>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-2">
                    {{-- Edit Button --}}
                    <a href="{{ route('expense-categories.edit', $category) }}" 
                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                       title="Edit">
                        <i class="bi bi-pencil-square text-lg"></i>
                    </a>
                    
                    {{-- Delete Button --}}
                    <button type="button" 
                            class="btn-delete text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                            data-id="{{ $category->id }}"
                            data-name="{{ $category->category_name }}"
                            title="Hapus">
                        <i class="bi bi-trash text-lg"></i>
                    </button>
                    
                    {{-- Hidden Delete Form --}}
                    <form id="delete-form-{{ $category->id }}" action="{{ route('expense-categories.destroy', $category) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center py-8 text-gray-500 dark:text-gray-400">
                <div class="flex flex-col items-center justify-center">
                    <i class="bi bi-folder-x text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                    <p class="font-medium">Belum ada kategori pengeluaran</p>
                    <p class="text-sm">Klik tombol "Tambah Kategori" untuk membuat kategori baru.</p>
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
        title: 'Hapus Kategori?',
        text: 'Kategori "' + name + '" akan dihapus permanen.',
        icon: 'warning',
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
