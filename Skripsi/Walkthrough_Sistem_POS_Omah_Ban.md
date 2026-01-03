# Walkthrough Sistem POS Omah Ban

Dokumen ini menjelaskan struktur menu sidebar sistem POS Omah Ban beserta deskripsi lengkap setiap fitur yang tersedia.

---

## Struktur Menu Sidebar

Sidebar sistem dikelompokkan menjadi **10 kategori utama** dengan hierarki sebagai berikut:

```
📂 OPERASIONAL
├── 🔔 Notifikasi
└── 🏠 Beranda (Dashboard)

📂 PRODUK
└── 📚 Manajemen Produk
    ├── 📁 Kategori Produk
    ├── 🏷️ Merek Produk
    ├── ➕ Tambah Produk Baru
    ├── 📦 Daftar Produk Baru
    ├── 🔄 Daftar Produk Bekas
    └── 🔧 Daftar Jasa

📂 STOK & GUDANG
└── 📋 Penyesuaian Stok
    ├── 📊 Stock Opname
    ├── 🆕 Buat Stock Opname
    ├── ✏️ Buat Penyesuaian Manual
    ├── 📜 Semua Penyesuaian
    └── ✅ Approval Penyesuaian

📂 RELASI
└── 👥 Data Relasi
    ├── 🏭 Daftar Supplier
    ├── ➕ Tambah Supplier
    ├── 👤 Daftar Customer
    └── ➕ Tambah Customer

📂 PEMBELIAN
└── 🛒 Pembelian Stok
    ├── ➕ Buat Pembelian
    ├── 📜 Daftar Pembelian
    └── 📜 Daftar Pembelian Bekas

📂 PENJUALAN
└── 🧾 Penjualan
    ├── 📝 Penawaran (Quotation)
    ├── 📜 Semua Penjualan
    └── ↩️ Retur Penjualan

📂 PENGELUARAN
└── 💸 Pengeluaran
    ├── 📁 Kategori Pengeluaran
    ├── ➕ Input Pengeluaran
    └── 📜 Daftar Pengeluaran

📂 LAPORAN
└── 📈 Laporan
    ├── 📅 Laporan Kas Harian
    ├── 🧑‍💼 Laporan Ringkas Kasir
    └── 💰 Laporan Laba/Rugi

📂 PENGGUNA
└── 👤 Manajemen User
    ├── ➕ Tambah Pengguna
    ├── 📜 Semua Pengguna
    └── 🔐 Peran & Hak Akses

📂 PENGATURAN
└── ⚙️ Pengaturan Sistem
    ├── 📏 Satuan Unit
    ├── 💱 Mata Uang
    ├── 📝 Pengaturan Umum
    └── 📱 WhatsApp Settings
```

---

## 1. OPERASIONAL

Kategori ini berisi menu-menu yang berkaitan dengan operasional harian sistem.

### 1.1 Notifikasi

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Menampilkan semua pemberitahuan penting dari sistem |
| **Akses** | Semua user yang sudah login |

**Isi Halaman:**
- Daftar notifikasi dengan filter (Semua, Belum Dibaca, Critical)
- Badge counter jumlah notifikasi belum dibaca
- Tombol "Tandai Semua Dibaca"
- Detail notifikasi saat diklik

**Jenis Notifikasi:**
| Tipe | Deskripsi |
|------|-----------|
| Low Stock Alert | Produk mencapai stok minimum |
| Manual Input | Transaksi POS dengan harga manual |
| Payment Success | Pembayaran Midtrans berhasil |
| Pending Approval | Penyesuaian stok menunggu persetujuan |

---

### 1.2 Beranda (Dashboard)

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Menampilkan ringkasan statistik bisnis secara real-time |
| **Akses** | Semua user yang sudah login |

**Isi Halaman:**

| Komponen | Deskripsi |
|----------|-----------|
| **Stats Cards** | Total Penjualan, Pembelian, Pengeluaran, dan Profit hari ini |
| **Grafik Mingguan** | Visualisasi perbandingan penjualan vs pembelian 7 hari terakhir |
| **Grafik Bulanan** | Visualisasi pendapatan per bulan dalam tahun berjalan |
| **Low Stock Alert** | Daftar produk dengan stok di bawah batas minimum |
| **Quick Actions** | Tombol akses cepat ke POS, tambah produk, dll |

