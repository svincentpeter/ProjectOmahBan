# Sistem Edit Penjualan

Dokumen ini menjelaskan cara kerja modul **Edit Penjualan** di OmahBan: arsitektur, alur data, endpoint, berkas-berkas yang terlibat, serta fitur dan prosedur debug.

---

## Ringkasan

* Edit dilakukan di `sales/{sale}/edit` menggunakan **cart session** (`Cart::instance('sale')`) sebagai *workspace* sementara.
* Saat membuka halaman, cart **hanya di-rebuild** dari `sale_details` jika cart masih **kosong**. Ini mencegah “nyangkut ke transaksi sebelumnya”.
* Perubahan nilai baris (qty/harga/diskon/pajak) dilakukan **inline** melalui AJAX, subtotal di UI selalu mengikuti perhitungan **server**.
* Saat **PATCH** (Update), sistem menjalankan **DB transaction** untuk menulis ulang header & detail, menghitung HPP/Profit, dan membuat **SalePayment** penyesuaian bila target bayar berubah.
* **Tidak ada mutasi stok** di proses Edit/Update (hanya di proses Store).

---

## Alur Kerja (end-to-end)

1. **Buka Edit**
   `GET /sales/{sale}/edit` → `SaleController@edit`

   * Set sesi `editing_sale_id`.
   * Jika pindah invoice, kosongkan cart lalu rebuild.
   * Rebuild cart hanya jika cart kosong. Saat `Cart::add([...])` **WAJIB** sertakan `weight: 0` dan isi `options` lengkap: `source_type`, `code`, `discount`, `tax`, `hpp`, `product_id`, `productable_type`, `productable_id`.

2. **Editor Keranjang (UI)**
   View `edit.blade.php` menampilkan tabel. Tiap baris memakai partial `partials/edit-row.blade.php` dengan komponen Alpine `rowEditor` (state: `rowId, source, price, qty, discount, tax`).

   * **AutoNumeric** memformat input uang (`.js-money`), Alpine menyimpan **integer rupiah** (dari helper `stripMoney`).
   * Item `second` memaksa `qty = 1` (readonly di UI & dijaga di server).

3. **Ubah Nilai Baris (Inline)**
   Event input men-debounce lalu **POST** ke `sales.cart.updateLine` → `CartController@updateLine`.

   * Server **sanitasi angka**, paksa `qty=1` untuk `second`.
   * Strategi aman: **remove+add** agar perubahan `price/options` selalu ter-update. Ini dapat menyebabkan **rowId baru**.
   * Response JSON:

     ```json
     {
       "ok": true,
       "rowIdNew": "abcd123",
       "lineSubtotal": 100000,
       "subtotalItems": 350000,
       "formatted": {"lineSubtotal":"100.000","subtotalItems":"350.000"}
     }
     ```
   * Frontend mengganti `rowId` bila ada `rowIdNew`, memperbarui subtotal baris (`.js-line-subtotal`) & subtotal item (`#js-cart-subtotal`).

4. **Tambah Item Manual**
   Form kirim ke `sales.cart.addManual` → `SaleController@addManualLine`.

   * Server menambah cart dengan `source_type='manual'`, `weight=0`.
   * Response JSON membawa `rowHtml` (baris siap pakai) + `subtotalItems/ formatted`.
   * UI menyisipkan baris, **re-init AutoNumeric & Alpine**, reset form.
   * **Loading SweetAlert** tidak di-`await`; gunakan `Swal.fire()` lalu `Swal.close()` setelah respon agar tidak macet.

5. **Hapus Baris**
   Tombol hapus → `sales.cart.removeLine` → hapus item di cart → hitung subtotal → kembalikan JSON; UI menghapus `<tr>`.

6. **Panel Ringkasan (Preview)**
   Komponen `invoiceSummary`:

   * Mengambil `subtotal` dari DOM (`#js-cart-subtotal`) dan mengamati perubahan via `MutationObserver`.
   * Menangani input `shipping`, `tax%`, `disc%`, `targetPaid`.
   * Menghitung **Grand Total** & **Status Pembayaran** (Unpaid/Partial/Paid).
   * Anti-autofill + **smart short-hand**: angka pendek (≤999) pada transaksi ribuan dianggap ribuan (100 ⇒ 100.000).

