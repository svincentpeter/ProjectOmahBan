{{-- 📁 resources/views/owner/dashboard.blade.php --}}

@extends('layouts.app')

@section('title', 'Dashboard Owner')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard Owner</li>
@endsection

@section('content')
<div class="container-fluid">
    
    {{-- 📊 STATISTICS CARDS --}}
    <div class="row mb-4">
        {{-- Notifikasi Belum Dibaca --}}
        <div class="col-md-3">
            <div class="card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Notifikasi Belum Dibaca</h6>
                            <h3 class="mb-0">{{ $stats['unread_notifications'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-bell fs-1 text-primary"></i>
                        </div>
                    </div>
                    @if($stats['critical_count'] > 0)
                        <small class="text-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                            {{ $stats['critical_count'] }} critical
                        </small>
                    @endif
                </div>
            </div>
        </div>

        {{-- Belum Direview --}}
        <div class="col-md-3">
            <div class="card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Belum Direview</h6>
                            <h3 class="mb-0">{{ $stats['unreviewed_count'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-clipboard-check fs-1 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Manual Input Hari Ini --}}
        <div class="col-md-3">
            <div class="card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Manual Input Hari Ini</h6>
                            <h3 class="mb-0">{{ $stats['manual_input_today'] }}</h3>
                            <small class="text-muted">
                                Minggu ini: {{ $stats['manual_input_this_week'] }}
                            </small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-pencil-square fs-1 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Approval --}}
        <div class="col-md-3">
            <div class="card border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Pending Approval</h6>
                            <h3 class="mb-0">{{ $stats['pending_approvals'] }}</h3>
                            @if($stats['critical_pending'] > 0)
                                <small class="text-danger">
                                    🚨 {{ $stats['critical_pending'] }} critical
                                </small>
                            @endif
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-shield-exclamation fs-1 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- 📈 CHART: Manual Input Per Kasir --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up"></i> Manual Input Per Kasir (7 Hari Terakhir)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="manualInputChart" height="80"></canvas>
                </div>
            </div>
        </div>

        {{-- 🔝 TOP 10 ITEMS MANUAL --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-trophy"></i> Top 10 Item Manual (Bulan Ini)
                    </h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @forelse($topManualItems as $index => $item)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                <strong>{{ $item->item_name }}</strong>
                                <br>
                                <small class="text-muted">
                                    {{ ucfirst($item->item_type) }} • 
                                    {{ $item->frequency }}x • 
                                    Total: {{ $item->total_qty }} unit
                                </small>
                            </div>
                            <div class="text-end">
                                <small>Avg: Rp {{ number_format($item->avg_price, 0, ',', '.') }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">Belum ada data</p>
                    @endforelse
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('owner.manual-inputs.summary') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Summary Lengkap
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        {{-- 📋 RECENT NOTIFICATIONS --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bell"></i> Notifikasi Terbaru
                    </h5>
                    <a href="{{ route('owner.notifications.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($recentNotifications as $notif)
                            <li class="list-group-item {{ $notif->is_read ? '' : 'bg-light' }}">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        {!! $notif->getSeverityBadge() !!}
                                        <strong>{{ $notif->title }}</strong>
                                    </div>
                                    <small class="text-muted">
                                        {{ $notif->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <p class="mb-1 text-muted small">{{ Str::limit($notif->message, 100) }}</p>
                                <a href="{{ route('owner.notifications.show', $notif->id) }}" class="btn btn-sm btn-link p-0">
                                    Detail →
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">
                                Tidak ada notifikasi
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        {{-- 🚨 URGENT APPROVALS --}}
        <div class="col-md-6">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-exclamation-triangle"></i> Approval Critical Pending
                    </h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($urgentApprovals as $log)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        {!! $log->getLevelBadge() !!}
                                        <strong>{{ $log->item_name }}</strong>
                                    </div>
                                    <small class="text-muted">
                                        {{ $log->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <p class="mb-1 small">
                                    Kasir: {{ $log->cashier->name }} •
                                    Invoice: <a href="{{ route('owner.manual-inputs.show', $log->sale_id) }}">
                                        {{ $log->sale->reference }}
                                    </a>
                                </p>
                                <p class="mb-1 text-danger small">
                                    Variance: {{ number_format($log->variance_percent, 1) }}%
                                    (Rp {{ number_format($log->variance_amount, 0, ',', '.') }})
                                </p>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">
                                Tidak ada approval pending
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// 📈 Chart: Manual Input Per Kasir
const ctx = document.getElementById('manualInputChart');
const chartData = @json($chartData);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: chartData.labels,
        datasets: chartData.datasets.map((dataset, index) => ({
            label: dataset.label,
            data: dataset.data,
            borderWidth: 2,
            tension: 0.3,
            fill: false
        }))
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            },
            title: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
@endpush

@endsection