---

## 2. PRODUK

Kategori ini berisi menu-menu untuk mengelola semua data produk.

### 2.1 Manajemen Produk

Menu dropdown yang berisi submenu pengelolaan produk.

---

#### 2.1.1 Kategori Produk

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Mengelola jenis/kategori produk |
| **Akses** | Permission: `access_product_categories` |

**Isi Halaman:**

| Elemen | Deskripsi |
|--------|-----------|
| Tabel Kategori | Daftar semua kategori dengan nama dan deskripsi |
| Tombol Tambah | Membuat kategori baru |
| Aksi Edit | Mengubah nama kategori |
| Aksi Hapus | Menghapus kategori (jika tidak ada produk terkait) |

**Contoh Kategori:**
- Ban Mobil, Ban Motor, Velg, Aki, Oli, Lampu, Aksesoris

---

#### 2.1.2 Merek Produk

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Mengelola brand/merek produk |
| **Akses** | Permission: `access_product_categories` |

**Isi Halaman:**

| Elemen | Deskripsi |
|--------|-----------|
| Tabel Merek | Daftar semua merek dengan logo |
| Tombol Tambah | Membuat merek baru |
| Upload Logo | Menyertakan gambar logo merek |
| Aksi CRUD | Edit dan hapus merek |

**Contoh Merek:**
- Bridgestone, Michelin, GT Radial, Dunlop, Achilles

---

#### 2.1.3 Tambah Produk Baru

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Form input produk baru ke inventory |
| **Akses** | Permission: `create_products` |

**Form Input:**

| Field | Tipe | Wajib | Keterangan |
|-------|------|:-----:|------------|
| Kode Produk | Text | ✅ | Kode unik produk (auto-generate atau manual) |
| Nama Produk | Text | ✅ | Nama lengkap produk |
| Kategori | Dropdown | ✅ | Pilih dari kategori yang ada |
| Merek | Dropdown | ✅ | Pilih dari merek yang ada |
| Barcode | Text | ❌ | Kode barcode untuk scanner |
| Harga Beli (HPP) | Currency | ✅ | Harga pokok dari supplier |
| Harga Jual | Currency | ✅ | Harga jual ke customer |
| Stok Awal | Number | ✅ | Jumlah stok awal |
| Alert Stok | Number | ❌ | Batas minimum stok (default: 10) |
| Satuan | Dropdown | ✅ | Satuan unit (pcs, set, dll) |
| Catatan | Textarea | ❌ | Keterangan tambahan |
| Gambar | File | ❌ | Foto produk (max 2MB) |

---

#### 2.1.4 Daftar Produk Baru

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Menampilkan semua produk kondisi baru |
| **Akses** | Permission: `access_products` |

**Isi Halaman:**

| Elemen | Deskripsi |
|--------|-----------|
| DataTable | Tabel dengan sorting, pencarian, dan pagination |
| Filter | Filter berdasarkan kategori dan merek |
| Indikator Stok | Highlight merah untuk stok di bawah minimum |
| Export | Export ke Excel dan PDF |
| Aksi | Edit, Hapus, Lihat Detail |

**Kolom Tabel:**
- Foto, Kode, Nama, Kategori, Merek, Stok, Harga Jual, Status, Aksi

---

#### 2.1.5 Daftar Produk Bekas

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Menampilkan produk second/bekas |
| **Akses** | Permission: `access_product_second` |

**Perbedaan dengan Produk Baru:**

| Aspek | Produk Baru | Produk Bekas |
|-------|-------------|--------------|
| Stok | Bisa > 1 | Selalu = 1 (unik) |
| Kondisi | Tidak ada | Baik/Cukup/Rusak |
| Asal | Pembelian supplier | Beli dari customer/supplier |
| Status | Aktif/Nonaktif | Available/Sold |

---

#### 2.1.6 Daftar Jasa

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Mengelola layanan jasa servis |
| **Akses** | Permission: `access_products` |

**Form Input Jasa:**

| Field | Tipe | Wajib | Keterangan |
|-------|------|:-----:|------------|
| Nama Jasa | Text | ✅ | Nama layanan servis |
| Harga | Currency | ✅ | Tarif jasa |
| Deskripsi | Textarea | ❌ | Detail layanan |

