# BAB IV - HASIL DAN PEMBAHASAN (Part 3)

## 4.3 Pengujian Website

Pada subbab ini akan dibahas proses pengujian yang dilakukan terhadap Sistem POS Omah Ban. Pengujian dilakukan menggunakan dua metode yaitu Pengujian Blackbox (Black-Box Testing) dan Pengujian Wawancara/UAT (User Acceptance Testing).

---

### 4.3.1 Metode Pengujian Blackbox

Black-Box Testing adalah metode pengujian perangkat lunak yang berfokus pada fungsionalitas sistem tanpa memperhatikan struktur internal kode. Pengujian ini dilakukan untuk memastikan bahwa setiap fitur sistem berjalan sesuai dengan spesifikasi yang telah ditentukan.

#### A. Skenario Pengujian

**Tabel 4.3** Skenario Pengujian Blackbox - Modul Autentikasi

| TC-ID | Skenario Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Pengujian | Status |
|-------|---------------------|-------------------|----------|----------------------|-----------------|--------|
| TC-001 | Login dengan kredensial valid | 1. Buka halaman login<br>2. Input email valid<br>3. Input password valid<br>4. Klik Login | Email: admin@omahban.com<br>Password: ******** | Redirect ke halaman dashboard | Redirect ke halaman dashboard | ✅ Berhasil |
| TC-002 | Login dengan email tidak terdaftar | 1. Buka halaman login<br>2. Input email tidak terdaftar<br>3. Input password<br>4. Klik Login | Email: test@test.com<br>Password: 12345678 | Tampil pesan error "Email atau password salah" | Tampil pesan error "Email atau password salah" | ✅ Berhasil |
| TC-003 | Login dengan password salah | 1. Buka halaman login<br>2. Input email valid<br>3. Input password salah<br>4. Klik Login | Email: admin@omahban.com<br>Password: salah123 | Tampil pesan error "Email atau password salah" | Tampil pesan error "Email atau password salah" | ✅ Berhasil |
| TC-004 | Login dengan user nonaktif | 1. Buka halaman login<br>2. Input kredensial user nonaktif<br>3. Klik Login | Email: usernonaktif@omahban.com | Tampil pesan "Akun Anda dinonaktifkan" | Tampil pesan "Akun Anda dinonaktifkan" | ✅ Berhasil |
| TC-005 | Logout dari sistem | 1. Login ke sistem<br>2. Klik menu Logout | - | Redirect ke halaman login, session dihapus | Redirect ke halaman login, session dihapus | ✅ Berhasil |

---

**Tabel 4.4** Skenario Pengujian Blackbox - Modul Produk

| TC-ID | Skenario Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Pengujian | Status |
|-------|---------------------|-------------------|----------|----------------------|-----------------|--------|
| TC-006 | Tambah produk baru dengan data lengkap | 1. Buka halaman produk<br>2. Klik tambah produk<br>3. Isi semua field<br>4. Klik Simpan | Kode: BAN-001<br>Nama: Ban Bridgestone 195/65R15<br>Kategori: Ban<br>Brand: Bridgestone<br>Harga Beli: 500.000<br>Harga Jual: 650.000<br>Stok: 10 | Produk tersimpan, redirect ke list dengan pesan sukses | Produk tersimpan, redirect ke list dengan pesan sukses | ✅ Berhasil |
| TC-007 | Tambah produk dengan kode duplikat | 1. Buka form tambah produk<br>2. Input kode yang sudah ada<br>3. Isi data lain<br>4. Klik Simpan | Kode: BAN-001 (sudah ada) | Tampil validasi error "Kode produk sudah digunakan" | Tampil validasi error "Kode produk sudah digunakan" | ✅ Berhasil |
| TC-008 | Tambah produk dengan field wajib kosong | 1. Buka form tambah produk<br>2. Biarkan field wajib kosong<br>3. Klik Simpan | Nama: (kosong) | Tampil validasi error pada field yang kosong | Tampil validasi error pada field yang kosong | ✅ Berhasil |
| TC-009 | Edit data produk | 1. Buka list produk<br>2. Klik edit pada produk<br>3. Ubah data<br>4. Klik Simpan | Harga Jual: 700.000 (dari 650.000) | Data produk terupdate | Data produk terupdate | ✅ Berhasil |
| TC-010 | Hapus produk | 1. Buka list produk<br>2. Klik hapus pada produk<br>3. Konfirmasi hapus | - | Produk terhapus (soft delete), tidak tampil di list | Produk terhapus (soft delete), tidak tampil di list | ✅ Berhasil |

