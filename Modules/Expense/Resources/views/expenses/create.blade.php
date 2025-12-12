@extends('layouts.app-flowbite')

@section('title', 'Tambah Pengeluaran')

@section('content')
    {{-- Breadcrumb --}}
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Pengeluaran Harian', 'url' => route('expenses.index')],
            ['text' => 'Tambah Pengeluaran', 'url' => '#'],
        ]
    ])

    <div class="p-4">
        <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
            
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                {{-- Header --}}
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700 flex flex-col md:flex-row justify-between md:items-center gap-4">
                    <div>
                        <h5 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 mb-1">Tambah Pengeluaran</h5>
                        <p class="text-sm text-zinc-500 mt-1">Catat pengeluaran operasional baru.</p>
                    </div>
                </div>

                {{-- Form Content --}}
                <div class="p-6">
                    @include('expense::expenses._form', ['expense' => null])
                </div>
            </div>

        </form>
    </div>
@endsection