**Contoh Jasa:**
- Jasa Pasang Ban, Jasa Spooring, Jasa Balancing, Jasa Tambal Ban

> **Catatan:** Jasa tidak mempengaruhi stok karena bukan barang fisik.

---

## 3. STOK & GUDANG

Kategori ini berisi menu-menu untuk pengelolaan stok dan pergudangan.

### 3.1 Penyesuaian Stok

Menu dropdown untuk fitur penyesuaian stok.

---

#### 3.1.1 Stock Opname

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Menampilkan daftar sesi stock opname |
| **Akses** | Permission: `access_stock_opname` |

**Isi Halaman:**

| Elemen | Deskripsi |
|--------|-----------|
| Daftar Opname | Semua sesi opname dengan status |
| Badge | Jumlah opname yang sedang berjalan |
| Status | In Progress, Completed, Pending Approval |
| Aksi | Lihat Detail, Lanjutkan Input |

---

#### 3.1.2 Buat Stock Opname

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Membuat sesi stock opname baru |
| **Akses** | Permission: `create_stock_opname` |

**Proses Stock Opname:**

```
1. Buat Sesi Baru
   └── Pilih scope: Semua Produk / Per Kategori
   └── Isi tanggal dan catatan

2. Generate Daftar Produk
   └── Sistem tampilkan produk sesuai scope
   └── Kolom: Nama, Stok Sistem, Stok Fisik (input)

3. Input Qty Fisik
   └── Masukkan hasil hitung fisik
   └── Sistem highlight jika ada selisih

4. Review & Submit
   └── Lihat ringkasan variance
   └── Submit untuk diproses

5. Hasil
   ├── Tidak ada selisih → Status: Completed
   └── Ada selisih → Generate Adjustment Pending
```

---

#### 3.1.3 Buat Penyesuaian Manual

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Melakukan adjustment stok manual |
| **Akses** | Permission: `create_adjustments` |

**Form Input:**

| Field | Tipe | Wajib | Keterangan |
|-------|------|:-----:|------------|
| Tanggal | Date | ✅ | Tanggal penyesuaian |
| Catatan Umum | Textarea | ❌ | Keterangan adjustment |
| **Detail Item:** | | | |
| Produk | Dropdown | ✅ | Pilih produk |
| Tipe | Radio | ✅ | Addition (tambah) / Subtraction (kurang) |
| Qty | Number | ✅ | Jumlah yang disesuaikan |
| Alasan | Text | ✅ | Alasan penyesuaian |

**Contoh Alasan:**
- Barang rusak, Barang hilang, Kesalahan input, Expired, Bonus supplier

---

#### 3.1.4 Semua Penyesuaian

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Riwayat semua penyesuaian stok |
| **Akses** | Permission: `access_adjustments` |

**Isi Halaman:**

| Elemen | Deskripsi |
|--------|-----------|
| Tabel Adjustment | Semua adjustment dengan status |
| Filter Status | Pending, Approved, Rejected |
| Filter Tanggal | Rentang tanggal |
| Detail | Lihat item-item dalam adjustment |

---

#### 3.1.5 Approval Penyesuaian

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Menyetujui/menolak adjustment pending |
| **Akses** | Permission: `approve_adjustments` (biasanya Owner) |

**Isi Halaman:**

| Elemen | Deskripsi |
|--------|-----------|
| Daftar Pending | Adjustment yang menunggu approval |
| Badge Counter | Jumlah pending di sidebar |
| Aksi Approve | Setujui → stok berubah |
| Aksi Reject | Tolak → stok tidak berubah, wajib isi alasan |

---

## 4. RELASI

Kategori ini berisi menu-menu untuk mengelola data relasi bisnis.

### 4.1 Data Relasi

Menu dropdown untuk data supplier dan customer.

---

#### 4.1.1 Daftar Supplier

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Mengelola data pemasok barang |
| **Akses** | Permission: `access_suppliers` |

**Kolom Tabel:**
- Nama Supplier, Kontak, Email, Telepon, Alamat, Total Hutang, Aksi

**Fitur:**
- Lihat riwayat pembelian per supplier
- Tracking hutang ke supplier
- Status aktif/nonaktif

