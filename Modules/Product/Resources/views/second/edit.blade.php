@extends('layouts.app')

@section('title', 'Edit Produk Bekas')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products_second.index') }}">Produk Bekas</a></li>
        <li class="breadcrumb-item active">Edit: {{ $product->name }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Alerts --}}
            @include('utils.alerts')

            <form id="product-form" action="{{ route('products_second.update', $product->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Sticky Action Bar --}}
                <div class="action-bar second-shadow-sm mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 font-weight-bold">
                                <i class="cil-pencil mr-2 text-primary"></i>
                                Edit Produk Bekas: {{ $product->name }}
                            </h5>
                            <small class="text-muted">Perbarui informasi produk bekas</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('products_second.index') }}" class="btn btn-outline-secondary">
                                <i class="cil-arrow-left mr-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="cil-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Include Modern Form (edit mode: dengan $product) --}}
                @include('product::second.partials._form', ['product' => $product])
            </form>

            {{-- Shared scripts for create & edit (Dropzone, MaskMoney, Swal, dll) --}}
            @include('product::second.partials._scripts')
        </div>
    </div>
@endsection

{{-- Section third_party_scripts & page_scripts JS lama DIHAPUS,
    karena sudah dipindah ke partial _scripts --}}

@push('page_styles')
    <style>
        .animated.fadeIn {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .action-bar {
            background: white;
            padding: 1.25rem;
            border-radius: 10px;
        }

        .d-flex.gap-2>* {
            margin-left: 0.5rem;
        }

        .d-flex.gap-2>*:first-child {
            margin-left: 0;
        }
    </style>
@endpush
