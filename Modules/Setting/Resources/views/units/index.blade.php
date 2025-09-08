@extends('layouts.app')

@section('title', 'Satuan')

@section('third_party_stylesheets')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.3/css/select.dataTables.min.css">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item active">Satuan</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                @include('utils.alerts')

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        @can('create_units')
                            <a href="{{ route('units.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Satuan
                            </a>
                        @endcan

                        <hr>

                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 text-center" id="data-table">
                                <thead>
                                <tr>
                                    <th class="align-middle">No.</th>
                                    <th class="align-middle">Nama</th>
                                    <th class="align-middle">Singkatan</th>
                                    <th class="align-middle">Operator</th>
                                    <th class="align-middle">Nilai Operasi</th>
                                    <th class="align-middle">Aksi</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($units as $unit)
                                    <tr>
                                        <td class="align-middle">{{ $loop->iteration }}</td>
                                        <td class="align-middle">{{ $unit->name }}</td>
                                        <td class="align-middle">{{ $unit->short_name }}</td>
                                        <td class="align-middle">{{ $unit->operator }}</td>
                                        <td class="align-middle">{{ $unit->operation_value }}</td>
                                        <td class="align-middle">
                                            @can('edit_units')
                                                <a href="{{ route('units.edit', $unit) }}" class="btn btn-primary btn-sm" title="Ubah">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endcan

                                            @can('delete_units')
                                                <button class="btn btn-danger btn-sm"
                                                        title="Hapus"
                                                        onclick="
                                                            event.preventDefault();
                                                            if (confirm('Hapus satuan &quot;{{ $unit->name }}&quot;? Tindakan tidak dapat dibatalkan.')) {
                                                                document.getElementById('destroy{{ $unit->id }}').submit();
                                                            }
                                                        ">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <form id="destroy{{ $unit->id }}" class="d-none"
                                                      action="{{ route('units.destroy', $unit) }}"
                                                      method="POST">
                                                    @csrf
                                                    @method('delete')
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.24/b-1.7.0/b-html5-1.7.0/b-print-1.7.0/datatables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
    <script>
        var table = $('#data-table').DataTable({
            dom: "<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4 justify-content-end'f>>tr<'row'<'col-md-5'i><'col-md-7 mt-2'p>>",
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            },
            buttons: [
                {extend: 'excel', text: '<i class="bi bi-file-earmark-excel-fill"></i> Excel'},
                {extend: 'csv',   text: '<i class="bi bi-file-earmark-excel-fill"></i> CSV'},
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer-fill"></i> Cetak',
                    title: "Satuan",
                    exportOptions: { columns: [0, 1, 2, 3, 4] },
                    customize: function (win) {
                        $(win.document.body).find('h1').css({'font-size':'15pt','text-align':'center','margin-bottom':'20px'});
                        $(win.document.body).css('margin', '35px 25px');
                    }
                }
            ],
            ordering: false
        });
    </script>
@endpush
