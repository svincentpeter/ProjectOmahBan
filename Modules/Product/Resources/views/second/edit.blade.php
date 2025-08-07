@extends('layouts.app')

@section('title', 'Edit Produk Bekas')

{{-- Menambahkan CSS untuk Dropzone --}}
@section('third_party_stylesheets')
    <link rel="stylesheet" href="{{ asset('css/dropzone.css') }}">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products_second.index') }}">Produk Bekas</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form action="{{ route('products_second.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-12">
                    @include('utils.alerts')
                    <div class="form-group">
                        <button class="btn btn-primary">Update Produk <i class="bi bi-check"></i></button>
                    </div>
                </div>
                <div class="col-lg-12">
                    {{-- Memanggil file form partial --}}
                    @include('product::second._form', ['product' => $product])
                </div>
            </div>
        </form>
    </div>
@endsection

@push('page_scripts')
    {{-- Memuat JavaScript untuk Dropzone dan Mask Money --}}
    <script src="{{ asset('js/dropzone.js') }}"></script>
    <script src="{{ asset('js/jquery-mask-money.js') }}"></script>
    
    <script>
        // Inisialisasi Dropzone
        var uploadedDocumentMap = {}
        Dropzone.options.documentDropzone = {
            url: '{{ route('dropzone.upload') }}',
            maxFilesize: 1,
            acceptedFiles: '.jpg, .jpeg, .png',
            maxFiles: 3,
            addRemoveLinks: true,
            dictRemoveFile: "<i class='bi bi-x-circle text-danger'></i> hapus",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            success: function(file, response) {
                $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">');
                uploadedDocumentMap[file.name] = response.name;
            },
            removedfile: function(file) {
                file.previewElement.remove();
                var name = uploadedDocumentMap[file.name] || file.file_name;
                $('form').find('input[name="document[]"][value="' + name + '"]').remove();
            },
            init: function() {
                @if(isset($product) && $product->getMedia('images'))
                    var files = {!! json_encode($product->getMedia('images')) !!};
                    for (var i in files) {
                        var file = files[i];
                        this.options.addedfile.call(this, file);
                        this.options.thumbnail.call(this, file, file.original_url);
                        file.previewElement.classList.add('dz-complete');
                        $('form').append('<input type="hidden" name="document[]" value="' + file.file_name + '">');
                    }
                @endif
            }
        }

        // Inisialisasi Mask Money
        $(document).ready(function () {
            $('#purchase_price, #selling_price').maskMoney({
                prefix:'{{ settings()->currency->symbol }}',
                thousands:'{{ settings()->currency->thousand_separator }}',
                decimal:'{{ settings()->currency->decimal_separator }}',
            });
            $('#purchase_price').maskMoney('mask');
            $('#selling_price').maskMoney('mask');

            $('form').submit(function () {
                $('#purchase_price').val($('#purchase_price').maskMoney('unmasked')[0]);
                $('#selling_price').val($('#selling_price').maskMoney('unmasked')[0]);
            });
        });
    </script>
@endpush
