-- ============================================================================
-- DATA DUMMY UNTUK PRESENTASI - ProjectOmahBan
-- Fokus: Module Adjustment + Berbagai Kasus Transaksi Penjualan
-- Tanggal: 17 Desember 2025
-- ============================================================================
-- PERHATIAN: Jalankan script ini SETELAH backup database existing!
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- ============================================================================
-- SECTION 1: DATA ADJUSTMENT (FOKUS UTAMA)
-- ============================================================================

-- Reset auto increment untuk demo yang bersih
-- ALTER TABLE adjustments AUTO_INCREMENT = 100;
-- ALTER TABLE adjusted_products AUTO_INCREMENT = 100;

-- ----------------------------------------------------------------------------
-- 1.1 ADJUSTMENT - PENDING (Menunggu Approval)
-- ----------------------------------------------------------------------------
INSERT INTO `adjustments` 
(`id`, `date`, `reference`, `note`, `status`, `requester_id`, `approver_id`, `reason`, `description`, `approval_notes`, `approval_date`, `total_value`, `created_at`, `updated_at`) 
VALUES
(100, CURDATE(), 'ADJ-DEMO-00001', 'Stok fisik lebih banyak dari sistem setelah stock opname', 'pending', 5, NULL, 'Lainnya', 'Ditemukan 3 unit ban GT Savero yang belum tercatat di sistem. Kemungkinan dari pengiriman supplier yang belum di-input.', NULL, NULL, 3842280.00, NOW(), NOW()),

(101, CURDATE(), 'ADJ-DEMO-00002', 'Ban rusak karena penyimpanan tidak tepat', 'pending', 5, NULL, 'Rusak', 'Ditemukan 2 unit Ban Bridgestone dengan kondisi retak karena terkena sinar matahari langsung di gudang.', NULL, NULL, 1450000.00, NOW(), NOW()),

(102, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'ADJ-DEMO-00003', 'Kehilangan stok velg', 'pending', 6, NULL, 'Hilang', 'Selisih stok velg HSR Ring 17 sebanyak 1 unit. Sudah dicari namun tidak ditemukan.', NULL, NULL, 2450000.00, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Detail produk untuk adjustment pending
INSERT INTO `adjusted_products` 
(`id`, `adjustment_id`, `product_id`, `quantity`, `type`, `created_at`, `updated_at`) 
VALUES
-- ADJ-DEMO-00001: Surplus stok (add)
(100, 100, 1, 3, 'add', NOW(), NOW()),  -- 3x Ban GT Savero

-- ADJ-DEMO-00002: Barang rusak (sub)
(101, 101, 2, 2, 'sub', NOW(), NOW()),  -- 2x Ban Bridgestone EP150

-- ADJ-DEMO-00003: Barang hilang (sub)
(102, 102, 5, 1, 'sub', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));  -- 1x Velg HSR

-- Log untuk pending adjustments
INSERT INTO `adjustment_logs` 
(`adjustment_id`, `user_id`, `action`, `old_status`, `new_status`, `notes`, `locked`, `created_at`) 
VALUES
(100, 5, 'created', NULL, 'pending', 'Pengajuan adjustment oleh Kasir Ani', 0, NOW()),
(101, 5, 'created', NULL, 'pending', 'Pengajuan adjustment oleh Kasir Ani - barang rusak', 0, NOW()),
(102, 6, 'created', NULL, 'pending', 'Pengajuan adjustment oleh Kasir Budi - kehilangan', 0, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- ----------------------------------------------------------------------------
-- 1.2 ADJUSTMENT - APPROVED (Sudah Disetujui)
-- ----------------------------------------------------------------------------
INSERT INTO `adjustments` 
(`id`, `date`, `reference`, `note`, `status`, `requester_id`, `approver_id`, `reason`, `description`, `approval_notes`, `approval_date`, `total_value`, `created_at`, `updated_at`) 
VALUES
(103, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'ADJ-DEMO-00004', 'Koreksi stok setelah audit bulanan', 'approved', 4, 1, 'Lainnya', 'Hasil audit bulanan menunjukkan ada 4 unit ban Dunlop tidak tercatat.', 'Approved. Stok sudah diverifikasi oleh Supervisor.', DATE_SUB(NOW(), INTERVAL 2 DAY), 3560000.00, DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)),

(104, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'ADJ-DEMO-00005', 'Ban kadaluarsa ditarik dari stok', 'approved', 4, 1, 'Kadaluarsa', 'Penarikan 2 unit ban lama (produksi 2020) yang sudah tidak layak jual.', 'Approved. Ban akan dimusnahkan sesuai SOP.', DATE_SUB(NOW(), INTERVAL 4 DAY), 1670000.00, DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY));

