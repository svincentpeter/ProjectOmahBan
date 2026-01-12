# BAB IV - HASIL DAN PEMBAHASAN (Part 2)

## 4.2 Hasil Tampilan Website

Hasil dari perancangan sistem pada penelitian ini adalah sebuah website Point of Sale (POS) untuk Omah Ban yang dibangun menggunakan framework Laravel 11. Sistem menyediakan fitur-fitur yang mendukung proses operasional toko, mulai dari pencatatan transaksi penjualan, pengelolaan master data produk (kategori, merek, produk baru, produk bekas), pengelolaan jasa layanan, hingga kebutuhan pelaporan dan kontrol stok.

Website dirancang dengan antarmuka yang responsif menggunakan TailwindCSS dan komponen Flowbite, sehingga nyaman digunakan pada perangkat desktop maupun tablet. Secara tampilan, sistem menggunakan komponen yang konsisten (kartu ringkasan, tabel data, tombol aksi, dan sidebar menu) agar pengguna dapat memahami alur penggunaan dengan cepat dan meminimalkan kesalahan saat input data.

Sistem ini menerapkan pembagian hak akses berbasis peran (role-based access control) dengan tiga role utama. Role pertama adalah Owner sebagai pemilik yang memiliki akses penuh ke seluruh fitur sistem termasuk laporan keuangan, manajemen user, dan pengaturan sistem. Role kedua adalah Kasir yang fokus pada transaksi penjualan melalui halaman POS serta pengelolaan data customer. Role ketiga adalah Staff Gudang yang mengelola stok, pembelian dari supplier, dan melakukan stock opname. Setiap halaman yang akan dijelaskan pada sub-bab ini akan mencantumkan role yang berhak mengakses beserta konteks penggunaannya.

### 4.2.1 Halaman Login

Halaman Login merupakan tampilan awal untuk mengakses sistem dan dapat diakses oleh seluruh role yaitu Owner, Kasir, dan Staff Gudang. Pada halaman ini, pengguna diminta mengisi Alamat Email dan Kata Sandi sesuai akun yang telah terdaftar. Tersedia juga fitur Ingat Saya untuk memudahkan login berikutnya dan tautan Lupa kata sandi? sebagai bantuan jika pengguna mengalami kendala akses. Tombol "Masuk ke Dashboard" digunakan untuk melakukan autentikasi dan masuk ke halaman utama sesuai hak akses pengguna.

Desain halaman login dibuat sederhana dan fokus pada fungsi autentikasi, dengan kartu login di tengah layar sehingga mudah dipahami pengguna. Halaman ini berperan penting sebagai pembatas akses, sehingga hanya pengguna yang memiliki akun dan izin yang dapat menggunakan fitur-fitur di dalam sistem. Setelah berhasil login, sistem akan mengarahkan pengguna ke Dashboard dengan tampilan menu sidebar yang disesuaikan berdasarkan role masing-masing, dimana Owner akan melihat menu lengkap, sementara Kasir dan Staff Gudang hanya melihat menu sesuai kewenangannya.

> **[Screenshot: Halaman Login]** > _Gambar 4.10 Halaman Login Sistem POS Omah Ban_

### 4.2.2 Halaman Dashboard

Setelah pengguna berhasil login, sistem menampilkan halaman Dashboard yang berfungsi sebagai tampilan ringkasan untuk membantu pengguna memantau kondisi toko secara cepat. Halaman ini dapat diakses oleh seluruh role namun dengan tampilan yang berbeda sesuai kewenangan masing-masing. Pada halaman ini ditampilkan informasi utama dalam bentuk kartu ringkasan (summary cards), seperti total penjualan, keuntungan/pendapatan, jumlah produk, dan informasi ringkas lain yang relevan. Selain itu, dashboard juga menampilkan informasi waktu (jam/hari/tanggal) sehingga memudahkan pencatatan aktivitas harian operasional.

Di sisi kiri, tersedia sidebar navigasi yang mengelompokkan menu berdasarkan fungsi. Owner dapat melihat seluruh menu termasuk laporan keuangan lengkap, grafik penjualan, manajemen user, dan akses ke pengaturan sistem. Kasir melihat ringkasan transaksi harian dan akses cepat ke halaman POS serta menu penjualan dan customer. Staff Gudang melihat ringkasan stok, peringatan stok rendah, dan akses ke menu pembelian, supplier, serta penyesuaian stok. Dengan tampilan yang ringkas namun informatif, dashboard membantu pengguna melihat gambaran besar sesuai tanggung jawab masing-masing role tanpa harus membuka halaman satu per satu.

> **[Screenshot: Halaman Dashboard - Login sebagai Owner]** > _Gambar 4.11 Halaman Dashboard dengan Sidebar Menu Lengkap (Role: Owner)_

### 4.2.3 Halaman Menu Manajemen Produk

Menu Manajemen Produk digunakan untuk mengelola master data yang menjadi dasar operasional sistem, khususnya data kategori, merek, produk baru, produk bekas, dan jasa. Fitur pada menu ini penting karena seluruh transaksi penjualan dan proses pengelolaan stok mengacu pada data master yang dibuat pada bagian ini. Secara umum, setiap halaman pada modul ini sudah dilengkapi dengan tabel data, pencarian, serta tombol aksi untuk mengelola data.

Owner memiliki akses penuh untuk menambah, mengubah, dan menghapus data produk serta bertanggung jawab menetapkan harga jual, harga modal, dan stok minimum untuk setiap produk. Kasir dapat melihat daftar produk untuk keperluan informasi saat melayani pelanggan namun tidak dapat mengubah data produk. Staff Gudang dapat melihat daftar produk untuk keperluan pengecekan stok dan stock opname namun tidak dapat mengubah harga atau data master produk.

#### a. Halaman Kategori Produk

Halaman Kategori Produk digunakan untuk mengelompokkan produk agar pencarian dan pengelolaan data menjadi lebih terstruktur. Pengguna dapat melihat daftar kategori dan melakukan pencarian kategori, sedangkan Owner dapat menambahkan kategori baru melalui tombol Tambah Kategori. Pengelompokan kategori ini membantu konsistensi input produk dan mempermudah proses filter pada halaman daftar produk maupun saat transaksi di POS.

> **[Screenshot: Halaman Kategori Produk - Login sebagai Owner]** > _Gambar 4.12 Halaman Kategori Produk dengan Akses CRUD Penuh (Role: Owner)_

