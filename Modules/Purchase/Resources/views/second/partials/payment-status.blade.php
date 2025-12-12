@if ($data->payment_status === 'Lunas')
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-gradient-to-r from-emerald-50 to-teal-100 text-emerald-700 border border-emerald-200 dark:from-emerald-900/30 dark:to-teal-900/30 dark:text-emerald-400 dark:border-emerald-800 shadow-sm">
        <i class="bi bi-check-all mr-1.5"></i>
        Lunas
    </span>
@else
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-gradient-to-r from-red-50 to-orange-100 text-red-700 border border-red-200 dark:from-red-900/30 dark:to-orange-900/30 dark:text-red-400 dark:border-red-800 shadow-sm">
        <i class="bi bi-exclamation-circle-fill mr-1.5"></i>
        Belum Lunas
    </span>
@endif
