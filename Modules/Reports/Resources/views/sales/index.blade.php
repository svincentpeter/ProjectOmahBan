@extends('layouts.app-flowbite')

@section('title', 'Sales Report')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Laporan', 'url' => route('reports.index')],
            ['text' => 'Laporan Penjualan', 'url' => '#', 'icon' => 'bi bi-file-earmark-bar-graph'],
        ]
    ])
@endsection

@section('content')
    <div class="px-4 pt-6">
        <livewire:reports.sales-report :customers="\Modules\People\Entities\Customer::all()"/>
    </div>
@endsection