---

#### 4.1.2 Tambah Supplier

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Form input supplier baru |
| **Akses** | Permission: `create_suppliers` |

**Form Input:**

| Field | Tipe | Wajib | Keterangan |
|-------|------|:-----:|------------|
| Nama Supplier | Text | ✅ | Nama perusahaan/toko |
| Kontak Person | Text | ❌ | Nama PIC |
| Email | Email | ❌ | Alamat email |
| Telepon | Text | ❌ | Nomor telepon |
| Alamat | Textarea | ❌ | Alamat lengkap |
| NPWP | Text | ❌ | Nomor NPWP |

---

#### 4.1.3 Daftar Customer

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Mengelola data pelanggan |
| **Akses** | Permission: `access_customers` |

**Kolom Tabel:**
- Nama, Email, Telepon, Alamat, Total Pembelian, Aksi

**Fitur:**
- Lihat riwayat transaksi per customer
- Total pembelian customer
- Status aktif/nonaktif

---

#### 4.1.4 Tambah Customer

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Form input customer baru |
| **Akses** | Permission: `create_customers` |

**Form Input:**

| Field | Tipe | Wajib | Keterangan |
|-------|------|:-----:|------------|
| Nama | Text | ✅ | Nama customer |
| Email | Email | ❌ | Alamat email |
| Telepon | Text | ❌ | Nomor telepon |
| Alamat | Textarea | ❌ | Alamat lengkap |

---

## 5. PEMBELIAN

Kategori ini berisi menu-menu untuk transaksi pembelian dari supplier.

### 5.1 Pembelian Stok

Menu dropdown untuk fitur pembelian.

---

#### 5.1.1 Buat Pembelian

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Mencatat transaksi pembelian baru |
| **Akses** | Permission: `create_purchases` |

**Form Input:**

| Field | Tipe | Wajib | Keterangan |
|-------|------|:-----:|------------|
| Supplier | Dropdown | ✅ | Pilih supplier |
| Tanggal | Date | ✅ | Tanggal pembelian |
| No. Referensi | Text | ❌ | Nomor PO/invoice supplier |
| Status | Dropdown | ✅ | Pending/Ordered/Completed |
| Status Bayar | Dropdown | ✅ | Unpaid/Partial/Paid |
| Catatan | Textarea | ❌ | Keterangan tambahan |
| **Detail Item:** | | | |
| Produk | Dropdown | ✅ | Pilih produk |
| Qty | Number | ✅ | Jumlah dibeli |
| Harga Beli | Currency | ✅ | Harga per unit |

> **Catatan:** Stok otomatis bertambah saat status = Completed

---

#### 5.1.2 Daftar Pembelian

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Menampilkan riwayat pembelian produk baru |
| **Akses** | Permission: `access_purchases` |

**Kolom Tabel:**
- No. Ref, Tanggal, Supplier, Total, Status, Status Bayar, Aksi

**Fitur:**
- Filter berdasarkan supplier, status, tanggal
- Detail pembelian dengan item-item
- Pencatatan pembayaran (partial payment)
- Edit dan hapus pembelian

---

#### 5.1.3 Daftar Pembelian Bekas

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Menampilkan pembelian produk second |
| **Akses** | Permission: `access_purchases` |

**Perbedaan:**
- Khusus untuk produk bekas
- Produk masuk ke inventory Produk Bekas
- Ada field kondisi produk

---

## 6. PENJUALAN

Kategori ini berisi menu-menu untuk transaksi penjualan.

### 6.1 Penjualan

Menu dropdown untuk fitur penjualan.

---

#### 6.1.1 Penawaran (Quotation)

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Membuat penawaran harga ke customer |
| **Akses** | Permission: `access_sales` |

**Fitur:**
- Buat draft penawaran dengan multi-item
- Set expired date penawaran
- Cetak penawaran PDF
- Konversi ke penjualan jika customer setuju

**Alur:**
```
Buat Penawaran → Kirim ke Customer → Customer Setuju → Konversi ke Penjualan
                                   → Customer Tolak → Archive/Hapus
```

---

#### 6.1.2 Semua Penjualan

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Menampilkan semua transaksi penjualan |
| **Akses** | Permission: `access_sales` |

