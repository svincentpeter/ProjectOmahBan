@php
    // Map status → warna Tailwind
    $map = [
        'Draft'     => ['bg-gray-100 text-gray-800 border-gray-400', 'Draft'],
        'Pending'   => ['bg-gradient-to-r from-yellow-50 to-amber-100 text-amber-700 border-amber-200',  'Menunggu'],
        'Shipped'   => ['bg-gradient-to-r from-blue-50 to-indigo-100 text-blue-700 border-blue-200',     'Dikirim'],
        'Completed' => ['bg-gradient-to-r from-green-50 to-emerald-100 text-emerald-700 border-emerald-200',  'Selesai'],
    ];

    [$cls, $label] = $map[$data->status] ?? ['bg-gray-100 text-gray-800 border-gray-400', ucfirst($data->status)];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold border {{ $cls }} shadow-sm">
    @if($data->status === 'Completed') <i class="bi bi-check-circle-fill mr-1.5"></i> @endif
    @if($data->status === 'Pending') <i class="bi bi-hourglass-split mr-1.5"></i> @endif
    @if($data->status === 'Shipped') <i class="bi bi-truck mr-1.5"></i> @endif
    {{ $label }}
</span>
