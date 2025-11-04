@extends('layouts.app')

@section('title', 'Master Data Jasa')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Jasa</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Cards Statistik --}}
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-purple">
                        <div class="stats-icon"><i class="cil-settings"></i></div>
                        <div class="stats-content">
                            <div class="stats-label">Total Jasa</div>
                            <div class="stats-value">{{ \Modules\Product\Entities\ServiceMaster::count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-success">
                        <div class="stats-icon"><i class="cil-check-circle"></i></div>
                        <div class="stats-content">
                            <div class="stats-label">Aktif</div>
                            <div class="stats-value">
                                {{ \Modules\Product\Entities\ServiceMaster::where('status', 1)->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card stats-card-warning">
                        <div class="stats-icon"><i class="cil-x-circle"></i></div>
                        <div class="stats-content">
                            <div class="stats-label">Nonaktif</div>
                            <div class="stats-value">
                                {{ \Modules\Product\Entities\ServiceMaster::where('status', 0)->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Card --}}
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-2 mb-md-0">
                            <h5 class="mb-1 font-weight-bold">
                                <i class="cil-list mr-2 text-primary"></i>
                                Daftar Jasa
                            </h5>
                            <small class="text-muted">Kelola master jasa & harga standar</small>
                        </div>

                        <div class="btn-group" role="group">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#addServiceModal">
                                <i class="cil-plus mr-2"></i> Tambah Jasa
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Quick Filters --}}
                <div class="card-body py-4 border-bottom"
                    style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
                    <div class="filter-container">
                        <div class="d-flex align-items-center mb-3">
                            <i class="cil-bolt text-primary mr-2" style="font-size: 1.25rem;"></i>
                            <h6 class="mb-0 font-weight-bold text-dark">Filter Cepat</h6>
                        </div>
                        <div class="quick-filters mb-3">
                            <button type="button" class="filter-pill active" data-filter="all">
                                <i class="cil-apps"></i>
                                <span>Semua Jasa</span>
                            </button>
                            <button type="button" class="filter-pill" data-filter="active">
                                <i class="cil-check-circle"></i>
                                <span>Aktif</span>
                            </button>
                            <button type="button" class="filter-pill" data-filter="inactive">
                                <i class="cil-x-circle"></i>
                                <span>Nonaktif</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- DataTable --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <div class="datatable-wrapper">
                            {!! $dataTable->table(['class' => 'table table-hover mb-0', 'id' => 'service-masters-table']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- PARTIALS --}}
@include('product::service-masters.partials._modal-add')
@include('product::service-masters.partials._modal-edit')
@include('product::service-masters.partials._modal-delete')

@push('page_styles')
    <style>
        .animated.fadeIn {
            animation: fadeIn .3s ease-in
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08) !important
        }

        .stats-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            height: 100%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            transition: .3s;
            border-left: 4px solid
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, .12)
        }

        .stats-card-purple {
            border-left-color: #4834DF
        }

        .stats-card-success {
            border-left-color: #2eb85c
        }

        .stats-card-warning {
            border-left-color: #f9b115
        }

        .stats-card-info {
            border-left-color: #39f
        }

        .stats-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            flex-shrink: 0;
            color: #fff
        }

        .stats-card-purple .stats-icon {
            background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%)
        }

        .stats-card-success .stats-icon {
            background: linear-gradient(135deg, #2eb85c 0%, #51d88a 100%)
        }

        .stats-card-warning .stats-icon {
            background: linear-gradient(135deg, #f9b115 0%, #ffc451 100%)
        }

        .stats-card-info .stats-icon {
            background: linear-gradient(135deg, #39f 0%, #5dadec 100%)
        }

        .stats-content {
            flex: 1
        }

        .stats-label {
            font-size: .75rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #6c757d;
            letter-spacing: .5px;
            margin-bottom: 4px
        }

        .stats-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2d3748;
            line-height: 1
        }

        .quick-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px
        }

        .filter-pill {
            display: inline-flex;
            align-items: center;
            padding: 12px 24px;
            background: #fff;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            color: #4f5d73;
            font-size: .875rem;
            font-weight: 500;
            transition: .3s;
            cursor: pointer;
            white-space: nowrap
        }

        .filter-pill i {
            margin-right: 8px;
            font-size: 1rem
        }

        .filter-pill:hover {
            border-color: #4834DF;
            color: #4834DF;
            background: #f8f7ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(72, 52, 223, .15)
        }

        .filter-pill.active {
            background: linear-gradient(135deg, #4834DF 0%, #686DE0 100%);
            border-color: #4834DF;
            color: #fff;
            box-shadow: 0 4px 15px rgba(72, 52, 223, .3)
        }

        .datatable-wrapper {
            padding: 1rem
        }

        #service-masters-table thead th {
            font-size: .8125rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            font-weight: 600;
            color: #4f5d73;
            padding: 14px 12px;
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #e9ecef
        }

        #service-masters-table tbody td {
            padding: 14px 12px;
            vertical-align: middle;
            font-size: .875rem
        }

        #service-masters-table tbody tr {
            transition: .2s
        }

        #service-masters-table tbody tr:hover {
            background-color: rgba(72, 52, 223, .03) !important
        }

        @media (max-width:992px) {
            .quick-filters {
                flex-direction: column
            }

            .filter-pill {
                width: 100%;
                justify-content: center
            }
        }

        @media (max-width:768px) {
            .stats-card {
                flex-direction: column;
                text-align: center
            }

            .datatable-wrapper {
                padding: .5rem
            }
        }

        /* aksi icons */
        .action-buttons .btn-icon {
            width: 34px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .06)
        }

        .action-buttons .btn-icon i {
            font-size: .95rem;
            line-height: 1
        }

        .action-buttons>* {
            margin-right: .375rem
        }

        .action-buttons>*:last-child {
            margin-right: 0
        }

        #service-masters-table th:last-child,
        #service-masters-table td:last-child {
            white-space: nowrap;
            width: 1%;
            text-align: center
        }
    </style>