#### b. Halaman Merek Produk

Halaman Merek Produk digunakan untuk mengelola data brand/merek. Halaman ini menyediakan daftar merek beserta jumlah produk yang terkait pada masing-masing merek. Data merek berfungsi sebagai referensi saat input produk, sehingga penamaan merek menjadi konsisten dan mengurangi risiko salah input. Fitur tambah, edit, dan hapus merek hanya tersedia untuk Owner.

> **[Screenshot: Halaman Merek Produk - Login sebagai Owner]** > _Gambar 4.13 Halaman Merek Produk (Role: Owner)_

#### c. Halaman Produk Baru

Halaman Daftar Produk Baru memuat data produk yang dijual dalam kondisi baru. Halaman ini menyajikan tabel informasi produk seperti nama produk, kategori/merek, stok, serta informasi harga sesuai kebutuhan sistem. Selain itu, tersedia fitur filter berdasarkan merek atau status stok, fitur pencarian, serta tombol aksi untuk mengelola data. Owner dapat memantau ketersediaan produk dan melakukan pembaruan data produk secara cepat, sementara Kasir dan Staff Gudang dapat melihat informasi stok untuk keperluan operasional tanpa dapat mengubah data.

> **[Screenshot: Halaman Produk Baru - Login sebagai Owner]** > _Gambar 4.14 Halaman Produk Baru dengan Tombol Aksi Edit/Hapus (Role: Owner)_

#### d. Halaman Produk Bekas

Halaman Produk Bekas digunakan untuk mengelola produk second (bekas). Pemisahan menu produk baru dan produk bekas dilakukan agar pencatatan dan kontrol data lebih jelas, karena produk bekas umumnya memiliki kondisi, harga, dan jumlah stok yang berbeda dari produk baru. Setiap produk bekas bersifat unik dengan stok sama dengan satu per item. Halaman ini menampilkan daftar produk bekas beserta informasi penting untuk operasional, serta menyediakan fitur pengelolaan data bagi Owner.

> **[Screenshot: Halaman Produk Bekas - Login sebagai Owner]** > _Gambar 4.15 Halaman Produk Bekas (Role: Owner)_

#### e. Halaman Daftar Jasa

Halaman Daftar Jasa berisi layanan servis seperti jasa pasang ban, spooring, dan balancing yang dijual di toko. Pencatatan jasa sebagai master data diperlukan agar jasa dapat dipilih langsung saat transaksi POS dan tercatat rapi di laporan. Halaman ini menampilkan ringkasan jumlah data jasa dan menyediakan tombol tambah jasa bagi Owner untuk menambahkan layanan baru. Kasir dapat melihat daftar jasa untuk keperluan informasi saat melayani customer.

> **[Screenshot: Halaman Daftar Jasa - Login sebagai Owner]** > _Gambar 4.16 Halaman Daftar Jasa (Role: Owner)_

#### f. Halaman Tambah Produk Baru

Halaman Tambah Produk Baru hanya dapat diakses oleh Owner dan digunakan untuk memasukkan data produk ke dalam sistem. Form ini mencakup informasi utama seperti nama dan kode barang, kategori, merek, spesifikasi seperti ring atau ukuran, harga modal dan harga jual, pengaturan stok meliputi stok awal dan stok minimum, satuan unit, catatan tambahan, serta unggah gambar produk. Ketersediaan form yang terstruktur membantu memastikan data produk tersimpan lengkap dan konsisten, sehingga mendukung transaksi POS dan kontrol stok berjalan lebih akurat.

> **[Screenshot: Halaman Tambah Produk Baru - Login sebagai Owner]** > _Gambar 4.17 Halaman Tambah Produk Baru (Role: Owner)_

### 4.2.4 Halaman Menu Penyesuaian Stok

Menu Penyesuaian Stok berada pada bagian Stok dan Gudang serta digunakan untuk memastikan data stok di sistem tetap akurat dan dapat dipertanggungjawabkan. Pada menu ini sistem menyediakan dua mekanisme utama, yaitu Stock Opname untuk pencocokan stok fisik dengan stok sistem dan Penyesuaian Manual untuk koreksi stok karena alasan tertentu seperti barang rusak atau hilang. Selain itu, tersedia halaman Semua Penyesuaian untuk melihat riwayat adjustment serta halaman Approval Penyesuaian untuk proses persetujuan oleh Owner.

Staff Gudang bertugas membuat sesi stock opname, menghitung stok fisik, dan mengajukan penyesuaian manual jika ditemukan selisih atau kerusakan barang. Owner bertugas mereview dan menyetujui atau menolak pengajuan penyesuaian dari Staff Gudang. Kasir tidak memiliki akses ke menu ini karena tidak terkait dengan tugas transaksi penjualan.

#### a. Halaman Stock Opname

Halaman Stock Opname menampilkan daftar sesi opname yang pernah dilakukan dan dapat diakses oleh Owner dan Staff Gudang. Staff Gudang menggunakan halaman ini untuk memulai proses penghitungan stok fisik secara berkala sesuai kebijakan toko, baik harian, mingguan, atau bulanan. Owner dapat memonitor seluruh aktivitas opname yang dilakukan oleh Staff Gudang melalui halaman ini.

> **[Screenshot: Halaman Stock Opname - Login sebagai Staff Gudang]** > _Gambar 4.18 Halaman Stock Opname dengan Sidebar Menu Terbatas (Role: Staff Gudang)_

#### b. Halaman Buat Stock Opname

Halaman Buat Stock Opname digunakan untuk membuat sesi opname baru dan terutama diakses oleh Staff Gudang sebagai pembuat utama, meskipun Owner juga dapat membuat jika diperlukan. Pada halaman ini pengguna menentukan tanggal opname dan sistem menghasilkan reference secara otomatis. Selanjutnya pengguna memilih scope perhitungan berupa Semua Produk, Per Kategori, atau Pilih Manual sesuai kebutuhan. Setelah sesi dibuat, sistem akan menghasilkan daftar produk sesuai scope, lalu pengguna menginput jumlah stok fisik. Sistem akan membantu proses kontrol dengan menampilkan perbedaan variance antara stok fisik dan stok sistem.