-- Detail produk untuk adjustment approved
INSERT INTO `adjusted_products` 
(`id`, `adjustment_id`, `product_id`, `quantity`, `type`, `created_at`, `updated_at`) 
VALUES
-- ADJ-DEMO-00004: Koreksi audit (add)
(103, 103, 3, 4, 'add', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY)),  -- 4x Dunlop

-- ADJ-DEMO-00005: Kadaluarsa (sub)
(104, 104, 4, 2, 'sub', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY));  -- 2x GT Champiro

-- Log untuk approved adjustments
INSERT INTO `adjustment_logs` 
(`adjustment_id`, `user_id`, `action`, `old_status`, `new_status`, `notes`, `locked`, `created_at`) 
VALUES
(103, 4, 'created', NULL, 'pending', 'Pengajuan oleh Supervisor', 1, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(103, 1, 'approved', 'pending', 'approved', 'Disetujui oleh Owner. Stok sudah diverifikasi fisik.', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(104, 4, 'created', NULL, 'pending', 'Pengajuan penarikan ban kadaluarsa', 1, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(104, 1, 'approved', 'pending', 'approved', 'Disetujui. Ban akan dimusnahkan.', 1, DATE_SUB(NOW(), INTERVAL 4 DAY));

-- ----------------------------------------------------------------------------
-- 1.3 ADJUSTMENT - REJECTED (Ditolak)
-- ----------------------------------------------------------------------------
INSERT INTO `adjustments` 
(`id`, `date`, `reference`, `note`, `status`, `requester_id`, `approver_id`, `reason`, `description`, `approval_notes`, `approval_date`, `total_value`, `created_at`, `updated_at`) 
VALUES
(105, DATE_SUB(CURDATE(), INTERVAL 7 DAY), 'ADJ-DEMO-00006', 'Klaim kehilangan tidak valid', 'rejected', 5, 4, 'Hilang', 'Kasir mengklaim 5 unit ban hilang dari gudang.', 'DITOLAK: Setelah investigasi CCTV, stok masih lengkap. Kesalahan hitung.', DATE_SUB(NOW(), INTERVAL 6 DAY), 4625000.00, DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY)),

(106, DATE_SUB(CURDATE(), INTERVAL 10 DAY), 'ADJ-DEMO-00007', 'Pengajuan tidak lengkap', 'rejected', 6, 4, 'Rusak', 'Pengajuan adjustmen untuk barang rusak tanpa bukti foto.', 'DITOLAK: Tidak ada dokumentasi foto. Harap ajukan ulang dengan bukti.', DATE_SUB(NOW(), INTERVAL 9 DAY), 890000.00, DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 9 DAY));

-- Detail produk untuk adjustment rejected
INSERT INTO `adjusted_products` 
(`id`, `adjustment_id`, `product_id`, `quantity`, `type`, `created_at`, `updated_at`) 
VALUES
(105, 105, 2, 5, 'sub', DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 7 DAY)),
(106, 106, 3, 1, 'sub', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY));

