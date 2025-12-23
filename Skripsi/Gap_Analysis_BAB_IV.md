# 📊 Gap Analysis: Spesifikasi Produk vs Kebutuhan BAB IV

## Executive Summary

Dokumen ini menganalisis kesenjangan antara **Spesifikasi_Produk_Bab_IV.md** yang sudah ada dengan **kebutuhan BAB IV skripsi** berdasarkan rekomendasi GPT dan standar akademis.

---

## 🔍 Status Analisis

| Kategori          | Status di Spek Produk | Rekomendasi GPT               | Gap Status |
| ----------------- | --------------------- | ----------------------------- | ---------- |
| Arsitektur sistem | ✅ Lengkap            | ✅ Sudah ada                  | ✔️ OK      |
| Flowchart proses  | ⚠️ Sebagian           | Perlu 4-5 flow                | ⚠️ PARTIAL |
| Data model (ERD)  | ⚠️ 2 tabel            | Perlu 6-10 tabel              | ❌ KURANG  |
| Implementasi UI   | ⚠️ 4 halaman          | Perlu semua modul             | ❌ KURANG  |
| API/Endpoint      | ⚠️ Daftar saja        | Perlu contoh request/response | ⚠️ PARTIAL |
| Black-box testing | ✅ 15 test cases      | Perlu actual output           | ⚠️ PARTIAL |
| UAT/Usability     | ✅ Instrumen ada      | Perlu hasil nyata             | ❌ KURANG  |
| Deployment        | ⚠️ Rencana            | Perlu kondisi aktual          | ⚠️ PARTIAL |

---

## 📁 Mapping File Proyek vs Kebutuhan

### 1. Entitas/Model yang Sudah Ada di Proyek

#### Sale Module (`Modules/Sale/Entities/`)

| File                   | Status di Spek | Perlu Ditambahkan        |
| ---------------------- | -------------- | ------------------------ |
| `Sale.php`             | ✅ Tabel F.1.1 | ✔️ Sudah lengkap         |
| `SaleDetails.php`      | ❌ Belum ada   | ⚠️ **PERLU DITAMBAHKAN** |
| `SalePayment.php`      | ❌ Belum ada   | ⚠️ **PERLU DITAMBAHKAN** |
| `SaleReturn.php`       | ❌ Belum ada   | ⚠️ **PERLU DITAMBAHKAN** |
| `SaleReturnDetail.php` | ❌ Belum ada   | Optional                 |
| `ManualInputLog.php`   | ❌ Belum ada   | Optional                 |
| `Quotation.php`        | ❌ Belum ada   | Optional                 |

#### Product Module (`Modules/Product/Entities/`)

| File                | Status di Spek | Perlu Ditambahkan        |
| ------------------- | -------------- | ------------------------ |
| `Product.php`       | ✅ Tabel F.1.2 | ✔️ Sudah lengkap         |
| `ProductSecond.php` | ❌ Belum ada   | ⚠️ **PERLU DITAMBAHKAN** |
| `Category.php`      | ❌ Belum ada   | ⚠️ **PERLU DITAMBAHKAN** |
| `Brand.php`         | ❌ Belum ada   | ⚠️ **PERLU DITAMBAHKAN** |
| `ServiceMaster.php` | ❌ Belum ada   | Optional (untuk jasa)    |
| `StockLedger.php`   | ❌ Belum ada   | Optional                 |

#### Purchase Module (`Modules/Purchase/Entities/`)

| File                  | Status di Spek | Perlu Ditambahkan        |
| --------------------- | -------------- | ------------------------ |
| `Purchase.php`        | ❌ Belum ada   | ⚠️ **PERLU DITAMBAHKAN** |
| `PurchaseDetail.php`  | ❌ Belum ada   | ⚠️ **PERLU DITAMBAHKAN** |
| `PurchasePayment.php` | ❌ Belum ada   | ⚠️ **PERLU DITAMBAHKAN** |
| `PurchaseSecond.php`  | ❌ Belum ada   | Optional                 |

#### Adjustment Module (`Modules/Adjustment/Entities/`)