Hasil proses opname bersifat otomatis dimana apabila tidak ada selisih maka sesi akan berstatus Completed, namun apabila ada selisih maka sistem akan membentuk adjustment dengan status pending untuk diproses oleh Owner sesuai mekanisme persetujuan.

> **[Screenshot: Halaman Buat Stock Opname - Login sebagai Staff Gudang]** > _Gambar 4.19 Halaman Buat Stock Opname (Role: Staff Gudang)_

#### c. Halaman Buat Penyesuaian Manual

Halaman Buat Penyesuaian Manual digunakan untuk melakukan koreksi stok secara manual ketika terdapat kondisi tertentu di lapangan dan terutama diakses oleh Staff Gudang sebagai pembuat pengajuan. Staff Gudang mengisi informasi pengajuan seperti tanggal yang bersifat wajib, referensi yang dibuat otomatis, memilih alasan penyesuaian seperti rusak, hilang, kesalahan input, atau bonus supplier, serta menuliskan keterangan detail sebagai pendukung. Sistem juga menyediakan fitur unggah bukti gambar untuk meningkatkan akuntabilitas pengajuan.

Di bagian daftar item, pengguna menambahkan produk yang akan disesuaikan, menentukan tipe berupa Penambahan atau Pengurangan beserta qty. Setelah pengajuan dikirim melalui tombol Ajukan Penyesuaian, data akan masuk ke daftar pengajuan yang menunggu persetujuan Owner.

> **[Screenshot: Halaman Buat Penyesuaian Manual - Login sebagai Staff Gudang]** > _Gambar 4.20 Halaman Buat Penyesuaian Manual (Role: Staff Gudang)_

#### d. Halaman Semua Penyesuaian

Halaman Semua Penyesuaian menampilkan riwayat seluruh adjustment stok yang pernah diajukan maupun diproses. Owner dapat melihat semua pengajuan dari seluruh Staff Gudang, sedangkan Staff Gudang hanya dapat melihat pengajuan yang mereka buat sendiri. Pada bagian atas terdapat ringkasan jumlah penyesuaian berdasarkan status seperti Pending, Approved, dan Rejected agar pengguna dapat memantau kondisi pengajuan secara cepat. Halaman ini juga menyediakan filter berdasarkan status, tipe, pengaju, serta rentang tanggal untuk memudahkan pencarian data penyesuaian.

> **[Screenshot: Halaman Semua Penyesuaian - Login sebagai Owner]** > _Gambar 4.21 Halaman Semua Penyesuaian Menampilkan Seluruh Pengajuan (Role: Owner)_

#### e. Halaman Approval Penyesuaian

Halaman Approval Penyesuaian hanya dapat diakses oleh Owner dan digunakan untuk memproses pengajuan adjustment yang masih pending. Pada halaman ini sistem menampilkan indikator ringkas seperti jumlah pending, pengajuan yang bersifat urgent karena melewati batas waktu tertentu, serta jumlah pengajuan yang telah disetujui atau ditolak. Daftar pengajuan yang tampil memuat informasi seperti kode referensi, pihak yang membuat pengajuan, alasan, jumlah produk, dan tanggal pengajuan.

Owner dapat memilih Approve atau Reject untuk setiap pengajuan. Jika disetujui maka perubahan stok akan diterapkan ke sistem dan tercatat sebagai log pergerakan stok, sedangkan jika ditolak maka stok tidak berubah dan pengajuan berstatus rejected disertai alasan penolakan. Mekanisme ini memastikan perubahan stok tidak dilakukan sembarangan serta meningkatkan kontrol dan akuntabilitas dalam pengelolaan inventaris.

> **[Screenshot: Halaman Approval Penyesuaian - Login sebagai Owner]** > _Gambar 4.22 Halaman Approval Penyesuaian dengan Tombol Approve/Reject (Role: Owner)_

### 4.2.5 Halaman Menu Data Relasi

Menu Data Relasi digunakan untuk mengelola data pihak yang berhubungan langsung dengan transaksi, yaitu Supplier sebagai pemasok pembelian dan Customer sebagai pelanggan penjualan. Data pada menu ini berperan sebagai master data relasi yang akan dipanggil otomatis pada modul pembelian maupun penjualan, sehingga input transaksi menjadi lebih cepat, konsisten, dan mudah ditelusuri. Terdapat pembagian akses berdasarkan jenis relasi dimana Supplier dikelola oleh Owner dan Staff Gudang, sedangkan Customer dikelola oleh Owner dan Kasir.

#### a. Halaman Daftar Supplier

Halaman Daftar Supplier digunakan untuk menampilkan dan mengelola daftar pemasok yang terdaftar pada sistem. Halaman ini dapat diakses oleh Owner dengan akses CRUD penuh dan Staff Gudang yang dapat melihat serta menambahkan supplier untuk keperluan pembelian. Pada bagian atas halaman terdapat ringkasan seperti Total Supplier, Supplier Aktif, jangkauan kota, dan Total Transaksi pembelian yang pernah dilakukan. Owner menggunakan halaman ini untuk mengelola hubungan dengan supplier dan memantau total hutang yang belum dibayar, sedangkan Staff Gudang dapat melihat daftar supplier dan menambahkan supplier baru saat akan membuat transaksi pembelian. Kasir tidak memiliki akses ke halaman ini karena tidak terkait dengan proses penjualan.

> **[Screenshot: Halaman Daftar Supplier - Login sebagai Staff Gudang]** > _Gambar 4.23 Halaman Daftar Supplier dengan Sidebar Menu Terbatas (Role: Staff Gudang)_

#### b. Halaman Tambah Supplier

Halaman Tambah Supplier dapat diakses oleh Owner dan Staff Gudang untuk input data supplier baru sebagai referensi pada transaksi pembelian. Form berisi field utama seperti Nama Supplier, Email, No. Telepon, Kota, Negara, serta Alamat Lengkap. Sistem memberikan bantuan melalui panel Tips Pengisian agar data yang dimasukkan konsisten dan lengkap.

> **[Screenshot: Halaman Tambah Supplier - Login sebagai Staff Gudang]** > _Gambar 4.24 Halaman Tambah Supplier (Role: Staff Gudang)_

#### c. Halaman Daftar Customer

