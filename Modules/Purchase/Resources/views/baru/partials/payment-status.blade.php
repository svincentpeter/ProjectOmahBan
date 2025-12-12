{{-- Modern Flowbite Payment Status Badges --}}
@if ($data->payment_status == 'Partial')
    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-yellow-100 to-orange-100 text-yellow-800 dark:from-yellow-900/30 dark:to-orange-900/30 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-800">
        <i class="bi bi-hourglass-split mr-1.5"></i>
        {{ $data->payment_status }}
    </span>
@elseif ($data->payment_status == 'Paid' || $data->payment_status == 'Lunas')
    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 dark:from-green-900/30 dark:to-emerald-900/30 dark:text-green-300 border border-green-200 dark:border-green-800">
        <i class="bi bi-check-circle-fill mr-1.5"></i>
        {{ $data->payment_status == 'Paid' ? 'Lunas' : $data->payment_status }}
    </span>
@else
    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-red-100 to-pink-100 text-red-800 dark:from-red-900/30 dark:to-pink-900/30 dark:text-red-300 border border-red-200 dark:border-red-800">
        <i class="bi bi-exclamation-circle-fill mr-1.5"></i>
        {{ $data->payment_status }}
    </span>
@endif
