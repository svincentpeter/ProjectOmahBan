@extends('layouts.app-flowbite')

@section('title', 'Notification Center')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Operasional', 'url' => '#'],
        ['text' => 'Notifikasi', 'url' => route('notifications.index'), 'icon' => 'bi bi-bell-fill']
    ]])
@endsection

@section('content')
    <!-- Stats Grid (Vibrant Gradients) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Belum Dibaca -->
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg shadow-blue-200 transform transition-all hover:scale-[1.02] cursor-pointer" onclick="$('#filterRead').val('0').trigger('change')">
            <div class="flex items-center justify-between z-10 relative">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">Pesan Belum Dibaca</p>
                    <h3 class="text-3xl font-bold">{{ $stats['unread_count'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                    <i class="bi bi-envelope text-2xl text-white"></i>
                </div>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        </div>

        <!-- Hari Ini -->
        <div class="relative overflow-hidden bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg shadow-purple-200 transform transition-all hover:scale-[1.02]">
            <div class="flex items-center justify-between z-10 relative">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">Aktivitas Hari Ini</p>
                    <h3 class="text-3xl font-bold">{{ $stats['today_count'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                    <i class="bi bi-calendar4-week text-2xl text-white"></i>
                </div>
            </div>
             <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        </div>

        <!-- Belum Direview -->
        <div class="relative overflow-hidden bg-gradient-to-br from-orange-400 to-orange-500 rounded-2xl p-6 text-white shadow-lg shadow-orange-200 transform transition-all hover:scale-[1.02] cursor-pointer" onclick="$('#filterReviewed').val('0').trigger('change')">
            <div class="flex items-center justify-between z-10 relative">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">Menunggu Review</p>
                    <h3 class="text-3xl font-bold">{{ $stats['unreviewed_count'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                    <i class="bi bi-clipboard-check text-2xl text-white"></i>
                </div>
            </div>
             <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        </div>

        <!-- Critical -->
        <div class="relative overflow-hidden bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl p-6 text-white shadow-lg shadow-red-200 transform transition-all hover:scale-[1.02] cursor-pointer" onclick="$('#filterSeverity').val('critical').trigger('change')">
            <div class="flex items-center justify-between z-10 relative">
                <div>
                    <p class="text-red-100 text-sm font-medium mb-1">Notifikasi Penting</p>
                    <h3 class="text-3xl font-bold">{{ $stats['critical_count'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                    <i class="bi bi-shield-exclamation text-2xl text-white"></i>
                </div>
            </div>
             <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-xl shadow-slate-200/50 dark:bg-gray-800 dark:border-gray-700">
        
        {{-- Header & Filters --}}
        <div class="p-6 border-b border-zinc-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <h5 class="text-xl font-bold text-black dark:text-white tracking-tight">Log Aktivitas</h5>
                    <p class="text-sm text-zinc-600 mt-1">Pantau semua notifikasi sistem secara real-time.</p>
                </div>
                <div>
                    <button class="text-sm font-semibold text-blue-700 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-5 py-2.5 rounded-xl transition-all shadow-sm hover:shadow">
                        <i class="bi bi-download me-1"></i> Ekspor Laporan
                    </button>
                </div>
            </div>

            {{-- New Global Filter Component --}}
            @include('layouts.filter-card', [
                'action' => route('notifications.index'),
                'title' => 'Filter Log',
                'icon' => 'bi bi-sliders',
                'quickFilters' => [
                    [
                        'label' => 'Semua',
                        'url' => route('notifications.index'),
                        'param' => 'filter',
                        'value' => 'all',
                        'icon' => 'bi bi-grid'
                    ],
                    [
                        'label' => 'Belum Dibaca',
                        'url' => route('notifications.index', ['is_read' => '0']),
                        'param' => 'is_read',
                        'value' => '0',
                        'icon' => 'bi bi-envelope'
                    ],
                    [
                        'label' => 'Menunggu Review',
                        'url' => route('notifications.index', ['is_reviewed' => '0']),
                        'param' => 'is_reviewed',
                        'value' => '0',
                        'icon' => 'bi bi-clipboard-check'
                    ],
                    [
                        'label' => 'Penting (Kritis)',
                        'url' => route('notifications.index', ['severity' => 'critical']),
                        'param' => 'severity',
                        'value' => 'critical',
                        'icon' => 'bi bi-exclamation-triangle'
                    ]
                ],
                'filters' => [
                    [
                        'name' => 'is_read',
                        'label' => 'Status Pesan',
                        'type' => 'select',
                        'icon' => 'bi bi-envelope',
                        'options' => [
                            '0' => 'Belum Dibaca',
                            '1' => 'Sudah Dibaca'
                        ]
                    ],
                    [
                        'name' => 'is_reviewed',
                        'label' => 'Status Review',
                        'type' => 'select',
                        'icon' => 'bi bi-check2-circle',
                        'options' => [
                            '0' => 'Menunggu Review',
                            '1' => 'Selesai Direview'
                        ]
                    ],
                    [
                        'name' => 'severity',
                        'label' => 'Tingkat Urgensi',
                        'type' => 'select',
                        'icon' => 'bi bi-shield-exclamation',
                        'options' => [
                            'critical' => 'Kritis/Bahaya',
                            'warning' => 'Peringatan',
                            'info' => 'Informasi'
                        ]
                    ],
                    [
                        'name' => 'type',
                        'label' => 'Tipe Notifikasi',
                        'type' => 'select',
                        'icon' => 'bi bi-tags',
                        'options' => [
                            'manual_input_alert' => 'Input Manual',
                            'price_adjustment' => 'Perubahan Harga',
                            'discount_alert' => 'Diskon Diatas Batas'
                        ]
                    ]
                ]
            ])
        </div>

        <!-- Table Wrapper -->
        <div class="p-6 pt-0 mt-2 overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500 dark:text-gray-400" id="notificationsTable">
                <thead class="text-xs text-slate-400 uppercase bg-slate-50/50 border-b border-slate-100">
                    <tr>
                         <th scope="col" class="px-6 py-4 font-bold tracking-wider">No</th>
                         <th style="display:none">TS</th>
                         <th scope="col" class="px-6 py-4 font-bold tracking-wider">Waktu</th>
                         <th scope="col" class="px-6 py-4 font-bold tracking-wider">Tingkat</th>
                         <th scope="col" class="px-6 py-4 font-bold tracking-wider">Tipe</th>
                         <th scope="col" class="px-6 py-4 font-bold tracking-wider w-1/4">Judul</th>
                         <th scope="col" class="px-6 py-4 font-bold tracking-wider">Status</th>
                         <th scope="col" class="px-6 py-4 font-bold tracking-wider">Review</th>
                         <th scope="col" class="px-6 py-4 font-bold tracking-wider">Fontee</th>
                         <th scope="col" class="px-6 py-4 font-bold tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50"></tbody>
            </table>
        </div>
    </div>
@endsection

@push('page_styles')
    @include('includes.datatables-flowbite-css')
    <style>
    /* DataTables Premium Override */
    .dataTables_wrapper { font-family: 'Inter', sans-serif; color: #000000; margin-top: 10px; }


    /* Badges */
    .badge-soft-danger { background-color: #fee2e2; color: #dc2626; padding: 6px 10px; border-radius: 6px; font-weight: 700; font-size: 0.7rem; letter-spacing: 0.025em; }
    .badge-soft-warning { background-color: #fef3c7; color: #d97706; padding: 6px 10px; border-radius: 6px; font-weight: 700; font-size: 0.7rem; letter-spacing: 0.025em; }
    .badge-soft-info { background-color: #e0f2fe; color: #0284c7; padding: 6px 10px; border-radius: 6px; font-weight: 700; font-size: 0.7rem; letter-spacing: 0.025em; }
    .badge-soft-purple { background-color: #f3e8ff; color: #9333ea; padding: 6px 10px; border-radius: 6px; font-weight: 700; font-size: 0.7rem; letter-spacing: 0.025em; }
    .badge-soft-success { background-color: #dcfce7; color: #16a34a; padding: 6px 10px; border-radius: 6px; font-weight: 700; font-size: 0.7rem; letter-spacing: 0.025em; }
    .badge-soft-secondary { background-color: #f1f5f9; color: #64748b; padding: 6px 10px; border-radius: 6px; font-weight: 700; font-size: 0.7rem; letter-spacing: 0.025em; }
</style>
@endpush

@push('page_scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    @include('includes.datatables-flowbite-js')
    <script>
        $(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            const table = $('#notificationsTable').DataTable({
                processing: true, serverSide: true,
                ajax: {
                    url: "{{ route('notifications.data') }}", type: "GET",
                    data: function(d) { d.is_read = $('#filterRead').val(); d.is_reviewed = $('#filterReviewed').val(); d.severity = $('#filterSeverity').val(); d.type = $('#filterType').val(); }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center font-bold text-zinc-500' },
                    { data: 'created_at_ts', visible: false, searchable: false },
                    { data: 'time_ago', className: 'whitespace-nowrap font-bold text-black', searchable: false },
                    { 
                        data: 'severity_badge', orderable: false, searchable: false,
                        render: function(data, type, row) {
                            const map = { critical: '<span class="badge-soft-danger">Kritis</span>', warning: '<span class="badge-soft-warning">Peringatan</span>', info: '<span class="badge-soft-info">Info</span>' };
                            return map[row.severity] || '<span class="badge-soft-secondary">-</span>';
                        }
                    },
                    { 
                        data: 'type_badge', orderable: false, searchable: false,
                        render: function(data, type, row) {
                            // Use notification_type from backend, fallback to 'type' or default
                            const rawType = row.notification_type || row.type || 'other';
                            
                            const map = { 
                                manual_input_alert: '<span class="badge-soft-purple">Input Manual</span>', 
                                price_adjustment: '<span class="badge-soft-info">Ubah Harga</span>',
                                discount_alert: '<span class="badge-soft-success">Diskon</span>',
                                high_value_transaction: '<span class="badge-soft-danger">Transaksi Besar</span>' 
                            };
                            
                            // If mapped, return map. If not, format the raw string nicely
                            if (map[rawType]) return map[rawType];
                            
                            const formatted = rawType.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                            return `<span class="badge-soft-secondary">${formatted}</span>`;
                        }
                    },
                    { data: 'title', className: 'font-extrabold text-black w-1/4' },
                    { 
                        data: 'read_status', orderable: false, searchable: false,
                        render: function(data, type, row) {
                            return row.is_read ? '<span class="text-green-500 text-xs font-bold"><i class="bi bi-check2-all me-1"></i>Dibaca</span>' : '<span class="text-blue-600 text-xs font-bold"><i class="bi bi-circle-fill me-1" style="font-size:6px"></i>Baru</span>';
                        }
                    },
                    { 
                        data: 'reviewed_status', orderable: false, searchable: false, 
                        render: function(d,t,r){ return r.is_reviewed ? '<span class="badge-soft-success">Selesai</span>' : '<span class="badge-soft-warning">Menunggu</span>'; } 
                    },
                    { 
                        data: 'fontee_status_badge', orderable: false, searchable: false,
                        render: function(d,t,r){ return r.fontee_message_id ? '<i class="bi bi-whatsapp text-green-500"></i>' : '-'; }
                    },
                    { 
                        data: 'action', orderable: false, searchable: false,
                        render: function(data, type, row) {
                            const showUrl = "{{ url('/notifications') }}/" + row.id;
                            return `<div class="flex items-center justify-center gap-2">
                              <a href="${showUrl}" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"><i class="bi bi-eye"></i></a>
                              <button class="delete-notif p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" data-id="${row.id}"><i class="bi bi-trash"></i></button>
                            </div>`;
                        }
                    }
                ],
                drawCallback: function() {
                    const api = this.api();
                    
                    // Specific Logic: Unread Highlighting
                    api.rows().every(function() {
                        const $row = $(this.node());
                        const data = this.data();
                        if (!(data.is_read)) { $row.addClass('unread'); } else { $row.removeClass('unread'); }
                    });
                    
                    // Re-bind delete buttons
                    bindDeleteButtons();
                    
                    // --- Global Styling Logic Merger ---
                    var wrapper = $(api.table().container());
                    // Ensure pagination buttons have consistent rounded styles
                    wrapper.find('.paginate_button').removeClass('dt-button');
                    
                    // Add scroll hint if table is wider than wrapper
                    var scrollBody = wrapper.find('.dataTables_scrollBody');
                    if (scrollBody.length && scrollBody[0].scrollWidth > scrollBody.width()) {
                       wrapper.addClass('has-scroll');
                    }
                }
            });

            function bindDeleteButtons() {
                $('.delete-notif').off('click').on('click', function() {
                    const id = $(this).data('id');
                    Swal.fire({
                        title: 'Hapus?', text: "Tindakan ini permanen.", icon: 'warning',
                        showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                           $.ajax({ url: "{{ url('/api/notifications') }}/" + id, type: 'DELETE', success: function() { table.ajax.reload(null, false); }, error: function() { Swal.fire('Error', 'Gagal menghapus.', 'error'); } });
                        }
                    })
                });
            }
            
            // Filter events
            $('#filterRead, #filterReviewed, #filterSeverity, #filterType').on('change', function() { table.ajax.reload(); });
            $('#btnReset').on('click', function() { $('#filterRead, #filterReviewed, #filterSeverity, #filterType').val('').trigger('change'); });
        });
    </script>
@endpush