-- Log untuk rejected adjustments
INSERT INTO `adjustment_logs` 
(`adjustment_id`, `user_id`, `action`, `old_status`, `new_status`, `notes`, `locked`, `created_at`) 
VALUES
(105, 5, 'created', NULL, 'pending', 'Pengajuan kehilangan stok', 1, DATE_SUB(NOW(), INTERVAL 7 DAY)),
(105, 4, 'rejected', 'pending', 'rejected', 'DITOLAK: Investigasi CCTV menunjukkan stok lengkap.', 1, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(106, 6, 'created', NULL, 'pending', 'Pengajuan barang rusak', 1, DATE_SUB(NOW(), INTERVAL 10 DAY)),
(106, 4, 'rejected', 'pending', 'rejected', 'DITOLAK: Tidak ada dokumentasi foto.', 1, DATE_SUB(NOW(), INTERVAL 9 DAY));


-- ============================================================================
-- SECTION 2: TRANSAKSI PENJUALAN DENGAN BERBAGAI KASUS
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 2.1 SALE - NORMAL (Tanpa Diskon/Manual Input)
-- ----------------------------------------------------------------------------
INSERT INTO `sales` 
(`id`, `customer_id`, `date`, `reference`, `user_id`, `customer_name`, `has_price_adjustment`, `tax_percentage`, `tax_amount`, `discount_percentage`, `discount_amount`, `shipping_amount`, `total_amount`, `has_manual_input`, `total_hpp`, `total_profit`, `paid_amount`, `due_amount`, `status`, `payment_status`, `payment_method`, `bank_name`, `note`, `manual_input_count`, `created_at`, `updated_at`)
VALUES
-- Transaksi Normal #1 - Cash Lunas
(100, 1, CURDATE(), 'OB2-DEMO-001', 5, 'Peter Vincent', 0, 0, 0, 0, 0, 0, 1850000, 0, 1450000, 400000, 1850000, 0, 'Completed', 'Paid', 'Tunai', NULL, 'Pembelian 2 ban untuk mobil keluarga', 0, NOW(), NOW()),

-- Transaksi Normal #2 - Transfer Lunas
(101, NULL, CURDATE(), 'OB2-DEMO-002', 5, 'Walk-in Customer', 0, 0, 0, 0, 0, 0, 3050000, 0, 2450000, 600000, 3050000, 0, 'Completed', 'Paid', 'Transfer', 'BCA', 'Pembelian velg HSR', 0, NOW(), NOW());

-- Detail untuk transaksi normal
INSERT INTO `sale_details` 
(`id`, `sale_id`, `item_name`, `product_id`, `source_type`, `product_name`, `product_code`, `quantity`, `price`, `original_price`, `is_price_adjusted`, `hpp`, `unit_price`, `sub_total`, `subtotal_profit`, `product_discount_amount`, `product_discount_type`, `product_tax_amount`, `created_at`, `updated_at`)
VALUES
-- Sale 100: 2x Ban Bridgestone
(100, 100, 'Ban Bridgestone Ecopia EP150', 2, 'new', 'Ban Bridgestone Ecopia EP150 185/65 R15', 'BS-EP150-18565R15', 2, 925000, 925000, 0, 725000, 925000, 1850000, 400000, 0, 'fixed', 0, NOW(), NOW()),

-- Sale 101: 1x Velg HSR
(101, 101, 'Velg HSR Samurai Ring 17', 5, 'new', 'Velg HSR Samurai Ring 17 5x114.3', 'HSR-SAM-R17-51143', 1, 3050000, 3050000, 0, 2450000, 3050000, 3050000, 600000, 0, 'fixed', 0, NOW(), NOW());

-- Payment Records untuk normal sales
INSERT INTO `sale_payments` 
(`sale_id`, `amount`, `date`, `reference`, `payment_method`, `bank_name`, `note`, `created_at`, `updated_at`)
VALUES
(100, 1850000, CURDATE(), 'INV/OB2-DEMO-001', 'Tunai', NULL, 'Pembayaran tunai lunas', NOW(), NOW()),
(101, 3050000, CURDATE(), 'INV/OB2-DEMO-002', 'Transfer', 'BCA', 'Transfer ke rekening BCA toko', NOW(), NOW());


-- ----------------------------------------------------------------------------
-- 2.2 SALE - DENGAN DISKON HARGA (Price Adjustment)
-- ----------------------------------------------------------------------------
INSERT INTO `sales` 
(`id`, `customer_id`, `date`, `reference`, `user_id`, `customer_name`, `has_price_adjustment`, `tax_percentage`, `tax_amount`, `discount_percentage`, `discount_amount`, `shipping_amount`, `total_amount`, `has_manual_input`, `total_hpp`, `total_profit`, `paid_amount`, `due_amount`, `status`, `payment_status`, `payment_method`, `bank_name`, `note`, `manual_input_count`, `created_at`, `updated_at`)
VALUES
-- Diskon 5% untuk pelanggan loyal
(102, 1, CURDATE(), 'OB2-DEMO-003', 5, 'Peter Vincent', 1, 0, 0, 0, 0, 0, 878750, 0, 725000, 153750, 878750, 0, 'Completed', 'Paid', 'Tunai', NULL, 'Diskon 5% pelanggan setia', 0, NOW(), NOW()),

-- Diskon khusus negosiasi (10%)
(103, NULL, CURDATE(), 'OB2-DEMO-004', 4, 'Bengkel Maju Jaya', 1, 0, 0, 0, 0, 0, 2745000, 0, 2450000, 295000, 2745000, 0, 'Completed', 'Paid', 'Transfer', 'Mandiri', 'Harga khusus bengkel partner - sudah approval supervisor', 0, NOW(), NOW()),

-- Diskon besar (15%) dengan approval Owner
(104, NULL, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'OB2-DEMO-005', 5, 'PT Logistik Nusantara', 1, 0, 0, 0, 0, 0, 4837500, 0, 3842280, 995220, 4837500, 0, 'Completed', 'Paid', 'Transfer', 'BRI', 'Pembelian bulk 4 ban - diskon 15% approval Owner', 0, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Detail untuk transaksi diskon
INSERT INTO `sale_details` 
(`id`, `sale_id`, `item_name`, `product_id`, `source_type`, `product_name`, `product_code`, `quantity`, `price`, `original_price`, `is_price_adjusted`, `price_adjustment_amount`, `price_adjustment_note`, `adjusted_by`, `adjusted_at`, `hpp`, `unit_price`, `sub_total`, `subtotal_profit`, `product_discount_amount`, `product_discount_type`, `product_tax_amount`, `created_at`, `updated_at`)
VALUES
-- Sale 102: Diskon 5% (Rp 925.000 → Rp 878.750)
(102, 102, 'Ban Bridgestone Ecopia EP150', 2, 'new', 'Ban Bridgestone Ecopia EP150 185/65 R15', 'BS-EP150-18565R15', 1, 878750, 925000, 1, 46250, 'Diskon 5% pelanggan loyal - sudah 10x transaksi', 5, NOW(), 725000, 878750, 878750, 153750, 46250, 'fixed', 0, NOW(), NOW()),

-- Sale 103: Diskon 10% velg (Rp 3.050.000 → Rp 2.745.000)
(103, 103, 'Velg HSR Samurai Ring 17', 5, 'new', 'Velg HSR Samurai Ring 17 5x114.3', 'HSR-SAM-R17-51143', 1, 2745000, 3050000, 1, 305000, 'Harga partner bengkel - approval Supervisor', 4, NOW(), 2450000, 2745000, 2745000, 295000, 305000, 'fixed', 0, NOW(), NOW()),

-- Sale 104: Diskon 15% bulk 4 ban GT Savero
(104, 104, 'Ban GT Savero', 1, 'new', 'Ban GT Savero', 'GT_Savero', 4, 1209375, 1425000, 1, 215625, 'Diskon 15% pembelian bulk - approval Owner Bpk. Budi', 1, DATE_SUB(NOW(), INTERVAL 1 DAY), 1280760, 1209375, 4837500, 995220, 862500, 'fixed', 0, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Payment Records untuk diskon sales
INSERT INTO `sale_payments` 
(`sale_id`, `amount`, `date`, `reference`, `payment_method`, `bank_name`, `note`, `created_at`, `updated_at`)
VALUES
(102, 878750, CURDATE(), 'INV/OB2-DEMO-003', 'Tunai', NULL, 'Diskon pelanggan loyal', NOW(), NOW()),
(103, 2745000, CURDATE(), 'INV/OB2-DEMO-004', 'Transfer', 'Mandiri', 'Partner bengkel', NOW(), NOW()),
(104, 4837500, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'INV/OB2-DEMO-005', 'Transfer', 'BRI', 'Pembeli corporate', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));


-- ----------------------------------------------------------------------------
-- 2.3 SALE - DENGAN INPUT MANUAL (Service & Goods)
-- ----------------------------------------------------------------------------
INSERT INTO `sales` 
(`id`, `customer_id`, `date`, `reference`, `user_id`, `customer_name`, `has_price_adjustment`, `tax_percentage`, `tax_amount`, `discount_percentage`, `discount_amount`, `shipping_amount`, `total_amount`, `has_manual_input`, `total_hpp`, `total_profit`, `paid_amount`, `due_amount`, `status`, `payment_status`, `payment_method`, `bank_name`, `note`, `manual_input_count`, `manual_input_summary`, `is_manual_input_notified`, `notified_at`, `created_at`, `updated_at`)
VALUES
-- Sale dengan jasa pasang + balancing
(105, NULL, CURDATE(), 'OB2-DEMO-006', 5, 'Walk-in Customer', 0, 0, 0, 0, 0, 0, 1040000, 1, 725000, 315000, 1040000, 0, 'Completed', 'Paid', 'Tunai', NULL, 'Beli ban + pasang + balancing', 2, '[{"name":"Pasang Ban","quantity":1,"price":25000,"type":"service"},{"name":"Balancing","quantity":1,"price":20000,"type":"service"}]', 1, NOW(), NOW(), NOW()),

-- Sale dengan nitrogen + pentil
(106, NULL, CURDATE(), 'OB2-DEMO-007', 6, 'Walk-in Customer', 0, 0, 0, 0, 0, 0, 893000, 1, 640000, 253000, 893000, 0, 'Completed', 'Paid', 'QRIS', NULL, 'Beli ban + isi nitrogen + ganti pentil', 2, '[{"name":"Nitrogen","quantity":4,"price":8000,"type":"goods"},{"name":"Pentil Tubeless","quantity":4,"price":5000,"type":"goods"}]', 1, NOW(), NOW(), NOW()),

-- Sale jasa spooring
(107, 1, CURDATE(), 'OB2-DEMO-008', 5, 'Peter Vincent', 0, 0, 0, 0, 0, 0, 150000, 1, 0, 150000, 150000, 0, 'Completed', 'Paid', 'Tunai', NULL, 'Jasa spooring saja', 1, '[{"name":"Spooring Ban","quantity":1,"price":150000,"type":"service"}]', 1, NOW(), NOW(), NOW()),

-- Sale dengan barang manual (second hand tidak di sistem)
(108, NULL, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'OB2-DEMO-009', 5, 'Walk-in Customer', 0, 0, 0, 0, 0, 0, 750000, 1, 500000, 250000, 750000, 0, 'Completed', 'Paid', 'Tunai', NULL, 'Ban bekas kondisi 85% - belum input di sistem', 1, '[{"name":"Ban Bekas Dunlop 205/65 R15","quantity":1,"price":750000,"type":"goods","reason":"Barang second belum di-input ke inventory"}]', 1, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Detail untuk transaksi manual input
INSERT INTO `sale_details` 
(`id`, `sale_id`, `item_name`, `product_id`, `productable_id`, `productable_type`, `source_type`, `manual_kind`, `product_name`, `product_code`, `quantity`, `price`, `original_price`, `is_price_adjusted`, `hpp`, `manual_hpp`, `unit_price`, `sub_total`, `subtotal_profit`, `product_discount_amount`, `product_discount_type`, `product_tax_amount`, `created_at`, `updated_at`)
VALUES
-- Sale 105: Ban + Pasang + Balancing
(105, 105, 'Ban Bridgestone Ecopia EP150', 2, NULL, NULL, 'new', NULL, 'Ban Bridgestone Ecopia EP150 185/65 R15', 'BS-EP150-18565R15', 1, 925000, 925000, 0, 725000, NULL, 925000, 925000, 200000, 0, 'fixed', 0, NOW(), NOW()),
(106, 105, 'Pasang Ban', NULL, 2, 'Modules\\Product\\Entities\\ServiceMaster', 'manual', 'service', 'Pasang Ban', 'SRV-2', 1, 25000, 25000, 0, 0, NULL, 25000, 25000, 25000, 0, 'fixed', 0, NOW(), NOW()),
(107, 105, 'Balancing', NULL, 3, 'Modules\\Product\\Entities\\ServiceMaster', 'manual', 'service', 'Balancing', 'SRV-3', 4, 20000, 20000, 0, 0, NULL, 20000, 80000, 80000, 0, 'fixed', 0, NOW(), NOW()),

-- Sale 106: Ban + Nitrogen + Pentil
(108, 106, 'Ban GT Radial Champiro Eco', 4, NULL, NULL, 'new', NULL, 'Ban GT Radial Champiro Eco 195/65 R15', 'GT-CE-19565R15', 1, 835000, 835000, 0, 640000, NULL, 835000, 835000, 195000, 0, 'fixed', 0, NOW(), NOW()),
(109, 106, 'Nitrogen', NULL, NULL, NULL, 'manual', 'goods', 'Isi Nitrogen', 'GD-N2', 4, 8000, 8000, 0, 0, 0, 8000, 32000, 32000, 0, 'fixed', 0, NOW(), NOW()),
(110, 106, 'Pentil Tubeless', NULL, NULL, NULL, 'manual', 'goods', 'Pentil Tubeless', 'GD-PENTIL', 4, 5000, 5000, 0, 0, 2000, 5000, 20000, 12000, 0, 'fixed', 0, NOW(), NOW()),

-- Sale 107: Jasa spooring saja
(111, 107, 'Spooring Ban', NULL, 1, 'Modules\\Product\\Entities\\ServiceMaster', 'manual', 'service', 'Spooring Ban', 'SRV-1', 1, 150000, 150000, 0, 0, NULL, 150000, 150000, 150000, 0, 'fixed', 0, NOW(), NOW()),

-- Sale 108: Barang manual (bekas tidak di sistem)
(112, 108, 'Ban Bekas Dunlop 205/65 R15', NULL, NULL, NULL, 'manual', 'goods', 'Ban Bekas Dunlop 205/65 R15', '-', 1, 750000, 750000, 0, 0, 500000, 750000, 750000, 250000, 0, 'fixed', 0, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Manual Input Details (untuk tracking)
INSERT INTO `manual_input_details` 
(`sale_id`, `sale_detail_id`, `cashier_id`, `item_type`, `item_name`, `quantity`, `price`, `manual_reason`, `cost_price`, `created_at`, `updated_at`)
VALUES
(105, 106, 5, 'service', 'Pasang Ban', 1, 25000, 'Jasa pemasangan ban standar', 0, NOW(), NOW()),
(105, 107, 5, 'service', 'Balancing', 4, 20000, 'Balancing 4 roda', 0, NOW(), NOW()),
(106, 109, 6, 'goods', 'Nitrogen', 4, 8000, 'Isi nitrogen 4 ban', 0, NOW(), NOW()),
(106, 110, 6, 'goods', 'Pentil Tubeless', 4, 5000, 'Ganti pentil tubeless', 2000, NOW(), NOW()),
(107, 111, 5, 'service', 'Spooring Ban', 1, 150000, 'Jasa spooring roda depan', 0, NOW(), NOW()),
(108, 112, 5, 'goods', 'Ban Bekas Dunlop 205/65 R15', 1, 750000, 'Barang second belum di-input ke inventory', 500000, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Payment Records untuk manual input sales
INSERT INTO `sale_payments` 
(`sale_id`, `amount`, `date`, `reference`, `payment_method`, `bank_name`, `note`, `created_at`, `updated_at`)
VALUES
(105, 1040000, CURDATE(), 'INV/OB2-DEMO-006', 'Tunai', NULL, 'Beli ban + jasa', NOW(), NOW()),
(106, 893000, CURDATE(), 'INV/OB2-DEMO-007', 'QRIS', NULL, 'Via QRIS', NOW(), NOW()),
(107, 150000, CURDATE(), 'INV/OB2-DEMO-008', 'Tunai', NULL, 'Jasa spooring', NOW(), NOW()),
(108, 750000, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'INV/OB2-DEMO-009', 'Tunai', NULL, 'Ban bekas', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));


-- ----------------------------------------------------------------------------
-- 2.4 SALE - PEMBAYARAN PARTIAL (Belum Lunas)
-- ----------------------------------------------------------------------------
-- Insert dengan paid_amount=0 dulu, nanti diupdate setelah payment
INSERT INTO `sales` 
(`id`, `customer_id`, `date`, `reference`, `user_id`, `customer_name`, `has_price_adjustment`, `tax_percentage`, `tax_amount`, `discount_percentage`, `discount_amount`, `shipping_amount`, `total_amount`, `has_manual_input`, `total_hpp`, `total_profit`, `paid_amount`, `due_amount`, `status`, `payment_status`, `payment_method`, `bank_name`, `note`, `manual_input_count`, `created_at`, `updated_at`)
VALUES
-- Partial payment 50% (insert as unpaid first)
(109, 1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'OB2-DEMO-010', 5, 'Peter Vincent', 0, 0, 0, 0, 0, 0, 2180000, 0, 1780000, 400000, 0, 2180000, 'Pending', 'Unpaid', 'Transfer', 'BCA', 'DP 50%, sisanya 1 minggu', 0, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)),

-- Partial payment minimal (insert as unpaid first)
(110, 2, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'OB2-DEMO-011', 6, 'Peter', 0, 0, 0, 0, 0, 0, 1425000, 0, 1280760, 144240, 0, 1425000, 'Pending', 'Unpaid', 'Tunai', NULL, 'DP Rp 500.000, sisanya menyusul', 0, DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY));

-- Detail untuk partial payment
INSERT INTO `sale_details` 
(`id`, `sale_id`, `item_name`, `product_id`, `source_type`, `product_name`, `product_code`, `quantity`, `price`, `original_price`, `is_price_adjusted`, `hpp`, `unit_price`, `sub_total`, `subtotal_profit`, `product_discount_amount`, `product_discount_type`, `product_tax_amount`, `created_at`, `updated_at`)
VALUES
-- Sale 109: 2x Ban Dunlop
(113, 109, 'Ban Dunlop SP Touring R1', 3, 'new', 'Ban Dunlop SP Touring R1 205/65 R16', 'DN-SPR1-20565R16', 2, 1090000, 1090000, 0, 890000, 1090000, 2180000, 400000, 0, 'fixed', 0, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)),

-- Sale 110: 1x Ban GT Savero
(114, 110, 'Ban GT Savero', 1, 'new', 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 1280760, 1425000, 1425000, 144240, 0, 'fixed', 0, DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY));

