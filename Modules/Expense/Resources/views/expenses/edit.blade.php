@extends('layouts.app-flowbite')

@section('title', 'Edit Pengeluaran')

@section('content')
    {{-- Breadcrumb --}}
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Pengeluaran Harian', 'url' => route('expenses.index')],
            ['text' => 'Edit Pengeluaran', 'url' => '#'],
        ]
    ])

    <div class="p-4">
        <form id="expense-form" action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                {{-- Header --}}
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700 flex flex-col md:flex-row justify-between md:items-center gap-4">
                    <div>
                        <h5 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 mb-1">Edit Pengeluaran</h5>
                        <p class="text-sm text-zinc-500 mt-1">Perbarui data pengeluaran: {{ $expense->reference }}</p>
                    </div>
                </div>

                {{-- Form Content --}}
                <div class="p-6">
                    @include('expense::expenses._form', ['expense' => $expense])
                </div>
            </div>

        </form>
    </div>
@endsection
