@extends('layouts.app')

@section('title', 'Semua Penjualan')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Semua Penjualan</li>
    </ol>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-striped table-hover w-100']) !!}
            </div>
        </div>
    </div>
</div>
@endsection

@once
@push('page_scripts')
    {!! $dataTable->scripts() !!}
@endpush
@endonce