Halaman Daftar Customer digunakan untuk mengelola data pelanggan yang digunakan pada transaksi penjualan. Halaman ini dapat diakses oleh Owner dengan akses CRUD penuh dan Kasir yang dapat melihat serta menambahkan customer untuk keperluan transaksi. Pada bagian ringkasan, sistem menampilkan informasi seperti Total Customer, Customer Aktif, sebaran kota, serta Total Transaksi penjualan. Owner menggunakan halaman ini untuk menganalisis data pelanggan dan melihat riwayat transaksi, sedangkan Kasir dapat melihat daftar customer dan menambahkan customer baru saat proses transaksi POS jika pelanggan belum terdaftar. Staff Gudang tidak memiliki akses ke halaman ini karena tidak terkait dengan proses pembelian atau stok.

> **[Screenshot: Halaman Daftar Customer - Login sebagai Kasir]** > _Gambar 4.25 Halaman Daftar Customer dengan Sidebar Menu Terbatas (Role: Kasir)_

#### d. Halaman Tambah Customer

Halaman Tambah Customer dapat diakses oleh Owner dan Kasir untuk menambahkan data customer baru. Form input berisi data utama seperti Nama Customer, Email, No. Telepon, Kota, Negara, dan Alamat Lengkap. Kasir biasanya mengakses halaman ini saat ada pelanggan baru yang ingin didaftarkan sebelum melakukan transaksi di POS.

> **[Screenshot: Halaman Tambah Customer - Login sebagai Kasir]** > _Gambar 4.26 Halaman Tambah Customer (Role: Kasir)_

### 4.2.6 Halaman Menu Pembelian Stok

Menu Pembelian digunakan untuk mencatat dan memantau transaksi pembelian stok yang masuk ke toko, baik pembelian produk baru dari supplier maupun pembelian produk bekas dari customer. Fitur pada menu ini membantu pencatatan pembelian menjadi lebih rapi karena sistem menyimpan detail item, status proses pembelian, serta status pembayaran berupa lunas, parsial, atau belum lunas. Selain itu, pembelian yang berstatus Completed akan menjadi acuan perubahan stok secara sistematis sehingga kontrol persediaan lebih akurat.

Owner memiliki akses penuh untuk memonitor seluruh transaksi pembelian dan memantau status pembayaran kepada supplier. Staff Gudang bertugas membuat dan mengelola transaksi pembelian sehari-hari termasuk mencatat barang yang diterima dari supplier. Kasir tidak memiliki akses ke menu ini karena fokus pada transaksi penjualan.

#### a. Halaman Buat Pembelian

Halaman Buat Pembelian dapat diakses oleh Owner dan Staff Gudang untuk mencatat transaksi pembelian baru. Pengguna memilih produk yang akan dibeli melalui bagian Pilih Produk, kemudian item yang dipilih akan masuk ke daftar Item Pembelian untuk diisi jumlah qty dan harga beli. Pada sisi kanan, terdapat bagian Detail Pembelian yang berisi informasi header transaksi seperti Supplier, Tanggal Pembelian, Metode Pembayaran, nomor referensi opsional, status transaksi, serta catatan tambahan. Sistem juga menyediakan perhitungan total belanja dan kontrol pembayaran termasuk opsi pelunasan cepat seperti Lunas 100%, 50%, atau 25% untuk mendukung pembayaran parsial. Stok akan bertambah secara otomatis ketika status transaksi diubah menjadi Completed.

> **[Screenshot: Halaman Buat Pembelian - Login sebagai Staff Gudang]** > _Gambar 4.27 Halaman Buat Pembelian (Role: Staff Gudang)_

#### b. Halaman Daftar Pembelian

Halaman Daftar Pembelian menampilkan riwayat pembelian stok baru dari supplier dan dapat diakses oleh Owner untuk monitoring penuh serta Staff Gudang untuk mengelola pembelian. Pada bagian atas halaman terdapat ringkasan seperti Total Pembelian, Total Nilai, Terbayar, dan Sisa Hutang sehingga pengguna dapat memantau kondisi pembayaran pembelian secara cepat. Halaman ini dilengkapi fitur filter yang memudahkan pencarian data seperti filter cepat untuk hari ini, kemarin, minggu ini, atau bulan ini, filter rentang tanggal, pemilihan supplier, serta filter status bayar. Staff Gudang menggunakan halaman ini untuk melihat status pembelian yang mereka buat dan melakukan follow-up jika ada barang yang belum diterima.

> **[Screenshot: Halaman Daftar Pembelian - Login sebagai Owner]** > _Gambar 4.28 Halaman Daftar Pembelian dengan Sidebar Menu Lengkap (Role: Owner)_

#### c. Halaman Daftar Pembelian Bekas

Halaman Daftar Pembelian Bekas menampilkan riwayat pembelian produk second atau bekas yang umumnya berasal dari customer yang ingin menjual barang bekasnya. Halaman ini dapat diakses oleh Owner dan Staff Gudang dengan tampilan dan fitur serupa daftar pembelian stok baru, namun fokusnya pada transaksi pembelian produk bekas. Ringkasan di bagian atas berupa total transaksi, total nilai, terbayar, dan sisa hutang membantu pengguna memantau kondisi transaksi pembelian bekas dengan cepat.

> **[Screenshot: Halaman Daftar Pembelian Bekas - Login sebagai Staff Gudang]** > _Gambar 4.29 Halaman Daftar Pembelian Bekas (Role: Staff Gudang)_

### 4.2.7 Halaman Menu Penjualan

Menu Penjualan merupakan modul yang digunakan untuk mengelola seluruh aktivitas penjualan, mulai dari pembuatan penawaran harga atau quotation, pencatatan transaksi penjualan, hingga proses retur dan refund. Pada modul ini sistem menyediakan tampilan ringkasan berupa summary card, filter data, serta tabel transaksi yang mendukung pencarian dan ekspor laporan sehingga memudahkan admin atau kasir dalam memantau transaksi harian. Owner memiliki akses penuh untuk monitoring dan analisis sedangkan Kasir fokus pada operasional penjualan. Staff Gudang tidak memiliki akses ke menu ini karena fokus pada pengelolaan stok dan pembelian.

#### a. Halaman Penawaran (Quotation)

