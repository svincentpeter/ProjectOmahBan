@extends('layouts.app-flowbite')

@section('title', 'Detail Penawaran')

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
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Detail</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
             {{-- Info Card --}}
            <div class="md:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <!-- Header -->
                 <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        Penawaran: <span class="text-blue-600">{{ $quotation->reference }}</span>
                    </h2>
                     <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded border border-blue-400">
                        {{ $quotation->status }}
                    </span>
                 </div>
                 
                 <!-- Customer & Date -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Info Pelanggan</h3>
                        <p class="text-gray-900 font-bold mt-1">{{ $quotation->customer_name }}</p>
                        @if($quotation->customer)
                            <p class="text-sm text-gray-600"><i class="bi bi-telephone mr-1"></i> {{ $quotation->customer->customer_phone }}</p>
                            <p class="text-sm text-gray-600"><i class="bi bi-envelope mr-1"></i> {{ $quotation->customer->customer_email }}</p>
                        @endif
                    </div>
                     <div>
                        <h3 class="text-sm font-medium text-gray-500">Tanggal Penawaran</h3>
                        <p class="text-gray-900 font-bold mt-1">{{ \Carbon\Carbon::parse($quotation->date)->locale('id')->translatedFormat('d F Y') }}</p>
                    </div>
                </div>
                
                <!-- Items Table -->
                <div class="relative overflow-x-auto mb-6 border rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                            <tr>
                                <th scope="col" class="px-6 py-3">Produk</th>
                                <th scope="col" class="px-6 py-3 text-right">Harga Satuan</th>
                                <th scope="col" class="px-6 py-3 text-center">Qty</th>
                                <th scope="col" class="px-6 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotation->quotationDetails as $item)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $item->product_name }} <br>
                                    <span class="text-xs text-gray-500">{{ $item->product_code }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    {{ format_currency($item->unit_price) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    {{ format_currency($item->sub_total) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                 <!-- Summary -->
                 <div class="flex justify-end">
                    <div class="w-full md:w-1/2 p-4 bg-gray-50 rounded-lg border border-gray-100">
                         <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Pajak ({{ $quotation->tax_percentage }}%)</span>
                            <span class="font-medium">{{ format_currency($quotation->tax_amount) }}</span>
                        </div>
                         <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Diskon ({{ $quotation->discount_percentage }}%)</span>
                            <span class="font-medium">{{ format_currency($quotation->discount_amount) }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Biaya Pengiriman</span>
                            <span class="font-medium">{{ format_currency($quotation->shipping_amount) }}</span>
                        </div>
                         <div class="flex justify-between border-t border-gray-200 pt-2 mt-2">
                            <span class="text-lg font-bold text-gray-900">Total</span>
                            <span class="text-lg font-bold text-blue-800">{{ format_currency($quotation->total_amount) }}</span>
                        </div>
                    </div>
                 </div>
                 
                 @if($quotation->note)
                 <div class="mt-6 border-t pt-4">
                     <h3 class="text-sm font-medium text-gray-500 mb-2">Catatan</h3>
                     <p class="text-gray-700 bg-yellow-50 p-3 rounded-lg border border-yellow-100 text-sm">
                        <i class="bi bi-sticky mr-2 text-yellow-500"></i>
                        {{ $quotation->note }}
                    </p>
                 </div>
                 @endif
            </div>
            
            {{-- Actions --}}
            <div class="md:col-span-1">
                 <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                     <h3 class="text-lg font-bold text-gray-900 mb-4">Aksi</h3>
                     
                     <div class="flex flex-col gap-3">
                         @can('create_sales')
                             @if($quotation->status != 'Converted')
                                <a href="{{ route('quotations.convert-to-sale', $quotation) }}" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-500 dark:hover:bg-green-600 focus:outline-none flex items-center justify-center transition-all">
                                    <i class="bi bi-cart-check mr-2"></i> Convert to Sale
                                </a>
                             @else
                                <div class="text-center p-3 bg-purple-100 text-purple-800 rounded-lg border border-purple-200">
                                    <i class="bi bi-check-circle-fill mr-1"></i> Converted to Sale
                                </div>
                             @endif
                         @endcan
                         
                         @can('edit_sales')
                         <a href="{{ route('quotations.edit', $quotation) }}" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none flex items-center justify-center transition-all">
                            <i class="bi bi-pencil mr-2"></i> Edit
                        </a>
                        @endcan
  
                        {{-- TODO: PDF --}}
                        <button disabled class="text-gray-400 bg-gray-100 border border-gray-200 cursor-not-allowed font-medium rounded-lg text-sm px-5 py-2.5 flex items-center justify-center" title="Coming Soon">
                            <i class="bi bi-printer mr-2"></i> Print PDF
                        </button>
                         
                         @can('delete_sales')
                         <form action="{{ route('quotations.destroy', $quotation) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this quotation?');">
                             @csrf
                             @method('DELETE')
                             <button type="submit" class="w-full text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-500 dark:hover:bg-red-600 focus:outline-none flex items-center justify-center transition-all">
                                <i class="bi bi-trash mr-2"></i> Delete
                            </button>
                         </form>
                         @endcan
                     </div>
                 </div>
            </div>
        </div>
    </div>
@endsection
