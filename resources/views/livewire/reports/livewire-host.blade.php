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
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $title ?? 'Laporan' }}</h1>
            <div class="flex items-center space-x-2">
                @stack('page-actions')
            </div>
        </div>

        @livewire($component)
    </div>
@endsection
