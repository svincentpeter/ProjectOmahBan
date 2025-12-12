@extends('layouts.app-flowbite')

@section('title', 'Approval Penyesuaian Stok')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', [
        'items' => [
            ['text' => 'Penyesuaian Stok', 'url' => route('adjustments.index')],
            ['text' => 'Approval', 'url' => '#', 'icon' => 'bi bi-clipboard-check'],
        ],
    ])
@endsection

@section('content')
    {{-- Statistics Section --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        {{-- Pending --}}
        <div class="bg-white border border-zinc-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all flex items-center gap-4 relative overflow-hidden group">
            <div class="w-14 h-14 bg-yellow-50 rounded-xl flex items-center justify-center text-yellow-600 shadow-sm group-hover:scale-110 transition-transform">
                <i class="bi bi-hourglass-split text-2xl"></i>
            </div>
            <div class="z-10">
                <p class="text-xs uppercase text-zinc-500 font-bold tracking-wider mb-1">Pending</p>
                <p class="text-2xl font-black text-zinc-800">{{ $pendingCount ?? 0 }}</p>
            </div>
            <div class="absolute -bottom-4 -right-4 w-20 h-20 bg-yellow-50/50 rounded-full group-hover:bg-yellow-100 transition-colors z-0"></div>
        </div>

        {{-- Urgent --}}
        <div class="bg-white border border-zinc-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all flex items-center gap-4 relative overflow-hidden group">
            <div class="w-14 h-14 bg-red-50 rounded-xl flex items-center justify-center text-red-600 shadow-sm group-hover:scale-110 transition-transform">
                <i class="bi bi-exclamation-octagon text-2xl"></i>
            </div>
            <div class="z-10">
                <p class="text-xs uppercase text-zinc-500 font-bold tracking-wider mb-1">Urgent (>7 Hari)</p>
                <p class="text-2xl font-black text-zinc-800">{{ $urgentCount ?? 0 }}</p>
            </div>
            <div class="absolute -bottom-4 -right-4 w-20 h-20 bg-red-50/50 rounded-full group-hover:bg-red-100 transition-colors z-0"></div>
        </div>

        {{-- Approved --}}
        <div class="bg-white border border-zinc-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all flex items-center gap-4 relative overflow-hidden group">
            <div class="w-14 h-14 bg-green-50 rounded-xl flex items-center justify-center text-green-600 shadow-sm group-hover:scale-110 transition-transform">
                <i class="bi bi-check-circle text-2xl"></i>
            </div>
            <div class="z-10">
                <p class="text-xs uppercase text-zinc-500 font-bold tracking-wider mb-1">Total Approved</p>
                <p class="text-2xl font-black text-zinc-800">{{ $approvedCount ?? 0 }}</p>
            </div>
            <div class="absolute -bottom-4 -right-4 w-20 h-20 bg-green-50/50 rounded-full group-hover:bg-green-100 transition-colors z-0"></div>
        </div>

        {{-- Rejected --}}
        <div class="bg-white border border-zinc-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all flex items-center gap-4 relative overflow-hidden group">
            <div class="w-14 h-14 bg-red-50 rounded-xl flex items-center justify-center text-red-600 shadow-sm group-hover:scale-110 transition-transform">
                <i class="bi bi-x-circle text-2xl"></i>
            </div>
            <div class="z-10">
                <p class="text-xs uppercase text-zinc-500 font-bold tracking-wider mb-1">Total Rejected</p>
                <p class="text-2xl font-black text-zinc-800">{{ $rejectedCount ?? 0 }}</p>
            </div>
            <div class="absolute -bottom-4 -right-4 w-20 h-20 bg-red-50/50 rounded-full group-hover:bg-red-100 transition-colors z-0"></div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm text-sm overflow-hidden">
        <div class="p-4 border-b border-zinc-100 flex justify-between items-center bg-zinc-50/50">
            <h5 class="font-bold text-zinc-800 flex items-center gap-2">
                <i class="bi bi-list-check text-lg text-blue-600"></i>
                Daftar Persetujuan Pending
            </h5>
            <div>
                <button type="button" onclick="$('#approvalTable').DataTable().ajax.reload()" 
                        class="text-zinc-500 hover:text-blue-600 transition-colors p-2 rounded-lg hover:bg-zinc-100">
                    <i class="bi bi-arrow-clockwise text-lg"></i>
                </button>
            </div>
        </div>
        
        <div class="p-0">
            <div class="overflow-x-auto">
                {{-- Table --}}
                <table id="approvalTable" class="w-full text-left border-collapse">
                    <thead class="bg-zinc-50 text-zinc-500 uppercase text-xs font-bold leading-normal">
                        <tr>
                            <th class="px-5 py-3 border-b border-zinc-100">#</th>
                            <th class="px-5 py-3 border-b border-zinc-100">Kode Ref</th>
                            <th class="px-5 py-3 border-b border-zinc-100">Dibuat Oleh</th>
                            <th class="px-5 py-3 border-b border-zinc-100">Alasan</th>
                            <th class="px-5 py-3 border-b border-zinc-100 text-center">Produk</th>
                            <th class="px-5 py-3 border-b border-zinc-100">Tanggal</th>
                            <th class="px-5 py-3 border-b border-zinc-100 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-zinc-600 text-sm font-light bg-white divide-y divide-zinc-100"></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('page_styles')
    @include('includes.datatables-flowbite-css')
@endpush

@push('page_scripts')
    @include('includes.datatables-flowbite-js')
    <script>
        $(function() {
            const $table = $('#approvalTable');
            const dt = $table.DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                stateSave: true,
                ajax: "{{ route('adjustments.getPendingAdjustments') }}",
                order: [[5, 'desc']], // Order by Date
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'px-5 py-4 text-center' },
                    { data: 'reference', name: 'reference', className: 'px-5 py-4 font-medium text-zinc-900' },
                    { data: 'requester_name', name: 'requester_name', className: 'px-5 py-4' },
                    { data: 'reason', name: 'reason', className: 'px-5 py-4' },
                    { data: 'product_count', name: 'product_count', className: 'px-5 py-4 text-center' },
                    { data: 'created_at_formatted', name: 'created_at_formatted', className: 'px-5 py-4' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'px-5 py-4 text-center' }
                ]
            });

            // Handle Approve Button Click
            $(document).on('click', '.btn-approve-action', function() {
                const id = $(this).data('id');
                const action = $(this).data('action'); // approve | reject
                const approve = action === 'approve';
                const rowData = dt.row($(this).closest('tr')).data() || {};

                // UI Helper
                const previewHTML = `
                    <div class="text-left bg-zinc-50 p-4 rounded-xl border border-zinc-200 text-sm">
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <span class="text-zinc-500">Kode:</span>
                            <span class="font-mono font-bold text-zinc-800">${rowData.reference || '-'}</span>
                            
                            <span class="text-zinc-500">Oleh:</span>
                            <span class="font-medium text-zinc-800">${rowData.requester_name || '-'}</span>
                            
                            <span class="text-zinc-500">Produk:</span>
                            <span class="font-medium text-zinc-800">${rowData.product_count || '0'} items</span>
                        </div>
                        <div class="pt-2 border-t border-zinc-200 mt-2">
                            <p class="text-zinc-500 text-xs mb-1">Alasan:</p>
                            <p class="italic text-zinc-800">${rowData.reason || '-'}</p>
                        </div>
                    </div>
                    <p class="text-xs text-zinc-500 mt-3 text-center">
                        ${approve ? 'Pastikan stok fisik sudah sesuai sebelum menyetujui.' : 'Tolak ajuan ini jika data tidak valid.'}
                    </p>
                `;

                Swal.fire({
                    title: approve ? 'Setujui Penyesuaian?' : 'Tolak Penyesuaian?',
                    html: previewHTML,
                    icon: approve ? 'question' : 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Lanjut',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: approve ? 'bg-blue-600' : 'bg-red-600',
                        cancelButton: 'bg-zinc-200 text-zinc-800'
                    }
                }).then((firstResult) => {
                    if (!firstResult.isConfirmed) return;

                    // Step 2: Confirmation with Notes
                    Swal.fire({
                        title: approve ? 'Konfirmasi Persetujuan' : 'Konfirmasi Penolakan',
                        input: 'textarea',
                        inputPlaceholder: 'Tulis catatan approval disini (opsional)...',
                        inputAttributes: {
                            'aria-label': 'Catatan approval'
                        },
                        showCancelButton: true,
                        confirmButtonText: approve ? 'Ya, Setujui' : 'Ya, Tolak',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: approve ? '#10b981' : '#ef4444', 
                        showLoaderOnConfirm: true,
                        preConfirm: (notes) => {
                            return $.ajax({
                                url: `/adjustments/${id}/approve`,
                                type: 'POST',
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    action: action,
                                    approval_notes: notes
                                }
                            }).catch(error => {
                                Swal.showValidationMessage(`Request failed: ${error.responseJSON?.message || 'Unknown error'}`);
                            });
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: result.value.message || 'Data berhasil diproses.',
                                icon: 'success'
                            });
                            dt.ajax.reload(null, false);
                        }
                    });
                });
            });
        });
    </script>
@endpush