Halaman Daftar Penawaran dapat diakses oleh Owner dan Kasir untuk membuat dan mengelola penawaran harga kepada customer sebelum transaksi penjualan dilakukan. Halaman ini menampilkan daftar penawaran berdasarkan reference, nama customer, status penawaran, dan total nilai. Kasir biasanya membuat penawaran untuk customer yang memerlukan perbandingan harga atau sedang mempertimbangkan pembelian dalam jumlah besar. Fitur utama pada penawaran mencakup pembuatan draft penawaran multi-item, penetapan masa berlaku expired date, pencetakan penawaran ke PDF, serta alur konversi penawaran menjadi penjualan ketika customer menyetujui.

> **[Screenshot: Halaman Penawaran - Login sebagai Kasir]** > _Gambar 4.30 Halaman Penawaran dengan Sidebar Menu Terbatas (Role: Kasir)_

#### b. Halaman Semua Penjualan

Halaman Daftar Transaksi Penjualan menampilkan seluruh histori transaksi penjualan dan dapat diakses oleh Owner untuk analisis dan monitoring penuh serta Kasir untuk melihat transaksi yang dibuat. Di bagian atas halaman tersedia ringkasan seperti total penjualan, total profit, dan total transaksi sehingga pengguna dapat melihat gambaran performa penjualan secara cepat. Owner menggunakan halaman ini untuk menganalisis performa penjualan, melihat tren, dan memantau kinerja kasir. Kasir dapat melihat transaksi yang mereka buat untuk keperluan pengecekan atau mencetak ulang invoice. Halaman ini juga menyediakan panel Filter Data Penjualan berdasarkan periode, kasir, status, serta fitur pencarian dan ekspor ke Excel, PDF, atau Print.

> **[Screenshot: Halaman Semua Penjualan - Login sebagai Owner]** > _Gambar 4.31 Halaman Semua Penjualan dengan Filter dan Akses Lengkap (Role: Owner)_

#### c. Halaman Retur Penjualan

Halaman Retur Penjualan digunakan untuk mencatat pengembalian barang oleh customer dan proses refund. Kasir bertugas membuat pengajuan retur berdasarkan keluhan customer, kemudian Owner mereview dan menyetujui proses refund. Halaman ini menampilkan ringkasan status retur seperti Menunggu Persetujuan, Disetujui, Selesai, serta total Refund Bulan Ini. Dalam proses retur, pengguna memilih invoice terkait, menentukan item dan kuantitas retur, menambahkan alasan retur, serta memilih metode refund seperti Cash atau Store Credit. Setelah retur diproses dan disetujui Owner, stok akan bertambah kembali secara otomatis sesuai item yang diretur.

> **[Screenshot: Halaman Retur Penjualan - Login sebagai Kasir]** > _Gambar 4.32 Halaman Retur Penjualan (Role: Kasir)_

### 4.2.8 Halaman Menu Pengeluaran

Menu Pengeluaran merupakan modul yang digunakan untuk mengelola biaya operasional bisnis atau expense secara terstruktur. Modul ini hanya dapat diakses oleh Owner karena berkaitan dengan keuangan dan pengeluaran toko. Kasir dan Staff Gudang tidak memiliki akses ke menu ini untuk menjaga keamanan dan akuntabilitas keuangan.

#### a. Halaman Kategori Pengeluaran

Halaman Kategori Pengeluaran hanya dapat diakses oleh Owner untuk mengelola master kategori pengeluaran yang akan digunakan pada saat pencatatan expense. Kategori ini berfungsi sebagai pengelompokan pengeluaran operasional agar laporan pengeluaran lebih rapi dan mudah dianalisis. Contoh kategori yang digunakan antara lain listrik, air, internet, gaji, transport, konsumsi, perlengkapan, dan maintenance. Pada halaman ini sistem menampilkan ringkasan jumlah kategori dan menyediakan tabel daftar kategori yang dapat dicari melalui fitur pencarian.

> **[Screenshot: Halaman Kategori Pengeluaran - Login sebagai Owner]** > _Gambar 4.33 Halaman Kategori Pengeluaran dengan Sidebar Menu Lengkap (Role: Owner)_

#### b. Halaman Input Pengeluaran

Halaman Tambah Pengeluaran hanya dapat diakses oleh Owner untuk mencatat transaksi pengeluaran operasional baru. Pada bagian atas, pengguna mengisi Nominal Pengeluaran sebagai nilai utama transaksi. Selanjutnya pada bagian Detail Transaksi, pengguna menentukan Tanggal Transaksi, memilih Kategori Pengeluaran, serta mengisi Keterangan atau Keperluan agar tujuan pengeluaran terdokumentasi dengan jelas. Halaman ini juga menyediakan pilihan Metode Pembayaran berupa Tunai atau Transfer, dimana jika pengguna memilih transfer maka sistem menyediakan field tambahan untuk mengisi Akun Bank atau E-Wallet yang digunakan. Selain itu terdapat bagian Lampiran untuk mengunggah bukti transaksi opsional seperti nota atau bukti pembayaran.

> **[Screenshot: Halaman Input Pengeluaran - Login sebagai Owner]** > _Gambar 4.34 Halaman Input Pengeluaran (Role: Owner)_

#### c. Halaman Daftar Pengeluaran

Halaman Daftar Pengeluaran hanya dapat diakses oleh Owner untuk menampilkan riwayat seluruh pengeluaran yang telah dicatat. Di bagian atas halaman tersedia ringkasan seperti Total Filter, Hari Ini, Bulan Ini, dan Rata-rata per Transaksi sehingga Owner dapat melihat gambaran pengeluaran secara cepat berdasarkan periode yang dipilih. Halaman ini dilengkapi panel Filter Data Pengeluaran yang memudahkan pencarian data menggunakan filter cepat berupa hari ini, kemarin, minggu ini, bulan ini, atau semua, filter rentang tanggal, serta filter berdasarkan kategori. Tersedia juga fitur ekspor ke Excel, PDF, atau Print untuk mendukung dokumentasi serta pelaporan pengeluaran.

> **[Screenshot: Halaman Daftar Pengeluaran - Login sebagai Owner]** > _Gambar 4.35 Halaman Daftar Pengeluaran (Role: Owner)_

### 4.2.9 Halaman Menu Laporan

Menu Laporan berfungsi untuk menyajikan ringkasan performa operasional dan keuangan toko dalam periode tertentu. Modul laporan hanya dapat diakses oleh Owner karena berisi informasi sensitif seperti profit, laba rugi, dan kinerja karyawan. Kasir dan Staff Gudang tidak dapat mengakses laporan untuk menjaga kerahasiaan data bisnis. Sistem juga menyediakan fitur ekspor ke Excel, PDF, atau CSV agar data dapat digunakan untuk dokumentasi dan analisis lanjutan.

