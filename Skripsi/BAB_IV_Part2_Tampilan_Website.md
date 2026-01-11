# BAB IV - HASIL DAN PEMBAHASAN (Part 2)

## 4.2 Hasil Tampilan Website

Hasil dari perancangan sistem informasi ini adalah sebuah website Point of Sale (POS) yang dibangun menggunakan framework Laravel 11. Sistem ini memiliki berbagai fitur yang dirancang untuk memenuhi kebutuhan pengelolaan toko ban, mulai dari transaksi penjualan, manajemen produk, hingga laporan keuangan. Website ini memiliki antarmuka yang responsif menggunakan TailwindCSS dan komponen Flowbite, sehingga mudah digunakan baik di desktop maupun tablet. Setiap halaman disusun dengan rapi dan menggunakan warna serta ikon yang konsisten untuk memberikan kesan profesional.

### 4.2.1 Halaman Login

Halaman Login merupakan tampilan awal dimana pengguna dapat mengakses sistem informasi. Pengguna harus mengisi dua kolom pada halaman ini yaitu Email dan Password sesuai dengan akun yang dimiliki. Desain halaman login dibuat dengan tampilan sederhana dan modern menggunakan framework Laravel.

> **[Screenshot: Halaman Login]**
> *Gambar 4.9 Halaman Login Sistem POS Omah Ban*

Halaman ini menggunakan latar belakang dengan gradasi warna yang memberikan kesan profesional dan menarik. Tombol "Masuk" berfungsi sebagai penanda utama untuk melakukan autentikasi. Sistem juga dilengkapi dengan fitur keamanan berupa pembatasan percobaan login, dimana akun akan diblokir sementara setelah beberapa kali percobaan gagal.

### 4.2.2 Halaman Dashboard

Setelah pengguna berhasil masuk ke sistem, halaman Dashboard berfungsi sebagai tampilan awal. Dashboard menampilkan ringkasan informasi penting secara real-time, seperti total penjualan, pembelian, pengeluaran, dan profit hari ini. Tampilan dashboard disesuaikan berdasarkan role pengguna yang login.

> **[Screenshot: Halaman Dashboard]**
> *Gambar 4.10 Halaman Dashboard Sistem POS Omah Ban*

Di bagian bawah terdapat grafik penjualan mingguan dan bulanan yang menunjukkan tren keuangan secara visual. Fitur ini membantu pemilik toko mengawasi kinerja bisnis dengan mudah. Sidebar di sisi kiri berisi menu navigasi yang mencakup semua fitur sistem seperti produk, penjualan, pembelian, stok, dan laporan. Dashboard juga menampilkan peringatan stok rendah untuk produk yang perlu segera di-restock.

### 4.2.3 Halaman Produk

Halaman Produk berisi daftar semua produk yang tersedia dalam sistem. Pengguna dapat melihat detail seperti kode produk, nama produk, kategori, brand, stok, dan harga jual pada halaman ini. Sistem membedakan antara Produk Baru dan Produk Second (bekas) dalam menu terpisah.

> **[Screenshot: Halaman Produk]**
> *Gambar 4.11 Halaman Daftar Produk Baru*

Terdapat tombol "Edit" untuk mengubah data produk dan "Hapus" untuk menghapus produk yang tidak diperlukan. Untuk menambahkan produk baru ke dalam database, pengguna dapat mengklik tombol "Tambah Produk" di bagian kanan atas. Halaman ini juga dilengkapi fitur pencarian dan filter untuk memudahkan pencarian produk tertentu.

### 4.2.4 Halaman POS (Point of Sale)

Halaman POS merupakan fitur inti sistem yang digunakan untuk transaksi penjualan di kasir. Halaman ini dirancang untuk kecepatan dan kemudahan operasional. Di sisi kiri terdapat grid produk yang dapat diklik untuk ditambahkan ke keranjang, sedangkan di sisi kanan menampilkan daftar item yang akan dibeli beserta total harga.

> **[Screenshot: Halaman POS]**
> *Gambar 4.12 Halaman POS (Point of Sale)*

Sistem POS mendukung pencarian produk secara real-time, input menggunakan barcode scanner, serta penambahan item manual untuk jasa servis. Pengguna dapat mengatur jumlah, harga, dan diskon untuk setiap item. Metode pembayaran yang tersedia meliputi Cash, Transfer Bank, dan pembayaran digital melalui Midtrans/QRIS.

### 4.2.5 Halaman Penjualan

Halaman Penjualan menampilkan daftar semua transaksi penjualan yang telah dilakukan. Tabel menampilkan informasi seperti nomor invoice, tanggal transaksi, nama customer, kasir yang melayani, total harga, dan status pembayaran.

> **[Screenshot: Halaman Penjualan]**
> *Gambar 4.13 Halaman Daftar Penjualan*

Ada tombol "Detail" untuk melihat rincian transaksi, "Edit" untuk mengubah data, dan "Hapus" untuk menghapus transaksi. Pengguna juga dapat mencetak invoice PDF langsung dari halaman ini. Fitur filter berdasarkan tanggal dan status pembayaran memudahkan pencarian transaksi tertentu.

### 4.2.6 Halaman Pembelian

