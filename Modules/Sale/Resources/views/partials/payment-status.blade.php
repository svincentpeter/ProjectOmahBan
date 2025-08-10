@php
    $map = [
        'Paid'   => ['badge-success', 'Lunas'],
        'Unpaid' => ['badge-danger',  'Belum Dibayar'],
        'Partial'=> ['badge-warning', 'Sebagian'],
    ];

    [$cls, $label] = $map[$data->payment_status] ?? ['badge-light', ucfirst($data->payment_status)];
@endphp

<span class="badge {{ $cls }}">{{ $label }}</span>