-- Payment Records untuk partial
INSERT INTO `sale_payments` 
(`sale_id`, `amount`, `date`, `reference`, `payment_method`, `bank_name`, `note`, `created_at`, `updated_at`)
VALUES
(109, 1090000, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'INV/OB2-DEMO-010-DP', 'Transfer', 'BCA', 'DP 50%', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)),
(110, 500000, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'INV/OB2-DEMO-011-DP', 'Tunai', NULL, 'DP awal', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY));

-- Update sales setelah payment records dibuat (manual update untuk bypass constraint)
UPDATE `sales` SET 
  `paid_amount` = 1090000,
  `due_amount` = 1090000,
  `payment_status` = 'Partial'
WHERE `id` = 109;

UPDATE `sales` SET 
  `paid_amount` = 500000,
  `due_amount` = 925000,
  `payment_status` = 'Partial'
WHERE `id` = 110;


-- ----------------------------------------------------------------------------
-- 2.5 SALE - DRAFT/BELUM BAYAR
-- ----------------------------------------------------------------------------
INSERT INTO `sales` 
(`id`, `customer_id`, `date`, `reference`, `user_id`, `customer_name`, `has_price_adjustment`, `tax_percentage`, `tax_amount`, `discount_percentage`, `discount_amount`, `shipping_amount`, `total_amount`, `has_manual_input`, `total_hpp`, `total_profit`, `paid_amount`, `due_amount`, `status`, `payment_status`, `payment_method`, `bank_name`, `note`, `manual_input_count`, `created_at`, `updated_at`)
VALUES
-- Draft - customer minta hold dulu
(111, 3, CURDATE(), 'OB2-DEMO-012', 5, 'Peter', 0, 0, 0, 0, 0, 0, 925000, 0, 725000, 200000, 0, 925000, 'Draft', 'Unpaid', 'Tunai', NULL, 'Customer minta hold, akan ambil sore', 0, NOW(), NOW());

