{{-- Action buttons untuk approval DataTable --}}
<div class="btn-group" role="group">
    <!-- Detail Button -->
    <a href="{{ route('adjustments.show', $row->id) }}" class="btn btn-sm btn-info" title="Lihat Detail">
        <i class="cil-eye"></i> Detail
    </a>

    <!-- Approve Button -->
    <button type="button" class="btn btn-sm btn-success btn-approve-action" data-id="{{ $row->id }}"
        data-action="approve" title="Setujui">
        <i class="cil-check"></i> Setuju
    </button>

    <!-- Reject Button -->
    <button type="button" class="btn btn-sm btn-danger btn-approve-action" data-id="{{ $row->id }}"
        data-action="reject" title="Tolak">
        <i class="cil-x"></i> Tolak
    </button>
</div>
