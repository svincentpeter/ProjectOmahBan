@php
    $map = [
        'Paid'   => ['bg-gradient-to-r from-green-50 to-emerald-100 text-emerald-700 border-emerald-200', 'Lunas'],
        'Unpaid' => ['bg-gradient-to-r from-red-50 to-orange-100 text-red-700 border-red-200',  'Belum Dibayar'],
        'Partial'=> ['bg-gradient-to-r from-yellow-50 to-amber-100 text-amber-700 border-amber-200', 'Sebagian'],
    ];

    [$cls, $label] = $map[$data->payment_status] ?? ['bg-gray-100 text-gray-800 border-gray-400', ucfirst($data->payment_status)];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold border {{ $cls }} shadow-sm">
    @if($data->payment_status === 'Paid') <i class="bi bi-check-all mr-1.5"></i> @endif
    @if($data->payment_status === 'Unpaid') <i class="bi bi-exclamation-circle-fill mr-1.5"></i> @endif
    @if($data->payment_status === 'Partial') <i class="bi bi-pie-chart-fill mr-1.5"></i> @endif
    {{ $label }}
</span>