-- Detail untuk draft
INSERT INTO `sale_details` 
(`id`, `sale_id`, `item_name`, `product_id`, `source_type`, `product_name`, `product_code`, `quantity`, `price`, `original_price`, `is_price_adjusted`, `hpp`, `unit_price`, `sub_total`, `subtotal_profit`, `product_discount_amount`, `product_discount_type`, `product_tax_amount`, `created_at`, `updated_at`)
VALUES
(115, 111, 'Ban Bridgestone Ecopia EP150', 2, 'new', 'Ban Bridgestone Ecopia EP150 185/65 R15', 'BS-EP150-18565R15', 1, 925000, 925000, 0, 725000, 925000, 925000, 200000, 0, 'fixed', 0, NOW(), NOW());


-- ----------------------------------------------------------------------------
-- 2.6 SALE - KOMBINASI (Diskon + Manual Input + Service)
-- ----------------------------------------------------------------------------
INSERT INTO `sales` 
(`id`, `customer_id`, `date`, `reference`, `user_id`, `customer_name`, `has_price_adjustment`, `tax_percentage`, `tax_amount`, `discount_percentage`, `discount_amount`, `shipping_amount`, `total_amount`, `has_manual_input`, `total_hpp`, `total_profit`, `paid_amount`, `due_amount`, `status`, `payment_status`, `payment_method`, `bank_name`, `note`, `manual_input_count`, `manual_input_summary`, `is_manual_input_notified`, `notified_at`, `created_at`, `updated_at`)
VALUES
-- Paket lengkap: Ban diskon + jasa + extras
(112, 1, CURDATE(), 'OB2-DEMO-013', 4, 'Peter Vincent', 1, 0, 0, 0, 0, 0, 4340000, 1, 3170760, 1169240, 4340000, 0, 'Completed', 'Paid', 'Transfer', 'BCA', 'Paket ganti 4 ban include jasa - diskon spesial VIP', 3, '[{"name":"Pasang Ban","quantity":4,"price":25000,"type":"service"},{"name":"Balancing","quantity":4,"price":20000,"type":"service"},{"name":"Nitrogen","quantity":4,"price":8000,"type":"goods"}]', 1, NOW(), NOW(), NOW());

