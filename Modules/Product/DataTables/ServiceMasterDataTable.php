<?php

namespace Modules\Product\DataTables;

use Illuminate\Support\Str;
use Modules\Product\Entities\ServiceMaster;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ServiceMasterDataTable extends DataTable
{
    public function dataTable($query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('standard_price', function ($d) {
                return '<span class="font-bold text-emerald-600">' . format_currency($d->standard_price) . '</span>';
            })
            ->editColumn('category', function ($d) {
                $map = [
                    'service' => '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Service</span>',
                    'goods' => '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Goods</span>',
                    'custom' => '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">Custom</span>',
                ];
                return $map[$d->category] ?? $d->category;
            })
            ->editColumn('description', function ($d) {
                if (!$d->description) {
                    return '<span class="text-zinc-400">-</span>';
                }
                return e(Str::limit($d->description, 60));
            })
            ->addColumn('status_text', function ($d) {
                return $d->status
                    ? '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Aktif</span>'
                    : '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-zinc-100 text-zinc-600">Nonaktif</span>';
            })
            ->addColumn('action', function ($d) {
                // Use global action component
                // Note: ServiceMaster has custom actions like edit modal trigger so we might need a slightly different approach 
                // OR we adapt the global component to support standard routes if standard routes exist, OR use customActions slot.
                // The current index.blade.php uses modals (openModal) instead of page redirection.
                // The global partial supports standard routes. Let's see if we can adapt or if we should keep it simple.
                // The previous code used `view('product::service-masters.partials.actions')`.
                
                // Let's stick to the user request: "Service buatkan juga" -> imply similar UI.
                // Product uses 3-dot menu.
                // Service Master uses Modals.
                // We customize the global partial for modals? The global partial takes routes. 
                // Or we pass `customActions` to the global partial.
                
                // Construct custom buttons for modals
                $editBtn = '
                    <li>
                        <a href="javascript:void(0)" 
                           class="flex items-center gap-2 px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-600 dark:hover:text-white transition-colors btn-edit"
                           data-id="'.$d->id.'"
                           data-name="'.$d->service_name.'"
                           data-price="'.$d->standard_price.'"
                           data-category="'.$d->category.'"
                           data-description="'.$d->description.'"
                        >
                            <i class="bi bi-pencil-square text-amber-600 dark:text-amber-400"></i>
                            <span>Edit</span>
                        </a>
                    </li>';
                    
                $deleteBtn = '
                    <li>
                        <a href="javascript:void(0)" 
                           class="flex items-center gap-2 px-4 py-2.5 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-600 transition-colors btn-delete"
                           data-id="'.$d->id.'"
                           data-name="'.$d->service_name.'"
                           data-price="'.$d->standard_price.'"
                           data-category="'.$d->category.'"
                        >
                            <i class="bi bi-trash"></i>
                            <span>Hapus</span>
                        </a>
                    </li>';

                return view('partials.datatable-actions', [
                    'id' => $d->id,
                    'itemName' => $d->service_name,
                    // No standard routes, pass custom actions
                    'customActions' => $editBtn . $deleteBtn
                ])->render();
            })
            ->rawColumns(['standard_price', 'category', 'status_text', 'description', 'action']);
    }

    public function query(ServiceMaster $model)
    {
        $q = $model->newQuery();

        // category filter
        if (request()->has('category') && request('category') != '' && request('category') != 'all') {
            $q->where('category', request('category'));
        }

        // status filter (quick)
        if (request()->has('quick') && request('quick') != 'all') {
            $quick = request('quick');
            if ($quick === 'active') {
                $q->where('status', 1);
            } elseif ($quick === 'inactive') {
                $q->where('status', 0);
            }
        }

        return $q->select(['id', 'service_name', 'standard_price', 'category', 'description', 'status']);
    }


    public function html()
    {
        return $this->builder()
            ->setTableId('service-masters-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'asc')
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'drawCallback' => 'function() { 
                    window.scrollTo(0, 0); 
                    if (typeof initFlowbite === "function") {
                        initFlowbite();
                    }
                }'
            ]);
    }

    protected function getColumns()
    {
        return [
            Column::computed('DT_RowIndex')->title('No')->width(60)->addClass('text-center')->orderable(false)->searchable(false),
            Column::make('service_name')->title('Nama Jasa'),
            Column::make('standard_price')->title('Harga Standar')->addClass('text-end')->width(160),
            Column::make('category')->title('Kategori')->width(140)->orderable(false)->searchable(false),
            Column::make('description')->title('Deskripsi')->orderable(false),
            Column::computed('status_text')->title('Status')->width(120)->orderable(false)->searchable(false),
            Column::computed('action')->title('Aksi')->width(140)->addClass('text-center')->orderable(false)->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'ServiceMasters_' . date('YmdHis');
    }
}
