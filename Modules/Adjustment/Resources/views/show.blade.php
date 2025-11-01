@extends('layouts.app')

@section('title', 'Detail Penyesuaian Stok')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('adjustments.index') }}">Penyesuaian Stok</a></li>
        <li class="breadcrumb-item active">{{ $adjustment->reference }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="animated fadeIn">
            {{-- Action Bar --}}
            <div class="action-bar shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 font-weight-bold">
                            <i class="cil-file-alt mr-2 text-primary"></i>
                            Detail Penyesuaian: {{ $adjustment->reference }}
                        </h5>
                        <small class="text-muted">Informasi lengkap penyesuaian stok</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('adjustments.index') }}" class="btn btn-outline-secondary">
                            <i class="cil-arrow-left mr-1"></i> Kembali
                        </a>
                        @if ($adjustment->status === 'pending')
                            <a href="{{ route('adjustments.edit', $adjustment->id) }}" class="btn btn-warning">
                                <i class="cil-pencil mr-1"></i> Edit
                            </a>
                        @endif
                        <a href="{{ route('adjustments.pdf', $adjustment->id) }}" target="_blank" class="btn btn-info">
                            <i class="cil-print mr-1"></i> Print PDF
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Main Content --}}
                <div class="col-lg-8">
                    {{-- Products Card --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-list mr-2 text-primary"></i>
                                Daftar Produk yang Disesuaikan
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th width="40%">Produk</th>
                                            <th width="15%" class="text-center">Jumlah</th>
                                            <th width="20%" class="text-center">Tipe</th>
                                            <th width="20%" class="text-right">Stok Saat Ini</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($adjustment->adjustedProducts as $index => $item)
                                            <tr>
                                                <td class="text-center align-middle">
                                                    <span class="badge-index">{{ $index + 1 }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        {{-- ✅ FIX: Ganti product_name ke field yang benar --}}
                                                        <span class="font-weight-semibold">
                                                            {{ $item->product?->product_name ?? ($item->product?->name ?? '-') }}
                                                        </span>
                                                        <div class="d-flex align-items-center mt-1">
                                                            <span class="badge badge-secondary mr-2">
                                                                <i class="cil-barcode mr-1"></i>
                                                                {{ $item->product?->product_code ?? 'N/A' }}
                                                            </span>
                                                            @if ($item->product?->category)
                                                                <small class="text-muted">
                                                                    <i class="cil-tag mr-1"></i>
                                                                    {{ $item->product->category->category_name }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    {{-- ✅ FIX: Tampilkan simbol +/- saja (tanpa 'add'/'sub') --}}
                                                    <span
                                                        class="badge-contrast {{ $item->type == 'add' ? 'success' : 'danger' }}">
                                                        {{ $item->type == 'add' ? '+' : '-' }}{{ $item->quantity }}
                                                    </span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    @if ($item->type == 'add')
                                                        <span class="chip add"><i class="cil-plus"></i> Tambah</span>
                                                    @else
                                                        <span class="chip sub"><i class="cil-minus"></i> Kurang</span>
                                                    @endif
                                                </td>
                                                <td class="text-right align-middle">
                                                    {{-- ✅ Ganti current_stock ke product_quantity --}}
                                                    <span class="badge-contrast info">
                                                        {{ $item->product?->product_quantity ?? 0 }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <div class="empty-state">
                                                        <i class="cil-inbox" style="font-size: 3rem; color: #e2e8f0;"></i>
                                                        <p class="text-muted mt-2 mb-0">Tidak ada produk yang disesuaikan
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Foto Bukti Card (NEW) --}}
                    @if ($adjustment->adjustmentFiles->count() > 0)
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="cil-image mr-2 text-primary"></i>
                                    Foto Bukti ({{ $adjustment->adjustmentFiles->count() }})
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @foreach ($adjustment->adjustmentFiles as $file)
                                        <div class="col-md-4 col-sm-6">
                                            <div class="position-relative border rounded overflow-hidden"
                                                style="aspect-ratio: 1;">
                                                <img src="{{ asset('storage/' . $file->file_path) }}" alt="Bukti"
                                                    class="img-fluid w-100 h-100 object-fit-cover" data-toggle="modal"
                                                    data-target="#imageModal{{ $file->id }}"
                                                    style="cursor: pointer; object-fit: cover;">
                                            </div>
                                        </div>

                                        {{-- Modal Foto Besar --}}
                                        <div class="modal fade" id="imageModal{{ $file->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{ $file->file_name }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="{{ asset('storage/' . $file->file_path) }}"
                                                            alt="Bukti" class="img-fluid">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Notes Card (if exists) --}}
                    @if ($adjustment->note || $adjustment->description)
                        <div class="card shadow-sm">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="cil-notes mr-2 text-primary"></i>
                                    Catatan & Deskripsi
                                </h6>
                            </div>
                            <div class="card-body">
                                @if ($adjustment->note)
                                    <div class="alert alert-info mb-3" role="alert">
                                        <div class="d-flex align-items-start">
                                            <i class="cil-info mr-2 mt-1" style="font-size: 1.25rem;"></i>
                                            <div>
                                                <strong>Catatan:</strong>
                                                <p class="mb-0 mt-1">{{ $adjustment->note }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if ($adjustment->description)
                                    <div class="alert alert-light mb-0" role="alert">
                                        <div class="d-flex align-items-start">
                                            <i class="cil-info mr-2 mt-1" style="font-size: 1.25rem;"></i>
                                            <div>
                                                <strong>Deskripsi:</strong>
                                                <p class="mb-0 mt-1">{{ $adjustment->description }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    {{-- Status Badge --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-4 text-center">
                            <span
                                class="status-badge
  {{ $adjustment->status === 'approved'
      ? 'status-approved'
      : ($adjustment->status === 'rejected'
          ? 'status-rejected'
          : 'status-pending') }}">
                                {{ strtoupper($adjustment->status) }}
                            </span>

                        </div>
                    </div>

                    {{-- Summary Info Card --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-info mr-2 text-primary"></i>
                                Informasi Penyesuaian
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            {{-- Date --}}
                            <div class="info-item mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="cil-calendar text-primary mr-2" style="font-size: 1.25rem;"></i>
                                    <span class="text-muted small">Tanggal</span>
                                </div>
                                <h6 class="mb-0 ml-4">{{ \Carbon\Carbon::parse($adjustment->date)->format('d F Y') }}</h6>
                            </div>

                            <hr class="my-3">

                            {{-- Reference --}}
                            <div class="info-item mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="cil-barcode text-primary mr-2" style="font-size: 1.25rem;"></i>
                                    <span class="text-muted small">Referensi</span>
                                </div>
                                <h6 class="mb-0 ml-4">
                                    <code class="bg-light px-2 py-1"
                                        style="font-size: 1rem;">{{ $adjustment->reference }}</code>
                                </h6>
                            </div>

                            <hr class="my-3">

                            {{-- Total Products --}}
                            <div class="info-item">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="cil-layers text-primary mr-2" style="font-size: 1.25rem;"></i>
                                    <span class="text-muted small">Total Produk</span>
                                </div>
                                <h6 class="mb-0 ml-4">{{ $adjustment->adjustedProducts->count() }} Produk</h6>
                            </div>
                        </div>
                    </div>

                    {{-- Statistics Cards --}}
                    @if ($adjustment->adjustedProducts->count() > 0)
                        {{-- Addition Stats --}}
                        <div class="card shadow-sm mb-3">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-success-light text-success mr-3">
                                        <i class="cil-arrow-circle-top" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1 text-muted small">Penambahan</p>
                                        <h4 class="mb-0 font-weight-bold text-success">
                                            {{ $adjustment->adjustedProducts->where('type', 'add')->count() }} Produk
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Subtraction Stats --}}
                        <div class="card shadow-sm mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-danger-light text-danger mr-3">
                                        <i class="cil-arrow-circle-bottom" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1 text-muted small">Pengurangan</p>
                                        <h4 class="mb-0 font-weight-bold text-danger">
                                            {{ $adjustment->adjustedProducts->where('type', 'sub')->count() }} Produk
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Approval Section (NEW) --}}
                    @if ($adjustment->status === 'pending' && Auth::user()->can('approve_adjustments'))
                        <div class="card shadow-sm border-warning">
                            <div class="card-header bg-warning text-white py-3">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="cil-check-circle mr-2"></i>
                                    Approval Penyesuaian
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <form id="approvalForm" method="POST"
                                    action="{{ route('adjustments.approve', $adjustment->id) }}">
                                    @csrf
                                    {{-- hidden agar bisa diisi JS --}}
                                    <input type="hidden" name="action" value="approve">

                                    <div class="form-group mb-3">
                                        <label class="form-label">Catatan Approval</label>
                                        <textarea class="form-control" name="approval_notes" rows="3" placeholder="Masukkan catatan (opsional)"></textarea>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-success flex-fill js-approve">
                                            <i class="cil-check-circle mr-1"></i> Setujui
                                        </button>
                                        <button type="button" class="btn btn-danger flex-fill js-reject">
                                            <i class="cil-x-circle mr-1"></i> Tolak
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @elseif($adjustment->status !== 'pending')
                        {{-- Show Approval Info --}}
                        <div class="card shadow-sm">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="cil-check-circle mr-2 text-primary"></i>
                                    Info Approval
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                @if ($adjustment->approver)
                                    <div class="info-item mb-3">
                                        <span class="text-muted small">Disetujui oleh:</span>
                                        <h6 class="mb-0">{{ $adjustment->approver->name }}</h6>
                                    </div>

                                    <hr class="my-3">

                                    <div class="info-item mb-3">
                                        <span class="text-muted small">Tanggal Approval:</span>
                                        <h6 class="mb-0">{{ $adjustment->approval_date?->format('d F Y H:i') ?? '-' }}
                                        </h6>
                                    </div>

                                    @if ($adjustment->approval_notes)
                                        <hr class="my-3">
                                        <div class="info-item">
                                            <span class="text-muted small">Catatan:</span>
                                            <p class="mb-0 mt-1">{{ $adjustment->approval_notes }}</p>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Activity Log --}}
                    <div class="card shadow-sm mt-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="cil-history mr-2 text-primary"></i>
                                Riwayat Aktivitas
                            </h6>
                        </div>
                        <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                            @forelse($adjustment->logs as $log)
                                <div class="px-4 py-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span
                                            class="badge badge-{{ $log->action === 'create' ? 'primary' : ($log->action === 'update' ? 'info' : ($log->action === 'approved' ? 'success' : 'danger')) }}">
                                            {{ strtoupper($log->action) }}
                                        </span>
                                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1 small"><strong>{{ $log->user->name ?? 'System' }}</strong></p>
                                    @if ($log->notes)
                                        <p class="mb-0 small text-muted">{{ $log->notes }}</p>
                                    @endif
                                </div>
                            @empty
                                <div class="p-4 text-center">
                                    <p class="text-muted small mb-0">Tidak ada aktivitas</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('page_scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('approvalForm');
            if (!form) return;

            const actionInput = form.querySelector('input[name="action"]');
            const notesInput = form.querySelector('textarea[name="approval_notes"]');
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const runApproval = async (actionValue) => {
                const label = actionValue === 'approve' ? 'Setujui' : 'Tolak';
                const icon = actionValue === 'approve' ? 'question' : 'warning';
                const text = actionValue === 'approve' ?
                    'Anda yakin ingin MENYETUJUI penyesuaian ini?' :
                    'Anda yakin ingin MENOLAK penyesuaian ini?';

                const {
                    isConfirmed
                } = await Swal.fire({
                    icon,
                    title: label + ' Penyesuaian?',
                    text,
                    showCancelButton: true,
                    confirmButtonText: label,
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                });

                if (!isConfirmed) return;

                // ✅ FIX: Set value dulu SEBELUM kirim
                actionInput.value = actionValue;

                // ✅ Kirim via fetch JSON
                const payload = {
                    action: actionValue, // ✅ Kirim string value, bukan element
                    approval_notes: notesInput.value || ''
                };

                try {
                    const resp = await fetch(form.getAttribute('action'), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(payload) // ✅ Kirim JSON langsung
                    });

                    const data = await resp.json();

                    if (resp.ok && data?.success) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message || 'Approval berhasil diproses',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        window.location.href = data.redirect ||
                            '{{ route('adjustments.show', $adjustment->id) }}';
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data?.message || 'Terjadi kesalahan saat memproses approval'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Tidak dapat terhubung ke server'
                    });
                }
            };

            // ✅ Bind tombol
            form.querySelector('.js-approve')?.addEventListener('click', () => runApproval('approve'));
            form.querySelector('.js-reject')?.addEventListener('click', () => runApproval('reject'));
        });
    </script>
