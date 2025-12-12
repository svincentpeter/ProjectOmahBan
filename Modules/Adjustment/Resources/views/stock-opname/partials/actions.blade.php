{{-- Tombol aksi untuk setiap baris di DataTable --}}
<div class="flex items-center justify-center gap-1">
    
    {{-- Tombol Lihat Detail (semua status) --}}
    @can('show_stock_opname')
    <a href="{{ route('stock-opnames.show', $so->id) }}" 
       class="p-2 text-zinc-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" 
       title="Lihat Detail">
        <i class="bi bi-eye text-sm"></i>
    </a>
    @endcan

    {{-- Tombol Lanjutkan Hitung (hanya draft & in_progress) --}}
    @can('edit_stock_opname')
        @if(in_array($so->status, ['draft', 'in_progress']))
        <a href="{{ route('stock-opnames.counting', $so->id) }}" 
           class="p-2 text-zinc-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" 
           title="Lanjutkan Penghitungan">
            <i class="bi bi-calculator text-sm"></i>
        </a>
        @endif
    @endcan

    {{-- Tombol Selesaikan (hanya in_progress dengan progress 100%) --}}
    @can('edit_stock_opname')
        @if($so->status === 'in_progress' && $so->completion_percentage >= 100)
        <form action="{{ route('stock-opnames.complete', $so->id) }}" 
              method="POST" 
              class="inline"
              id="complete-form-{{ $so->id }}">
            @csrf
            <button type="button" 
                    class="complete-opname p-2 text-zinc-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" 
                    data-id="{{ $so->id }}"
                    title="Selesaikan Opname">
                <i class="bi bi-check-circle text-sm"></i>
            </button>
        </form>
        @endif
    @endcan

    {{-- Tombol Hapus (hanya draft) --}}
    @can('delete_stock_opname')
        @if($so->status === 'draft')
        <form action="{{ route('stock-opnames.destroy', $so->id) }}" 
              method="POST" 
              class="inline"
              id="delete-form-{{ $so->id }}">
            @csrf
            @method('DELETE')
            <button type="button" 
                    class="delete-opname p-2 text-zinc-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" 
                    data-id="{{ $so->id }}"
                    title="Hapus">
                <i class="bi bi-trash text-sm"></i>
            </button>
        </form>
        @endif
    @endcan
</div>

@push('page_scripts')
<script>
$(function() {
    // Complete Opname
    $(document).off('click', '.complete-opname').on('click', '.complete-opname', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Selesaikan Stok Opname?',
            html: 'Yakin ingin menyelesaikan stok opname ini?<br><small class="text-zinc-500">Adjustment akan otomatis dibuat untuk variance.</small>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="bi bi-check-circle"></i> Ya, Selesaikan!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                document.getElementById(`complete-form-${id}`).submit();
            }
        });
    });

    // Delete Opname
    $(document).off('click', '.delete-opname').on('click', '.delete-opname', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Hapus Stok Opname?',
            html: 'Yakin ingin menghapus stok opname ini?<br><small class="text-red-500">Data tidak bisa dikembalikan!</small>',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
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
