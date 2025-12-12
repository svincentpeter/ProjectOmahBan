<?php

namespace App\DataTables;

use App\Models\OwnerNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class NotificationsDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('checkbox', fn() => '')
            ->addColumn('severity_badge', function ($row) {
                // Match old JS logic for soft badges
                $map = [
                    'critical' => '<span class="badge-soft-danger">Kritis</span>',
                    'warning' => '<span class="badge-soft-warning">Peringatan</span>',
                    'info' => '<span class="badge-soft-info">Info</span>'
                ];
                return $map[$row->severity] ?? '<span class="badge-soft-secondary">-</span>';
            })
            ->addColumn('type_badge', function ($row) {
                // Match old JS logic for soft badges
                $rawType = $row->notification_type ?? $row->type ?? 'other';
                $map = [
                    'manual_input_alert' => '<span class="badge-soft-purple">Input Manual</span>',
                    'price_adjustment' => '<span class="badge-soft-info">Ubah Harga</span>',
                    'discount_alert' => '<span class="badge-soft-success">Diskon</span>',
                    'high_value_transaction' => '<span class="badge-soft-danger">Transaksi Besar</span>'
                ];
                
                if (isset($map[$rawType])) {
                    return $map[$rawType];
                }

                $formatted = ucfirst(str_replace('_', ' ', $rawType));
                return '<span class="badge-soft-secondary">'.e($formatted).'</span>';
            })
            ->addColumn('created_at_ts', fn($row) => optional($row->created_at)->timestamp ?? 0)
            ->addColumn('time_ago', fn($row) => optional($row->created_at)->diffForHumans() ?? '-')
            ->editColumn('title', fn($row) => e(Str::limit($row->title ?? '-', 60)))
            ->addColumn('read_status', fn($row) => $row->is_read ? '<span class="text-green-500 text-xs font-bold"><i class="bi bi-check2-all me-1"></i>Dibaca</span>' : '<span class="text-blue-600 text-xs font-bold"><i class="bi bi-circle-fill me-1" style="font-size:6px"></i>Baru</span>')
            ->addColumn('reviewed_status', fn($row) => $row->is_reviewed ? '<span class="badge-soft-success">Selesai</span>' : '<span class="badge-soft-warning">Menunggu</span>')
            ->addColumn('fontee_status_badge', function ($row) {
                if (!$row->fontee_message_id) {
                    return '-';
                }
                 return '<i class="bi bi-whatsapp text-green-500"></i>';
            })
            ->addColumn('action', function ($row) {
                // Use view for cleaner separation, or keep inline if very simple. 
                // Using inline to match previous simple style but with Tailwind classes
                $showUrl = route('notifications.show', $row->id);
                return '
                    <div class="flex items-center justify-center gap-2">
                        <a href="'.$showUrl.'" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"><i class="bi bi-eye"></i></a>
                        <button class="delete-notif p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" data-id="'.$row->id.'"><i class="bi bi-trash"></i></button>
                    </div>
                ';
            })
            ->rawColumns(['checkbox', 'severity_badge', 'type_badge', 'read_status', 'reviewed_status', 'fontee_status_badge', 'action']);
    }

    public function query(OwnerNotification $model): Builder
    {
        $uid = auth()->id();
        $query = $model->newQuery()
            ->where(fn($q) => $q->where('user_id', $uid)->orWhereNull('user_id'))
            ->orderByDesc('created_at');

        // Apply filters from request
        $request = $this->request();
        if ($request->filled('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }
        if ($request->filled('is_reviewed')) {
             // Handle special case where filters might send '0' string
            $query->where('is_reviewed', $request->boolean('is_reviewed'));
        }
        if ($request->filled('severity')) {
            $query->where('severity', (string) $request->input('severity'));
        }
        if ($request->filled('type')) {
            $query->where('notification_type', (string) $request->input('type'));
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('notifications-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('rt<"p-5 flex items-center justify-between"lp>')
            ->orderBy(1)
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'drawCallback' => 'function() {
                    // Re-bind delete events here or use commandPattern
                    // Note: Views should handle delegation $(document).on(...)
                }'
            ]);
    }

    protected function getColumns()
    {
        return [
            Column::computed('DT_RowIndex')->title('No')->addClass('text-center font-bold text-zinc-500')->width(50),
            Column::make('created_at')->visible(false), // Hidden for sorting
            Column::computed('time_ago')->title('Waktu')->addClass('whitespace-nowrap font-bold text-black'),
            Column::computed('severity_badge')->title('Tingkat'),
            Column::computed('type_badge')->title('Tipe'),
            Column::make('title')->title('Judul')->addClass('font-extrabold text-black w-1/4'),
            Column::computed('read_status')->title('Status'),
            Column::computed('reviewed_status')->title('Review'),
            Column::computed('fontee_status_badge')->title('Fontee')->addClass('text-center'),
            Column::computed('action')->title('Aksi')->addClass('text-center')->width(100),
        ];
    }

    protected function filename(): string
    {
        return 'Notifications_' . date('YmdHis');
    }
}
