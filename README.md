# 🚗 Omah Ban - POS System

Sistem Point of Sale (POS) untuk toko ban dengan fitur lengkap manajemen produk, penjualan, pembelian, dan laporan.

## 📋 Requirements

-   **PHP** >= 8.1
-   **MySQL** >= 5.7 / MariaDB >= 10.3
-   **Composer** >= 2.x
-   **Node.js** >= 18.x (with NPM)
-   **Laravel** 10.x

## 🚀 Installation

### 1. Clone Repository

```bash
git clone https://github.com/your-username/ProjectOmahBan.git
cd ProjectOmahBan
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Setup Environment

Copy file `.env.example` ke `.env`:

```bash
cp .env.example .env
```

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_omah_ban
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Setup Database

#### Option A: Import dari SQL File (Recommended)

1. Buat database baru di MySQL dengan nama `project_omah_ban`
2. Import file SQL yang disertakan:

```bash
# Menggunakan MySQL CLI
mysql -u root -p project_omah_ban < "project-omah_ban (DATABASE).sql"

# Atau menggunakan phpMyAdmin / HeidiSQL
# Import file: project-omah_ban (DATABASE).sql
```

#### Option B: Fresh Migration (Data Kosong)

```bash
php artisan migrate --seed
```

### 7. Create Storage Link

```bash
php artisan storage:link
```

### 8. Build Frontend Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 9. Run the Application

```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

---

## 📁 Project Structure

```
ProjectOmahBan/
├── Modules/              # Modular Laravel (nwidart/laravel-modules)
│   ├── Adjustment/       # Stock Adjustment
│   ├── Currency/         # Currency Management
│   ├── Expense/          # Expense Tracking
│   ├── People/           # Customer & Supplier
│   ├── Product/          # Product Management
│   ├── Purchase/         # Purchase Orders
│   ├── Quotation/        # Quotations
│   ├── Report/           # Reports
│   ├── Sale/             # Sales & POS
│   ├── Setting/          # System Settings
│   └── User/             # User Management
├── app/                  # Core Application Code
├── config/               # Configuration Files
├── database/             # Migrations & Seeders
├── public/               # Public Assets
├── resources/            # Views & Raw Assets
├── routes/               # Route Definitions
└── storage/              # Logs, Cache, Uploads
```

---

## 🛠 Tech Stack

| Category   | Technology                           |
| ---------- | ------------------------------------ |
| Backend    | Laravel 10, Livewire 3, Filament 3.3 |
| Frontend   | TailwindCSS, Flowbite, AlpineJS      |
| Build Tool | Vite                                 |
| Database   | MySQL / MariaDB                      |
| PDF Export | DomPDF, Snappy                       |
| DataTables | Yajra DataTables                     |
| Excel      | Maatwebsite Excel                    |

---

## 🔧 Troubleshooting

### Storage Permission Error

```bash
chmod -R 775 storage bootstrap/cache
```

### Clear All Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Regenerate Autoload

```bash
composer dump-autoload
```

### Vite Hot Reload Not Working

Delete `/public/hot` file and run `npm run dev` again.

---

## 📄 License

MIT License - See [LICENSE](LICENSE) file for details.
