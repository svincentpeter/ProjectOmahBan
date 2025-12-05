{{-- Tombol aksi untuk setiap baris di DataTable --}}
<div class="btn-group btn-group-sm" role="group">
    
    {{-- Tombol Lihat Detail (semua status) --}}
    @can('show_stock_opname')
    <a href="{{ route('stock-opnames.show', $so->id) }}" 
       class="btn btn-info" 
       title="Lihat Detail">
        <i class="bi bi-eye"></i>
    </a>
    @endcan

    {{-- Tombol Lanjutkan Hitung (hanya draft & in_progress) --}}
    @can('edit_stock_opname')
        @if(in_array($so->status, ['draft', 'in_progress']))
        <a href="{{ route('stock-opnames.counting', $so->id) }}" 
           class="btn btn-warning" 
           title="Lanjutkan Penghitungan">
            <i class="bi bi-calculator"></i>
        </a>
        @endif
    @endcan

    {{-- Tombol Selesaikan (hanya in_progress dengan progress 100%) --}}
    @can('edit_stock_opname')
        @if($so->status === 'in_progress' && $so->completion_percentage >= 100)
        <form action="{{ route('stock-opnames.complete', $so->id) }}" 
              method="POST" 
              class="d-inline"
              onsubmit="return confirm('Yakin ingin menyelesaikan stok opname ini? Adjustment akan otomatis dibuat untuk variance.')">
            @csrf
            <button type="submit" class="btn btn-success" title="Selesaikan Opname">
                <i class="bi bi-check-circle"></i>
            </button>
        </form>
        @endif
    @endcan

    {{-- Tombol Hapus (hanya draft) --}}
    @can('delete_stock_opname')
        @if($so->status === 'draft')
        <form action="{{ route('stock-opnames.destroy', $so->id) }}" 
              method="POST" 
              class="d-inline"
              onsubmit="return confirm('Yakin ingin menghapus stok opname ini? Data tidak bisa dikembalikan!')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" title="Hapus">
                <i class="bi bi-trash"></i>
            </button>
        </form>
        @endif
    @endcan
</div>
