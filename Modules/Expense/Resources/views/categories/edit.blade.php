@extends('layouts.app')

@section('title', 'Edit Kategori Pengeluaran')

@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expense-categories.index') }}">Kategori Pengeluaran</a></li>
    <li class="breadcrumb-item active">Edit</li>
</ol>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>
                        Edit Kategori
                    </h5>
                </div>

                <form action="{{ route('expense-categories.update', $category->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('expense::categories.form')

                    <div class="card-footer bg-light d-flex justify-content-between">
                        <a href="{{ route('expense-categories.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save me-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
