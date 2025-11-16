{{-- Modules/Product/Resources/views/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Produk')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
        <li class="breadcrumb-item active">Edit: {{ $product->product_name }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Alerts --}}
            @include('utils.alerts')

            <form id="product-form" action="{{ route('products.update', $product->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                {{-- Sticky Action Bar --}}
                <div class="action-bar shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 font-weight-bold">
                                <i class="cil-pencil mr-2 text-primary"></i>
                                Edit Produk: {{ $product->product_name }}
                            </h5>
                            <small class="text-muted">Perbarui informasi produk</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                <i class="cil-x mr-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="cil-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Form utama (dipakai juga di Create) --}}
                @include('product::products.partials._form')

            </form>
        </div>
    </div>
@endsection

{{-- Pakai partial script bersama (maskMoney + margin + Swal + Dropzone) --}}
@include('product::products.partials._scripts')

@push('page_styles')
    <style>
        /* ========== Same styles as Create page ========== */
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
            position: sticky;
            top: 0;
            z-index: 1020;
            background: white;
            padding: 1.25rem;
            border-radius: 10px;
            margin-bottom: 0;
        }

        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }

        .form-control-lg {
            height: 50px;
            font-size: 1rem;
        }

        .form-control:focus,
        select.form-control:focus,
        textarea.form-control:focus {
            border-color: #4834DF;
            box-shadow: 0 0 0 0.2rem rgba(72, 52, 223, 0.25);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 60px;
        }

        .sticky-sidebar {
            position: sticky;
            top: 100px;
        }

        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }

        .alert-info {
            background-color: #e7f6fc;
            border-color: #8ad4ee;
            color: #115293;
            border-radius: 8px;
        }

        .d-flex.gap-2>* {
            margin-left: 0.5rem;
        }

        .d-flex.gap-2>*:first-child {
            margin-left: 0;
        }

        @media (max-width: 992px) {
            .sticky-sidebar {
                position: relative;
                top: 0;
                margin-top: 1rem;
            }

            .action-bar {
                position: relative;
            }

            .action-bar .d-flex {
                flex-direction: column;
                gap: 1rem;
            }

            .action-bar .d-flex>div {
                width: 100%;
            }
        }
    </style>
@endpush
