@extends('layouts.app-flowbite')

@section('title', 'Peran & Hak Akses')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Manajemen Pengguna', 'url' => route('users.index')],
        ['text' => 'Peran & Hak Akses', 'url' => '#']
    ]])
@endsection

@section('content')
    {{-- Warning Alert --}}
    <div class="mb-4">
        <div class="flex items-center p-4 text-sm text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300 dark:border-yellow-800" role="alert">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0 inline w-4 h-4 me-3"></i>
            <div class="flex-1">
                <span class="font-medium">Perhatian:</span> Peran mengontrol akses pengguna ke fitur sistem. Pastikan memberikan permissions yang tepat untuk menjaga keamanan data.
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <x-flowbite-table 
        title="Peran & Hak Akses" 
        description="Kelola peran dan izin akses pengguna sistem"
        icon="bi-shield-lock"
        :items="$roles"
        :addRoute="route('roles.create')"
        addLabel="Tambah Peran"
        searchPlaceholder="Cari peran..."
    >
        {{-- Table Header --}}
        <x-slot name="thead">
            <tr>
                <th scope="col" class="px-6 py-4">No</th>
                <th scope="col" class="px-6 py-4">Nama Peran</th>
                <th scope="col" class="px-6 py-4">Jumlah Pengguna</th>
                <th scope="col" class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </x-slot>

        {{-- Table Body --}}
        @forelse($roles as $index => $role)
        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                {{ $roles->firstItem() + $index }}
            </td>
            <td class="px-6 py-4">
                <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-lg dark:bg-blue-900 dark:text-blue-300">
                    {{ $role->name }}
                </span>
            </td>
            <td class="px-6 py-4">
                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                    {{ $role->users_count ?? 0 }} pengguna
                </span>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-2">
                    {{-- Edit Button --}}
                    <a href="{{ route('roles.edit', $role) }}" 
                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                       title="Edit">
                        <i class="bi bi-pencil-square text-lg"></i>
                    </a>
                    
                    {{-- Delete Button --}}
                    @if($role->name !== 'Super Admin')
                    <button type="button" 
                            class="btn-delete text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                            data-id="{{ $role->id }}"
                            data-name="{{ $role->name }}"
                            title="Hapus">
                        <i class="bi bi-trash text-lg"></i>
                    </button>
                    
                    <form id="delete-form-{{ $role->id }}" action="{{ route('roles.destroy', $role) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center py-8 text-gray-500 dark:text-gray-400">
                <div class="flex flex-col items-center justify-center">
                    <i class="bi bi-shield-x text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                    <p class="font-medium">Belum ada peran</p>
                    <p class="text-sm">Klik tombol "Tambah Peran" untuk membuat peran baru.</p>
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
        title: 'Hapus Peran?',
        html: `Peran <strong>"${name}"</strong> akan dihapus dari sistem.<br><br>` +
              `<span class="text-red-500 text-sm font-medium"><i class="bi bi-exclamation-triangle me-1"></i>` +
              `Pengguna dengan peran ini akan kehilangan akses mereka!</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#E02424',
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
