{{-- Modules/Adjustment/Resources/views/partials/actions-approval.blade.php --}}
@php
    // Ambil id dari model/array; kalau tidak ada, null
    $adjustmentId = data_get($row ?? null, 'id');
@endphp

<div class="btn-group" role="group">
    <!-- Detail Button -->
    <a href="{{ $adjustmentId ? route('adjustments.show', $adjustmentId) : 'javascript:void(0)' }}"
       class="btn btn-soft {{ $adjustmentId ? '' : 'disabled' }}"
       @unless($adjustmentId) aria-disabled="true" @endunless>
        <i class="bi bi-eye"></i> Detail
    </a>

    <!-- Approve Button -->
    <button type="button"
            class="btn btn-sm btn-success btn-approve-action"
            data-id="{{ $adjustmentId }}"
            data-action="approve"
            title="Setujui"
            {{ $adjustmentId ? '' : 'disabled' }}>
        <i class="cil-check"></i> Setuju
    </button>

    <!-- Reject Button -->
    <button type="button"
            class="btn btn-sm btn-danger btn-approve-action"
            data-id="{{ $adjustmentId }}"
            data-action="reject"
            title="Tolak"
            {{ $adjustmentId ? '' : 'disabled' }}>
        <i class="cil-x"></i> Tolak
    </button>
</div>