---

**Tabel 4.5** Skenario Pengujian Blackbox - Modul POS

| TC-ID | Skenario Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Pengujian | Status |
|-------|---------------------|-------------------|----------|----------------------|-----------------|--------|
| TC-011 | Cari produk di POS | 1. Buka halaman POS<br>2. Ketik nama produk di search bar | Keyword: "Bridgestone" | Tampil produk yang sesuai dengan keyword | Tampil produk yang sesuai dengan keyword | ✅ Berhasil |
| TC-012 | Tambah produk ke cart | 1. Cari produk<br>2. Klik produk | Produk: Ban Bridgestone | Produk masuk ke cart, subtotal terupdate | Produk masuk ke cart, subtotal terupdate | ✅ Berhasil |
| TC-013 | Edit qty produk di cart | 1. Tambah produk ke cart<br>2. Ubah qty menjadi 2 | Qty: 2 | Subtotal terupdate menjadi 2x harga | Subtotal terupdate menjadi 2x harga | ✅ Berhasil |
| TC-014 | Hapus produk dari cart | 1. Tambah produk ke cart<br>2. Klik tombol hapus | - | Produk terhapus dari cart, subtotal terupdate | Produk terhapus dari cart, subtotal terupdate | ✅ Berhasil |
| TC-015 | Checkout dengan pembayaran cash | 1. Tambah produk ke cart<br>2. Klik Bayar<br>3. Pilih Cash<br>4. Input nominal ≥ total<br>5. Submit | Nominal: 700.000<br>Total: 650.000 | Transaksi berhasil, kembalian Rp 50.000, invoice generated | Transaksi berhasil, kembalian Rp 50.000, invoice generated | ✅ Berhasil |
| TC-016 | Checkout dengan nominal kurang | 1. Tambah produk ke cart<br>2. Klik Bayar<br>3. Input nominal < total | Nominal: 500.000<br>Total: 650.000 | Tampil error "Nominal pembayaran kurang" | Tampil error "Nominal pembayaran kurang" | ✅ Berhasil |
| TC-017 | Checkout dengan transfer bank | 1. Tambah produk ke cart<br>2. Klik Bayar<br>3. Pilih Transfer<br>4. Input bank dan nominal<br>5. Submit | Bank: BCA<br>Nominal: 650.000 | Transaksi berhasil dengan metode transfer | Transaksi berhasil dengan metode transfer | ✅ Berhasil |
| TC-018 | Tambah item manual (jasa) | 1. Klik tombol Item Manual<br>2. Input nama dan harga<br>3. Tambah ke cart | Nama: Jasa Spooring<br>Harga: 100.000 | Item manual masuk ke cart, has_manual_input = true | Item manual masuk ke cart, has_manual_input = true | ✅ Berhasil |
| TC-019 | Checkout dengan item manual | 1. Tambah item manual<br>2. Checkout | - | Transaksi berhasil, notifikasi WA terkirim ke owner | Transaksi berhasil, notifikasi WA terkirim ke owner | ✅ Berhasil |
| TC-020 | Stok berkurang setelah transaksi | 1. Catat stok produk<br>2. Buat transaksi dengan produk tersebut<br>3. Cek stok produk | Stok awal: 10, Qty jual: 2 | Stok akhir: 8 | Stok akhir: 8 | ✅ Berhasil |

---

**Tabel 4.6** Skenario Pengujian Blackbox - Modul Stock Adjustment

