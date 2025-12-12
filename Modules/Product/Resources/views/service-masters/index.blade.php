@extends('layouts.app-flowbite')

@section('title', 'Master Data Jasa')

@section('breadcrumb')
    @include('layouts.breadcrumb-flowbite', ['items' => [
        ['text' => 'Manajemen Produk', 'url' => '#'],
        ['text' => 'Master Jasa', 'url' => route('service-masters.index'), 'icon' => 'bi bi-wrench']
    ]])
@endsection

@section('content')
    {{-- Alerts --}}
    @include('utils.alerts')

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Total Jasa --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl p-6 text-white shadow-lg shadow-purple-200 transform transition-all hover:scale-[1.02]">
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white shadow-inner">
                    <i class="bi bi-wrench text-2xl"></i>
                </div>
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">Total Jasa</p>
                    <p class="text-3xl font-bold">{{ \Modules\Product\Entities\ServiceMaster::count() }}</p>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        {{-- Aktif --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-6 text-white shadow-lg shadow-teal-200 transform transition-all hover:scale-[1.02] cursor-pointer" onclick="window.location.href='{{ route('service-masters.index', ['quick' => 'active']) }}'">
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white shadow-inner">
                    <i class="bi bi-check-circle text-2xl"></i>
                </div>
                <div>
                    <p class="text-emerald-100 text-sm font-medium mb-1">Aktif</p>
                    <p class="text-3xl font-bold">{{ \Modules\Product\Entities\ServiceMaster::where('status', 1)->count() }}</p>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        {{-- Nonaktif --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg shadow-orange-200 transform transition-all hover:scale-[1.02] cursor-pointer" onclick="window.location.href='{{ route('service-masters.index', ['quick' => 'inactive']) }}'">
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white shadow-inner">
                    <i class="bi bi-x-circle text-2xl"></i>
                </div>
                <div>
                    <p class="text-amber-100 text-sm font-medium mb-1">Nonaktif</p>
                    <p class="text-3xl font-bold">{{ \Modules\Product\Entities\ServiceMaster::where('status', 0)->count() }}</p>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="bg-white border border-slate-100 rounded-2xl shadow-xl shadow-slate-200/50 dark:bg-gray-800 dark:border-gray-700">
        
        {{-- Card Header --}}
        <div class="p-6 border-b border-zinc-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <h5 class="text-xl font-bold text-black dark:text-white tracking-tight flex items-center gap-2">
                        <i class="bi bi-wrench text-purple-600"></i>
                        Daftar Jasa
                    </h5>
                    <p class="text-sm text-zinc-600 mt-1">Kelola master jasa & harga standar</p>
                </div>
                
                <button type="button" onclick="openModal('modal-add-service')"
                        class="inline-flex items-center text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 px-5 py-2.5 rounded-xl transition-all shadow-sm hover:shadow">
                    <i class="bi bi-plus-lg me-2"></i> Tambah Jasa
                </button>
            </div>

            {{-- Global Filter Component --}}
            @include('layouts.filter-card', [
                'action' => route('service-masters.index'),
                'title' => 'Filter Data',
                'icon' => 'bi bi-funnel',
                'quickFilters' => [
                    [
                        'label' => 'Semua Jasa',
                        'url' => route('service-masters.index', ['quick' => 'all']),
                        'param' => 'quick',
                        'value' => 'all',
                        'icon' => 'bi bi-grid'
                    ],
                    [
                        'label' => 'Aktif',
                        'url' => route('service-masters.index', ['quick' => 'active']),
                        'param' => 'quick',
                        'value' => 'active',
                        'icon' => 'bi bi-check-circle'
                    ],
                    [
                        'label' => 'Nonaktif',
                        'url' => route('service-masters.index', ['quick' => 'inactive']),
                        'param' => 'quick',
                        'value' => 'inactive',
                        'icon' => 'bi bi-x-circle'
                    ]
                ],
                'filters' => []
            ])
        </div>

        {{-- DataTable --}}
        <div class="p-6 overflow-x-auto">
            {!! $dataTable->table(['class' => 'w-full text-sm text-left', 'id' => 'service-masters-table']) !!}
        </div>
    </div>
@endsection

{{-- MODAL PARTIALS --}}
@include('product::service-masters.partials._modal-add')
@include('product::service-masters.partials._modal-edit')
@include('product::service-masters.partials._modal-delete')

@push('page_styles')
<style>
    @include('includes.datatables-flowbite-css')
</style>
@endpush

@push('page_scripts')
@include('includes.datatables-flowbite-js')
{!! $dataTable->scripts() !!}
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.10.5/dist/autoNumeric.min.js"></script>
<script>
// Modal Helper Functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
}

// Close modal on backdrop click
document.querySelectorAll('[id^="modal-"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });
});

// AutoNumeric Configuration
const AN_OPS_IDR = {
    digitGroupSeparator: '.',
    decimalCharacter: ',',
    decimalPlaces: 0,
    minimumValue: '0',
    maximumValue: '999999999',
    modifyValueOnWheel: false
};

let anAddPrice, anEditPrice;

$(function() {
    // Initialize AutoNumeric
    anAddPrice = new AutoNumeric('#addStandardPrice', AN_OPS_IDR);
    anEditPrice = new AutoNumeric('#editStandardPrice', AN_OPS_IDR);


    // EDIT Button Click Handler
    $(document).on('click', '.btn-edit', function(e) {
        e.preventDefault();
        const btn = $(this);
        const id = btn.data('id');
        const name = btn.data('name') || '';
        const price = parseInt(btn.data('price')) || 0;
        const category = btn.data('category') || 'service';
        const description = btn.data('description') || '';

        // Set form action
        const actionUrl = '{{ route("service-masters.update", ":id") }}'.replace(':id', id);
        $('#editServiceForm').attr('action', actionUrl);

        // Populate fields
        $('#editServiceName').val(name);
        $('#editCategory').val(category);
        $('#editDescription').val(description);

        // Price display
        const fIDR = (v) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v);
        $('#oldPriceDisplay').text(fIDR(price));
        $('#editServiceForm').data('oldPrice', price);
        anEditPrice.set(price);

        // Reset price change alert
        $('#priceChangeAlert').addClass('hidden');

        openModal('modal-edit-service');
    });

    // DELETE Button Click Handler
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        const btn = $(this);
        const id = btn.data('id');
        const name = btn.data('name');
        const price = parseInt(btn.data('price')) || 0;
        const category = btn.data('category') || '-';

        // Set form action
        const actionUrl = '{{ route("service-masters.destroy", ":id") }}'.replace(':id', id);
        $('#deleteServiceForm').attr('action', actionUrl);

        // Populate display
        $('#deleteServiceName').text(name);
        const fIDR = (v) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v);
        $('#deleteServicePrice').text(fIDR(price));

        const categoryMap = { 'service': 'Service', 'goods': 'Goods', 'custom': 'Custom' };
        $('#deleteServiceCategory').text(categoryMap[category] || category);

        // Reset checkbox
        $('#confirmDelete').prop('checked', false);
        $('#btnConfirmDelete').prop('disabled', true);

        openModal('modal-delete-service');
    });

    // Delete confirmation checkbox
    $('#confirmDelete').on('change', function() {
        $('#btnConfirmDelete').prop('disabled', !this.checked);
    });

    // Price change detection for Edit
    $('#editStandardPrice').on('input change keyup', function() {
        const oldPrice = parseInt($('#editServiceForm').data('oldPrice')) || 0;
        const newPrice = anEditPrice.getNumber() || 0;

        if (oldPrice !== newPrice) {
            const diff = newPrice - oldPrice;
            const percent = oldPrice > 0 ? ((diff / oldPrice) * 100).toFixed(1) : 0;
            const fIDR = (v) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v);

            $('#priceChangeOld').text(fIDR(oldPrice));
            $('#priceChangeNew').text(fIDR(newPrice));
            $('#priceChangePercent').text((diff > 0 ? '+' : '') + percent + '%')
                .removeClass('text-red-600 text-emerald-600')
                .addClass(diff > 0 ? 'text-red-600' : 'text-emerald-600');
            $('#priceChangeAlert').removeClass('hidden');
        } else {
            $('#priceChangeAlert').addClass('hidden');
        }
    });

    // ADD Form Submit
    $('#formAddService').on('submit', function(e) {
        e.preventDefault();
        $('#addStandardPrice').val(anAddPrice.getNumericString());

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: new FormData(this),
            contentType: false,
            processData: false,
            headers: { 'Accept': 'application/json' },
            success: function() {
                closeModal('modal-add-service');
                Swal.fire({ title: 'Sukses!', text: 'Jasa berhasil ditambahkan', icon: 'success' })
                    .then(() => {
                        if (window.LaravelDataTables && window.LaravelDataTables['service-masters-table']) {
                            window.LaravelDataTables['service-masters-table'].ajax.reload();
                        }
                        document.getElementById('formAddService').reset();
                        anAddPrice.set(0);
                    });
            },
            error: function(xhr) {
                Swal.fire({ title: 'Error!', text: xhr.responseJSON?.message || 'Terjadi kesalahan', icon: 'error' });
            }
        });
    });

    // EDIT Form Submit
    $('#editServiceForm').on('submit', function(e) {
        e.preventDefault();
        $('#editStandardPrice').val(anEditPrice.getNumericString());

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: new FormData(this),
            contentType: false,
            processData: false,
            headers: { 'Accept': 'application/json' },
            success: function() {
                closeModal('modal-edit-service');
                Swal.fire({ title: 'Sukses!', text: 'Jasa berhasil diperbarui', icon: 'success' })
                    .then(() => {
                        if (window.LaravelDataTables && window.LaravelDataTables['service-masters-table']) {
                            window.LaravelDataTables['service-masters-table'].ajax.reload();
                        }
                    });
            },
            error: function(xhr) {
                Swal.fire({ title: 'Error!', text: xhr.responseJSON?.message || 'Terjadi kesalahan', icon: 'error' });
            }
        });
    });

    // DELETE Form Submit
    $('#deleteServiceForm').on('submit', function(e) {
        e.preventDefault();
        if (!$('#confirmDelete').is(':checked')) {
            Swal.fire({ title: 'Konfirmasi Gagal', text: 'Anda harus mencentang checkbox', icon: 'warning' });
            return;
        }

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: new FormData(this),
            contentType: false,
            processData: false,
            headers: { 'Accept': 'application/json' },
            success: function() {
                closeModal('modal-delete-service');
                Swal.fire({ title: 'Sukses!', text: 'Jasa berhasil dihapus', icon: 'success' })
                    .then(() => {
                        if (window.LaravelDataTables && window.LaravelDataTables['service-masters-table']) {
                            window.LaravelDataTables['service-masters-table'].ajax.reload();
                        }
                    });
            },
            error: function(xhr) {
                Swal.fire({ title: 'Error!', text: xhr.responseJSON?.message || 'Terjadi kesalahan', icon: 'error' });
            }
        });
    });
});
</script>
@endpush
