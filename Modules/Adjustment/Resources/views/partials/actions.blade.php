{{-- File: Modules/Adjustment/Resources/views/partials/actions.blade.php --}}

<div class="btn-group" role="group">
    {{-- Show Button --}}
    @can('show_adjustments')
    <a href="{{ route('adjustments.show', $adjustment->id) }}" 
       class="btn btn-info btn-sm" 
       data-toggle="tooltip" 
       title="Lihat Detail">
        <i class="bi bi-eye"></i>
    </a>
    @endcan
    
    {{-- Edit Button --}}
    @can('edit_adjustments')
    <a href="{{ route('adjustments.edit', $adjustment->id) }}" 
       class="btn btn-warning btn-sm" 
       data-toggle="tooltip" 
       title="Edit">
        <i class="bi bi-pencil"></i>
    </a>
    @endcan
    
    {{-- Print PDF Button --}}
    <a href="{{ route('adjustments.pdf', $adjustment->id) }}" 
       target="_blank"
       class="btn btn-secondary btn-sm" 
       data-toggle="tooltip" 
       title="Print PDF">
        <i class="bi bi-printer"></i>
    </a>
    
    {{-- Delete Button dengan SweetAlert --}}
    @can('delete_adjustments')
    <button type="button" 
            class="btn btn-danger btn-sm delete-adjustment" 
            data-id="{{ $adjustment->id }}"
            data-reference="{{ $adjustment->reference }}"
            data-toggle="tooltip" 
            title="Hapus">
        <i class="bi bi-trash"></i>
    </button>
    
    {{-- Hidden Delete Form --}}
    <form id="delete-form-{{ $adjustment->id }}" 
          action="{{ route('adjustments.destroy', $adjustment->id) }}" 
          method="POST" 
          class="d-none">
        @csrf
        @method('DELETE')
    </form>
    @endcan
</div>

@push('page_scripts')
<script>
$(document).ready(function() {
    // Delete with SweetAlert
    $('.delete-adjustment').click(function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const reference = $(this).data('reference');
        
        Swal.fire({
            title: 'Hapus Penyesuaian?',
            html: `Apakah Anda yakin ingin menghapus <strong>${reference}</strong>?<br><small class="text-muted">Stok produk akan dikembalikan ke kondisi sebelum penyesuaian.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus!',
            cancelButtonText: '<i class="bi bi-x-circle"></i> Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menghapus...',
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
