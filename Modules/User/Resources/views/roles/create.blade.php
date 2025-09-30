@extends('layouts.app')

@section('title', 'Buat Peran Baru')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Peran & Hak Akses</a></li>
        <li class="breadcrumb-item active">Buat</li>
    </ol>
@endsection

@push('page_css')
    <style>
        .custom-control-label {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @include('utils.alerts')
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Buat Peran <i class="bi bi-check"></i></button>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Nama Peran <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="name" required>
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
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">Dashboard</div>
                                        <div class="card-body">
                                            @include('user::roles.partials.permissions-list', ['group' => 'dashboard'])
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">Manajemen Pengguna</div>
                                        <div class="card-body">
                                            @include('user::roles.partials.permissions-list', ['group' => 'user_management'])
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">Produk</div>
                                        <div class="card-body">
                                            @include('user::roles.partials.permissions-list', ['group' => 'products'])
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">Penyesuaian Stok</div>
                                        <div class="card-body">
                                            @include('user::roles.partials.permissions-list', ['group' => 'adjustments'])
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">Penawaran Harga</div>
                                        <div class="card-body">
                                            @include('user::roles.partials.permissions-list', ['group' => 'quotations'])
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">Pengeluaran</div>
                                        <div class="card-body">
                                            @include('user::roles.partials.permissions-list', ['group' => 'expenses'])
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">Pelanggan</div>
                                        <div class="card-body">
                                            @include('user::roles.partials.permissions-list', ['group' => 'customers'])
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">Pemasok</div>
                                        <div class="card-body">
                                            @include('user::roles.partials.permissions-list', ['group' => 'suppliers'])
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">Penjualan</div>
                                        <div class="card-body">
                                            @include('user::roles.partials.permissions-list', ['group' => 'sales'])
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">Retur Penjualan</div>
                                        <div class="card-body">
                                            @include('user::roles.partials.permissions-list', ['group' => 'sale_returns'])
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">Pembelian</div>
                                        <div class="card-body">
                                            @include('user::roles.partials.permissions-list', ['group' => 'purchases'])
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">Retur Pembelian</div>
                                        <div class="card-body">
                                            @include('user::roles.partials.permissions-list', ['group' => 'purchase_returns'])
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">Laporan</div>
                                        <div class="card-body">
                                            @include('user::roles.partials.permissions-list', ['group' => 'reports'])
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">Pengaturan</div>
                                        <div class="card-body">
                                            @include('user::roles.partials.permissions-list', ['group' => 'settings'])
                                        </div>
                                    </div>
                                </div>
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
            $('#select-all').click(function() {
                var checked = this.checked;
                $('input[type="checkbox"]').each(function() {
                    this.checked = checked;
                });
            })
        });
    </script>
@endpush