Halaman Pembelian digunakan untuk mencatat transaksi pembelian barang dari supplier. Tabel menampilkan informasi seperti nomor referensi, tanggal pembelian, nama supplier, total harga, status pengiriman, dan status pembayaran.

> **[Screenshot: Halaman Pembelian]**
> *Gambar 4.14 Halaman Daftar Pembelian*

Dengan menekan tombol "Tambah Pembelian", pengguna dapat menambahkan data pembelian baru dengan memilih supplier, tanggal, dan daftar produk yang dibeli. Sistem secara otomatis akan menambah stok produk ketika status pembelian diubah menjadi "Selesai".

### 4.2.7 Halaman Customer

Halaman Customer menampilkan dan mengelola informasi pelanggan yang terdaftar dalam sistem. Data yang ditampilkan meliputi nama, email, nomor telepon, dan alamat pelanggan.

> **[Screenshot: Halaman Customer]**
> *Gambar 4.15 Halaman Daftar Customer*

Dengan menekan tombol "Edit", pengguna dapat melakukan perubahan data atau menghapus data pelanggan dengan tombol "Hapus". Selain itu, ada tombol "Tambah Customer" di bagian kanan atas untuk menambahkan data pelanggan baru. Pengguna juga dapat melihat riwayat transaksi setiap customer.

### 4.2.8 Halaman Supplier

Halaman Supplier menampilkan daftar supplier atau pemasok barang yang terdaftar dalam sistem. Data yang ditampilkan meliputi nama supplier, email, nomor telepon, dan alamat.

> **[Screenshot: Halaman Supplier]**
> *Gambar 4.16 Halaman Daftar Supplier*

Dari halaman ini, pengguna dapat melihat riwayat pembelian dari setiap supplier serta total hutang yang belum dibayar. Fitur ini membantu admin mengelola hubungan dengan supplier secara efisien.

### 4.2.9 Halaman Stock Adjustment

Halaman Stock Adjustment digunakan untuk melakukan penyesuaian stok manual dengan alasan tertentu. Fitur ini diperlukan ketika ada selisih antara stok fisik dengan stok di sistem, misalnya karena barang rusak atau hilang.

> **[Screenshot: Halaman Stock Adjustment]**
> *Gambar 4.17 Halaman Daftar Stock Adjustment*

Setiap penyesuaian stok memerlukan persetujuan dari Owner sebelum perubahan stok diterapkan. Status adjustment meliputi Pending (menunggu persetujuan), Approved (disetujui), dan Rejected (ditolak). Fitur ini membantu menjaga akuntabilitas perubahan stok.

### 4.2.10 Halaman Stock Opname

Halaman Stock Opname digunakan untuk penghitungan stok fisik dan sinkronisasi dengan data di sistem. Pengguna dapat membuat sesi opname baru, kemudian menginput jumlah stok fisik untuk setiap produk.

> **[Screenshot: Halaman Stock Opname]**
> *Gambar 4.18 Halaman Stock Opname*

Sistem akan secara otomatis menghitung selisih antara stok fisik dan stok sistem. Jika terdapat selisih, sistem akan menghasilkan adjustment yang perlu disetujui oleh Owner. Fitur ini membantu menjaga akurasi data stok.

### 4.2.11 Halaman Laporan

Halaman Laporan menyediakan berbagai jenis laporan bisnis untuk analisis performa toko. Jenis laporan yang tersedia meliputi Laporan Harian, Laba Rugi, Ringkasan Kasir, Stok Rendah, dan Pergerakan Stok.

> **[Screenshot: Halaman Laporan]**
> *Gambar 4.19 Halaman Laporan Penjualan Harian*

Setiap laporan dapat difilter berdasarkan rentang tanggal dan dapat diekspor ke format Excel atau PDF. Grafik visualisasi data juga disediakan untuk memudahkan analisis. Halaman ini membantu pemilik toko memantau keuangan dan performa bisnis dengan mudah.

### 4.2.12 Halaman Pengaturan

Halaman Pengaturan digunakan untuk mengkonfigurasi berbagai aspek sistem. Pengaturan yang tersedia meliputi Profil Toko (nama, alamat, telepon, logo), Kop Surat untuk invoice, Satuan Unit produk, dan konfigurasi notifikasi WhatsApp.

> **[Screenshot: Halaman Pengaturan]**
> *Gambar 4.20 Halaman Pengaturan Sistem*

Fitur notifikasi WhatsApp memungkinkan sistem mengirimkan pemberitahuan otomatis kepada pemilik toko untuk transaksi tertentu atau peringatan stok rendah. Halaman ini hanya dapat diakses oleh pengguna dengan role Owner atau Admin.

### 4.2.13 Halaman Manajemen User

Halaman Manajemen User digunakan untuk mengelola pengguna sistem dan pembagian role. Data yang ditampilkan meliputi nama user, email, role, dan status aktif.

> **[Screenshot: Halaman User]**
> *Gambar 4.21 Halaman Daftar User*

Pengguna dapat menambah user baru, mengubah data user, mengaktifkan/menonaktifkan user, dan reset password. Sistem juga menyediakan halaman terpisah untuk mengelola Role dan Permission, dimana admin dapat membuat role baru dan mengatur izin akses untuk setiap role.

---

_[Lanjut ke Bagian 4.3 Pengujian Website - Part 3]_
