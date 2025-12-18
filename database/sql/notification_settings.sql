-- ========================================
-- Notification Settings Table
-- Created for ProjectOmahBan WhatsApp Integration
-- ========================================

-- Create table
CREATE TABLE IF NOT EXISTS `notification_settings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique identifier: manual_input, low_stock, daily_report, etc',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Human readable name',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Description of this notification type',
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bi-bell' COMMENT 'Bootstrap icon class',
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Whether this notification type is active',
  `template` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Message template with {placeholders}',
  `placeholders` json DEFAULT NULL COMMENT 'Available placeholders for this template',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_settings_type_unique` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Insert Default Notification Templates
-- ========================================

INSERT INTO `notification_settings` (`type`, `label`, `description`, `icon`, `is_enabled`, `template`, `placeholders`, `created_at`, `updated_at`) VALUES
('manual_input', 'Manual Input Alert', 'Notifikasi saat kasir input item manual di POS (item tidak dari katalog)', 'bi-pencil-square', 1, 
'🔔 *⚠️ Input Manual - Inv {invoice}*\n\nKasir *{cashier}* membuat transaksi dengan *{item_count} item* input manual:\n\n{items_list}\n\n💰 *Total: Rp {total}*\n📋 Invoice: {invoice}\n⏰ Waktu: {datetime}',
'{"invoice": "Nomor invoice", "cashier": "Nama kasir", "item_count": "Jumlah item manual", "items_list": "Daftar item (nama, qty, harga)", "total": "Total transaksi (Rp)", "datetime": "Waktu transaksi"}',
NOW(), NOW()),

('low_stock', 'Low Stock Alert', 'Notifikasi saat stok produk mencapai batas minimum', 'bi-box-seam', 1,
'⚠️ *ALERT STOK RENDAH*\n\nDitemukan *{product_count} produk* dengan stok rendah:\n\n{products_list}\n\n📦 Segera lakukan restok!',
'{"product_count": "Jumlah produk stok rendah", "products_list": "Daftar produk (nama, qty)"}',
NOW(), NOW()),

('daily_report', 'Laporan Harian', 'Ringkasan penjualan harian yang dikirim otomatis setiap tutup toko', 'bi-graph-up-arrow', 1,
'📊 *LAPORAN HARIAN*\n📅 {date}\n\n💰 Total Penjualan: *Rp {total_sales}*\n🧾 Jumlah Transaksi: *{transaction_count}*\n💵 Total Tunai: Rp {cash_total}\n💳 Total Transfer: Rp {transfer_total}\n📉 Total Pengeluaran: Rp {expense_total}\n💎 Laba Bersih: *Rp {net_profit}*\n🏆 Produk Terlaris: {top_product}',
'{"date": "Tanggal laporan", "total_sales": "Total penjualan", "transaction_count": "Jumlah transaksi", "cash_total": "Total tunai", "transfer_total": "Total transfer", "expense_total": "Total pengeluaran", "net_profit": "Laba bersih", "top_product": "Produk terlaris"}',
NOW(), NOW()),

('login_alert', 'Login Alert', 'Notifikasi saat ada user yang login ke sistem', 'bi-shield-lock', 0,
'🔐 *LOGIN ALERT*\n\nUser *{user_name}* ({role}) telah login pada:\n⏰ {datetime}\n🌐 IP: {ip_address}\n💻 Browser: {browser}',
'{"user_name": "Nama user", "role": "Role user", "datetime": "Waktu login", "ip_address": "Alamat IP", "browser": "Browser/device"}',
NOW(), NOW())

ON DUPLICATE KEY UPDATE 
  `label` = VALUES(`label`),
  `description` = VALUES(`description`),
  `icon` = VALUES(`icon`),
  `placeholders` = VALUES(`placeholders`),
  `updated_at` = NOW();

-- ========================================
-- Add migration record (optional)
-- ========================================
INSERT INTO `migrations` (`migration`, `batch`) VALUES 
('2025_12_18_191119_create_notification_settings_table', 31);
