@extends('layouts.app')

@section('title', 'Edit Peran')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Peran & Hak Akses</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
@endsection

@push('page_css')
    <style>
        .custom-control-label { cursor: pointer; }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @include('utils.alerts')
                <form action="{{ route('roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('patch')
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            Perbarui Peran <i class="bi bi-check"></i>
                        </button>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Nama Peran <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="name" required value="{{ $role->name }}">
                            </div>

                            <hr>

                            <div class="form-group">
                                <label for="permissions">Hak Akses <span class="text-danger">*</span></label>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="select-all">
                                    <label class="custom-control-label" for="select-all">Berikan Semua Hak Akses</label>
                                </div>
                            </div>

                            <div class="row">
                                @php
                                    $groups = [
                                        'dashboard' => 'Dashboard',
                                        'user_management' => 'Manajemen Pengguna',
                                        'products' => 'Produk',
                                        'adjustments' => 'Penyesuaian Stok',
                                        'quotations' => 'Penawaran Harga',
                                        'expenses' => 'Pengeluaran',
                                        'customers' => 'Pelanggan',
                                        'suppliers' => 'Pemasok',
                                        'sales' => 'Penjualan',
                                        'sale_returns' => 'Retur Penjualan',
                                        'purchases' => 'Pembelian',
                                        'purchase_returns' => 'Retur Pembelian',
                                        'reports' => 'Laporan',
                                        'settings' => 'Pengaturan',
                                    ];
                                @endphp

                                @foreach ($groups as $key => $title)
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">{{ $title }}</div>
                                        <div class="card-body">
                                            @include('user::roles.partials.permissions-list', [
                                                'group' => $key,
                                                'rolePermissions' => $rolePermissions 
                                            ])
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            $('#select-all').on('click', function() {
                const checked = this.checked;
                $('input[type="checkbox"]').each(function() {
                    this.checked = checked;
                });
            });
        });
    </script>
@endpush