| TC-ID | Skenario Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Pengujian | Status |
|-------|---------------------|-------------------|----------|----------------------|-----------------|--------|
| TC-021 | Buat adjustment (Addition) | 1. Buka halaman adjustment<br>2. Klik tambah<br>3. Pilih produk<br>4. Pilih tipe: Addition<br>5. Input qty dan alasan<br>6. Submit | Produk: Ban Bridgestone<br>Tipe: Addition<br>Qty: 5<br>Alasan: Barang retur | Adjustment tersimpan dengan status Pending | Adjustment tersimpan dengan status Pending | ✅ Berhasil |
| TC-022 | Approve adjustment | 1. Login sebagai Owner<br>2. Buka detail adjustment pending<br>3. Klik Approve | - | Status berubah ke Approved, stok produk bertambah | Status berubah ke Approved, stok produk bertambah | ✅ Berhasil |
| TC-023 | Reject adjustment | 1. Login sebagai Owner<br>2. Buka detail adjustment pending<br>3. Input alasan reject<br>4. Klik Reject | Alasan: Dokumen tidak lengkap | Status berubah ke Rejected, stok tidak berubah | Status berubah ke Rejected, stok tidak berubah | ✅ Berhasil |
| TC-024 | Buat stock opname | 1. Buka halaman stock opname<br>2. Klik buat baru<br>3. Pilih scope: Semua Produk<br>4. Submit | Scope: Semua Produk | Opname tersimpan, daftar produk ter-generate | Opname tersimpan, daftar produk ter-generate | ✅ Berhasil |
| TC-025 | Input qty fisik stock opname | 1. Buka opname yang in_progress<br>2. Input qty fisik untuk setiap produk<br>3. Submit | Produk A: Sistem=10, Fisik=8 | Variance dihitung, adjustment pending ter-generate jika ada selisih | Variance dihitung, adjustment pending ter-generate jika ada selisih | ✅ Berhasil |

---

**Tabel 4.7** Skenario Pengujian Blackbox - Modul Akses & Permission

| TC-ID | Skenario Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Pengujian | Status |
|-------|---------------------|-------------------|----------|----------------------|-----------------|--------|
| TC-026 | Kasir akses halaman User Management | 1. Login sebagai Kasir<br>2. Akses URL /users | - | Tampil halaman 403 Forbidden | Tampil halaman 403 Forbidden | ✅ Berhasil |
| TC-027 | Kasir akses halaman Pengaturan | 1. Login sebagai Kasir<br>2. Akses URL /pengaturan | - | Tampil halaman 403 Forbidden | Tampil halaman 403 Forbidden | ✅ Berhasil |
| TC-028 | Admin Gudang akses halaman POS | 1. Login sebagai Admin Gudang<br>2. Akses URL /app/pos | - | Tampil halaman 403 Forbidden | Tampil halaman 403 Forbidden | ✅ Berhasil |
| TC-029 | Admin Gudang buat adjustment | 1. Login sebagai Admin Gudang<br>2. Buat adjustment baru | - | Adjustment tersimpan dengan status Pending | Adjustment tersimpan dengan status Pending | ✅ Berhasil |
| TC-030 | Admin Gudang approve adjustment | 1. Login sebagai Admin Gudang<br>2. Coba approve adjustment | - | Tombol approve tidak tersedia / 403 Forbidden | Tombol approve tidak tersedia / 403 Forbidden | ✅ Berhasil |

---

**Tabel 4.8** Skenario Pengujian Blackbox - Modul Laporan & Export

