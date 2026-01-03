@extends('layouts.app-flowbite')

@section('title', $title ?? 'Laporan')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Laporan', 'url' => route('reports.index')],
            ['text' => $title ?? 'Detail', 'url' => '#', 'icon' => 'bi bi-file-earmark-text'],
        ]
    ])
@endsection

@section('content')
    <div class="px-4 pt-6">
        @livewire($component)
    </div>
@endsection