#### a. Halaman Laporan Harian

Halaman Laporan Kas Harian hanya dapat diakses oleh Owner untuk menampilkan ringkasan kas per hari. Owner dapat memfilter laporan berdasarkan tanggal, kasir, metode pembayaran, serta bank secara opsional agar ringkasan lebih spesifik. Di bagian ringkasan, sistem menampilkan metrik seperti total transaksi, total omzet, total pengeluaran, dan income bersih berupa omzet dikurangi pengeluaran. Pada bagian detail, sistem menyediakan ringkasan penerimaan per metode pembayaran serta tab detail transaksi untuk membantu audit transaksi harian.

> **[Screenshot: Halaman Laporan Harian - Login sebagai Owner]** > _Gambar 4.36 Halaman Laporan Harian dengan Sidebar Menu Lengkap (Role: Owner)_

#### b. Halaman Laporan Kinerja Kasir

Halaman Laporan Kinerja Kasir hanya dapat diakses oleh Owner untuk mengevaluasi performa kasir pada periode tertentu. Owner menentukan rentang tanggal, memilih kasir, serta status pembayaran untuk menampilkan data yang relevan. Sistem menampilkan ringkasan metrik seperti jumlah transaksi, total omzet, total HPP, dan total profit, kemudian menyajikan tabel kinerja individual kasir untuk membandingkan performa antar kasir. Laporan ini memudahkan evaluasi operasional karena Owner dapat menilai kontribusi penjualan per kasir secara terukur dan objektif.

> **[Screenshot: Halaman Laporan Kinerja Kasir - Login sebagai Owner]** > _Gambar 4.37 Halaman Laporan Kinerja Kasir (Role: Owner)_

#### c. Halaman Laporan Laba Rugi

Halaman Laporan Laba Rugi hanya dapat diakses oleh Owner untuk menghitung laba atau rugi pada periode tertentu. Owner dapat memilih rentang tanggal dan memanfaatkan filter cepat seperti bulan ini, bulan lalu, atau tahun ini agar perhitungan lebih praktis. Struktur laporan mengikuti alur perhitungan standar akuntansi yaitu Pendapatan atau Revenue dikurangi HPP atau COGS menghasilkan Laba Kotor atau Gross Profit, kemudian dikurangi Biaya Operasional menghasilkan Laba atau Rugi Bersih. Sistem menampilkan ringkasan nilai pada kartu perhitungan sehingga Owner dapat memahami komponen pembentuk laba atau rugi secara jelas.

> **[Screenshot: Halaman Laporan Laba Rugi - Login sebagai Owner]** > _Gambar 4.38 Halaman Laporan Laba Rugi (Role: Owner)_

### 4.2.10 Halaman Menu Manajemen User

Menu Manajemen User digunakan untuk mengelola akun pengguna sistem beserta peran atau role dan hak akses atau permission. Modul ini hanya dapat diakses oleh Owner untuk menjaga keamanan sistem karena setiap pengguna hanya dapat mengakses fitur sesuai kewenangan yang ditetapkan. Pada menu ini, Owner dapat menambahkan pengguna baru, melihat daftar pengguna, mengatur status aktif atau nonaktif, serta mengelola role dan permission.

#### a. Halaman Tambah Pengguna

Halaman Form Tambah Pengguna hanya dapat diakses oleh Owner untuk membuat user baru. Owner mengisi informasi akun seperti nama lengkap, email, dan password minimal 8 karakter beserta konfirmasi password. Sistem juga menyediakan area unggah foto profil secara opsional serta pengaturan peran atau role dan status akun berupa aktif atau nonaktif. Halaman ini penting untuk memastikan proses pembuatan akun terstandarisasi dan sesuai kebijakan akses sistem.

> **[Screenshot: Halaman Form Tambah Pengguna - Login sebagai Owner]** > _Gambar 4.39 Halaman Form Tambah Pengguna (Role: Owner)_

#### b. Halaman Semua Pengguna

Halaman Semua Pengguna hanya dapat diakses oleh Owner untuk menampilkan daftar seluruh pengguna yang terdaftar di sistem. Di bagian atas tersedia ringkasan seperti total pengguna, jumlah pengguna aktif, nonaktif, serta total role yang tersedia. Halaman ini juga menyediakan fitur pencarian dan filter berdasarkan role serta status untuk memudahkan pengelompokan data. Pada tabel, sistem menampilkan informasi seperti nama, email, role, status, dan aksi seperti edit atau pengelolaan akun. Melalui halaman ini Owner dapat melakukan pengelolaan user seperti pembaruan data, pengaturan status aktif atau nonaktif, serta proses administrasi akun sesuai kebutuhan operasional.

> **[Screenshot: Halaman Semua Pengguna - Login sebagai Owner]** > _Gambar 4.40 Halaman Semua Pengguna (Role: Owner)_

#### c. Halaman Peran dan Hak Akses

Halaman Peran dan Hak Akses hanya dapat diakses oleh Owner untuk mengelola role dan permission pengguna. Sistem menampilkan daftar peran beserta jumlah pengguna pada masing-masing peran sehingga Owner dapat memantau distribusi akses. Owner dapat menambahkan role baru melalui tombol Tambah Peran, mengubah permission pada role tertentu, serta melakukan penghapusan role apabila aman dan tidak digunakan. Pengaturan role ini mendukung pembagian akses seperti Owner dengan akses penuh, Kasir dengan akses POS dan penjualan, serta Staff Gudang dengan akses stok dan pembelian sesuai kebutuhan implementasi.

> **[Screenshot: Halaman Peran dan Hak Akses - Login sebagai Owner]** > _Gambar 4.41 Halaman Peran dan Hak Akses (Role: Owner)_

### 4.2.11 Halaman Menu Pengaturan Sistem

Menu Pengaturan Sistem digunakan untuk mengelola konfigurasi dasar aplikasi yang berdampak pada proses transaksi, format tampilan nilai, serta identitas toko pada invoice dan laporan. Modul ini hanya dapat diakses oleh Owner karena perubahan pengaturan akan mempengaruhi seluruh operasional sistem. Pengaturan ini mencakup master data seperti satuan unit dan mata uang, pengaturan umum perusahaan berupa identitas toko, hingga konfigurasi notifikasi WhatsApp.