**Kolom Tabel:**
- No. Invoice, Tanggal, Customer, Kasir, Total, Status Bayar, Aksi

**Fitur:**
- Filter berdasarkan tanggal, kasir, status
- Detail penjualan dengan item-item
- Cetak invoice PDF
- Export ke Excel

---

#### 6.1.3 Retur Penjualan

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Mencatat pengembalian barang oleh customer |
| **Akses** | Permission: `access_sale_returns` |

**Form Input:**

| Field | Tipe | Wajib | Keterangan |
|-------|------|:-----:|------------|
| Invoice | Dropdown | ✅ | Pilih invoice yang diretur |
| Item Retur | Multi-select | ✅ | Pilih item yang dikembalikan |
| Qty Retur | Number | ✅ | Jumlah yang diretur |
| Alasan | Text | ✅ | Alasan pengembalian |
| Metode Refund | Dropdown | ✅ | Cash/Store Credit |

> **Catatan:** Stok produk otomatis bertambah saat retur diproses

---

### 6.2 Halaman POS (Point of Sale)

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Halaman kasir untuk transaksi penjualan cepat |
| **Akses** | Permission: `create_pos_sales` |
| **Lokasi** | Icon di header navigasi (bukan di sidebar) |

**Layout Halaman:**

```
┌─────────────────────────────────────────────────────────────────┐
│  🔍 Cari produk...                        [📷 Scan]  [⚙️ Menu]  │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌───────────────────────────┐    ┌───────────────────────────┐ │
│  │                           │    │      KERANJANG            │ │
│  │      GRID PRODUK          │    │                           │ │
│  │                           │    │  ┌───────────────────────┐│ │
│  │  [Ban A] [Ban B] [Velg]   │    │  │ Item 1      Rp500.000 ││ │
│  │  [Aki]   [Oli]   [Lamp]   │    │  │ Item 2      Rp300.000 ││ │
│  │  [Jasa1] [Jasa2] [...]    │    │  └───────────────────────┘│ │
│  │                           │    │                           │ │
│  │                           │    │  Subtotal     Rp 800.000  │ │
│  │                           │    │  Diskon       Rp  50.000  │ │
│  │                           │    │  ─────────────────────────│ │
│  │                           │    │  TOTAL        Rp 750.000  │ │
│  └───────────────────────────┘    │                           │ │
│                                   │  [    💳 BAYAR SEKARANG  ]│ │
│                                   └───────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

**Fitur POS:**

| Fitur | Deskripsi |
|-------|-----------|
| Real-time Search | Pencarian produk instan saat mengetik |
| Barcode Scanner | Support USB/Bluetooth barcode scanner |
| Product Grid | Klik produk untuk tambah ke keranjang |
| Cart Management | Ubah qty, ubah harga, hapus item |
| Manual Input | Tambah item manual (jasa custom) |
| Multi Payment | Cash, Transfer Bank, QRIS (Midtrans) |
| Guest Customer | Transaksi tanpa data customer |
| Quick Discount | Diskon per item atau total |
| Print Invoice | Cetak invoice PDF |

**Metode Pembayaran:**

| Metode | Keterangan |
|--------|------------|
| **Cash** | Bayar tunai, sistem hitung kembalian otomatis |
| **Transfer** | Bayar via transfer bank, pilih nama bank |
| **QRIS/Midtrans** | Pembayaran digital via QR code |

**Alur Transaksi POS:**

```
1. Tambah Produk ke Keranjang
   ├── Ketik nama/kode di kotak pencarian
   ├── Scan barcode produk
   └── Klik produk dari grid

2. Atur Keranjang
   ├── Ubah qty dengan +/-
   ├── Edit harga jika ada diskon
   └── Hapus item dengan tombol X

3. Proses Pembayaran
   ├── Klik "BAYAR SEKARANG"
   ├── Pilih metode pembayaran
   ├── Cash → Input nominal → Lihat kembalian
   ├── Transfer → Pilih bank → Input nominal
   └── QRIS → Generate QR → Customer scan

4. Selesai
   ├── Invoice tergenerate otomatis
   ├── Stok berkurang otomatis
   ├── Notifikasi ke Owner (jika diaktifkan)
   └── Cetak invoice (opsional)