-- Detail kombinasi
INSERT INTO `sale_details` 
(`id`, `sale_id`, `item_name`, `product_id`, `productable_id`, `productable_type`, `source_type`, `manual_kind`, `product_name`, `product_code`, `quantity`, `price`, `original_price`, `is_price_adjusted`, `price_adjustment_amount`, `price_adjustment_note`, `adjusted_by`, `adjusted_at`, `hpp`, `manual_hpp`, `unit_price`, `sub_total`, `subtotal_profit`, `product_discount_amount`, `product_discount_type`, `product_tax_amount`, `created_at`, `updated_at`)
VALUES
-- 4x Ban GT Savero dengan diskon 10%
(116, 112, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 4, 1282500, 1425000, 1, 142500, 'Diskon 10% VIP Customer - pembelian 4 ban sekaligus', 4, NOW(), 1280760, NULL, 1282500, 5130000, 6960, 570000, 'fixed', 0, NOW(), NOW()),

-- Jasa pasang 4 ban
(117, 112, 'Pasang Ban', NULL, 2, 'Modules\\Product\\Entities\\ServiceMaster', 'manual', 'service', 'Pasang Ban', 'SRV-2', 4, 25000, 25000, 0, 0, NULL, NULL, NULL, 0, NULL, 25000, 100000, 100000, 0, 'fixed', 0, NOW(), NOW()),