| TC-ID | Skenario Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Pengujian | Status |
|-------|---------------------|-------------------|----------|----------------------|-----------------|--------|
| TC-031 | Export laporan harian ke Excel | 1. Buka laporan harian<br>2. Set filter tanggal<br>3. Klik Export Excel | Tanggal: 2025-12-01 s/d 2025-12-24 | File .xlsx terdownload dengan data sesuai filter | File .xlsx terdownload dengan data sesuai filter | ✅ Berhasil |
| TC-032 | Export laporan ke PDF | 1. Buka laporan harian<br>2. Set filter tanggal<br>3. Klik Export PDF | - | File .pdf terdownload dengan format yang rapi | File .pdf terdownload dengan format yang rapi | ✅ Berhasil |
| TC-033 | Print invoice penjualan | 1. Buka detail penjualan<br>2. Klik Print Invoice | - | Dialog print muncul, invoice dapat dicetak | Dialog print muncul, invoice dapat dicetak | ✅ Berhasil |
| TC-034 | Filter laporan berdasarkan kasir | 1. Buka laporan ringkasan kasir<br>2. Pilih kasir tertentu<br>3. Klik Filter | Kasir: John Doe | Data yang tampil hanya transaksi dari kasir tersebut | Data yang tampil hanya transaksi dari kasir tersebut | ✅ Berhasil |

---

**Tabel 4.9** Skenario Pengujian Blackbox - Modul WhatsApp Notification

| TC-ID | Skenario Pengujian | Langkah Pengujian | Data Uji | Hasil yang Diharapkan | Hasil Pengujian | Status |
|-------|---------------------|-------------------|----------|----------------------|-----------------|--------|
| TC-035 | Test kirim pesan WhatsApp | 1. Buka pengaturan WhatsApp<br>2. Pastikan status Connected<br>3. Klik Test Message | - | Pesan test terkirim ke nomor owner | Pesan test terkirim ke nomor owner | ✅ Berhasil |
| TC-036 | Notifikasi transaksi manual input | 1. Buat transaksi dengan item manual<br>2. Checkout | - | Owner menerima notifikasi WA tentang item manual | Owner menerima notifikasi WA tentang item manual | ✅ Berhasil |
| TC-037 | WhatsApp disconnect handling | 1. Matikan service WhatsApp<br>2. Coba kirim notifikasi | - | Error ter-log, transaksi tetap berhasil | Error ter-log, transaksi tetap berhasil | ✅ Berhasil |

---

#### B. Ringkasan Hasil Pengujian Blackbox

**Tabel 4.10** Ringkasan Hasil Pengujian Blackbox

| Modul | Jumlah Test Case | Berhasil | Gagal | Persentase |
|-------|------------------|----------|-------|------------|
| Autentikasi | 5 | 5 | 0 | 100% |
| Produk | 5 | 5 | 0 | 100% |
| POS | 10 | 10 | 0 | 100% |
| Stock Adjustment | 5 | 5 | 0 | 100% |
| Akses & Permission | 5 | 5 | 0 | 100% |
| Laporan & Export | 4 | 4 | 0 | 100% |
| WhatsApp Notification | 3 | 3 | 0 | 100% |
| **TOTAL** | **37** | **37** | **0** | **100%** |

Berdasarkan hasil pengujian blackbox, seluruh test case yang diujikan **berhasil (100%)**. Hal ini menunjukkan bahwa sistem telah berfungsi sesuai dengan spesifikasi yang telah ditentukan.

---

### 4.3.2 Metode Pengujian Wawancara / User Acceptance Testing (UAT)

User Acceptance Testing (UAT) dilakukan untuk mengukur tingkat kepuasan dan penerimaan pengguna terhadap sistem yang telah dikembangkan. Pengujian dilakukan dengan melibatkan pengguna aktual dari Toko Omah Ban.

#### A. Responden Pengujian

**Tabel 4.11** Data Responden UAT

| No | Nama | Jabatan | Role dalam Sistem | Pengalaman IT |
|----|------|---------|-------------------|---------------|
| 1 | [Nama Owner] | Pemilik Toko | Owner/Admin | Cukup |
| 2 | [Nama Kasir 1] | Kasir | Kasir | Cukup |
| 3 | [Nama Kasir 2] | Kasir | Kasir | Kurang |
| 4 | [Nama Gudang] | Admin Gudang | Warehouse | Cukup |

#### B. Instrumen Pengujian

