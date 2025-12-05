@extends('layouts.app')

@section('title', 'Edit Stock Opname - ' . $stockOpname->reference)

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('stock-opnames.index') }}">Stock Opname</a></li>
        <li class="breadcrumb-item"><a href="{{ route('stock-opnames.show', $stockOpname->id) }}">{{ $stockOpname->reference }}</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- HEADER CARD --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square"></i> Edit Stock Opname
                    </h4>
                    <small class="d-block mt-1">{{ $stockOpname->reference }} - Status: {!! $stockOpname->status_badge !!}</small>
                </div>

                <div class="card-body">
                    {{-- WARNING ALERT --}}
                    <div class="alert alert-warning border-left-warning mb-4" role="alert">
                        <div class="d-flex">
                            <div class="mr-3">
                                <i class="bi bi-exclamation-triangle-fill" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">Perhatian!</h5>
                                <p class="mb-0">
                                    Opname ini berstatus <strong>DRAFT</strong>. 
                                    Anda masih bisa mengubah tanggal, scope produk, atau catatan. 
                                    Namun, <strong>jika Anda mengubah produk yang dipilih, semua item akan direset ulang</strong>.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- INCLUDE PARTIAL FORM --}}
                    @include('adjustment::stock-opname.partials._form', [
                        'isEdit' => true,
                        'stockOpname' => $stockOpname,
                        'categories' => $categories
                    ])
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-warning {
        border-left: 4px solid #f6c23e;
    }
</style>
@endsection
