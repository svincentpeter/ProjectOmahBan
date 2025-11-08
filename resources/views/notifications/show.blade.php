@extends('layouts.app')

@section('title', 'Detail Notifikasi')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('notifications.index') }}">Notifications</a></li>
        <li class="breadcrumb-item active">Detail</li>
    </ol>
@endsection

@section('content')
    @php
        // Warna severity: danger | warning | info | success | primary, dsb.
        $sev = $notification->getSeverityColor();
    @endphp

    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                {{-- ===== Main Content ===== --}}
                <div class="col-lg-8 mb-3">
                    <div class="card shadow-sm border-left border-4 border-{{ $sev }}">
                        {{-- Header putih (seragam) --}}
                        <div class="card-header bg-white py-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div>
                                    <h5 class="mb-1 font-weight-bold">
                                        <i class="cil-bell mr-2 text-primary"></i>
                                        {{ $notification->title }}
                                    </h5>
                                    <small class="text-muted d-inline-flex align-items-center">
                                        <i class="cil-clock mr-1"></i>
                                        {{ $notification->created_at->format('d M Y H:i:s') }}
                                        <span class="mx-1">•</span>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="text-nowrap">
                                    {!! $notification->getSeverityBadge() !!}
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            {{-- Pesan --}}
                            <h6 class="mb-2 fw-semibold">Pesan</h6>
                            <pre class="code-block">{!! nl2br(e($notification->message)) !!}</pre> {{-- ✅ escape dulu --}}


                            {{-- Transaksi terkait (opsional) --}}
                            @if ($notification->sale_id && $notification->sale)
                                <div class="alert alert-info d-flex align-items-start mt-4" role="alert">
                                    <i class="cil-receipt mr-2 mt-1"></i>
                                    <div>
                                        <div class="fw-semibold mb-1">Transaksi Terkait</div>
                                        <a href="{{ url('/sales/' . $notification->sale_id) }}"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="cil-magnifying-glass mr-1"></i>
                                            Lihat Transaksi #{{ $notification->sale->reference ?? $notification->sale_id }}
                                        </a>
                                    </div>
                                </div>
                            @endif


                            {{-- Aksi utama --}}
                            <div class="mt-3 pt-3 border-top d-flex flex-wrap gap-2">
                                <a href="{{ route('notifications.index') }}" class="btn btn-secondary">
                                    <i class="cil-arrow-left mr-1"></i> Kembali
                                </a>

                                @if (!$notification->is_read)
                                    <form action="{{ route('notifications.mark-as-read', $notification->id) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="cil-check-alt mr-1"></i> Tandai Sudah Dibaca
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Hapus notifikasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="cil-trash mr-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== Sidebar ===== --}}
                <div class="col-lg-4">

                    {{-- Info Card (existing) --}}
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0">
                                <i class="cil-info mr-2 text-primary"></i>
                                Informasi
                            </h6>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-5 font-weight-semibold">Status Baca</dt>
                                <dd class="col-sm-7 mb-2">
                                    @if ($notification->is_read)
                                        <span class="badge bg-secondary">
                                            <i class="cil-check-circle mr-1"></i>Sudah Dibaca
                                        </span>
                                    @else
                                        <span class="badge bg-primary">
                                            <i class="cil-bell mr-1"></i>Belum Dibaca
                                        </span>
                                    @endif
                                </dd>

                                <dt class="col-sm-5 font-weight-semibold">Severity</dt>
                                <dd class="col-sm-7 mb-2">
                                    {!! $notification->getSeverityBadge() !!}
                                </dd>

                                <dt class="col-sm-5 font-weight-semibold">Dibuat</dt>
                                <dd class="col-sm-7 mb-2">
                                    <small class="text-muted">{{ $notification->created_at->format('d M Y H:i') }}</small>
                                </dd>

                                @if ($notification->is_read)
                                    <dt class="col-sm-5 font-weight-semibold">Dibaca</dt>
                                    <dd class="col-sm-7">
                                        <small class="text-muted">
                                            {{ $notification->read_at?->format('d M Y H:i') ?? '-' }}
                                        </small>
                                    </dd>
                                @endif
                            </dl>
                        </div>
                    </div>

                    {{-- ⭐ NEW: Review Notifikasi Card --}}
                    @if (!$notification->is_reviewed)
                        <div class="card shadow-sm mb-3 border-left border-4 border-primary">
                            <div class="card-header bg-primary text-white py-3 border-bottom">
                                <h6 class="mb-0">
                                    <i class="cil-task mr-2"></i>
                                    Review Notifikasi
                                </h6>
                            </div>
                            <div class="card-body">
                                <form id="reviewForm" method="POST"
                                    action="{{ route('notifications.mark-as-reviewed', $notification->id) }}">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="reviewNotes" class="form-label font-weight-semibold">
                                            Catatan Review
                                        </label>
                                        <textarea class="form-control @error('review_notes') is-invalid @enderror" id="reviewNotes" name="review_notes"
                                            rows="4" placeholder="Masukkan catatan review Anda..."></textarea>
                                        @error('review_notes')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted d-block mt-1">Maksimal 500 karakter</small>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="cil-check-alt mr-1"></i> Tandai Direview
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        {{-- ✓ Status Sudah Direview --}}
                        <div class="card shadow-sm mb-3 border-left border-4 border-success">
                            <div class="card-header bg-success text-white py-3 border-bottom">
                                <h6 class="mb-0">
                                    <i class="cil-check-circle mr-2"></i>
                                    Sudah Direview
                                </h6>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0 small">
                                    <dt class="col-sm-5 font-weight-semibold">Oleh</dt>
                                    <dd class="col-sm-7 mb-2">
                                        @if ($notification->reviewer)
                                            {{ $notification->reviewer->name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </dd>

                                    <dt class="col-sm-5 font-weight-semibold">Tanggal</dt>
                                    <dd class="col-sm-7 mb-2">
                                        {{ $notification->reviewed_at?->format('d M Y H:i') ?? '-' }}
                                    </dd>

                                    @if ($notification->review_notes)
                                        <dt class="col-sm-5 font-weight-semibold">Catatan</dt>
                                        <dd class="col-sm-7">
                                            <div class="bg-light p-2 rounded"
                                                style="max-height: 150px; overflow-y: auto;">
                                                {{ $notification->review_notes }}
                                            </div>
                                        </dd>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    @endif

                    {{-- ⭐ NEW: Fontee Status Card (jika ada) --}}
                    @if ($notification->fontee_message_id)
                        <div
                            class="card shadow-sm border-left border-4 
                            {{ $notification->fontee_status === 'failed' ? 'border-danger' : 'border-success' }}">
                            <div class="card-header bg-light py-3 border-bottom">
                                <h6 class="mb-0">
                                    <i class="cil-chat-bubble mr-2 text-success"></i>
                                    Status Fontee (WhatsApp)
                                </h6>
                            </div>
                            <div class="card-body small">
                                <dl class="row mb-0">
                                    <dt class="col-sm-5 font-weight-semibold">Status</dt>
                                    <dd class="col-sm-7 mb-2">
                                        <span
                                            class="badge bg-{{ $notification->fontee_status === 'sent'
                                                ? 'info'
                                                : ($notification->fontee_status === 'read'
                                                    ? 'success'
                                                    : ($notification->fontee_status === 'failed'
                                                        ? 'danger'
                                                        : 'warning')) }}">
                                            @if ($notification->fontee_status === 'sent')
                                                <i class="cil-check mr-1"></i>Terkirim
                                            @elseif($notification->fontee_status === 'read')
                                                <i class="cil-check-circle mr-1"></i>Dibaca
                                            @elseif($notification->fontee_status === 'failed')
                                                <i class="cil-x mr-1"></i>Gagal
                                            @else
                                                <i class="cil-clock mr-1"></i>Pending
                                            @endif
                                        </span>
                                    </dd>

                                    <dt class="col-sm-5 font-weight-semibold">Message ID</dt>
                                    <dd class="col-sm-7 mb-2">
                                        <code class="bg-light p-1 rounded d-inline-block" style="font-size: 11px;">
                                            {{ substr($notification->fontee_message_id, 0, 20) }}...
                                        </code>
                                    </dd>

                                    @if ($notification->fontee_sent_at)
                                        <dt class="col-sm-5 font-weight-semibold">Dikirim</dt>
                                        <dd class="col-sm-7 mb-2">
                                            <small class="text-muted">
                                                {{ $notification->fontee_sent_at->format('d M Y H:i:s') }}
                                            </small>
                                        </dd>
                                    @endif

                                    @if ($notification->fontee_error_message)
                                        <dt class="col-sm-5 font-weight-semibold">Error</dt>
                                        <dd class="col-sm-7">
                                            <div class="alert alert-danger mb-0 p-2" style="font-size: 12px;">
                                                {{ $notification->fontee_error_message }}
                                            </div>
                                        </dd>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    @endif

                </div>
            </div> {{-- /row --}}
        </div>
    </div>
@endsection

@push('page_styles')
    <style>
        /* Animasi & shadow (seragam dengan halaman Jasa) */
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

        .shadow-sm {
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08) !important
        }

        /* Border-left util (dipakai di card utama) */
        .border-left {
            border-left: 4px solid !important
        }

        /* Code block untuk pesan (terbaca jelas) */
        .code-block {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 14px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            white-space: pre-wrap;
            word-break: break-word;
            color: #2d3748;
        }

        /* Tipografi kecil */
        .font-weight-semibold {
            font-weight: 600
        }

        /* Custom style untuk form review */
        #reviewForm .form-control {
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        #reviewForm .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        #reviewForm .form-control.is-invalid {
            border-color: #dc3545;
        }

        /* Scrollable untuk review notes */
        .scrollable-notes {
            max-height: 120px;
            overflow-y: auto;
            background: #f8f9fa;
            border-radius: 6px;
            padding: 8px;
        }
    </style>
@endpush

@push('page_scripts')
    <script>
        document.getElementById('reviewForm')?.addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;

            // Disable & loading state
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

            // Submit form (biarkan normal submit untuk error handling)
            setTimeout(() => {
                this.submit();
            }, 300);
        });
    </script>
@endpush