@endpush

@push('page_scripts')
    {!! $dataTable->scripts() !!}
    <script>
        $(function() {
            // ================== Quick filter (assumes your DataTable AJAX reads window._svcQuick) ==================
            window._svcQuick = 'all';
            $('.filter-pill').on('click', function() {
                $('.filter-pill').removeClass('active');
                $(this).addClass('active');
                window._svcQuick = $(this).data('filter');
                if (window.LaravelDataTables && window.LaravelDataTables['service-masters-table']) {
                    window.LaravelDataTables['service-masters-table'].ajax.reload();
                }
            });

            // ================== Tooltips ==================
            function initTooltips() {
                $('[data-toggle="tooltip"]').tooltip({
                    container: 'body'
                });
                // tombol modal (pakai title saja supaya tidak bentrok dengan data-toggle="modal")
                $('.btn-edit[title], .btn-delete[title]').tooltip({
                    container: 'body',
                    placement: 'top'
                });
            }
            initTooltips();
            $('#service-masters-table').on('draw.dt', initTooltips);

            // ================== Delegated click: EDIT (populate + show) ==================
            $(document).on('click', '.btn-edit', function(e) {
                e.preventDefault();
                const btn = $(this);

                const id = btn.data('id');
                const name = btn.data('name') || '';
                const price = parseInt(btn.data('price')) || 0;
                const category = btn.data('category') || 'service';
                const description = btn.data('description') || '';

                // action URL
                const actionUrl = '{{ route('service-masters.update', ':id') }}'.replace(':id', id);
                $('#editServiceForm').attr('action', actionUrl);

                // isi field
                $('#editServiceName').val(name);
                $('#editCategory').val(category);
                $('#editDescription').val(description);

                // harga lama & set AutoNumeric (jika ada)
                const fIDR = (v) => new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(v);
                $('#oldPriceDisplay').text(fIDR(price));
                $('#editServiceForm').data('oldPrice', price);

                try {
                    const anEl = AutoNumeric.getAutoNumericElement('#editStandardPrice');
                    if (anEl) anEl.set(price);
                    else $('#editStandardPrice').val(price);
                } catch (_e) {
                    $('#editStandardPrice').val(price);
                }

                // reset alert perubahan harga
                $('#priceChangeAlert, #priceChangeReasonDiv').hide();
                $('#editPriceChangeReason').val('');

                // tampilkan modal
                $('#editServiceModal').modal('show');
            });

            // ================== ADD: Submit (AJAX) ==================
            $('#formAddService').on('submit', function(e) {
                e.preventDefault();

                // unformat AutoNumeric jika ada
                try {
                    const anAdd = AutoNumeric.getAutoNumericElement('#addStandardPrice');
                    if (anAdd) $('#addStandardPrice').val(anAdd.getNumericString());
                } catch (_) {}

                const formData = new FormData(this);
                const actionUrl = $(this).attr('action');

                $.ajax({
                    url: actionUrl,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'Accept': 'application/json'
                    },
                    success: function() {
                        $('#addServiceModal').modal('hide');
                        Swal.fire({
                                title: 'Sukses!',
                                text: 'Jasa berhasil ditambahkan',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            })
                            .then(() => {
                                if (window.LaravelDataTables && window.LaravelDataTables[
                                        'service-masters-table']) {
                                    window.LaravelDataTables['service-masters-table'].ajax
                                        .reload();
                                }
                                document.getElementById('formAddService').reset();
                            });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan';
                        if (xhr.responseJSON && xhr.responseJSON.message) errorMessage = xhr
                            .responseJSON.message;
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors || {};
                            Object.keys(errors).forEach(field => {
                                const input = $(`[name="${field}"]`);
                                input.addClass('is-invalid');
                                input.after(
                                    `<span class="invalid-feedback d-block">${errors[field][0]}</span>`
                                    );
                            });
                        }
                    }
                });
            });

            // ================== EDIT: Submit (AJAX) ==================
            $('#editServiceForm').on('submit', function(e) {
                e.preventDefault();

                // unformat AutoNumeric jika ada
                try {
                    const anEdit = AutoNumeric.getAutoNumericElement('#editStandardPrice');
                    if (anEdit) $('#editStandardPrice').val(anEdit.getNumericString());
                } catch (_) {}

                const formData = new FormData(this);
                const actionUrl = $(this).attr('action');

                $.ajax({
                    url: actionUrl,
                    method: 'POST', // pakai POST + _method=PUT dari form
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'Accept': 'application/json'
                    },
                    success: function() {
                        $('#editServiceModal').modal('hide');
                        Swal.fire({
                                title: 'Sukses!',
                                text: 'Jasa berhasil diperbarui',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            })
                            .then(() => {
                                if (window.LaravelDataTables && window.LaravelDataTables[
                                        'service-masters-table']) {
                                    window.LaravelDataTables['service-masters-table'].ajax
                                        .reload();
                                }
                            });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan';
                        if (xhr.responseJSON && xhr.responseJSON.message) errorMessage = xhr
                            .responseJSON.message;
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // ================== DELETE: Submit (AJAX) ==================
            $('#deleteServiceForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const actionUrl = $(this).attr('action');

                $.ajax({
                    url: actionUrl,
                    method: 'POST', // pakai POST + _method=DELETE dari form
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'Accept': 'application/json'
                    },
                    success: function() {
                        $('#deleteServiceModal').modal('hide');
                        Swal.fire({
                                title: 'Sukses!',
                                text: 'Jasa berhasil dihapus',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            })
                            .then(() => {
                                if (window.LaravelDataTables && window.LaravelDataTables[
                                        'service-masters-table']) {
                                    window.LaravelDataTables['service-masters-table'].ajax
                                        .reload();
                                }
                            });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan';
                        if (xhr.responseJSON && xhr.responseJSON.message) errorMessage = xhr
                            .responseJSON.message;
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>
@endpush