| File                  | Status di Spek | Perlu Ditambahkan        |
| --------------------- | -------------- | ------------------------ |
| `Adjustment.php`      | ❌ Belum ada   | ⚠️ **PERLU DITAMBAHKAN** |
| `StockMovement.php`   | ⚠️ Disebutkan  | ✔️ Deskripsi ada         |
| `StockOpname.php`     | ⚠️ Flow ada    | Perlu tabel detail       |
| `StockOpnameItem.php` | ❌ Belum ada   | Optional                 |

#### People Module (Customer & Supplier)

| File           | Status di Spek | Perlu Ditambahkan        |
| -------------- | -------------- | ------------------------ |
| `Customer.php` | ❌ Belum ada   | ⚠️ **PERLU DITAMBAHKAN** |
| `Supplier.php` | ❌ Belum ada   | ⚠️ **PERLU DITAMBAHKAN** |

#### Other Modules

| File                  | Status di Spek | Perlu Ditambahkan        |
| --------------------- | -------------- | ------------------------ |
| `Expense.php`         | ❌ Belum ada   | ⚠️ **PERLU DITAMBAHKAN** |
| `ExpenseCategory.php` | ❌ Belum ada   | Optional                 |
| User/Role/Permission  | ❌ Belum ada   | ⚠️ **PERLU DITAMBAHKAN** |

---

### 2. Flowchart yang Sudah Ada vs yang Dibutuhkan

| Flowchart                    | Status di Spek | Rekomendasi GPT          |
| ---------------------------- | -------------- | ------------------------ |
| Authentication & Role        | ✅ D.1         | ✔️ Sudah lengkap         |
| POS Checkout                 | ✅ D.2         | ✔️ Sudah lengkap         |
| Stock Opname                 | ✅ D.3         | ✔️ Sudah lengkap         |
| Purchase sampai penerimaan   | ❌ Belum ada   | ⚠️ **PERLU DITAMBAHKAN** |
| Sale Return                  | ❌ Belum ada   | ⚠️ **PERLU DITAMBAHKAN** |
| Expense dan dampak laba-rugi | ❌ Belum ada   | Optional                 |
| Notifikasi stok rendah       | ❌ Belum ada   | Optional                 |

---

### 3. Implementasi Antarmuka (Screenshot) yang Dibutuhkan

#### Yang Sudah Ada di Spek (E.2):

| Halaman         | Status   | Keterangan          |
| --------------- | -------- | ------------------- |
| Login           | ✅ E.2.1 | Spesifikasi lengkap |
| Dashboard       | ✅ E.2.2 | Spesifikasi lengkap |
| POS             | ✅ E.2.3 | Spesifikasi lengkap |
| Products (Baru) | ✅ E.2.4 | Spesifikasi lengkap |

#### Yang BELUM Ada (Perlu Ditambahkan):

| Halaman                 | Priority  | Lokasi File                                        |
| ----------------------- | --------- | -------------------------------------------------- |
| Products Second (Bekas) | 🔴 High   | `Modules/Product/Resources/views/products_second/` |
| Purchases               | 🔴 High   | `Modules/Purchase/Resources/views/`                |
| Customers               | 🟡 Medium | `Modules/People/Resources/views/customers/`        |
| Suppliers               | 🟡 Medium | `Modules/People/Resources/views/suppliers/`        |
| Expenses                | 🟡 Medium | `Modules/Expense/Resources/views/`                 |
| Stock Adjustment        | 🔴 High   | `Modules/Adjustment/Resources/views/adjustments/`  |
| Stock Opname            | 🔴 High   | `Modules/Adjustment/Resources/views/stock-opname/` |
| Reports (2-3 contoh)    | 🔴 High   | `Modules/Reports/Resources/views/`                 |
| User Management         | 🟡 Medium | `Modules/User/Resources/views/`                    |
| Settings                | 🟢 Low    | `Modules/Setting/Resources/views/`                 |
| WhatsApp Settings       | 🟡 Medium | `resources/views/whatsapp/`                        |

---

### 4. Black-Box Testing: Dari Rencana ke Hasil