#### a. Halaman Satuan Unit

Halaman Satuan Unit hanya dapat diakses oleh Owner untuk mengelola satuan produk seperti pcs, set, pasang, liter, kg, meter, box, dan pack. Selain nama dan kode satuan, sistem menampilkan pengaturan konversi menggunakan operator dan nilai operasi untuk kebutuhan konversi antar satuan jika digunakan pada produk. Owner dapat melakukan pencarian satuan, menambah satuan baru, serta melakukan edit atau hapus untuk menjaga konsistensi data master produk.

> **[Screenshot: Halaman Satuan Unit - Login sebagai Owner]** > _Gambar 4.42 Halaman Satuan Unit (Role: Owner)_

#### b. Halaman Mata Uang

Halaman Mata Uang hanya dapat diakses oleh Owner untuk mengatur mata uang yang digunakan sistem, termasuk informasi seperti nama mata uang, kode, simbol, dan exchange rate jika multi-currency diterapkan. Pengaturan mata uang berpengaruh pada format tampilan nominal pada transaksi, invoice, laporan, serta halaman POS.

> **[Screenshot: Halaman Mata Uang - Login sebagai Owner]** > _Gambar 4.43 Halaman Mata Uang (Role: Owner)_

#### c. Halaman Pengaturan Umum

Halaman Pengaturan Umum hanya dapat diakses oleh Owner untuk mengatur identitas toko atau perusahaan yang digunakan pada invoice dan kebutuhan administrasi, seperti nama perusahaan, email, telepon, dan alamat lengkap. Pada halaman ini juga tersedia pengaturan notifikasi berupa email penerima notifikasi, serta fitur backup dan restore database untuk mendukung keamanan data. Selain itu, terdapat informasi terkait format mata uang atau preview format yang memastikan tampilan nominal konsisten pada berbagai bagian sistem seperti invoice, POS, dan laporan.

> **[Screenshot: Halaman Pengaturan Umum - Login sebagai Owner]** > _Gambar 4.44 Halaman Pengaturan Umum (Role: Owner)_

#### d. Halaman WhatsApp Settings

Halaman WhatsApp Settings hanya dapat diakses oleh Owner untuk konfigurasi notifikasi WhatsApp. Sistem menampilkan status layanan berupa connected atau disconnected dan menyediakan mekanisme koneksi via QR, fitur reconnect, serta fasilitas test message untuk memastikan integrasi berjalan. Halaman ini juga memuat konfigurasi teknis melalui env atau parameter yang digunakan, pengaturan event notifikasi yang dapat diaktifkan seperti stok rendah, manual input, laporan harian, dan notifikasi lain sesuai implementasi, serta pengelolaan daftar penerima notifikasi lengkap dengan pengaturan izin notifikasi dan toggle aktif atau nonaktif. Dengan pengaturan ini, Owner dapat menerima notifikasi operasional secara otomatis tanpa harus selalu memantau sistem.

> **[Screenshot: Halaman WhatsApp Settings - Login sebagai Owner]** > _Gambar 4.45 Halaman WhatsApp Settings (Role: Owner)_

### 4.2.12 Halaman POS (Point of Sale)

Halaman POS atau Point of Sale merupakan halaman inti sistem yang digunakan untuk melakukan transaksi penjualan di kasir secara cepat dan efisien. Berbeda dengan menu lainnya yang diakses melalui sidebar, halaman POS diakses melalui ikon khusus di header navigasi untuk memudahkan akses kasir. Halaman ini dirancang dengan tampilan dua kolom atau split-screen yang menampilkan katalog produk di sisi kiri dan panel keranjang belanja di sisi kanan. Sistem POS mendukung berbagai metode input produk berupa pencarian manual, scan barcode, dan klik grid, pengelolaan keranjang secara real-time, serta multi-metode pembayaran meliputi tunai, transfer bank, dan QRIS melalui Midtrans.

Owner dapat menggunakan halaman POS untuk melakukan transaksi jika diperlukan, namun Kasir adalah pengguna utama yang mengoperasikan POS sehari-hari untuk melayani customer. Staff Gudang tidak memiliki akses ke halaman POS karena fokus tugasnya pada pengelolaan stok dan pembelian, bukan transaksi penjualan kepada customer.

#### a. Tampilan Utama Halaman POS

Tampilan utama halaman POS menggunakan layout dua kolom dengan bagian kiri menampilkan katalog produk dalam bentuk grid dan bagian kanan menampilkan panel keranjang belanja. Di bagian atas halaman terdapat search bar untuk pencarian produk berdasarkan nama atau kode produk dimana sistem menampilkan hasil pencarian secara real-time tanpa perlu menekan tombol Enter, sehingga kasir dapat menemukan produk dengan cepat. Selain itu tersedia filter kategori untuk mempersempit tampilan produk berdasarkan jenis tertentu seperti Ban Mobil, Oli, atau Aki.

Pada area grid produk, sistem menampilkan card produk yang berisi foto, nama produk, dan harga jual. Kasir cukup mengklik card produk untuk menambahkannya ke keranjang dimana setiap klik akan menambahkan 1 qty produk, dan apabila produk sudah ada di keranjang maka qty akan bertambah secara otomatis. Produk dengan stok menipis ditandai dengan indikator visual sehingga kasir dapat menginformasikan kepada customer apabila stok terbatas. Selain produk fisik, kasir juga dapat menambahkan jasa atau service seperti Jasa Pasang Ban atau Jasa Spooring yang tidak mempengaruhi stok. Untuk kebutuhan khusus, tersedia fitur Tambah Item Manual yang memungkinkan kasir menginput item dengan nama dan harga custom.

> **[Screenshot: Halaman Utama POS - Login sebagai Kasir]** > _Gambar 4.46 Halaman Utama POS dengan Sidebar Menu Terbatas (Role: Kasir)_

#### b. Pengelolaan Keranjang Belanja

