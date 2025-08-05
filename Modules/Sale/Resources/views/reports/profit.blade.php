@extends('layouts.app')

@section('title', 'Laporan Laba Kotor')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item active">Laba Kotor</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">Filter Laporan</div>
            <div class="card-body">
                <form class="form-row" action="{{ route('sales.reports.profit') }}" method="GET">
                    <div class="col-md-5">
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-5">
                        <label for="end_date">Tanggal Selesai</label>
                        <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Penjualan</h5>
                        <p class="card-text h3">{{ format_currency($totalPenjualan) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-warning">
                    <div class="card-body">
                        <h5 class="card-title">Total Modal (HPP)</h5>
                        <p class="card-text h3">{{ format_currency($totalHpp) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-body">
                        <h5 class="card-title">Total Laba Kotor</h5>
                        <p class="card-text h3">{{ format_currency($totalLaba) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Rincian Laba per Jenis Sumber</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Jenis Sumber</th>
                                <th>Jumlah Item Terjual</th>
                                <th>Total Penjualan</th>
                                <th>Total Laba</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($labaBreakdown as $type => $data)
                                <tr>
                                    <td><strong>{{ ucfirst($type) }}</strong></td>
                                    <td>{{ $data['count'] }} item</td>
                                    <td>{{ format_currency($data['total_penjualan']) }}</td>
                                    <td class="text-success font-weight-bold">{{ format_currency($data['total_laba']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data penjualan pada rentang tanggal ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection