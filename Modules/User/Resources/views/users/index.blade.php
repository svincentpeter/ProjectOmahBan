@extends('layouts.app-flowbite')

@section('title', 'Manajemen Pengguna')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Manajemen Pengguna', 'url' => route('users.index')],
        ['text' => 'Index', 'url' => '#']
    ]])
@endsection


@section('content')

@php
    // Statistik ringan (aman untuk index)
    $totalUsers    = (int) \App\Models\User::count();
    $activeUsers   = (int) \App\Models\User::where('is_active', 1)->count();
    $inactiveUsers = (int) \App\Models\User::where('is_active', 0)->count();
    $totalRoles    = (int) \Spatie\Permission\Models\Role::count();

    // URLs (ikut filter yang sudah Anda pakai: role, status, search)
    $urlAll      = route('users.index');
    $urlActive   = route('users.index', array_filter(['status' => 1, 'role' => request('role'), 'search' => request('search')]));
    $urlInactive = route('users.index', array_filter(['status' => 'inactive', 'role' => request('role'), 'search' => request('search')]));
    $urlRole     = route('users.index', array_filter(['role' => request('role'), 'search' => request('search')]));
@endphp

{{-- Stats Cards (Line Color) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">

    {{-- Total Pengguna --}}
    <a href="{{ $urlAll }}"
       class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition
              border-l-4 border-l-blue-600 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="mb-1 text-sm font-semibold text-slate-600 dark:text-gray-300">Total Pengguna</p>
                <p class="text-2xl md:text-[26px] font-extrabold text-slate-900 dark:text-white leading-tight tabular-nums tracking-tight">
                    {{ number_format($totalUsers, 0, ',', '.') }}
                </p>
                <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">Semua akun terdaftar</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-blue-50 text-blue-700 ring-1 ring-blue-100
                        dark:bg-blue-900/30 dark:text-blue-300 dark:ring-blue-900/50">
                <i class="bi bi-people text-xl"></i>
            </div>
        </div>
    </a>

    {{-- Aktif --}}
    <a href="{{ $urlActive }}"
       class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition
              border-l-4 border-l-emerald-600 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="mb-1 text-sm font-semibold text-slate-600 dark:text-gray-300">Aktif</p>
                <p class="text-2xl md:text-[26px] font-extrabold text-slate-900 dark:text-white leading-tight tabular-nums tracking-tight">
                    {{ number_format($activeUsers, 0, ',', '.') }}
                </p>
                <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">Bisa login & akses</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100
                        dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-900/50">
                <i class="bi bi-check-circle text-xl"></i>
            </div>
        </div>
    </a>

    {{-- Nonaktif --}}
    <a href="{{ $urlInactive }}"
       class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition
              border-l-4 border-l-rose-600 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="mb-1 text-sm font-semibold text-slate-600 dark:text-gray-300">Nonaktif</p>
                <p class="text-2xl md:text-[26px] font-extrabold text-slate-900 dark:text-white leading-tight tabular-nums tracking-tight">
                    {{ number_format($inactiveUsers, 0, ',', '.') }}
                </p>
                <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">Akun dibatasi</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-rose-50 text-rose-700 ring-1 ring-rose-100
                        dark:bg-rose-900/30 dark:text-rose-300 dark:ring-rose-900/50">
                <i class="bi bi-x-circle text-xl"></i>
            </div>
        </div>
    </a>

    {{-- Total Role --}}
    <a href="{{ $urlRole }}"
       class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition
              border-l-4 border-l-amber-500 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="mb-1 text-sm font-semibold text-slate-600 dark:text-gray-300">Total Role</p>
                <p class="text-2xl md:text-[26px] font-extrabold text-slate-900 dark:text-white leading-tight tabular-nums tracking-tight">
                    {{ number_format($totalRoles, 0, ',', '.') }}
                </p>
                <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">Role di sistem</p>
            </div>
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                        bg-amber-50 text-amber-700 ring-1 ring-amber-100
                        dark:bg-amber-900/30 dark:text-amber-300 dark:ring-amber-900/50">
                <i class="bi bi-shield-lock text-xl"></i>
            </div>
        </div>
    </a>

</div>

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