7. **Submit (PATCH)**
   Tombol “Update Sale” → konfirmasi → **unmask** input uang (integer) → **disable tombol** (anti double submit) → kirim ke `sales.update` → `SaleController@update`.

8. **Proses Update (Server)**

   * **Validasi** & normalisasi angka.
   * **Fail-fast** kalau cart kosong.
   * **DB::transaction()** untuk seluruh proses.
   * Bangun array detail dari cart (qty=1 jika `second`), hitung `sub_total` & `subtotal_profit`.
   * Hapus detail lama → insert ulang **(WAJIB isi `item_name`)**.
   * Hitung **total\_hpp** & **total\_profit**; update header (`total_amount`, `shipping_amount`, `tax_percentage`, `discount_percentage`, `status`, `payment_method`, `note`, dll.).
   * **Pembayaran penyesuaian**: jika target bayar ≠ paid sebelumnya → buat `SalePayment` dengan **`reference` unik** (mis. `SP-YYYYMMDD-HHMMSS-XXXXX`), nilai +/− sesuai delta, lalu sinkronkan `paid_amount` & `payment_status`.
   * **Tidak ada mutasi stok** pada Update (hanya pada Store).

9. **Show/Detail**
   `GET /sales/{sale}` → menampilkan header & `sale_details` terbaru, badge jenis (new/second/manual), total HPP, subtotal jual, laba, dan ringkasan pembayaran.

---

## Berkas yang Terlibat

### Controllers

* **`SaleController.php`**

  * `edit()` – rebuild cart sekali (jika kosong), set sesi `editing_sale_id`.
  * `addManualLine()` – tambah item manual ke cart; balas `rowHtml` + summary.
  * `removeLine()` – hapus 1 baris dari cart; balas summary.
  * `update()` – validasi, transaksi, tulis ulang header & detail, hitung HPP/Profit, buat `SalePayment` penyesuaian.
* **`CartController.php`**

  * `updateLine()` – update baris (sanitize, qty=1 untuk second, **remove+add**), kembalikan `rowIdNew` + subtotal.
* **(Opsional)** `PosController.php` – bila item berasal dari POS saat pembuatan awal.
* **(Opsional)** `SalePaymentsController.php` – jika ada UI khusus riwayat pembayaran.

### Views

* **`edit.blade.php`** – tabel editor, form PATCH, panel ringkasan, form tambah item manual; script:

  * Helpers `stripMoney/formatIDR`, `postJSON` (dengan timeout & abort), inisialisasi **AutoNumeric**, komponen Alpine `rowEditor` & `invoiceSummary`, serta penanganan **SweetAlert** (tanpa `await` pada loading).
* **`partials/edit-row.blade.php`** – baris item; event `onPriceInput/onQtyInput/onDiscInput/onTaxInput`, tombol sinkron (`_pushUpdate`) & hapus; elemen `.js-line-subtotal`.
* **`show.blade.php`** – tampilan detail penjualan terbaru.

### Routes (`web.php`)

```php
GET   /sales/{sale}/edit           -> SaleController@edit           -> name: sales.edit
PATCH /sales/{sale}                -> SaleController@update         -> name: sales.update
POST  /sales/cart/update-line      -> CartController@updateLine     -> name: sales.cart.updateLine
POST  /sales/cart/line/remove      -> SaleController@removeLine     -> name: sales.cart.removeLine
POST  /sales/cart/line/add-manual  -> SaleController@addManualLine  -> name: sales.cart.addManual
```

> **Pastikan tidak ada duplikasi nama route.**

### Models & Tabel

* **`Sale`** (header): `total_amount`, `paid_amount`, `shipping_amount`, `tax_percentage`, `discount_percentage`, `payment_status`, `total_hpp`, `total_profit`, `note`, dll.
* **`SaleDetails`** (detail): `item_name` (NOT NULL), `product_name`, `product_code`, `source_type` (new/second/manual), `quantity` (second=1), `price`, `hpp`, `sub_total`, `subtotal_profit`, `product_discount_amount`, `product_tax_amount`, `product_id`, `productable_*`.
* **`SalePayment`** (pembayaran): `reference` (NOT NULL), `amount` (+/− sesuai desain), `payment_method`, `date`, `note`.