| TC-ID  | Feature                   | Status Saat Ini | Yang Perlu Diisi            |
| ------ | ------------------------- | --------------- | --------------------------- |
| TC-001 | Login Valid               | ✅ Rencana      | ⚠️ Actual Output, Pass/Fail |
| TC-002 | Login Invalid Password    | ✅ Rencana      | ⚠️ Actual Output, Pass/Fail |
| TC-003 | Login User Nonaktif       | ✅ Rencana      | ⚠️ Actual Output, Pass/Fail |
| TC-004 | Create Product            | ✅ Rencana      | ⚠️ Actual Output, Pass/Fail |
| TC-005 | Create Product Duplicate  | ✅ Rencana      | ⚠️ Actual Output, Pass/Fail |
| TC-006 | POS Add to Cart           | ✅ Rencana      | ⚠️ Actual Output, Pass/Fail |
| TC-007 | POS Checkout Cash         | ✅ Rencana      | ⚠️ Actual Output, Pass/Fail |
| TC-008 | POS Checkout Insufficient | ✅ Rencana      | ⚠️ Actual Output, Pass/Fail |
| TC-009 | Stock Adjustment Create   | ✅ Rencana      | ⚠️ Actual Output, Pass/Fail |
| TC-010 | Stock Adjustment Approve  | ✅ Rencana      | ⚠️ Actual Output, Pass/Fail |
| TC-011 | Role Access Denied        | ✅ Rencana      | ⚠️ Actual Output, Pass/Fail |
| TC-012 | Report Export Excel       | ✅ Rencana      | ⚠️ Actual Output, Pass/Fail |
| TC-013 | Midtrans Callback         | ✅ Rencana      | ⚠️ Actual Output, Pass/Fail |
| TC-014 | Stock Opname Complete     | ✅ Rencana      | ⚠️ Actual Output, Pass/Fail |
| TC-015 | WhatsApp Test Message     | ✅ Rencana      | ⚠️ Actual Output, Pass/Fail |

**Tambahan yang diperlukan:**

-   ✅ Kolom "Actual Output"
-   ✅ Kolom "Status (Pass/Fail)"
-   ✅ Lingkungan uji (PHP 8.2, MariaDB 10.x, Chrome, Windows)
-   ✅ Pembahasan temuan/bug

---

### 5. UAT/Evaluasi Pengguna

| Aspek               | Status Saat Ini | Yang Perlu Ditambahkan                     |
| ------------------- | --------------- | ------------------------------------------ |
| Instrumen kuesioner | ✅ H.2.2        | ✔️ Sudah ada                               |
| Profil responden    | ❌ Belum ada    | ⚠️ Nama, role, pengalaman                  |
| Hasil skor          | ❌ Belum ada    | ⚠️ **Rata-rata skor Likert per indikator** |
| Temuan/feedback     | ❌ Belum ada    | ⚠️ **Poin keluhan & rekomendasi**          |
| Ringkasan evaluasi  | ❌ Belum ada    | ⚠️ **Kesimpulan UAT**                      |

---

### 6. Deployment & Environment

| Aspek             | Status Saat Ini | Rekomendasi                                  |
| ----------------- | --------------- | -------------------------------------------- |
| Environment table | ✅ I.1          | ⚠️ Ubah ke "pengujian lokal" jika belum live |
| Dependencies      | ✅ I.2          | ✔️ Sudah lengkap                             |
| Env variables     | ✅ I.3          | ✔️ Sudah lengkap                             |
| Backup strategy   | ✅ I.4          | ⚠️ Hilangkan jika belum diimplementasi       |

---

## 📋 CHECKLIST PRIORITAS

### 🔴 WAJIB (High Priority)

1. **Tambahkan Data Model Tabel:**

    - [ ] SaleDetails (struktur kolom)
    - [ ] SalePayments (struktur kolom)
    - [ ] Purchase & PurchaseDetails
    - [ ] Customer & Supplier
    - [ ] Adjustment & StockMovement
    - [ ] ERD diagram (atau deskripsi relasi)

2. **Tambahkan Flowchart:**

    - [ ] Purchase workflow (dari PO sampai penerimaan barang)
    - [ ] Sale Return workflow (jika fitur ada)

