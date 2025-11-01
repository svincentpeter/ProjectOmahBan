@extends('layouts.app')

@section('title', 'Approval Penyesuaian Stok')

@section('content')
    <div class="card border-0 shadow-sm rounded-lg">
        {{-- Header --}}
        <div class="card-body pb-2 border-bottom">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="page-title mb-1">
                        <i class="bi bi-clipboard-check mr-2 text-primary"></i>
                        Approval Penyesuaian Stok
                    </h4>
                    <small class="text-muted">Kelola approval adjustment stok produk</small>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge badge-soft-warning">
                        <i class="bi bi-hourglass-split mr-1"></i>{{ $pendingCount }} Pending
                    </span>
                    <span class="badge badge-soft-success">
                        <i class="bi bi-check-circle mr-1"></i>{{ $approvedCount }} Approved
                    </span>
                    <span class="badge badge-soft-danger">
                        <i class="bi bi-x-circle mr-1"></i>{{ $rejectedCount }} Rejected
                    </span>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body">
            @if ($pendingCount > 0)
                <div class="table-responsive">
                    <table id="approvalTable" class="table table-hover table-sm align-middle approval-table w-100">
                        <thead class="thead-soft">
                            <tr>
                                <th>#</th>
                                <th>Kode Ref</th>
                                <th>Dibuat Oleh</th>
                                <th>Alasan</th>
                                <th>Produk</th>
                                <th>Tanggal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-soft-primary d-flex align-items-start mb-0">
                    <i class="bi bi-check-circle-fill mr-2 mt-1"></i>
                    <div>
                        <strong>Semua adjustment sudah di-review!</strong>
                        <div class="small text-muted">Tidak ada adjustment yang menunggu approval.</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Detail --}}
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg rounded-lg">
                <div class="modal-header modal-header-soft">
                    <h5 class="modal-title" id="detailModalLabel">
                        <i class="bi bi-file-earmark-text mr-2 text-primary"></i>
                        Detail Penyesuaian Stok
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="detailContent"></div>
            </div>
        </div>
    </div>
@endsection

@push('page_css')
    <style>
        /* ===== Headings ===== */
        .page-title {
            font-weight: 600;
            color: #2d3748;
            font-size: 1.25rem
        }

        /* ===== Badges (soft variants) ===== */
        .badge {
            border-radius: .5rem;
            font-weight: 600;
            padding: .4rem .6rem
        }

        .badge-soft-warning {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            color: #b45309
        }

        .badge-soft-success {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #166534
        }

        .badge-soft-danger {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b
        }

        /* ===== Table ===== */
        .thead-soft th {
            background: #f9fafb !important;
            border-bottom: 2px solid #e5e7eb !important;
            color: #6b7280;
            text-transform: uppercase;
            font-size: .78rem;
            letter-spacing: .04em;
            font-weight: 600
        }

        .approval-table td {
            color: #4b5563;
            padding: .8rem
        }

        .approval-table tbody tr {
            transition: background-color .18s ease, box-shadow .18s ease;
            border-bottom: 1px solid #e5e7eb
        }

        .approval-table tbody tr:hover {
            background: #f9fafb;
            box-shadow: inset 0 0 0 1px rgba(90, 103, 216, .05)
        }

        /* ===== Buttons (soft) ===== */
        .btn-soft {
            border: 0;
            border-radius: .5rem;
            font-size: .8rem;
            padding: .4rem .7rem;
            transition: all .18s ease
        }

        .btn-soft:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, .08)
        }

        .btn-detail {
            color: #5a67d8;
            background: rgba(90, 103, 216, .12)
        }

        .btn-approve {
            color: #0f766e;
            background: rgba(16, 185, 129, .12)
        }

        .btn-reject {
            color: #b91c1c;
            background: rgba(239, 68, 68, .12)
        }

        /* ===== Alerts / Modal ===== */
        .alert-soft-primary {
            background: #eff6ff;
            border-left: 4px solid #3b82f6
        }

        .modal-header-soft {
            background: #f9fafb;
            border-bottom: 1px solid #e2e8f0
        }

        .rounded-lg {
            border-radius: .75rem
        }

        @media (max-width:768px) {
            .approval-table {
                font-size: .84rem
            }

            .badge {
                margin-top: .25rem
            }
        }
    </style>
@endpush

@push('page_scripts')
    <script>
        $(function() {
            const $table = $('#approvalTable');

            const dt = $table.DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                stateSave: true,
                ajax: "{{ route('adjustments.getPendingAdjustments') }}",
                order: [
                    [1, 'desc']
                ],
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'Semua']
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        width: '5%',
                        className: 'text-muted'
                    },
                    {
                        data: 'reference',
                        name: 'reference',
                        width: '14%'
                    },
                    {
                        data: 'requester_name',
                        name: 'requester_name',
                        width: '16%'
                    },
                    {
                        data: 'reason',
                        name: 'reason',
                        width: '22%'
                    },
                    {
                        data: 'product_count',
                        name: 'product_count',
                        width: '10%'
                    },
                    {
                        data: 'created_at_formatted',
                        name: 'created_at_formatted',
                        width: '14%'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        width: '19%',
                        className: 'text-center'
                    }
                ],
                language: {
                    processing: '<div class="py-3 text-center"><div class="spinner-border text-primary"></div></div>',
                    emptyTable: 'Tidak ada data',
                    lengthMenu: '_MENU_ per halaman',
                    info: 'Tampil _START_–_END_ dari _TOTAL_',
                    infoEmpty: 'Tidak ada data',
                    search: '',
                    searchPlaceholder: 'Cari…'
                },
                dom: "<'row align-items-center'<'col-md-6'l><'col-md-6 text-right'f>>" +
                    "t" +
                    "<'row align-items-center mt-2'<'col-md-6'i><'col-md-6 text-right'p>>",
            });

            // Detail
            $(document).on('click', '.btn-detail', function() {
                const id = $(this).data('id');
                $.get(`/adjustments/${id}`, function(html) {
                    $('#detailContent').html(html);
                    $('#detailModal').modal('show');
                }).fail(() => {
                    Swal.fire('Gagal', 'Tidak dapat memuat detail.', 'error');
                });
            });

            // Approve / Reject
            $(document).on('click', '.btn-approve-action', function() {
                const id = $(this).data('id');
                const action = $(this).data('action'); // approve | reject
                const approve = action === 'approve';

                Swal.fire({
                    title: approve ? 'Setujui Adjustment?' : 'Tolak Adjustment?',
                    html: '<textarea id="approvalNotes" class="form-control" rows="4" placeholder="Catatan (opsional)"></textarea>',
                    icon: approve ? 'question' : 'warning',
                    showCancelButton: true,
                    confirmButtonText: approve ? '✓ Setujui' : '✗ Tolak',
                    confirmButtonColor: approve ? '#10b981' : '#ef4444',
                    cancelButtonText: 'Batal',
                    didOpen: () => $('#approvalNotes').trigger('focus')
                }).then(res => {
                    if (!res.isConfirmed) return;
                    $.ajax({
                        url: `/adjustments/${id}/approve`,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            action: action,
                            approval_notes: $('#approvalNotes').val()
                        },
                        beforeSend: () => Swal.showLoading(),
                        success: (r) => {
                            Swal.close();
                            Swal.fire('Berhasil', r.message || 'Tersimpan.', 'success');
                            dt.ajax.reload(null, false);
                        },
                        error: (xhr) => {
                            Swal.close();
                            const msg = xhr?.responseJSON?.message ||
                                'Terjadi kesalahan.';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                });
            });
        });
    </script>
@endpush