---

## Kontrak API (Ringkas)

### `POST /sales/cart/update-line`

**Body**

```json
{ "rowId":"abc", "price":25000, "qty":2, "discount":0, "tax":0 }
```

**Response**

```json
{
  "ok": true,
  "rowIdNew": "def",            // opsional
  "lineSubtotal": 50000,
  "subtotalItems": 150000,
  "formatted": {"lineSubtotal":"50.000","subtotalItems":"150.000"}
}
```

### `POST /sales/cart/line/add-manual`

**Body**

```json
{ "name":"Jasa Balancing", "price":25000, "qty":1 }
```

**Response**

```json
{
  "ok": true,
  "rowHtml": "<tr>...</tr>",
  "subtotalItems": 175000,
  "formatted": {"subtotalItems":"175.000"}
}
```

### `POST /sales/cart/line/remove`

**Body**

```json
{ "rowId":"def" }
```

**Response**

```json
{ "ok": true, "subtotalItems": 125000, "formatted": {"subtotalItems":"125.000"} }
```

---

## Edge Case & Guard

* **Undefined array key `weight`** → sertakan `weight: 0` di setiap `Cart::add([...])`.
* **Cart kosong saat Update** → tolak update (fail-fast); tampilkan pesan; **jangan** menyentuh DB.
* **RowId berubah** setelah update-line → ambil `rowIdNew` di frontend & ganti state.
* **Item `second`** → `qty=1` (readonly di UI, dipaksa di server).
* **Kolom wajib DB** → `sale_details.item_name` & `sale_payments.reference` harus diisi di semua jalur penulisan.
* **Loading SweetAlert menggantung** → **jangan `await Swal.fire()`** untuk modal loading; panggil `Swal.close()` setelah fetch selesai.

---

## Checklist Uji Cepat

1. Buka `sales/{id}/edit`, ganti qty/harga/diskon/pajak → subtotal baris & item berubah **tanpa reload**.
2. Tambah item manual → baris muncul, subtotal bertambah, form bersih kembali.
3. Hapus baris → `<tr>` hilang, subtotal berkurang.
4. Ubah `Target Total Dibayar` → status bayar di preview berubah.
5. Klik **Update** → detail terbarui di `sales.show`, `SalePayment` penyesuaian tercatat (cek `reference`).
6. Coba submit ketika cart kosong → sistem menolak dengan pesan.

---

## Catatan Implementasi

* Gunakan **integer rupiah** di semua perhitungan; hindari `/100`.
* Profit yang disimpan: `subtotalItems - total_hpp` (tanpa memperhitungkan pajak/diskon **header**).
* Jika ingin refund ditulis **negatif**, pastikan kolom `amount` **tidak unsigned** atau simpan `type: refund`.

---

## Troubleshooting Cepat

* **Subtotal preview kedip/0 lalu berubah** → pastikan `invoiceSummary` menerima `initialSubtotal` dari server dan ada `MutationObserver` ke `#js-cart-subtotal`.
* **Target Bayar kembali ke 100** → aktifkan *smart short-hand* & anti-autofill; pastikan input memiliki `autocomplete="off"`.
* **Update menghasilkan detail kosong** → cek guard cart kosong & transaksi; pastikan loop insert menambah minimal 1 baris, jika tidak → throw & rollback.
* **Error 1364 (`item_name`/`reference`)** → tambahkan pengisian kolom tersebut pada proses insert.

---

## Perubahan Penting Terakhir

* Tambah `weight=0` pada rebuild & add manual.
* `CartController@updateLine` pakai **remove+add** dan mengembalikan `rowIdNew`.
* `SaleController@update` dibungkus **DB transaction**, isi `item_name`, buat `SalePayment` dengan `reference` unik.
* Skrip `edit.blade.php` menggunakan `postJSON` ber-timeout, anti-autoFill, dan **tidak** meng-`await` modal loading.

---

**Selesai.** Jika ada penyesuaian skema atau perilaku tambahan, tambahkan ke bagian *Perubahan Penting Terakhir* untuk jejak audit.