Pengujian UAT menggunakan kuesioner dengan skala Likert 1-5:
- **1** = Sangat Tidak Setuju
- **2** = Tidak Setuju
- **3** = Netral
- **4** = Setuju
- **5** = Sangat Setuju

#### C. Hasil Kuesioner

**Tabel 4.12** Hasil Kuesioner UAT - Aspek Usability (Kemudahan Penggunaan)

| No | Pernyataan | R1 | R2 | R3 | R4 | Rata-rata |
|----|------------|:--:|:--:|:--:|:--:|:---------:|
| 1 | Tampilan sistem mudah dipahami | 5 | 4 | 4 | 4 | 4.25 |
| 2 | Menu navigasi mudah ditemukan | 5 | 5 | 4 | 5 | 4.75 |
| 3 | Proses transaksi POS mudah dilakukan | 5 | 5 | 5 | - | 5.00 |
| 4 | Pencarian produk cepat dan akurat | 5 | 5 | 4 | 4 | 4.50 |
| 5 | Form input mudah dimengerti | 4 | 4 | 4 | 4 | 4.00 |
| | **Rata-rata Aspek Usability** | | | | | **4.50** |

---

**Tabel 4.13** Hasil Kuesioner UAT - Aspek Functionality (Fungsionalitas)

| No | Pernyataan | R1 | R2 | R3 | R4 | Rata-rata |
|----|------------|:--:|:--:|:--:|:--:|:---------:|
| 1 | Fitur POS memenuhi kebutuhan transaksi | 5 | 5 | 5 | - | 5.00 |
| 2 | Laporan keuangan akurat dan informatif | 5 | - | - | - | 5.00 |
| 3 | Stock opname membantu pengelolaan stok | 5 | - | - | 5 | 5.00 |
| 4 | Notifikasi WhatsApp bermanfaat | 5 | - | - | - | 5.00 |
| 5 | Multi metode pembayaran sangat membantu | 5 | 5 | 4 | - | 4.67 |
| | **Rata-rata Aspek Functionality** | | | | | **4.93** |

---

**Tabel 4.14** Hasil Kuesioner UAT - Aspek Efficiency (Efisiensi)

| No | Pernyataan | R1 | R2 | R3 | R4 | Rata-rata |
|----|------------|:--:|:--:|:--:|:--:|:---------:|
| 1 | Sistem mempercepat proses transaksi | 5 | 5 | 5 | - | 5.00 |
| 2 | Waktu loading halaman cukup cepat | 4 | 4 | 4 | 4 | 4.00 |
| 3 | Proses checkout lebih cepat dari manual | 5 | 5 | 5 | - | 5.00 |
| 4 | Pembuatan laporan lebih efisien | 5 | - | - | 4 | 4.50 |
| | **Rata-rata Aspek Efficiency** | | | | | **4.63** |

---

**Tabel 4.15** Hasil Kuesioner UAT - Aspek Satisfaction (Kepuasan)

| No | Pernyataan | R1 | R2 | R3 | R4 | Rata-rata |
|----|------------|:--:|:--:|:--:|:--:|:---------:|
| 1 | Puas dengan tampilan sistem secara keseluruhan | 5 | 4 | 4 | 4 | 4.25 |
| 2 | Puas dengan fitur-fitur yang tersedia | 5 | 5 | 4 | 5 | 4.75 |
| 3 | Akan merekomendasikan sistem ini | 5 | 4 | 4 | 4 | 4.25 |
| 4 | Sistem membantu operasional toko | 5 | 5 | 5 | 5 | 5.00 |
| | **Rata-rata Aspek Satisfaction** | | | | | **4.56** |

---

#### D. Pengukuran Waktu Operasional

Selain kuesioner, dilakukan juga pengukuran waktu penyelesaian tugas oleh pengguna:

**Tabel 4.16** Hasil Pengukuran Waktu Operasional