```

---

## 7. PENGELUARAN

Kategori ini berisi menu-menu untuk mencatat pengeluaran operasional.

### 7.1 Pengeluaran

Menu dropdown untuk fitur pengeluaran.

---

#### 7.1.1 Kategori Pengeluaran

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Mengelola jenis-jenis pengeluaran |
| **Akses** | Permission: `access_expense_categories` |

**Contoh Kategori:**
- Listrik, Air, Telepon/Internet, Gaji Karyawan, Transport, Konsumsi, Perlengkapan, Maintenance

---

#### 7.1.2 Input Pengeluaran

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Mencatat pengeluaran baru |
| **Akses** | Permission: `create_expenses` |

**Form Input:**

| Field | Tipe | Wajib | Keterangan |
|-------|------|:-----:|------------|
| Tanggal | Date | ✅ | Tanggal pengeluaran |
| Kategori | Dropdown | ✅ | Pilih kategori |
| Keterangan | Text | ✅ | Detail pengeluaran |
| Nominal | Currency | ✅ | Jumlah uang |
| Bukti | File | ❌ | Upload bukti pembayaran |

---

#### 7.1.3 Daftar Pengeluaran

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Menampilkan riwayat semua pengeluaran |
| **Akses** | Permission: `access_expenses` |

**Kolom Tabel:**
- Tanggal, Kategori, Keterangan, Nominal, Bukti, Aksi

**Fitur:**
- Filter berdasarkan kategori dan tanggal
- Total pengeluaran per periode
- Edit dan hapus pengeluaran

---

## 8. LAPORAN

Kategori ini berisi menu-menu untuk melihat laporan bisnis.

### 8.1 Laporan

Menu dropdown untuk fitur laporan.

---

#### 8.1.1 Laporan Kas Harian

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Ringkasan transaksi kas per hari |
| **Akses** | Permission: `access_reports` |

**Isi Laporan:**

| Komponen | Deskripsi |
|----------|-----------|
| Total Penjualan | Sum semua penjualan hari itu |
| Total Pembelian | Sum semua pembelian hari itu |
| Total Pengeluaran | Sum semua expense hari itu |
| Saldo Kas | Penjualan - Pembelian - Pengeluaran |
| Detail Transaksi | Breakdown transaksi per jam |

**Export:** Excel, PDF

---

#### 8.1.2 Laporan Ringkas Kasir

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Performa masing-masing kasir |
| **Akses** | Permission: `access_reports` |

**Isi Laporan:**

| Metrik | Deskripsi |
|--------|-----------|
| Jumlah Transaksi | Total transaksi per kasir |
| Total Penjualan | Sum nilai penjualan per kasir |
| Rata-rata Transaksi | Average nilai per transaksi |
| Perbandingan | Chart perbandingan antar kasir |

**Export:** Excel

---

#### 8.1.3 Laporan Laba/Rugi

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Kalkulasi laba/rugi dalam periode tertentu |
| **Akses** | Permission: `access_reports` |

**Struktur Laporan:**

```
LAPORAN LABA/RUGI
Periode: [Tanggal Awal] s/d [Tanggal Akhir]

PENDAPATAN
├── Total Penjualan                      Rp XXX.XXX.XXX
└── Subtotal Pendapatan                  Rp XXX.XXX.XXX

HARGA POKOK PENJUALAN (HPP)
├── Total HPP Barang Terjual            (Rp XXX.XXX.XXX)
└── Subtotal HPP                        (Rp XXX.XXX.XXX)

LABA KOTOR                               Rp XXX.XXX.XXX

BIAYA OPERASIONAL
├── Listrik                             (Rp X.XXX.XXX)
├── Gaji                                (Rp X.XXX.XXX)
├── Transport                           (Rp X.XXX.XXX)
├── Lain-lain                           (Rp X.XXX.XXX)
└── Subtotal Biaya                      (Rp XX.XXX.XXX)

