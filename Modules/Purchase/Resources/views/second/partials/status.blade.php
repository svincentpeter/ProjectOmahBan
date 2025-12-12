@if ($data->status === 'Completed')
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-gradient-to-r from-green-50 to-emerald-100 text-emerald-700 border border-emerald-200 dark:from-green-900/30 dark:to-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800 shadow-sm">
        <i class="bi bi-check-circle-fill mr-1.5"></i>
        Completed
    </span>
@else
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-gradient-to-r from-blue-50 to-indigo-100 text-blue-700 border border-blue-200 dark:from-blue-900/30 dark:to-indigo-900/30 dark:text-blue-400 dark:border-blue-800 shadow-sm">
        <i class="bi bi-hourglass-split mr-1.5"></i>
        Pending
    </span>
@endif