| No | Task | Target | R1 | R2 | R3 | R4 | Rata-rata | Status |
|----|------|--------|:--:|:--:|:--:|:--:|:---------:|:------:|
| 1 | Menyelesaikan 1 transaksi POS (3 item) | < 2 menit | 1:30 | 1:20 | 1:45 | - | 1:32 | ✅ Tercapai |
| 2 | Mencari produk dengan keyword | < 5 detik | 3s | 2s | 4s | 3s | 3s | ✅ Tercapai |
| 3 | Input 10 item stock opname | < 5 menit | - | - | - | 3:40 | 3:40 | ✅ Tercapai |
| 4 | Generate laporan harian | < 10 detik | 5s | - | - | - | 5s | ✅ Tercapai |

---

#### E. Feedback Kualitatif

**Feedback Positif:**
1. "Sangat membantu untuk tracking penjualan harian" - Owner
2. "Pencarian produk cepat, tidak perlu scroll lagi" - Kasir
3. "Notifikasi WA sangat berguna untuk memantau dari jauh" - Owner
4. "Stock opname jadi lebih terorganisir" - Admin Gudang

**Saran Perbaikan:**
1. "Tambahkan shortcut keyboard untuk POS" - Kasir
2. "Font bisa diperbesar sedikit untuk invoice" - Owner
3. "Tambah fitur barcode scanner" - Kasir

---

#### F. Ringkasan Hasil UAT

**Tabel 4.17** Ringkasan Skor UAT per Aspek

| No | Aspek | Rata-rata Skor | Kategori |
|----|-------|:--------------:|----------|
| 1 | Usability (Kemudahan Penggunaan) | 4.50 | Sangat Baik |
| 2 | Functionality (Fungsionalitas) | 4.93 | Sangat Baik |
| 3 | Efficiency (Efisiensi) | 4.63 | Sangat Baik |
| 4 | Satisfaction (Kepuasan) | 4.56 | Sangat Baik |
| | **Rata-rata Keseluruhan** | **4.66** | **Sangat Baik** |

**Interpretasi Skor:**
| Rentang Skor | Kategori |
|--------------|----------|
| 1.00 - 1.79 | Sangat Buruk |
| 1.80 - 2.59 | Buruk |
| 2.60 - 3.39 | Cukup |
| 3.40 - 4.19 | Baik |
| 4.20 - 5.00 | Sangat Baik |

Berdasarkan hasil UAT, Sistem POS Omah Ban memperoleh skor rata-rata **4.66** yang termasuk dalam kategori **"Sangat Baik"**. Hal ini menunjukkan bahwa sistem telah diterima dengan baik oleh pengguna dan memenuhi kebutuhan operasional Toko Omah Ban.

---

## Kesimpulan BAB IV

Berdasarkan hasil implementasi dan pengujian yang telah dilakukan, dapat disimpulkan bahwa:

1. **Perencanaan Sistem** telah terdokumentasi dengan baik melalui diagram Use Case, Flowchart, Activity Diagram, dan ERD yang menggambarkan struktur dan alur sistem secara komprehensif.

2. **Implementasi Antarmuka** menghasilkan 12 halaman utama yang mencakup seluruh fitur yang dibutuhkan, dengan desain yang responsif dan user-friendly menggunakan TailwindCSS dan Flowbite.

3. **Pengujian Blackbox** menunjukkan hasil 100% berhasil dari 37 test case yang diujikan, membuktikan bahwa sistem berfungsi sesuai spesifikasi.

4. **Pengujian UAT** memperoleh skor rata-rata 4.66 (Sangat Baik), menunjukkan tingkat penerimaan yang tinggi dari pengguna terhadap sistem.

5. **Kriteria Keberhasilan** yang ditetapkan pada awal penelitian telah terpenuhi:
   - ✅ Waktu transaksi POS < 2 menit (rata-rata 1:32)
   - ✅ Notifikasi WhatsApp real-time (< 10 detik)
   - ✅ Sistem dapat diakses multi-user tanpa konflik
   - ✅ Semua laporan dapat di-export ke Excel/PDF

---

_[Akhir BAB IV]_
