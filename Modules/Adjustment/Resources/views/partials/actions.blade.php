{{-- File: Modules/Adjustment/Resources/views/partials/actions.blade.php --}}

<div class="btn-group" role="group">
    {{-- Show --}}
    @can('show_adjustments')
        <a href="{{ route('adjustments.show', $adjustment->id) }}" class="btn btn-info btn-sm" data-toggle="tooltip"
            title="Lihat Detail">
            <i class="bi bi-eye"></i>
        </a>
    @endcan

    {{-- Edit (hanya pending) --}}
    @if ($adjustment->status === 'pending')
        @can('edit_adjustments')
            <a href="{{ route('adjustments.edit', $adjustment->id) }}" class="btn btn-warning btn-sm" data-toggle="tooltip"
                title="Edit Pengajuan">
                <i class="bi bi-pencil"></i>
            </a>
        @endcan
    @endif

    {{-- Approve / Reject (hanya pending + role berwenang) --}}
    @if ($adjustment->status === 'pending')
        @can('approve_adjustments')
            <button type="button" class="btn btn-success btn-sm approve-adjustment" data-id="{{ $adjustment->id }}"
                data-reference="{{ $adjustment->reference }}" data-toggle="tooltip" title="Approve Pengajuan">
                <i class="bi bi-check-circle"></i>
            </button>

            <button type="button" class="btn btn-danger btn-sm reject-adjustment" data-id="{{ $adjustment->id }}"
                data-reference="{{ $adjustment->reference }}" data-toggle="tooltip" title="Reject Pengajuan">
                <i class="bi bi-x-circle"></i>
            </button>

            {{-- Hidden Approve/Reject Form --}}
            <form id="approve-form-{{ $adjustment->id }}" action="{{ route('adjustments.approve', $adjustment->id) }}"
                method="POST" class="d-none">
                @csrf
                <input type="hidden" name="action" id="action-{{ $adjustment->id }}" value="">
                <textarea name="notes" id="notes-{{ $adjustment->id }}" class="d-none"></textarea>
            </form>
        @endcan
    @endif

    {{-- Print PDF --}}
    <a href="{{ route('adjustments.pdf', $adjustment->id) }}" target="_blank" class="btn btn-secondary btn-sm"
        data-toggle="tooltip" title="Print PDF">
        <i class="bi bi-printer"></i>
    </a>

    {{-- Delete (hanya pending) --}}
    @if ($adjustment->status === 'pending')
        @can('delete_adjustments')
            <button type="button" class="btn btn-dark btn-sm delete-adjustment" data-id="{{ $adjustment->id }}"
                data-reference="{{ $adjustment->reference }}" data-toggle="tooltip" title="Hapus Pengajuan">
                <i class="bi bi-trash"></i>
            </button>

            <form id="delete-form-{{ $adjustment->id }}" action="{{ route('adjustments.destroy', $adjustment->id) }}"
                method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endcan
    @endif
</div>

@push('page_scripts')
    <script>
        $(function() {
            // Approve
            $('.approve-adjustment').on('click', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const reference = $(this).data('reference');

                Swal.fire({
                    title: 'Approve Pengajuan?',
                    html: `Apakah Anda yakin ingin <strong class="text-success">menyetujui</strong> pengajuan <strong>${reference}</strong>?<br><small class="text-muted">Stok produk akan diupdate sesuai penyesuaian ini.</small>`,
                    icon: 'question',
                    input: 'textarea',
                    inputLabel: 'Catatan Approval (opsional)',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-check-circle"></i> Ya, Approve!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((r) => {
                    if (r.isConfirmed) {
                        $(`#action-${id}`).val('approve');
                        $(`#notes-${id}`).val(r.value || '');
                        Swal.fire({
                            title: 'Memproses...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                        document.getElementById(`approve-form-${id}`).submit();
                    }
                });
            });

            // Reject
            $('.reject-adjustment').on('click', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const reference = $(this).data('reference');

                Swal.fire({
                    title: 'Reject Pengajuan?',
                    html: `Apakah Anda yakin ingin <strong class="text-danger">menolak</strong> pengajuan <strong>${reference}</strong>?`,
                    icon: 'warning',
                    input: 'textarea',
                    inputLabel: 'Alasan Penolakan (wajib)',
                    inputValidator: (v) => !v && 'Alasan penolakan harus diisi!',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Reject!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((r) => {
                    if (r.isConfirmed) {
                        $(`#action-${id}`).val('reject');
                        $(`#notes-${id}`).val(r.value);
                        Swal.fire({
                            title: 'Memproses...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                        document.getElementById(`approve-form-${id}`).submit();
                    }
                });
            });

            // Delete
            $('.delete-adjustment').on('click', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const reference = $(this).data('reference');

                Swal.fire({
                    title: 'Hapus Pengajuan?',
                    html: `Yakin menghapus <strong>${reference}</strong>?<br><small class="text-danger">Aksi tidak dapat dibatalkan.</small>`,
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((r) => {
                    if (r.isConfirmed) {
                        Swal.fire({
                            title: 'Menghapus...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                        document.getElementById(`delete-form-${id}`).submit();
                    }
                });
            });

            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
