@extends('layouts.app-flowbite')

@section('title', 'Manajemen Pengguna')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Manajemen Pengguna', 'url' => route('users.index')],
        ['text' => 'Index', 'url' => '#']
    ]])
@endsection

@section('content')
    {{-- Main Table --}}
    <x-flowbite-table 
        title="Data Pengguna" 
        description="Kelola data dan hak akses pengguna sistem"
        icon="bi-people"
        :items="$users"
        :addRoute="route('users.create')"
        addLabel="Tambah Pengguna"
        searchPlaceholder="Cari pengguna..."
    >
        {{-- Filter Slot --}}
        <x-slot name="filters">
            <form action="{{ route('users.index') }}" method="GET" class="flex items-center gap-2">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <select name="role" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Semua Role</option>
                    @foreach(\Spatie\Permission\Models\Role::pluck('name', 'name') as $role)
                        <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ $role }}</option>
                    @endforeach
                </select>
                <select name="status" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </form>
        </x-slot>

        {{-- Table Header --}}
        <x-slot name="thead">
            <tr>
                <th scope="col" class="px-6 py-4">No</th>
                <th scope="col" class="px-6 py-4">Nama</th>
                <th scope="col" class="px-6 py-4">Email</th>
                <th scope="col" class="px-6 py-4">Role</th>
                <th scope="col" class="px-6 py-4">Status</th>
                <th scope="col" class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </x-slot>

        {{-- Table Body --}}
        @forelse($users as $index => $user)
        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                {{ $users->firstItem() + $index }}
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    @if($user->getFirstMediaUrl('avatars'))
                        <img src="{{ $user->getFirstMediaUrl('avatars') }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover">
                    @else
                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                            <span class="text-blue-600 dark:text-blue-400 font-bold text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                    @endif
                    <span class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</span>
                </div>
            </td>
            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                {{ $user->email }}
            </td>
            <td class="px-6 py-4">
                @foreach($user->roles as $role)
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                        {{ $role->name }}
                    </span>
                @endforeach
            </td>
            <td class="px-6 py-4">
                @if($user->is_active)
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                        Aktif
                    </span>
                @else
                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                        Nonaktif
                    </span>
                @endif
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-2">
                    {{-- Edit Button --}}
                    <a href="{{ route('users.edit', $user) }}" 
                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                       title="Edit">
                        <i class="bi bi-pencil-square text-lg"></i>
                    </a>
                    
                    {{-- Delete Button --}}
                    @if(auth()->id() !== $user->id)
                    <button type="button" 
                            class="btn-delete text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                            data-id="{{ $user->id }}"
                            data-name="{{ $user->name }}"
                            title="Hapus">
                        <i class="bi bi-trash text-lg"></i>
                    </button>
                    
                    <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center py-8 text-gray-500 dark:text-gray-400">
                <div class="flex flex-col items-center justify-center">
                    <i class="bi bi-people text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                    <p class="font-medium">Belum ada pengguna</p>
                    <p class="text-sm">Klik tombol "Tambah Pengguna" untuk membuat pengguna baru.</p>
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
        title: 'Hapus Pengguna?',
        html: `Pengguna <strong>"${name}"</strong> akan dihapus permanen.<br><small class="text-red-500">Akun ini tidak dapat dipulihkan!</small>`,
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
