<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $adjustment->reference }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.5; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { font-size: 18px; margin-bottom: 5px; }
        .header p { font-size: 11px; color: #666; }
        .section { margin-bottom: 15px; }
        .section-title { font-size: 13px; font-weight: bold; margin-bottom: 8px; border-bottom: 1px solid #ccc; padding-bottom: 3px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table td { padding: 5px; vertical-align: top; }
        .info-table td:first-child { width: 150px; font-weight: bold; }
        .products-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .products-table th, .products-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .products-table th { background-color: #f0f0f0; font-weight: bold; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-danger { background: #dc3545; color: white; }
        .footer { margin-top: 30px; font-size: 10px; color: #999; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; }
    </style>
</head>
<body>
    {{-- HEADER --}}
    <div class="header">
        <h2>FORMULIR PENYESUAIAN STOK</h2>
        <p>{{ config('app.name', 'POS System') }} | Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    {{-- INFO ADJUSTMENT --}}
    <div class="section">
        <div class="section-title">Informasi Pengajuan</div>
        <table class="info-table">
            <tr>
                <td>Referensi:</td>
                <td><strong>{{ $adjustment->reference }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal:</td>
                <td>{{ $adjustment->date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Pemohon:</td>
                <td>{{ $adjustment->requester->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Status:</td>
                <td>
                    @if ($adjustment->status === 'approved')
                        <span class="badge badge-success">DISETUJUI</span>
                    @elseif ($adjustment->status === 'rejected')
                        <span class="badge badge-danger">DITOLAK</span>
                    @else
                        <span class="badge badge-warning">PENDING</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Alasan:</td>
                <td>{{ $adjustment->reason ?? '-' }}</td>
            </tr>
            <tr>
                <td>Keterangan:</td>
                <td>{{ $adjustment->description ?? '-' }}</td>
            </tr>
            @if ($adjustment->approver_id)
            <tr>
                <td>Approver:</td>
                <td>{{ $adjustment->approver->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tgl Approval:</td>
                <td>{{ $adjustment->approval_date ? $adjustment->approval_date->format('d/m/Y H:i') : '-' }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- PRODUK YANG DISESUAIKAN --}}
    <div class="section">
        <div class="section-title">Daftar Produk</div>
        <table class="products-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="45%">Produk</th>
                    <th width="15%">Jumlah</th>
                    <th width="15%">Tipe</th>
                    <th width="20%">Stok Saat Ini</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($adjustment->adjustedProducts as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->product->product_name ?? '-' }}</strong><br>
                        <small>{{ $item->product->product_code ?? 'N/A' }}</small>
                    </td>
                    <td>{{ $item->formatted_quantity }}</td>
                    <td>{{ $item->type === 'add' ? 'Tambah' : 'Kurang' }}</td>
                    <td>{{ $item->product->product_quantity ?? 0 }} {{ $item->product->product_unit ?? 'PC' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada produk</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- CATATAN APPROVAL (jika ada) --}}
    @if ($adjustment->approval_notes)
    <div class="section">
        <div class="section-title">Catatan Approval</div>
        <p>{{ $adjustment->approval_notes }}</p>
    </div>
    @endif

    {{-- FOOTER --}}
    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh sistem. Referensi: {{ $adjustment->reference }}</p>
    </div>
</body>
</html>
