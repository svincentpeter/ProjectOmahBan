{{-- File: Modules/Adjustment/Resources/views/partials/actions.blade.php --}}

<div class="flex items-center justify-center gap-1">
    {{-- Show --}}
    @can('show_adjustments')
        <a href="{{ route('adjustments.show', $adjustment->id) }}" 
           class="p-2 text-zinc-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
           title="Lihat Detail">
            <i class="bi bi-eye text-sm"></i>
        </a>
    @endcan

    {{-- Edit (hanya pending) --}}
    @if ($adjustment->status === 'pending')
        @can('edit_adjustments')
            <a href="{{ route('adjustments.edit', $adjustment->id) }}" 
               class="p-2 text-zinc-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all"
               title="Edit Pengajuan">
                <i class="bi bi-pencil text-sm"></i>
            </a>
        @endcan
    @endif

    {{-- Approve / Reject (hanya pending + role berwenang) --}}
    @if ($adjustment->status === 'pending')
        @can('approve_adjustments')
            <button type="button" 
                    class="approve-adjustment p-2 text-zinc-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" 
                    data-id="{{ $adjustment->id }}"
                    data-reference="{{ $adjustment->reference }}" 
                    title="Approve Pengajuan">
                <i class="bi bi-check-circle text-sm"></i>
            </button>

            <button type="button" 
                    class="reject-adjustment p-2 text-zinc-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" 
                    data-id="{{ $adjustment->id }}"
                    data-reference="{{ $adjustment->reference }}" 
                    title="Reject Pengajuan">
                <i class="bi bi-x-circle text-sm"></i>
            </button>

            {{-- Hidden Approve/Reject Form --}}
            <form id="approve-form-{{ $adjustment->id }}" action="{{ route('adjustments.approve', $adjustment->id) }}"
                method="POST" class="hidden">
                @csrf
                <input type="hidden" name="action" id="action-{{ $adjustment->id }}" value="">
                <textarea name="notes" id="notes-{{ $adjustment->id }}" class="hidden"></textarea>
            </form>
        @endcan
    @endif

    {{-- Print PDF --}}
    <a href="{{ route('adjustments.pdf', $adjustment->id) }}" 
       target="_blank" 
       class="p-2 text-zinc-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-all"
       title="Print PDF">
        <i class="bi bi-printer text-sm"></i>
    </a>

    {{-- Delete (hanya pending) --}}
    @if ($adjustment->status === 'pending')
        @can('delete_adjustments')
            <button type="button" 
                    class="delete-adjustment p-2 text-zinc-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" 
                    data-id="{{ $adjustment->id }}"
                    data-reference="{{ $adjustment->reference }}" 
                    title="Hapus Pengajuan">
                <i class="bi bi-trash text-sm"></i>
            </button>

            <form id="delete-form-{{ $adjustment->id }}" action="{{ route('adjustments.destroy', $adjustment->id) }}"
                method="POST" class="hidden">
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
            $(document).off('click', '.approve-adjustment').on('click', '.approve-adjustment', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const reference = $(this).data('reference');

                Swal.fire({
                    title: 'Approve Pengajuan?',
                    html: `Apakah Anda yakin ingin <strong class="text-emerald-600">menyetujui</strong> pengajuan <strong>${reference}</strong>?<br><small class="text-zinc-500">Stok produk akan diupdate sesuai penyesuaian ini.</small>`,
                    icon: 'question',
                    input: 'textarea',
                    inputLabel: 'Catatan Approval (opsional)',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
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
            $(document).off('click', '.reject-adjustment').on('click', '.reject-adjustment', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const reference = $(this).data('reference');

                Swal.fire({
                    title: 'Reject Pengajuan?',
                    html: `Apakah Anda yakin ingin <strong class="text-red-600">menolak</strong> pengajuan <strong>${reference}</strong>?`,
                    icon: 'warning',
                    input: 'textarea',
                    inputLabel: 'Alasan Penolakan (wajib)',
                    inputValidator: (v) => !v && 'Alasan penolakan harus diisi!',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
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
            $(document).off('click', '.delete-adjustment').on('click', '.delete-adjustment', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const reference = $(this).data('reference');

                Swal.fire({
                    title: 'Hapus Pengajuan?',
                    html: `Yakin menghapus <strong>${reference}</strong>?<br><small class="text-red-500">Aksi tidak dapat dibatalkan.</small>`,
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
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
        });
    </script>
@endpush
