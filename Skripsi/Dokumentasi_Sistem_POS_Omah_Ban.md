# Dokumentasi Sistem POS Omah Ban

## Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Kategori Operasional](#2-kategori-operasional)
3. [Kategori Produk](#3-kategori-produk)
4. [Kategori Stok & Gudang](#4-kategori-stok--gudang)
5. [Kategori Relasi](#5-kategori-relasi)
6. [Kategori Pembelian](#6-kategori-pembelian)
7. [Kategori Penjualan](#7-kategori-penjualan)
8. [Kategori Pengeluaran](#8-kategori-pengeluaran)
9. [Kategori Laporan](#9-kategori-laporan)
10. [Kategori Pengguna](#10-kategori-pengguna)
11. [Kategori Pengaturan](#11-kategori-pengaturan)

---

## 1. Pendahuluan

Sistem POS (Point of Sale) Omah Ban adalah aplikasi manajemen toko ban berbasis web yang dibangun menggunakan framework Laravel 11 dengan arsitektur modular. Sistem ini dirancang untuk membantu operasional toko ban dalam mengelola produk, transaksi penjualan, pembelian, stok, dan pelaporan keuangan.

### Struktur Menu Sidebar

Sidebar aplikasi dikelompokkan menjadi **10 kategori utama**:
1. **Operasional** - Notifikasi dan Dashboard
2. **Produk** - Manajemen produk baru, bekas, dan jasa
3. **Stok & Gudang** - Penyesuaian stok dan stock opname
4. **Relasi** - Data supplier dan customer
5. **Pembelian** - Transaksi pembelian dari supplier
6. **Penjualan** - Transaksi penjualan ke customer (termasuk POS)
7. **Pengeluaran** - Pencatatan biaya operasional
8. **Laporan** - Laporan keuangan dan ringkasan
9. **Pengguna** - Manajemen user dan role
10. **Pengaturan** - Konfigurasi sistem

---

## 2. Kategori Operasional

### 2.1 Notifikasi

**Deskripsi:**  
Halaman Notifikasi menampilkan semua pemberitahuan penting yang terjadi dalam sistem. Notifikasi dikategorikan berdasarkan tingkat severity (critical, warning, info).

**Fitur:**
- Melihat daftar notifikasi dengan filter (semua, belum dibaca, critical)
- Menandai notifikasi sebagai sudah dibaca
- Menandai semua notifikasi sebagai sudah dibaca sekaligus
- Menghapus notifikasi satu per satu atau bulk delete
- Badge counter otomatis refresh setiap 30 detik
- Notifikasi critical ditandai dengan warna merah

**Jenis Notifikasi:**
- Stok produk mencapai batas minimum (low stock alert)
- Transaksi penjualan besar (manual input monitoring)
- Pembayaran Midtrans berhasil/gagal
- Penyesuaian stok menunggu approval

**Cara Menggunakan:**
1. Klik menu **Notifikasi** di sidebar
2. Lihat daftar notifikasi yang muncul
3. Klik notifikasi untuk melihat detail
4. Gunakan tombol "Tandai Sudah Dibaca" untuk menandai selesai
5. Gunakan filter untuk menyaring notifikasi tertentu

---

### 2.2 Beranda (Dashboard)

**Deskripsi:**  
Dashboard adalah halaman utama setelah login yang menampilkan ringkasan statistik bisnis secara real-time.

**Fitur:**
- **Stats Cards** - Menampilkan total penjualan, pembelian, pengeluaran, dan profit hari ini
- **Grafik Penjualan vs Pembelian** - Visualisasi tren mingguan menggunakan Chart.js
- **Grafik Pendapatan Bulanan** - Visualisasi pendapatan per bulan
- **Low Stock Alert** - Daftar produk dengan stok di bawah batas minimum
- **Quick Action Buttons** - Akses cepat ke fitur POS, tambah produk, dll

**Cara Menggunakan:**
1. Setelah login, sistem otomatis membuka Dashboard
2. Lihat ringkasan statistik di bagian atas
3. Scroll ke bawah untuk melihat grafik dan alert stok
4. Klik produk di "Low Stock Alert" untuk langsung ke halaman edit produk

---

## 3. Kategori Produk

### 3.1 Manajemen Produk

Menu dropdown yang berisi submenu untuk mengelola semua data produk.

---

#### 3.1.1 Kategori Produk

**Deskripsi:**  
Mengelola kategori/jenis produk yang tersedia di toko (misalnya: Ban Mobil, Ban Motor, Velg, Aki, Oli, dll).

**Fitur:**
- Menambah kategori baru
- Mengubah nama kategori
- Menghapus kategori (jika tidak ada produk terkait)
- Pencarian kategori

**Data yang Disimpan:**
| Field | Keterangan |
|-------|------------|
| Nama Kategori | Nama jenis produk |
| Deskripsi | Keterangan tambahan (opsional) |

**Cara Menggunakan:**
1. Buka menu **Produk > Manajemen Produk > Kategori Produk**
2. Klik tombol **Tambah Kategori** untuk menambah baru
3. Isi nama kategori dan klik Simpan
4. Untuk mengedit, klik tombol **Edit** di baris kategori
5. Untuk menghapus, klik tombol **Hapus** (pastikan tidak ada produk terkait)

---

#### 3.1.2 Merek Produk (Brand)

**Deskripsi:**  
Mengelola merek/brand produk yang dijual (misalnya: Bridgestone, Michelin, GT Radial, dll).

**Fitur:**
- CRUD merek produk
- Pencarian merek
- Filter berdasarkan status aktif/nonaktif

**Data yang Disimpan:**
| Field | Keterangan |
|-------|------------|
| Nama Merek | Nama brand produk |
| Logo | Gambar logo brand (opsional) |

**Cara Menggunakan:**
1. Buka menu **Produk > Manajemen Produk > Merek Produk**
2. Klik **Tambah Merek** dan isi data
3. Upload logo jika tersedia
4. Klik Simpan

---

#### 3.1.3 Tambah Produk Baru

**Deskripsi:**  
Form untuk menambahkan produk baru ke dalam inventory.

**Data yang Harus Diisi:**
| Field | Tipe | Keterangan |
|-------|------|------------|
| Kode Produk | Text | Kode unik (auto-generate atau manual) |
| Nama Produk | Text | Nama lengkap produk |
| Kategori | Dropdown | Pilih dari kategori yang ada |
| Merek | Dropdown | Pilih dari merek yang ada |
| Barcode | Text | Kode barcode (opsional) |
| Harga Beli (HPP) | Currency | Harga pokok beli dari supplier |
| Harga Jual | Currency | Harga jual ke customer |
| Stok Awal | Number | Jumlah stok awal |
| Alert Stok | Number | Batas minimum stok (default: 10) |
| Satuan | Dropdown | Pilih satuan (pcs, set, liter, dll) |
| Catatan | Textarea | Keterangan tambahan |
| Gambar | File | Foto produk (max 2MB) |

**Cara Menggunakan:**
1. Buka menu **Produk > Manajemen Produk > Tambah Produk Baru**
2. Isi semua field yang wajib (ditandai *)
3. Upload gambar produk jika ada
4. Klik tombol **Simpan**
5. Produk akan muncul di Daftar Produk Baru

---

#### 3.1.4 Daftar Produk Baru

**Deskripsi:**  
Menampilkan semua produk baru (kondisi baru) yang ada di inventory dengan fitur DataTable.

**Fitur:**
- Tabel dengan sorting dan pencarian
- Filter berdasarkan kategori dan merek
- Aksi Edit dan Hapus per produk
- Export ke Excel/PDF
- Indikator stok rendah (highlight merah)

**Kolom yang Ditampilkan:**
- Foto, Kode Produk, Nama, Kategori, Merek, Stok, Harga Jual, Status, Aksi

**Cara Menggunakan:**
1. Buka menu **Produk > Manajemen Produk > Daftar Produk Baru**
2. Gunakan kotak pencarian untuk mencari produk tertentu
3. Klik header kolom untuk mengurutkan data
4. Klik **Edit** untuk mengubah data produk
5. Klik **Hapus** untuk menghapus produk (harus stok = 0)

---

#### 3.1.5 Daftar Produk Bekas (Second)

**Deskripsi:**  
Menampilkan daftar produk bekas/second yang dibeli dari customer atau supplier. Setiap produk second adalah item unik dengan stok = 1.

**Fitur:**
- Melihat daftar produk bekas dengan kondisi
- Filter berdasarkan status (available, sold)
- Tracking asal pembelian produk
- Status kondisi produk (Baik/Cukup/Rusak)

**Perbedaan dengan Produk Baru:**
- Stok selalu = 1 (item unik)
- Ada field kondisi produk
- Otomatis ditandai "sold" setelah terjual

**Cara Menggunakan:**
1. Buka menu **Produk > Manajemen Produk > Daftar Produk Bekas**
2. Lihat status produk (Available = bisa dijual, Sold = sudah terjual)
3. Produk bekas otomatis masuk saat melakukan pembelian bekas

---

#### 3.1.6 Daftar Jasa (Service Master)

**Deskripsi:**  
Mengelola daftar jasa servis yang ditawarkan (misalnya: Jasa Pasang Ban, Jasa Spooring, Jasa Balancing, dll).

**Fitur:**
- CRUD jasa servis
- Pengaturan harga jasa
- Jasa tidak mengurangi stok

**Data yang Disimpan:**
| Field | Keterangan |
|-------|------------|
| Nama Jasa | Nama layanan servis |
| Harga | Tarif jasa |
| Deskripsi | Keterangan detail jasa |

**Cara Menggunakan:**
1. Buka menu **Produk > Manajemen Produk > Daftar Jasa**
2. Klik **Tambah Jasa** untuk menambah layanan baru
3. Isi nama jasa dan harga
4. Jasa akan tersedia untuk dipilih di halaman POS

---

## 4. Kategori Stok & Gudang

### 4.1 Penyesuaian Stok (Stock Adjustment)

Menu dropdown untuk mengelola penyesuaian stok manual.

---

#### 4.1.1 Stock Opname

**Deskripsi:**  
Fitur untuk melakukan penghitungan stok fisik dan sinkronisasi dengan data di sistem. Stock opname biasanya dilakukan secara berkala untuk memastikan akurasi data stok.

**Fitur:**
- Membuat sesi stock opname baru
- Input jumlah stok fisik per produk
- Sistem otomatis menghitung selisih (variance)
- Generate adjustment otomatis jika ada selisih
- Status tracking (In Progress, Completed, Pending Approval)

**Proses Stock Opname:**
1. **Buat Sesi Baru** - Pilih scope (semua produk atau per kategori)
2. **Input Qty Fisik** - Masukkan jumlah stok hasil hitung fisik
3. **Review Variance** - Sistem tampilkan selisih antara sistem vs fisik
4. **Submit** - Kirim hasil opname
5. **Approval** - Owner approve/reject adjustment yang dihasilkan

**Cara Menggunakan:**
1. Buka menu **Stok & Gudang > Penyesuaian Stok > Stock Opname**
2. Klik **Buat Stock Opname**
3. Pilih scope: Semua Produk atau Per Kategori tertentu
4. Masukkan tanggal pelaksanaan dan catatan
5. Klik **Generate** untuk membuat daftar produk
6. Input qty fisik untuk setiap produk
7. Klik **Submit** setelah selesai
8. Tunggu approval dari Owner

---

#### 4.1.2 Buat Penyesuaian Manual

**Deskripsi:**  
Fitur untuk melakukan penyesuaian stok secara manual dengan alasan tertentu (misalnya: barang rusak, hilang, expired, atau koreksi kesalahan input).

**Fitur:**
- Penambahan stok (Addition)
- Pengurangan stok (Subtraction)
- Multi-product dalam satu adjustment
- Alasan wajib diisi untuk audit trail
- Workflow approval oleh Owner

**Data yang Harus Diisi:**
| Field | Keterangan |
|-------|------------|
| Tanggal Adjustment | Tanggal penyesuaian dilakukan |
| Produk | Pilih produk yang disesuaikan |
| Tipe | Addition (tambah) atau Subtraction (kurang) |
| Jumlah | Qty yang ditambah/dikurangi |
| Alasan | Keterangan penyebab adjustment |
| Catatan | Keterangan tambahan |

**Cara Menggunakan:**
1. Buka menu **Stok & Gudang > Penyesuaian Stok > Buat Penyesuaian Manual**
2. Isi tanggal dan catatan umum
3. Klik **Tambah Produk** untuk menambah item
4. Pilih produk, tipe adjustment, qty, dan alasan
5. Ulangi untuk produk lain jika perlu
6. Klik **Simpan** untuk submit
7. Adjustment masuk status "Pending" menunggu approval

---

#### 4.1.3 Semua Penyesuaian

**Deskripsi:**  
Menampilkan riwayat semua penyesuaian stok yang pernah dilakukan.

**Fitur:**
- Filter berdasarkan status (Pending/Approved/Rejected)
- Filter berdasarkan tanggal
- Melihat detail adjustment
- Export data

**Cara Menggunakan:**
1. Buka menu **Stok & Gudang > Penyesuaian Stok > Semua Penyesuaian**
2. Lihat daftar semua adjustment dengan statusnya
3. Klik baris untuk melihat detail

---

#### 4.1.4 Approval Penyesuaian

**Deskripsi:**  
Halaman khusus untuk Owner melakukan approval atau reject terhadap adjustment yang pending.

**Fitur:**
- Melihat daftar adjustment pending
- Approve adjustment (stok akan berubah)
- Reject adjustment (stok tidak berubah)
- Badge counter untuk adjustment pending

**Cara Menggunakan:**
1. Buka menu **Stok & Gudang > Penyesuaian Stok > Approval Penyesuaian**
2. Lihat daftar adjustment yang menunggu approval
3. Review detail setiap adjustment
4. Klik **Approve** untuk menyetujui atau **Reject** untuk menolak
5. Isi alasan jika reject

---

## 5. Kategori Relasi

### 5.1 Data Relasi

Menu dropdown untuk mengelola data supplier dan customer.

---

#### 5.1.1 Daftar Supplier

**Deskripsi:**  
Mengelola data supplier/pemasok barang. Supplier adalah pihak yang memasok produk ke toko.

**Fitur:**
- CRUD data supplier
- Melihat riwayat pembelian per supplier
- Tracking total hutang ke supplier
- Status aktif/nonaktif

**Data yang Disimpan:**
| Field | Keterangan |
|-------|------------|
| Nama Supplier | Nama perusahaan/toko supplier |
| Kontak Person | Nama PIC |
| Email | Alamat email |
| Telepon | Nomor telepon |
| Alamat | Alamat lengkap |
| NPWP | Nomor NPWP (opsional) |

**Cara Menggunakan:**
1. Buka menu **Relasi > Data Relasi > Daftar Supplier**
2. Lihat semua supplier yang terdaftar
3. Klik **Tambah Supplier** untuk menambah baru
4. Isi data lengkap supplier
5. Klik baris supplier untuk melihat riwayat pembelian

---

#### 5.1.2 Tambah Supplier

Form untuk menambahkan supplier baru ke sistem.

---

#### 5.1.3 Daftar Customer

**Deskripsi:**  
Mengelola data customer/pelanggan yang melakukan transaksi di toko.

**Fitur:**
- CRUD data customer
- Melihat riwayat transaksi per customer
- Total pembelian customer
- Status aktif/nonaktif

**Data yang Disimpan:**
| Field | Keterangan |
|-------|------------|
| Nama | Nama customer |
| Email | Alamat email (opsional) |
| Telepon | Nomor telepon |
| Alamat | Alamat lengkap |

**Cara Menggunakan:**
1. Buka menu **Relasi > Data Relasi > Daftar Customer**
2. Lihat semua customer yang terdaftar
3. Klik **Tambah Customer** untuk menambah baru
4. Klik baris customer untuk melihat riwayat transaksi

---

#### 5.1.4 Tambah Customer

Form untuk menambahkan customer baru ke sistem.

---

## 6. Kategori Pembelian

### 6.1 Pembelian Stok

Menu dropdown untuk mengelola transaksi pembelian.

---

#### 6.1.1 Buat Pembelian

**Deskripsi:**  
Form untuk mencatat transaksi pembelian produk baru dari supplier.

**Fitur:**
- Memilih supplier dari dropdown
- Menambahkan multi-item dalam satu transaksi
- Input harga beli per item
- Status pembelian (Pending/Ordered/Completed)
- Status pembayaran (Unpaid/Partial/Paid)
- Auto-increment stok saat status = Completed

**Data yang Harus Diisi:**
| Field | Keterangan |
|-------|------------|
| Supplier | Pilih supplier |
| Tanggal | Tanggal pembelian |
| No. Referensi | Nomor PO/invoice supplier (opsional) |
| Produk | Pilih produk yang dibeli |
| Qty | Jumlah item |
| Harga Beli | Harga per unit |
| Status | Status pembelian |
| Status Bayar | Status pembayaran |
| Catatan | Keterangan tambahan |

**Cara Menggunakan:**
1. Buka menu **Pembelian > Pembelian Stok > Buat Pembelian**
2. Pilih supplier dari dropdown
3. Isi tanggal pembelian
4. Klik **Tambah Produk** untuk menambah item
5. Pilih produk, qty, dan harga beli
6. Ulangi untuk produk lain
7. Set status dan status pembayaran
8. Klik **Simpan**
9. Jika status = Completed, stok akan otomatis bertambah

---

#### 6.1.2 Daftar Pembelian

**Deskripsi:**  
Menampilkan semua transaksi pembelian produk baru.

**Fitur:**
- DataTable dengan sorting dan pencarian
- Filter berdasarkan supplier, status, tanggal
- Detail pembelian
- Edit dan hapus pembelian
- Pencatatan pembayaran (partial payment)

**Cara Menggunakan:**
1. Buka menu **Pembelian > Pembelian Stok > Daftar Pembelian**
2. Lihat semua pembelian yang tercatat
3. Klik **Detail** untuk melihat item-item pembelian
4. Gunakan filter untuk menyaring data

---

#### 6.1.3 Daftar Pembelian Bekas

**Deskripsi:**  
Menampilkan transaksi pembelian produk bekas dari customer atau supplier.

**Fitur:**
- Khusus untuk pembelian produk second
- Produk bekas otomatis masuk ke inventory second
- Pencatatan kondisi produk

**Cara Menggunakan:**
1. Buka menu **Pembelian > Pembelian Stok > Daftar Pembelian Bekas**
2. Lihat semua pembelian produk bekas
3. Setiap produk bekas yang dibeli akan muncul di Daftar Produk Bekas

---

## 7. Kategori Penjualan

### 7.1 Penjualan

Menu dropdown untuk mengelola transaksi penjualan.

---

#### 7.1.1 Penawaran (Quotation)

**Deskripsi:**  
Fitur untuk membuat penawaran harga kepada customer sebelum menjadi transaksi penjualan.

**Fitur:**
- Membuat draft penawaran
- Multi-item dalam satu penawaran
- Konversi penawaran menjadi penjualan
- Cetak penawaran PDF
- Expired date untuk penawaran

**Cara Menggunakan:**
1. Buka menu **Penjualan > Penjualan > Penawaran**
2. Klik **Buat Penawaran**
3. Pilih customer (atau isi sebagai "Walk-in")
4. Tambahkan produk/jasa yang ditawarkan
5. Atur harga dan diskon jika ada
6. Simpan penawaran
7. Klik **Konversi ke Penjualan** jika customer setuju

---

#### 7.1.2 Semua Penjualan

**Deskripsi:**  
Menampilkan semua transaksi penjualan yang sudah selesai (dari POS maupun manual).

**Fitur:**
- DataTable dengan sorting dan pencarian
- Filter berdasarkan tanggal, kasir, status pembayaran
- Lihat detail penjualan dengan breakdown item
- Cetak invoice PDF
- Export ke Excel

**Kolom yang Ditampilkan:**
- No. Invoice, Tanggal, Customer, Kasir, Total, Status Bayar, Aksi

**Cara Menggunakan:**
1. Buka menu **Penjualan > Penjualan > Semua Penjualan**
2. Lihat daftar semua transaksi
3. Klik **Detail** untuk melihat item-item penjualan
4. Klik **Print** untuk mencetak invoice

---

#### 7.1.3 Retur Penjualan

**Deskripsi:**  
Fitur untuk mencatat pengembalian barang oleh customer setelah transaksi.

**Fitur:**
- Memilih penjualan yang akan diretur
- Memilih item yang diretur (partial atau full)
- Input alasan retur
- Auto-adjust stok untuk produk yang dikembalikan
- Proses refund (cash back atau store credit)

**Cara Menggunakan:**
1. Buka menu **Penjualan > Penjualan > Retur Penjualan**
2. Cari invoice yang akan diretur
3. Pilih item dan qty yang diretur
4. Isi alasan retur
5. Pilih metode refund
6. Submit retur

---

### 7.2 Halaman POS (Point of Sale)

**Deskripsi:**  
Halaman utama kasir untuk melakukan transaksi penjualan secara cepat. POS diakses melalui icon di header navigasi.

**Layout:**
```
┌───────────────────────────────────────────────────────────┐
│  🔍 Cari produk...              📷 Scan Barcode           │
├───────────────────────────────────────────────────────────┤
│  ┌─────────────────────┐    ┌───────────────────────────┐ │
│  │   PRODUCT GRID      │    │      CART PANEL           │ │
│  │                     │    │                           │ │
│  │  [Ban] [Velg] [Aki] │    │  Item 1         Rp 500.000│ │
│  │  [Oli] [Lamp] [Jasa]│    │  Item 2         Rp 300.000│ │
│  │                     │    ├───────────────────────────┤ │
│  │                     │    │  Subtotal     Rp 800.000  │ │
│  │                     │    │  Diskon       Rp 50.000   │ │
│  └─────────────────────┘    │  TOTAL        Rp 750.000  │ │
│                             │                           │ │
│                             │  [    BAYAR SEKARANG    ] │ │
│                             └───────────────────────────┘ │
└───────────────────────────────────────────────────────────┘
```

**Fitur:**
| Fitur | Keterangan |
|-------|------------|
| Real-time Search | Pencarian produk instan saat mengetik |
| Barcode Scanner | Support input via USB/Bluetooth barcode scanner |
| Product Grid | Klik produk untuk langsung tambah ke cart |
| Cart Management | Ubah qty, ubah harga, hapus item |
| Manual Input | Tambah item manual (jasa custom) |
| Multi Payment | Cash, Transfer Bank, QRIS (Midtrans) |
| Guest Customer | Transaksi tanpa perlu data customer |
| Quick Discount | Diskon per item atau total |
| Print Invoice | Cetak invoice PDF setelah transaksi |

**Alur Penggunaan POS:**
1. **Tambah Produk ke Cart**
   - Ketik nama/kode produk di kotak pencarian
   - Atau scan barcode produk
   - Atau klik produk dari grid
   
2. **Atur Cart**
   - Ubah qty dengan tombol +/- atau input langsung
   - Ubah harga jika ada diskon khusus
   - Hapus item dengan tombol X
   
3. **Proses Pembayaran**
   - Klik tombol **BAYAR SEKARANG**
   - Pilih metode pembayaran (Cash/Transfer/QRIS)
   - Jika Cash: masukkan nominal dibayar, sistem hitung kembalian
   - Jika Transfer: pilih bank dan masukkan nominal
   - Jika QRIS: generate QR code Midtrans
   
4. **Selesai**
   - Klik **Proses Pembayaran**
   - Invoice otomatis tergenerate
   - Stok otomatis berkurang
   - Cetak invoice jika diperlukan

**Metode Pembayaran:**
| Metode | Keterangan |
|--------|------------|
| Cash | Bayar tunai, sistem hitung kembalian |
| Transfer | Bayar via transfer bank (pilih nama bank) |
| QRIS/Midtrans | Pembayaran digital via QR code |

---

## 8. Kategori Pengeluaran

### 8.1 Pengeluaran

Menu dropdown untuk mencatat pengeluaran/biaya operasional.

---

#### 8.1.1 Kategori Pengeluaran

**Deskripsi:**  
Mengelola kategori/jenis pengeluaran (misalnya: Listrik, Air, Gaji, Transport, Konsumsi, dll).

**Fitur:**
- CRUD kategori pengeluaran
- Pencarian kategori

**Cara Menggunakan:**
1. Buka menu **Pengeluaran > Pengeluaran > Kategori Pengeluaran**
2. Klik **Tambah Kategori** untuk menambah jenis pengeluaran baru
3. Isi nama kategori dan deskripsi
4. Simpan

---

#### 8.1.2 Input Pengeluaran

**Deskripsi:**  
Form untuk mencatat pengeluaran operasional toko.

**Data yang Harus Diisi:**
| Field | Keterangan |
|-------|------------|
| Tanggal | Tanggal pengeluaran |
| Kategori | Pilih kategori pengeluaran |
| Keterangan | Detail pengeluaran |
| Nominal | Jumlah uang yang dikeluarkan |
| Bukti | Upload bukti pembayaran (opsional) |

**Cara Menggunakan:**
1. Buka menu **Pengeluaran > Pengeluaran > Input Pengeluaran**
2. Isi tanggal dan pilih kategori
3. Tulis keterangan pengeluaran
4. Masukkan nominal
5. Upload bukti jika ada
6. Klik Simpan

---

#### 8.1.3 Daftar Pengeluaran

**Deskripsi:**  
Menampilkan semua pengeluaran yang tercatat.

**Fitur:**
- DataTable dengan sorting dan pencarian
- Filter berdasarkan kategori dan tanggal
- Total pengeluaran per periode
- Edit dan hapus pengeluaran

**Cara Menggunakan:**
1. Buka menu **Pengeluaran > Pengeluaran > Daftar Pengeluaran**
2. Lihat semua pengeluaran yang tercatat
3. Gunakan filter untuk menyaring data
4. Total akan otomatis dihitung

---

## 9. Kategori Laporan

### 9.1 Laporan

Menu dropdown untuk melihat berbagai laporan bisnis.

---

#### 9.1.1 Laporan Kas Harian

**Deskripsi:**  
Laporan yang menampilkan ringkasan transaksi kas per hari.

**Isi Laporan:**
- Total penjualan hari ini
- Total pembelian hari ini
- Total pengeluaran hari ini
- Saldo kas harian
- Detail transaksi per jam

**Cara Menggunakan:**
1. Buka menu **Laporan > Laporan > Laporan Kas Harian**
2. Pilih tanggal yang ingin dilihat
3. Lihat ringkasan kas harian
4. Export ke Excel atau PDF jika diperlukan

---

#### 9.1.2 Laporan Ringkas Kasir

**Deskripsi:**  
Laporan performa masing-masing kasir dalam periode tertentu.

**Isi Laporan:**
- Jumlah transaksi per kasir
- Total penjualan per kasir
- Rata-rata nilai transaksi
- Perbandingan antar kasir

**Cara Menggunakan:**
1. Buka menu **Laporan > Laporan > Laporan Ringkas Kasir**
2. Pilih rentang tanggal
3. Lihat performa masing-masing kasir
4. Export jika diperlukan

---

#### 9.1.3 Laporan Laba/Rugi

**Deskripsi:**  
Laporan keuangan yang menampilkan kalkulasi laba atau rugi dalam periode tertentu.

**Komponen Laporan:**
- **Pendapatan:** Total penjualan
- **HPP (Harga Pokok Penjualan):** Total harga beli barang yang terjual
- **Laba Kotor:** Pendapatan - HPP
- **Biaya Operasional:** Total pengeluaran
- **Laba Bersih:** Laba Kotor - Biaya Operasional

**Cara Menggunakan:**
1. Buka menu **Laporan > Laporan > Laporan Laba/Rugi**
2. Pilih periode (harian, mingguan, bulanan, custom)
3. Klik **Generate** untuk membuat laporan
4. Lihat breakdown pendapatan dan biaya
5. Export ke Excel atau PDF

---

## 10. Kategori Pengguna

### 10.1 Manajemen User

Menu dropdown untuk mengelola pengguna sistem.

---

#### 10.1.1 Tambah Pengguna

**Deskripsi:**  
Form untuk menambahkan user baru ke sistem.

**Data yang Harus Diisi:**
| Field | Keterangan |
|-------|------------|
| Nama | Nama lengkap user |
| Email | Email untuk login |
| Password | Password minimal 8 karakter |
| Role | Pilih role/peran |
| Status | Aktif/Nonaktif |

**Cara Menggunakan:**
1. Buka menu **Pengguna > Manajemen User > Tambah Pengguna**
2. Isi semua data yang diperlukan
3. Pilih role yang sesuai
4. Klik Simpan

---

#### 10.1.2 Semua Pengguna

**Deskripsi:**  
Menampilkan daftar semua pengguna yang terdaftar di sistem.

**Fitur:**
- Melihat daftar user dengan role
- Edit data user
- Aktivasi/deaktivasi user
- Reset password

**Cara Menggunakan:**
1. Buka menu **Pengguna > Manajemen User > Semua Pengguna**
2. Lihat daftar user dengan role masing-masing
3. Klik **Edit** untuk mengubah data atau role
4. Toggle status untuk aktivasi/deaktivasi

---

#### 10.1.3 Peran & Hak Akses (Role & Permission)

**Deskripsi:**  
Mengelola role dan permission untuk kontrol akses fitur sistem.

**Role Default:**
| Role | Keterangan |
|------|------------|
| Owner | Akses penuh ke semua fitur termasuk laporan dan approval |
| Admin | Akses ke sebagian besar fitur kecuali pengaturan sensitif |
| Kasir | Akses terbatas ke POS dan transaksi |
| Gudang | Akses ke manajemen stok dan produk |

**Fitur:**
- Membuat role baru
- Mengatur permission per role
- Assign multiple permission ke role
- Melihat user per role

**Cara Menggunakan:**
1. Buka menu **Pengguna > Manajemen User > Peran & Hak Akses**
2. Lihat daftar role yang ada
3. Klik role untuk melihat permission yang dimiliki
4. Klik **Tambah Role** untuk membuat role baru
5. Centang permission yang diinginkan
6. Simpan

---

## 11. Kategori Pengaturan

### 11.1 Pengaturan Sistem

Menu dropdown untuk mengkonfigurasi sistem.

---

#### 11.1.1 Satuan Unit

**Deskripsi:**  
Mengelola daftar satuan/unit yang digunakan untuk produk (misalnya: pcs, set, pasang, liter, kg, dll).

**Cara Menggunakan:**
1. Buka menu **Pengaturan > Pengaturan Sistem > Satuan Unit**
2. Lihat daftar satuan yang ada
3. Klik **Tambah Satuan** untuk menambah baru
4. Isi nama satuan dan singkatan
5. Simpan

---

#### 11.1.2 Mata Uang

**Deskripsi:**  
Mengatur mata uang dan format tampilan angka dalam sistem.

**Pengaturan:**
- Simbol mata uang (Rp)
- Posisi simbol (sebelum/sesudah angka)
- Pemisah ribuan (. atau ,)
- Pemisah desimal (, atau .)
- Jumlah digit desimal

**Cara Menggunakan:**
1. Buka menu **Pengaturan > Pengaturan Sistem > Mata Uang**
2. Atur format sesuai kebutuhan
3. Simpan perubahan

---

#### 11.1.3 Pengaturan Umum

**Deskripsi:**  
Mengatur informasi umum toko yang akan muncul di invoice dan dokumen lainnya.

**Pengaturan:**
| Field | Keterangan |
|-------|------------|
| Nama Toko | Nama usaha/toko |
| Alamat | Alamat lengkap toko |
| Telepon | Nomor telepon toko |
| Email | Email toko |
| Logo | Upload logo toko |
| Kop Surat | Template header untuk invoice |
| Footer | Teks footer untuk invoice |

**Cara Menggunakan:**
1. Buka menu **Pengaturan > Pengaturan Sistem > Pengaturan Umum**
2. Isi semua informasi toko
3. Upload logo toko
4. Atur template kop surat
5. Simpan perubahan

---

#### 11.1.4 WhatsApp Settings

**Deskripsi:**  
Mengkonfigurasi integrasi notifikasi WhatsApp menggunakan Baileys (WhatsApp Web API).

**Fitur:**
- Koneksi WhatsApp via QR Code
- Status koneksi (Connected/Disconnected)
- Pengaturan notifikasi per event
- Template pesan custom
- Daftar penerima notifikasi

**Jenis Notifikasi:**
| Event | Keterangan |
|-------|------------|
| Penjualan Besar | Notifikasi saat ada transaksi di atas threshold |
| Stok Rendah | Notifikasi saat stok mencapai batas minimum |
| Pembayaran Online | Notifikasi saat ada pembayaran Midtrans |

**Cara Menggunakan:**
1. Buka menu **Pengaturan > Pengaturan Sistem > WhatsApp Settings**
2. Jika belum terhubung, klik **Connect** untuk generate QR Code
3. Scan QR Code dengan WhatsApp di HP
4. Setelah Connected (indikator hijau), atur notifikasi
5. Aktifkan/nonaktifkan notifikasi sesuai kebutuhan
6. Atur template pesan jika diperlukan
7. Tambahkan nomor penerima notifikasi
8. Tes kirim pesan untuk memastikan berfungsi

---

## Lampiran: Alur Kerja Sistem

### Alur Penjualan (POS)

```
Customer datang
      ↓
Kasir buka POS
      ↓
Tambah produk ke cart
      ↓
Proses pembayaran
      ↓
┌─────────────────┐
│ Cash/Transfer   │ → Input nominal → Hitung kembalian → Selesai
└─────────────────┘
      atau
┌─────────────────┐
│ QRIS/Midtrans   │ → Generate QR → Customer bayar → Callback → Selesai
└─────────────────┘
      ↓
Invoice tergenerate
      ↓
Stok berkurang otomatis
      ↓
Notifikasi ke Owner (jika diaktifkan)
```

### Alur Stock Opname

```
Admin buat sesi opname
      ↓
Generate list produk
      ↓
Hitung stok fisik
      ↓
Input qty ke sistem
      ↓
Sistem hitung variance
      ↓
Ada selisih?
├── Tidak → Status: Completed
└── Ya → Generate Adjustment Pending
           ↓
         Owner review
           ↓
         ┌── Approve → Stok diubah
         └── Reject → Stok tetap
```

---

*Dokumentasi ini dibuat untuk membantu pengguna memahami dan mengoperasikan Sistem POS Omah Ban dengan efektif.*
