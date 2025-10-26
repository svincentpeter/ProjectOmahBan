@extends('layouts.app')

@section('title', 'Tambah Kategori Pengeluaran')

@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expense-categories.index') }}">Kategori Pengeluaran</a></li>
    <li class="breadcrumb-item active">Tambah</li>
</ol>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        Tambah Kategori Baru
                    </h5>
                </div>

                <form action="{{ route('expense-categories.store') }}" method="POST">
                    @csrf
                    @include('expense::categories._form')

                    <div class="card-footer bg-light d-flex justify-content-between">
                        <a href="{{ route('expense-categories.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