-- Balancing 4 roda
(118, 112, 'Balancing', NULL, 3, 'Modules\\Product\\Entities\\ServiceMaster', 'manual', 'service', 'Balancing', 'SRV-3', 4, 20000, 20000, 0, 0, NULL, NULL, NULL, 0, NULL, 20000, 80000, 80000, 0, 'fixed', 0, NOW(), NOW()),

-- Nitrogen 4 ban
(119, 112, 'Nitrogen', NULL, NULL, NULL, 'manual', 'goods', 'Isi Nitrogen', 'GD-N2', 4, 8000, 8000, 0, 0, NULL, NULL, NULL, 0, 0, 8000, 32000, 32000, 0, 'fixed', 0, NOW(), NOW());

-- Manual input details untuk kombinasi
INSERT INTO `manual_input_details` 
(`sale_id`, `sale_detail_id`, `cashier_id`, `item_type`, `item_name`, `quantity`, `price`, `manual_reason`, `cost_price`, `created_at`, `updated_at`)
VALUES
(112, 117, 4, 'service', 'Pasang Ban', 4, 25000, 'Paket pasang 4 ban', 0, NOW(), NOW()),
(112, 118, 4, 'service', 'Balancing', 4, 20000, 'Balancing 4 roda', 0, NOW(), NOW()),
(112, 119, 4, 'goods', 'Nitrogen', 4, 8000, 'Isi nitrogen 4 ban', 0, NOW(), NOW());

