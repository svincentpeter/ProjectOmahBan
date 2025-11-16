{{-- Modules/Product/Resources/views/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
        <li class="breadcrumb-item active">Tambah Produk</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Alerts --}}
            @include('utils.alerts')

            <form id="product-form" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Sticky Action Bar --}}
                <div class="action-bar shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 font-weight-bold">
                                <i class="cil-plus mr-2 text-primary"></i>
                                Tambah Produk Baru
                            </h5>
                            <small class="text-muted">Lengkapi informasi produk di bawah ini</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                <i class="cil-x mr-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="cil-check-circle mr-1"></i> Simpan Produk
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Form utama (shared dengan Edit) --}}
                @include('product::products.partials._form')

            </form>
        </div>
    </div>
@endsection

{{-- Script bersama (Dropzone autoDiscover OFF + maskMoney + margin + validasi + Swal) --}}
@include('product::products.partials._scripts')

@push('page_styles')
    <style>
        /* ========== Animations ========== */
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

        /* ========== Sticky Action Bar ========== */
        .action-bar {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: white;
            padding: 1.25rem;
            border-radius: 10px;
            margin-bottom: 0;
        }

        /* ========== Card Shadow ========== */
        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }

        /* ========== Form Enhancements ========== */
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
            min-height: 80px;
        }

        /* ========== Sticky Sidebar ========== */
        .sticky-sidebar {
            position: sticky;
            top: 100px;
        }

        /* ========== Badge Sizing ========== */
        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }

        /* ========== Input Group Button ========== */
        .input-group-append .btn {
            height: 50px;
            border-color: #ced4da;
        }

        .input-group-append .btn:hover {
            background-color: #f8f9fa;
            border-color: #4834DF;
            color: #4834DF;
        }

        /* ========== Alert Styling ========== */
        .alert-info {
            background-color: #e7f6fc;
            border-color: #8ad4ee;
            color: #115293;
            border-radius: 8px;
        }

        /* ========== Button Gap ========== */
        .d-flex.gap-2>* {
            margin-left: 0.5rem;
        }

        .d-flex.gap-2>*:first-child {
            margin-left: 0;
        }

        /* ========== Responsive ========== */
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
