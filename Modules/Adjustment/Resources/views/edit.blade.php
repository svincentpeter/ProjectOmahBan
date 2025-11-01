@extends('layouts.app')

@section('title', 'Edit Pengajuan Penyesuaian Stok')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('adjustments.index') }}">Penyesuaian Stok</a></li>
        <li class="breadcrumb-item active">Edit {{ $adjustment->reference }}</li>
    </ol>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            {{-- Alert jika bukan pending --}}
            @if($adjustment->status !== 'pending')
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill mr-2"></i>
                <strong>Perhatian!</strong> Pengajuan ini berstatus <strong>{{ ucfirst($adjustment->status) }}</strong> dan tidak dapat diubah.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square mr-2"></i>
                        Edit Pengajuan Penyesuaian Stok
                    </h4>
                    <p class="mb-0 mt-2"><small>Referensi: <strong>{{ $adjustment->reference }}</strong> | Status: <span class="badge badge-{{ $adjustment->status === 'pending' ? 'warning' : ($adjustment->status === 'approved' ? 'success' : 'danger') }}">{{ ucfirst($adjustment->status) }}</span></small></p>
                </div>
                <div class="card-body">
                    <form action="{{ route('adjustments.update', $adjustment->id) }}" method="POST" enctype="multipart/form-data" id="adjustment-form">
                        @csrf
                        @method('PUT')
                        @include('adjustment::partials._form', ['isEdit' => true, 'adjustment' => $adjustment])
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                @if($adjustment->status === 'pending')
                                <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                                    <i class="bi bi-save-fill mr-2"></i>Update Pengajuan
                                </button>
                                @else
                                <button type="button" class="btn btn-secondary btn-lg" disabled>
                                    <i class="bi bi-lock-fill mr-2"></i>Tidak Dapat Diubah
                                </button>
                                @endif
                                <a href="{{ route('adjustments.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="bi bi-arrow-left-circle mr-2"></i>Kembali
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