-- Payment untuk kombinasi
INSERT INTO `sale_payments` 
(`sale_id`, `amount`, `date`, `reference`, `payment_method`, `bank_name`, `note`, `created_at`, `updated_at`)
VALUES
(112, 4340000, CURDATE(), 'INV/OB2-DEMO-013', 'Transfer', 'BCA', 'Paket lengkap VIP customer', NOW(), NOW());


-- ============================================================================
-- SECTION 3: OWNER NOTIFICATIONS (Untuk Demo Alert)
-- ============================================================================

INSERT INTO `owner_notifications` 
(`user_id`, `sale_id`, `notification_type`, `title`, `message`, `data`, `severity`, `is_read`, `is_reviewed`, `created_at`, `updated_at`)
VALUES
-- Alert diskon besar
(1, 104, 'discount_alert', '⚠️ Diskon Besar 15% - OB2-DEMO-005', 'Diskon 15% untuk pembelian bulk PT Logistik Nusantara. Total: Rp 4.837.500. Perlu review approval.', '{"invoice_no":"OB2-DEMO-005","discount_percent":15,"total_amount":4837500,"cashier_name":"Kasir Ani"}', 'warning', 0, 0, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY)),

-- Alert manual input
(1, 108, 'manual_input_alert', '⚠️ Input Manual Barang - OB2-DEMO-009', 'Kasir input manual barang yang tidak ada di sistem: Ban Bekas Dunlop 205/65 R15', '{"invoice_no":"OB2-DEMO-009","items_count":1,"total_amount":750000,"cashier_name":"Kasir Ani"}', 'info', 0, 0, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY)),

-- Alert kombinasi VIP
(1, 112, 'high_value_transaction', '💎 Transaksi VIP - OB2-DEMO-013', 'Transaksi besar Rp 4.340.000 untuk customer VIP Peter Vincent. Include diskon 10% dan 3 jenis jasa.', '{"invoice_no":"OB2-DEMO-013","total_amount":4340000,"customer_name":"Peter Vincent","has_discount":true,"has_services":true}', 'info', 0, 0, NOW(), NOW()),

-- Untuk Supervisor juga
(4, 104, 'discount_alert', '⚠️ Diskon Besar 15% - OB2-DEMO-005', 'Diskon 15% untuk pembelian bulk PT Logistik Nusantara.', '{"invoice_no":"OB2-DEMO-005","discount_percent":15}', 'warning', 0, 0, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));


-- ============================================================================
-- SECTION 4: UPDATE STOK PRODUK (Sinkronisasi dengan transaksi)
-- ============================================================================

-- Update stok berdasarkan transaksi demo
-- Catatan: Trigger di database akan handle ini secara otomatis,
-- tapi untuk safety kita prepare update manual jika diperlukan

-- UPDATE products SET product_quantity = product_quantity - 2 WHERE id = 2;  -- 2x EP150 terjual
-- UPDATE products SET product_quantity = product_quantity - 4 WHERE id = 1;  -- 4x GT Savero bulk
-- ... dst (handled by triggers)


-- ============================================================================
-- RINGKASAN DATA DEMO
-- ============================================================================
/*
ADJUSTMENTS (6 record):
- 3x Pending (menunggu approval) - bisa demo approve/reject
- 2x Approved (sudah disetujui)
- 1x Rejected (ditolak)

SALES (13 record):
- 2x Normal (tanpa diskon/manual)
- 3x Dengan Diskon (5%, 10%, 15%)
- 4x Dengan Manual Input (jasa + barang)
- 2x Partial Payment (belum lunas)
- 1x Draft (belum bayar sama sekali)
- 1x Kombinasi (diskon + manual + service)

NOTIFICATIONS (4 record):
- Untuk demo notifikasi ke Owner/Supervisor
*/

COMMIT;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- QUERY VERIFIKASI (Jalankan setelah insert untuk memastikan data masuk)
-- ============================================================================
/*
-- Cek Adjustments
SELECT id, reference, status, reason, total_value FROM adjustments WHERE id >= 100 ORDER BY id;

-- Cek Sales
SELECT id, reference, status, payment_status, total_amount, has_price_adjustment, has_manual_input 
FROM sales WHERE id >= 100 ORDER BY id;

-- Cek Notifications
SELECT id, notification_type, title, severity, is_read FROM owner_notifications ORDER BY id DESC LIMIT 10;
*/
