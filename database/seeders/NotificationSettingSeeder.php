<?php

namespace Database\Seeders;

use App\Models\NotificationSetting;
use Illuminate\Database\Seeder;

class NotificationSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'type' => 'manual_input',
                'label' => 'Manual Input Alert',
                'description' => 'Notifikasi saat kasir input item manual di POS (item tidak dari katalog)',
                'icon' => 'bi-pencil-square',
                'is_enabled' => true,
                'template' => "🔔 *⚠️ Input Manual - Inv {invoice}*\n\nKasir *{cashier}* membuat transaksi dengan *{item_count} item* input manual:\n\n{items_list}\n\n💰 *Total: Rp {total}*\n📋 Invoice: {invoice}\n⏰ Waktu: {datetime}",
                'placeholders' => [
                    'invoice' => 'Nomor invoice',
                    'cashier' => 'Nama kasir',
                    'item_count' => 'Jumlah item manual',
                    'items_list' => 'Daftar item (nama, qty, harga)',
                    'total' => 'Total transaksi (Rp)',
                    'datetime' => 'Waktu transaksi',
                ],
            ],
            [
                'type' => 'low_stock',
                'label' => 'Low Stock Alert',
                'description' => 'Notifikasi saat stok produk mencapai batas minimum',
                'icon' => 'bi-box-seam',
                'is_enabled' => true,
                'template' => "⚠️ *ALERT STOK RENDAH*\n\nDitemukan *{product_count} produk* dengan stok rendah:\n\n{products_list}\n\n📦 Segera lakukan restok!",
                'placeholders' => [
                    'product_count' => 'Jumlah produk stok rendah',
                    'products_list' => 'Daftar produk (nama, qty)',
                ],
            ],
            [
                'type' => 'daily_report',
                'label' => 'Laporan Harian',
                'description' => 'Ringkasan penjualan harian yang dikirim otomatis setiap tutup toko',
                'icon' => 'bi-graph-up-arrow',
                'is_enabled' => true,
                'template' => "📊 *LAPORAN HARIAN*\n📅 {date}\n\n💰 Total Penjualan: *Rp {total_sales}*\n🧾 Jumlah Transaksi: *{transaction_count}*\n💵 Total Tunai: Rp {cash_total}\n💳 Total Transfer: Rp {transfer_total}\n📉 Total Pengeluaran: Rp {expense_total}\n💎 Laba Bersih: *Rp {net_profit}*\n🏆 Produk Terlaris: {top_product}",
                'placeholders' => [
                    'date' => 'Tanggal laporan',
                    'total_sales' => 'Total penjualan',
                    'transaction_count' => 'Jumlah transaksi',
                    'cash_total' => 'Total tunai',
                    'transfer_total' => 'Total transfer',
                    'expense_total' => 'Total pengeluaran',
                    'net_profit' => 'Laba bersih',
                    'top_product' => 'Produk terlaris',
                ],
            ],
            [
                'type' => 'login_alert',
                'label' => 'Login Alert',
                'description' => 'Notifikasi saat ada user yang login ke sistem',
                'icon' => 'bi-shield-lock',
                'is_enabled' => false,
                'template' => "🔐 *LOGIN ALERT*\n\nUser *{user_name}* ({role}) telah login pada:\n⏰ {datetime}\n🌐 IP: {ip_address}\n💻 Browser: {browser}",
                'placeholders' => [
                    'user_name' => 'Nama user',
                    'role' => 'Role user',
                    'datetime' => 'Waktu login',
                    'ip_address' => 'Alamat IP',
                    'browser' => 'Browser/device',
                ],
            ],
        ];

        foreach ($settings as $setting) {
            NotificationSetting::updateOrCreate(
                ['type' => $setting['type']],
                $setting
            );
        }
    }
}
