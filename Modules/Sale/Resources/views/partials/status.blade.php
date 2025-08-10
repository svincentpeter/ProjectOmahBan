@php
    // Map status → warna + label Indonesia
    $map = [
        'Draft'     => ['badge-secondary', 'Draft'],
        'Pending'   => ['badge-warning',  'Menunggu'],
        'Shipped'   => ['badge-info',     'Dikirim'],
        'Completed' => ['badge-success',  'Selesai'],
    ];

    [$cls, $label] = $map[$data->status] ?? ['badge-light', ucfirst($data->status)];
@endphp

<span class="badge {{ $cls }}">{{ $label }}</span>