LABA/RUGI BERSIH                         Rp XXX.XXX.XXX
```

**Export:** Excel, PDF

---

## 9. PENGGUNA

Kategori ini berisi menu-menu untuk manajemen user dan akses.

### 9.1 Manajemen User

Menu dropdown untuk fitur manajemen pengguna.

---

#### 9.1.1 Tambah Pengguna

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Membuat user baru |
| **Akses** | Permission: `access_user_management` |

**Form Input:**

| Field | Tipe | Wajib | Keterangan |
|-------|------|:-----:|------------|
| Nama | Text | ✅ | Nama lengkap |
| Email | Email | ✅ | Email untuk login |
| Password | Password | ✅ | Minimal 8 karakter |
| Role | Dropdown | ✅ | Pilih role |
| Status | Toggle | ✅ | Aktif/Nonaktif |

---

#### 9.1.2 Semua Pengguna

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Menampilkan daftar semua user |
| **Akses** | Permission: `access_user_management` |

**Kolom Tabel:**
- Nama, Email, Role, Status, Terakhir Login, Aksi

**Fitur:**
- Edit data user
- Aktivasi/deaktivasi user
- Reset password

---

#### 9.1.3 Peran & Hak Akses

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Mengelola role dan permission |
| **Akses** | Permission: `access_user_management` |

**Role Default:**

| Role | Deskripsi | Contoh Permission |
|------|-----------|-------------------|
| **Owner** | Akses penuh ke semua fitur | Semua permission |
| **Admin** | Akses ke sebagian besar fitur | Kecuali pengaturan sensitif |
| **Kasir** | Akses terbatas ke transaksi | POS, lihat penjualan |
| **Gudang** | Akses ke manajemen stok | Produk, adjustment, opname |

**Fitur:**
- Buat role baru
- Edit permission per role
- Lihat user per role
- Hapus role (jika tidak ada user)

---

## 10. PENGATURAN

Kategori ini berisi menu-menu untuk konfigurasi sistem.

### 10.1 Pengaturan Sistem

Menu dropdown untuk fitur pengaturan.

---

#### 10.1.1 Satuan Unit

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Mengelola satuan ukuran produk |
| **Akses** | Permission: `access_units` |

**Contoh Satuan:**
- pcs, set, pasang, liter, kg, meter, box, pack

---

#### 10.1.2 Mata Uang

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Mengatur format mata uang |
| **Akses** | Permission: `access_currencies` |

**Pengaturan:**

| Field | Contoh |
|-------|--------|
| Simbol | Rp |
| Posisi Simbol | Sebelum angka |
| Pemisah Ribuan | . (titik) |
| Pemisah Desimal | , (koma) |
| Digit Desimal | 0 |

---

#### 10.1.3 Pengaturan Umum

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Mengatur informasi toko |
| **Akses** | Permission: `access_settings` |

**Pengaturan:**

| Field | Deskripsi |
|-------|-----------|
| Nama Toko | Nama usaha yang tampil di invoice |
| Alamat | Alamat lengkap toko |
| Telepon | Nomor telepon toko |
| Email | Email toko |
| Logo | Logo yang tampil di invoice |
| Kop Surat | Template header invoice |
| Footer | Teks footer invoice |

---

#### 10.1.4 WhatsApp Settings

| Aspek | Keterangan |
|-------|------------|
| **Fungsi** | Konfigurasi notifikasi WhatsApp |
| **Akses** | Permission: `access_settings` |

**Fitur:**

| Fitur | Deskripsi |
|-------|-----------|
| Koneksi | Hubungkan WhatsApp via QR Code |
| Status | Indikator Connected (hijau) / Disconnected (merah) |
| Reconnect | Menyambung ulang jika terputus |
| Test Message | Kirim pesan tes |

**Jenis Notifikasi yang Dapat Diaktifkan:**

| Event | Deskripsi |
|-------|-----------|
| Penjualan Besar | Notifikasi saat transaksi > threshold |
| Stok Rendah | Notifikasi saat produk mencapai batas minimum |
| Pembayaran Online | Notifikasi saat ada pembayaran Midtrans berhasil |
| Manual Input | Notifikasi saat kasir input harga manual |

**Pengaturan Template:**
- Customizable template pesan untuk setiap event
- Variabel dinamis: nama produk, nominal, tanggal, dll

**Daftar Penerima:**
- Tambah/hapus nomor penerima notifikasi
- Toggle aktif/nonaktif per penerima

---

*Dokumentasi Walkthrough Sistem POS Omah Ban - Versi 1.0*
