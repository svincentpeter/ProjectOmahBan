@extends('layouts.app-flowbite')

@section('title', 'Buat Penawaran Baru')

@section('content')
    <div class="px-4 pt-4 mb-4">
        {{-- Breadcrumb --}}
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
               <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <i class="bi bi-house-door-fill mr-2"></i> Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="bi bi-chevron-right text-gray-400"></i>
                        <a href="{{ route('quotations.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Penawaran</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="bi bi-chevron-right text-gray-400"></i>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Buat Baru</span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- Search Product Section --}}
        <div class="mb-6">
            <livewire:search-product />
        </div>

        {{-- Form Section --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            @include('utils.alerts')
            
            <form id="quotation-form" action="{{ route('quotations.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                         <label for="reference" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Reference</label>
                         <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="bi bi-upc-scan text-gray-500"></i>
                            </div>
                            <input type="text" name="reference" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full pl-10 p-2.5 cursor-not-allowed" value="QT" readonly>
                        </div>
                    </div>
                    <div>
                        <label for="date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date <span class="text-red-500">*</span></label>
                        <div class="relative">
                             <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="bi bi-calendar-event text-gray-500"></i>
                            </div>
                            <input type="date" name="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full pl-10 p-2.5" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div>
                        <label for="customer_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Customer <span class="text-red-500">*</span></label>
                        <div class="relative">
                             <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="bi bi-person text-gray-500"></i>
                            </div>
                            <select name="customer_id" id="customer_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full pl-10 p-2.5" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Cart Component (Using 'quotation' instance) --}}
                <div class="mb-8">
                    <livewire:product-cart :cartInstance="'quotation'" />
                </div>
                
                {{-- Status --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status <span class="text-red-500">*</span></label>
                        <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" required>
                            <option value="Pending">Pending</option>
                            <option value="Sent">Sent</option>
                            <option value="Accepted">Accepted</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                 </div>

                {{-- Note --}}
                <div class="mb-6">
                    <label for="note" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Note</label>
                    <textarea id="note" name="note" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 placeholder-gray-400 focus:ring-blue-500 focus:border-blue-500" placeholder="Add note..."></textarea>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 focus:outline-none flex items-center shadow-lg hover:shadow-xl transition-all">
                        Create Quotation <i class="bi bi-check-lg ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