3. **Tambahkan Implementasi Antarmuka:**

    - [ ] Products Second (screenshot + penjelasan)
    - [ ] Purchases (screenshot + penjelasan)
    - [ ] Stock Adjustment (screenshot + penjelasan)
    - [ ] Stock Opname (screenshot + penjelasan)
    - [ ] Reports (2-3 contoh + export)

4. **Lengkapi Black-Box Testing:**

    - [ ] Jalankan semua 15 test case
    - [ ] Isi Actual Output
    - [ ] Isi Status Pass/Fail
    - [ ] Tulis pembahasan temuan

5. **Lengkapi UAT:**
    - [ ] Lakukan pengujian dengan user nyata (Owner, Kasir, Warehouse)
    - [ ] Kumpulkan skor kuesioner
    - [ ] Hitung rata-rata per indikator
    - [ ] Tulis kesimpulan evaluasi

### 🟡 SEBAIKNYA (Medium Priority)

6. **Perbaiki Inkonsistensi:**

    - [ ] Klarifikasi "approval" di Use Case Matrix (D.4) vs batasan "tidak ada multi approval"
    - [ ] Jelaskan bahwa approval adalah single-level oleh Owner/Admin

7. **Tambahkan Contoh API:**

    - [ ] Request/Response JSON untuk 3-5 endpoint penting
    - [ ] Daftar error codes (403, 422, 500)

8. **Perbaiki Deployment Section:**
    - [ ] Ubah ke "simulasi environment" jika belum live
    - [ ] Jelaskan bahwa pengujian di Laragon lokal

### 🟢 OPSIONAL (Low Priority)

9. **Flowchart tambahan:**

    - [ ] Expense dan dampak ke laporan laba-rugi
    - [ ] Notifikasi stok rendah (otomatis)

10. **Antarmuka tambahan:**
    - [ ] Settings (kop surat, dll)
    - [ ] WhatsApp settings page

---

## 📄 Format BAB IV yang Disarankan

Berdasarkan analisis, berikut struktur BAB IV yang direkomendasikan:

```
BAB IV IMPLEMENTASI DAN PENGUJIAN

4.1 Gambaran Umum Implementasi Sistem
    - Ringkasan modul yang diimplementasikan
    - Ringkasan role dan batasan akses

4.2 Lingkungan Pengembangan dan Implementasi
    - Spesifikasi perangkat keras
    - Spesifikasi perangkat lunak
    - Struktur folder proyek

4.3 Implementasi Basis Data
    - ERD atau deskripsi relasi tabel
    - Deskripsi tabel inti (6-10 tabel)
    - Aturan data penting

4.4 Implementasi Proses Bisnis dan Alur Sistem
    - Flowchart autentikasi
    - Flowchart POS checkout
    - Flowchart purchase
    - Flowchart stock opname/adjustment

4.5 Implementasi Antarmuka Sistem
    - Login & Dashboard
    - POS
    - Products (baru & bekas)
    - Purchases
    - Stock Adjustment & Opname
    - Reports
    - User Management
    - Settings

4.6 Implementasi Fungsi Backend dan Integrasi
    - RBAC (Spatie Permission)
    - Integrasi Midtrans
    - Integrasi WhatsApp Baileys

4.7 Pengujian Sistem
    4.7.1 Metode Pengujian
    4.7.2 Skenario dan Hasil Uji (Black-Box)
    4.7.3 Pembahasan Hasil Uji

4.8 Evaluasi Pengguna (UAT)
    - Profil responden
    - Instrumen kuesioner
    - Hasil pengujian
    - Kesimpulan evaluasi

4.9 Ringkasan Bab IV
```

---

## 💡 Saran Aksi Cepat

1. **Screenshot dulu** - Ambil screenshot semua halaman utama aplikasi untuk lampiran
2. **Jalankan test case** - Eksekusi 15 test case dan catat hasilnya
3. **Ekspor data model** - Gunakan tool seperti MySQL Workbench untuk generate ERD
4. **Wawancara user** - Lakukan UAT singkat dengan 1-2 user dan catat feedback

---

> [!TIP]
> Dokumen ini dapat digunakan sebagai panduan untuk melengkapi `Spesifikasi_Produk_Bab_IV.md` agar siap menjadi BAB IV skripsi yang lengkap.

**Generated:** 22 Desember 2025