@endpush
@push('page_styles')
    <style>
        /* ====== Palet kuat & variabel ====== */
        :root {
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-600: #475569;
            --sky-700: #0369a1;
            --sky-50: #f0f9ff;
            --emerald-700: #047857;
            --emerald-50: #ecfdf5;
            --rose-700: #b91c1c;
            --rose-50: #fff1f2;
            --amber-700: #b45309;
            --amber-50: #fffbeb;
            --violet-50: #eef2ff;
            --border: #e2e8f0;
            --muted: #475569;
            --muted-weak: #64748b;
            --table-head: #f1f5f9;
        }

        /* ====== Umum ====== */
        .animated.fadeIn {
            animation: fadeIn .3s ease-in
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .action-bar {
            background: #fff;
            padding: 1.25rem;
            border-radius: 10px
        }

        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08) !important
        }

        .card {
            border: none;
            border-radius: 10px;
            overflow: hidden
        }

        /* ====== Tabel ====== */
        .table {
            margin-bottom: 0
        }

        .table th {
            font-weight: 700;
            font-size: .875rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--slate-800);
            background: var(--table-head);
            border-top: 1px solid var(--border)
        }

        .table td {
            padding: 1rem;
            vertical-align: middle
        }

        .table-hover tbody tr:hover {
            background: var(--violet-50)
        }

        /* ====== Teks & ikon ====== */
        .text-muted {
            color: var(--muted) !important
        }

        .info-item i {
            opacity: .9
        }

        /* ====== Badge indeks (angka # kolom) ====== */
        .badge-index {
            display: inline-block;
            padding: .35rem .55rem;
            border-radius: .5rem;
            background: var(--slate-700);
            color: #fff;
            font-weight: 700
        }

        /* ====== Badge kuantitas/type dengan kontras tinggi ====== */
        .badge-contrast {
            padding: .4rem .65rem;
            border-radius: .5rem;
            font-weight: 700;
            color: #fff
        }

        .badge-contrast.success {
            background: var(--emerald-700)
        }

        .badge-contrast.danger {
            background: var(--rose-700)
        }

        .badge-contrast.info {
            background: var(--sky-700)
        }

        .badge-contrast.warning {
            background: var(--amber-700)
        }

        /* Tanda kecil pada kolom Tipe */
        .chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .25rem .5rem;
            border-radius: .5rem;
            font-weight: 600
        }

        .chip.add {
            background: var(--emerald-50);
            color: var(--emerald-700);
            border: 1px solid rgba(4, 120, 87, .25)
        }

        .chip.sub {
            background: var(--rose-50);
            color: var(--rose-700);
            border: 1px solid rgba(185, 28, 28, .25)
        }

        /* ====== Badge Status besar ====== */
        .status-badge {
            font-size: 1rem;
            padding: .75rem 1.25rem;
            font-weight: 800;
            border-radius: .75rem;
            color: #fff
        }

        .status-pending {
            background: var(--amber-700)
        }

        .status-approved {
            background: var(--emerald-700)
        }

        .status-rejected {
            background: var(--rose-700)
        }

        /* ====== Empty state ====== */
        .empty-state {
            padding: 2rem 0;
            color: var(--muted)
        }

        .empty-state i {
            color: var(--muted-weak)
        }

        /* ====== Form focus ====== */
        .form-control {
            border-radius: 6px;
            border: 1px solid var(--border)
        }

        .form-control:focus {
            border-color: var(--sky-700);
            box-shadow: 0 0 0 .2rem rgba(3, 105, 161, .25)
        }

        /* ====== Responsif ====== */
        @media (max-width:992px) {
            .action-bar .d-flex {
                flex-direction: column;
                gap: 1rem
            }

            .action-bar .d-flex>div {
                width: 100%
            }

            .col-lg-4 {
                margin-top: 1rem
            }
        }

        @media (max-width:768px) {
            .table-responsive {
                font-size: .9rem
            }

            .badge-contrast {
                font-size: .8rem
            }
        }
    </style>
@endpush
