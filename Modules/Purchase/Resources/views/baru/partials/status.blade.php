{{-- Modern Flowbite Status Badges --}}
@if ($data->status == 'Pending')
    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-800 dark:from-blue-900/30 dark:to-cyan-900/30 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
        <i class="bi bi-clock-history mr-1.5"></i>
        {{ $data->status }}
    </span>
@elseif ($data->status == 'Ordered')
    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-purple-100 to-indigo-100 text-purple-800 dark:from-purple-900/30 dark:to-indigo-900/30 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
        <i class="bi bi-cart-check-fill mr-1.5"></i>
        {{ $data->status }}
    </span>
@else
    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 dark:from-green-900/30 dark:to-emerald-900/30 dark:text-green-300 border border-green-200 dark:border-green-800">
        <i class="bi bi-check-circle-fill mr-1.5"></i>
        {{ $data->status }}
    </span>
@endif
