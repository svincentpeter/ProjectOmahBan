{{-- File: Modules/Adjustment/Resources/views/partials/actions.blade.php --}}

<div class="btn-group" role="group">
    {{-- Show Button - Selalu tampil untuk semua role yang punya permission --}}
    @can('show_adjustments')
        <a href="{{ route('adjustments.show', $adjustment->id) }}" class="btn btn-info btn-sm" data-toggle="tooltip"
            title="Lihat Detail">
            <i class="bi bi-eye"></i>
        </a>
    @endcan

    {{-- Edit Button - Hanya untuk status pending --}}
    @if ($adjustment->status === 'pending')
        @can('edit_adjustments')
            <a href="{{ route('adjustments.edit', $adjustment->id) }}" class="btn btn-warning btn-sm" data-toggle="tooltip"
                title="Edit Pengajuan">
                <i class="bi bi-pencil"></i>
            </a>
        @endcan
    @endif

    {{-- Approve Button - Khusus Owner untuk status pending --}}
    @if ($adjustment->status === 'pending')
        @can('approve_adjustments')
            <button type="button" class="btn btn-success btn-sm approve-adjustment" data-id="{{ $adjustment->id }}"
                data-reference="{{ $adjustment->reference }}" data-toggle="tooltip" title="Approve Pengajuan">
                <i class="bi bi-check-circle"></i>
            </button>

            {{-- Reject Button - Khusus Owner --}}
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

    {{-- Print PDF Button - Tersedia untuk semua status --}}
    <a href="{{ route('adjustments.pdf', $adjustment->id) }}" target="_blank" class="btn btn-secondary btn-sm"
        data-toggle="tooltip" title="Print PDF">
        <i class="bi bi-printer"></i>
    </a>

    {{-- Delete Button - Hanya untuk status pending --}}
    @if ($adjustment->status === 'pending')
        @can('delete_adjustments')
            <button type="button" class="btn btn-dark btn-sm delete-adjustment" data-id="{{ $adjustment->id }}"
                data-reference="{{ $adjustment->reference }}" data-toggle="tooltip" title="Hapus Pengajuan">
                <i class="bi bi-trash"></i>
            </button>

            {{-- Hidden Delete Form --}}
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
        $(document).ready(function() {
            // Approve Adjustment
            $('.approve-adjustment').click(function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const reference = $(this).data('reference');

                Swal.fire({
                    title: 'Approve Pengajuan?',
                    html: `Apakah Anda yakin ingin <strong class="text-success">menyetujui</strong> pengajuan <strong>${reference}</strong>?<br><small class="text-muted">Stok produk akan diupdate sesuai penyesuaian ini.</small>`,
                    icon: 'question',
                    input: 'textarea',
                    inputLabel: 'Catatan Approval (opsional)',
                    inputPlaceholder: 'Tambahkan catatan jika perlu...',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-check-circle"></i> Ya, Approve!',
                    cancelButtonText: '<i class="bi bi-x-circle"></i> Batal',
                    reverseButtons: true,
                    preConfirm: (notes) => {
                        return notes || ''; // Return notes or empty string
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Set form values
                        $(`#action-${id}`).val('approve');
                        $(`#notes-${id}`).val(result.value);

                        // Show loading
                        Swal.fire({
                            title: 'Memproses Approval...',
                            text: 'Mohon tunggu',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit form
                        document.getElementById(`approve-form-${id}`).submit();
                    }
                });
            });

            // Reject Adjustment
            $('.reject-adjustment').click(function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const reference = $(this).data('reference');

                Swal.fire({
                    title: 'Reject Pengajuan?',
                    html: `Apakah Anda yakin ingin <strong class="text-danger">menolak</strong> pengajuan <strong>${reference}</strong>?<br><small class="text-muted">Pengajuan akan dibatalkan dan stok tidak berubah.</small>`,
                    icon: 'warning',
                    input: 'textarea',
                    inputLabel: 'Alasan Penolakan (wajib)',
                    inputPlaceholder: 'Jelaskan alasan penolakan...',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Alasan penolakan harus diisi!'
                        }
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-x-circle"></i> Ya, Reject!',
                    cancelButtonText: '<i class="bi bi-arrow-left"></i> Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Set form values
                        $(`#action-${id}`).val('reject');
                        $(`#notes-${id}`).val(result.value);

                        // Show loading
                        Swal.fire({
                            title: 'Memproses Penolakan...',
                            text: 'Mohon tunggu',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit form
                        document.getElementById(`approve-form-${id}`).submit();
                    }
                });
            });

            // Delete Adjustment
            $('.delete-adjustment').click(function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const reference = $(this).data('reference');

                Swal.fire({
                    title: 'Hapus Pengajuan?',
                    html: `Apakah Anda yakin ingin menghapus pengajuan <strong>${reference}</strong>?<br><small class="text-danger">Perhatian: Aksi ini tidak dapat dibatalkan!</small><br><small class="text-muted">Stok produk akan dikembalikan ke kondisi sebelum pengajuan.</small>`,
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus!',
                    cancelButtonText: '<i class="bi bi-x-circle"></i> Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Menghapus Pengajuan...',
                            text: 'Mohon tunggu',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit form
                        document.getElementById(`delete-form-${id}`).submit();
                    }
                });
            });

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