Panel keranjang belanja pada halaman POS menampilkan seluruh item yang telah dipilih oleh customer beserta informasi qty, harga, dan subtotal. Kasir dapat melakukan pengelolaan keranjang dengan mengubah jumlah qty menggunakan tombol tambah dan kurang pada setiap baris item, atau mengklik angka qty untuk menginput jumlah secara langsung dimana sistem akan menghitung ulang subtotal dan total secara otomatis. Untuk keperluan diskon khusus atau negosiasi harga, kasir dapat mengubah harga satuan pada item tertentu, dan apabila fitur notifikasi diaktifkan maka sistem akan mencatat perubahan harga manual dan mengirimkan notifikasi kepada Owner untuk keperluan monitoring. Kasir dapat menghapus item dari keranjang dengan menekan tombol hapus pada baris item atau mengosongkan seluruh keranjang apabila customer membatalkan transaksi. Di bagian bawah panel ditampilkan ringkasan transaksi yang meliputi subtotal seluruh item, potongan diskon jika ada, dan total akhir yang harus dibayar oleh customer.

> **[Screenshot: Tampilan Keranjang dan Edit Harga - Login sebagai Kasir]** > _Gambar 4.47 Tampilan Keranjang dan Edit Harga (Role: Kasir)_

#### c. Modal Pembayaran

Modal pembayaran muncul setelah kasir menekan tombol Bayar Sekarang dan digunakan untuk menyelesaikan transaksi dengan mengisi informasi pembayaran. Pada bagian informasi customer, kasir dapat memilih customer dari dropdown yang berisi daftar customer terdaftar di sistem, atau menginput nama customer secara manual untuk walk-in customer yang tidak terdaftar dimana field ini bersifat opsional namun berguna untuk keperluan tracking dan analisis data penjualan.

Sistem menyediakan tiga metode pembayaran yang dapat dipilih sesuai kebutuhan transaksi. Metode pertama adalah Tunai atau Cash dimana kasir menginput nominal uang yang diterima dari customer dan sistem akan menghitung serta menampilkan kembalian secara otomatis. Metode kedua adalah Transfer Bank dimana kasir memilih nama bank tujuan dari dropdown dan menginput nominal transfer, kemudian pembayaran dicatat sebagai paid setelah konfirmasi. Metode ketiga adalah QRIS melalui Midtrans dimana sistem menghasilkan QR code pembayaran yang dipindai oleh customer menggunakan aplikasi e-wallet atau mobile banking, dan setelah pembayaran berhasil maka sistem menerima callback otomatis sehingga status transaksi berubah menjadi paid. Selain itu tersedia field catatan untuk menambahkan keterangan khusus pada transaksi seperti nomor kendaraan customer atau catatan pengambilan barang.

> **[Screenshot: Tampilan Modal Pembayaran - Login sebagai Kasir]** > _Gambar 4.48 Tampilan Modal Pembayaran (Role: Kasir)_

#### d. Invoice dan Penyelesaian Transaksi

Setelah transaksi berhasil diproses, sistem menampilkan tampilan invoice atau struk dan secara otomatis melakukan beberapa proses. Sistem menghasilkan invoice dengan nomor unik menggunakan format OB2-YYYYMMDD-XXXXXX dimana invoice berisi informasi lengkap meliputi identitas toko berupa nama, alamat, dan telepon, informasi transaksi berupa tanggal, kasir, dan customer, daftar item beserta qty dan harga, ringkasan pembayaran, serta metode dan status pembayaran.

Untuk produk fisik berupa produk baru, sistem secara otomatis mengurangi stok sesuai qty yang terjual. Untuk produk bekas atau second, sistem mengubah status menjadi Sold karena setiap produk bekas bersifat unik dengan stok sama dengan satu. Jasa atau service tidak mempengaruhi stok karena bukan barang fisik. Transaksi tercatat pada modul Penjualan dan dapat dilihat pada halaman Semua Penjualan dimana data penjualan juga digunakan untuk perhitungan laporan kas harian, laporan kinerja kasir, dan laporan laba rugi. Apabila integrasi WhatsApp diaktifkan, sistem mengirimkan notifikasi kepada Owner untuk event tertentu seperti transaksi di atas threshold tertentu, penggunaan harga manual oleh kasir, atau pembayaran online berhasil melalui Midtrans. Kasir dapat mencetak invoice dalam format PDF menggunakan tombol Cetak Invoice dan setelah transaksi selesai, halaman POS siap untuk menerima transaksi berikutnya.

> **[Screenshot: Tampilan Invoice - Login sebagai Kasir]** > _Gambar 4.49 Tampilan Invoice yang Berhasil Dibuat (Role: Kasir)_

### 4.2.13 Ringkasan Akses Halaman Berdasarkan Role

Berikut adalah tabel ringkasan akses halaman berdasarkan role pengguna yang menunjukkan pembagian kewenangan pada sistem Point of Sale Omah Ban.

| Halaman/Fitur             | Owner | Kasir | Staff Gudang |
| ------------------------- | :---: | :---: | :----------: |
| Login dan Dashboard       |   ✓   |   ✓   |      ✓       |
| Manajemen Produk (CRUD)   |   ✓   |   -   |      -       |
| Manajemen Produk (Lihat)  |   ✓   |   ✓   |      ✓       |
| Stock Opname              |   ✓   |   -   |      ✓       |
| Penyesuaian Manual (Buat) |   ✓   |   -   |      ✓       |
| Approval Penyesuaian      |   ✓   |   -   |      -       |
| Supplier (Kelola)         |   ✓   |   -   |      ✓       |
| Customer (Kelola)         |   ✓   |   ✓   |      -       |
| Pembelian Stok            |   ✓   |   -   |      ✓       |
| Halaman POS               |   ✓   |   ✓   |      -       |
| Penjualan dan Retur       |   ✓   |   ✓   |      -       |
| Pengeluaran               |   ✓   |   -   |      -       |
| Semua Laporan             |   ✓   |   -   |      -       |
| Manajemen User            |   ✓   |   -   |      -       |
| Pengaturan Sistem         |   ✓   |   -   |      -       |

Dari tabel di atas dapat dilihat bahwa Owner memiliki akses penuh ke seluruh fitur sistem, Kasir fokus pada transaksi penjualan dan pengelolaan customer, sedangkan Staff Gudang fokus pada pengelolaan stok, pembelian, dan supplier. Pembagian akses ini memastikan setiap role hanya dapat mengakses fitur yang relevan dengan tanggung jawabnya serta meningkatkan keamanan dan akuntabilitas sistem.

---

_[Lanjut ke Bagian 4.3 Pengujian Website - Part 3]_
