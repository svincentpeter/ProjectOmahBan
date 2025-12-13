-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 13, 2025 at 06:08 PM
-- Server version: 8.0.30
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project-omah_ban`
--

-- --------------------------------------------------------

--
-- Table structure for table `adjusted_products`
--

CREATE TABLE `adjusted_products` (
  `id` bigint UNSIGNED NOT NULL,
  `adjustment_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `adjusted_products`
--
DELIMITER $$
CREATE TRIGGER `trg_adjusted_products_after_change` AFTER INSERT ON `adjusted_products` FOR EACH ROW BEGIN
  UPDATE adjustments a
  SET a.total_value = (
    SELECT IFNULL(SUM(p.product_cost * ap.quantity), 0)
    FROM adjusted_products ap
    JOIN products p ON p.id = ap.product_id
    WHERE ap.adjustment_id = NEW.adjustment_id
  )
  WHERE a.id = NEW.adjustment_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_adjusted_products_after_delete` AFTER DELETE ON `adjusted_products` FOR EACH ROW BEGIN
  UPDATE adjustments a
  SET a.total_value = (
    SELECT IFNULL(SUM(p.product_cost * ap.quantity), 0)
    FROM adjusted_products ap
    JOIN products p ON p.id = ap.product_id
    WHERE ap.adjustment_id = OLD.adjustment_id
  )
  WHERE a.id = OLD.adjustment_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_adjusted_products_after_update` AFTER UPDATE ON `adjusted_products` FOR EACH ROW BEGIN
  UPDATE adjustments a
  SET a.total_value = (
    SELECT IFNULL(SUM(p.product_cost * ap.quantity), 0)
    FROM adjusted_products ap
    JOIN products p ON p.id = ap.product_id
    WHERE ap.adjustment_id = NEW.adjustment_id
  )
  WHERE a.id = NEW.adjustment_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `adjustments`
--

CREATE TABLE `adjustments` (
  `id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `requester_id` bigint UNSIGNED DEFAULT NULL,
  `approver_id` bigint UNSIGNED DEFAULT NULL,
  `reason` enum('Rusak','Hilang','Kadaluarsa','Lainnya') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `approval_notes` text COLLATE utf8mb4_unicode_ci,
  `approval_date` timestamp NULL DEFAULT NULL,
  `total_value` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `adjustment_files`
--

CREATE TABLE `adjustment_files` (
  `id` bigint UNSIGNED NOT NULL,
  `adjustment_id` bigint UNSIGNED NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint UNSIGNED DEFAULT NULL,
  `mime_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `adjustment_logs`
--

CREATE TABLE `adjustment_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `adjustment_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` longtext COLLATE utf8mb4_unicode_ci,
  `locked` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `category_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `categories_view`
-- (See below for the actual view)
--
CREATE TABLE `categories_view` (
`id` bigint unsigned
,`category_code` varchar(255)
,`name` varchar(255)
,`created_at` timestamp
,`updated_at` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` bigint UNSIGNED NOT NULL,
  `currency_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `thousand_separator` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `decimal_separator` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exchange_rate` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `amount` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `payment_method` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `category_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fontee_config`
--

CREATE TABLE `fontee_config` (
  `id` bigint UNSIGNED NOT NULL,
  `config_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `config_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuration untuk Fontee API integration';

-- --------------------------------------------------------

--
-- Table structure for table `manual_input_details`
--

CREATE TABLE `manual_input_details` (
  `id` bigint UNSIGNED NOT NULL,
  `sale_id` bigint UNSIGNED NOT NULL COMMENT 'ID transaksi',
  `sale_detail_id` bigint UNSIGNED NOT NULL COMMENT 'ID item dalam transaksi',
  `cashier_id` bigint UNSIGNED NOT NULL COMMENT 'ID kasir yang input',
  `item_type` enum('service','goods') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipe input',
  `item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `price` bigint UNSIGNED NOT NULL COMMENT 'Harga yang diinput kasir',
  `manual_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Alasan input manual dari kasir',
  `cost_price` bigint UNSIGNED DEFAULT NULL COMMENT 'Harga beli (jika diisi)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detail tracking item manual untuk audit & reporting';

--
-- Triggers `manual_input_details`
--
DELIMITER $$
CREATE TRIGGER `trg_manual_input_details_after_delete` AFTER DELETE ON `manual_input_details` FOR EACH ROW BEGIN
  UPDATE sales 
    SET manual_input_count = (
          SELECT COUNT(*) FROM manual_input_details WHERE sale_id = OLD.sale_id
        ),
        has_manual_input = CASE 
          WHEN (SELECT COUNT(*) FROM manual_input_details WHERE sale_id = OLD.sale_id) > 0 THEN 1 ELSE 0
        END
  WHERE id = OLD.sale_id;

  UPDATE manual_input_summary_daily
    SET total_manual_items  = GREATEST(total_manual_items - OLD.quantity, 0),
        total_manual_value  = GREATEST(total_manual_value - (OLD.quantity * OLD.price), 0),
        manual_input_count  = GREATEST(manual_input_count - 1, 0)
  WHERE `date` = CURDATE() AND `cashier_id` = OLD.cashier_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_manual_input_details_after_insert` AFTER INSERT ON `manual_input_details` FOR EACH ROW BEGIN
  UPDATE sales 
    SET has_manual_input = 1,
        manual_input_count = (SELECT COUNT(*) FROM manual_input_details WHERE sale_id = NEW.sale_id)
  WHERE id = NEW.sale_id;

  INSERT INTO manual_input_summary_daily
    (`date`, `cashier_id`, `total_transactions`, `manual_input_count`, `total_manual_items`, `total_manual_value`)
  VALUES
    (DATE(NEW.created_at), NEW.cashier_id, 0, 1, NEW.quantity, NEW.quantity * NEW.price)
  ON DUPLICATE KEY UPDATE
    manual_input_count = manual_input_count + 1,
    total_manual_items = total_manual_items + NEW.quantity,
    total_manual_value = total_manual_value + (NEW.quantity * NEW.price);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `manual_input_logs`
--

CREATE TABLE `manual_input_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `sale_id` bigint UNSIGNED NOT NULL,
  `sale_detail_id` bigint UNSIGNED DEFAULT NULL,
  `cashier_id` bigint UNSIGNED NOT NULL COMMENT 'Kasir yang input',
  `input_type` enum('manual_item','manual_service','price_override','discount_applied') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual_item',
  `item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `standard_price` bigint UNSIGNED DEFAULT NULL COMMENT 'Harga standar (jika ada)',
  `input_price` bigint UNSIGNED NOT NULL COMMENT 'Harga yang diinput',
  `price_variance` bigint DEFAULT NULL COMMENT 'Selisih dengan standar (bisa negatif)',
  `variance_percent` float DEFAULT NULL COMMENT 'Persentase deviasi',
  `reason_provided` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Alasan wajib dari kasir',
  `supervisor_pin_required` tinyint(1) DEFAULT '0' COMMENT 'Apakah butuh PIN supervisor?',
  `supervisor_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Supervisor yang approve (jika butuh PIN)',
  `approval_status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `approval_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `owner_notified` tinyint(1) DEFAULT '0',
  `owner_notification_id` bigint UNSIGNED DEFAULT NULL,
  `owner_notified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit trail lengkap untuk setiap input manual & approval chain';

-- --------------------------------------------------------

--
-- Table structure for table `manual_input_summary_daily`
--

CREATE TABLE `manual_input_summary_daily` (
  `id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `cashier_id` bigint UNSIGNED NOT NULL,
  `total_transactions` int UNSIGNED DEFAULT '0' COMMENT 'Total transaksi hari ini',
  `manual_input_count` int UNSIGNED DEFAULT '0' COMMENT 'Jumlah transaksi dengan input manual',
  `total_manual_items` int UNSIGNED DEFAULT '0' COMMENT 'Total item manual (qty)',
  `total_manual_value` bigint UNSIGNED DEFAULT '0' COMMENT 'Total nilai item manual',
  `pending_approvals` int UNSIGNED DEFAULT '0' COMMENT 'Jumlah approval pending',
  `top_manual_items` json DEFAULT NULL COMMENT '{"item1": count, "item2": count, ...}',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pre-calculated summary harian untuk dashboard performa';

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversions_disk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint UNSIGNED NOT NULL,
  `manipulations` json NOT NULL,
  `custom_properties` json NOT NULL,
  `generated_conversions` json NOT NULL,
  `responsive_images` json NOT NULL,
  `order_column` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `owner_notifications`
--

CREATE TABLE `owner_notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL COMMENT 'Owner/Supervisor yang menerima notifikasi',
  `sale_id` bigint UNSIGNED NOT NULL COMMENT 'Link ke transaksi',
  `notification_type` enum('manual_input_alert','price_adjustment','discount_alert','high_value_transaction') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual_input_alert',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` json DEFAULT NULL COMMENT 'Extra data: {cashier_name, items_count, total_amount, invoice_no}',
  `severity` enum('info','warning','critical') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT '0',
  `is_reviewed` tinyint(1) DEFAULT '0' COMMENT 'Owner sudah review transaksinya?',
  `read_at` timestamp NULL DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint UNSIGNED DEFAULT NULL COMMENT 'User ID yang review',
  `review_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fontee_message_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID dari Fontee API',
  `fontee_status` enum('pending','sent','failed','read') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `fontee_sent_at` timestamp NULL DEFAULT NULL,
  `fontee_error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log semua notifikasi untuk owner terhadap input manual & aktivitas penting';

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `category_id` bigint UNSIGNED NOT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_quantity` int NOT NULL,
  `stok_awal` int NOT NULL DEFAULT '0',
  `product_cost` int NOT NULL,
  `product_price` int NOT NULL,
  `product_unit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_stock_alert` int NOT NULL,
  `product_order_tax` int DEFAULT NULL,
  `product_tax_type` tinyint DEFAULT NULL,
  `product_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `product_size` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ring` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_year` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_seconds`
--

CREATE TABLE `product_seconds` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `unique_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `condition_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `purchase_price` bigint NOT NULL,
  `selling_price` bigint NOT NULL,
  `status` enum('available','sold') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `size` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ring` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_year` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `supplier_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` bigint NOT NULL DEFAULT '0',
  `paid_amount` bigint NOT NULL DEFAULT '0',
  `due_amount` bigint NOT NULL DEFAULT '0',
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_details`
--

CREATE TABLE `purchase_details` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` bigint NOT NULL,
  `sub_total` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_payments`
--

CREATE TABLE `purchase_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED NOT NULL,
  `amount` bigint NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_seconds`
--

CREATE TABLE `purchase_seconds` (
  `id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL COMMENT 'Tanggal beli bekas',
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Format: PBS-YYYYMMDD-0001',
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama customer yang jual bekas',
  `customer_phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nomor HP customer',
  `total_amount` bigint NOT NULL DEFAULT '0' COMMENT 'Total bayar ke customer',
  `paid_amount` bigint NOT NULL DEFAULT '0' COMMENT 'Jumlah yang sudah dibayar',
  `due_amount` bigint NOT NULL DEFAULT '0' COMMENT 'Sisa yang belum dibayar',
  `status` enum('Pending','Completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending' COMMENT 'Status pembelian',
  `payment_status` enum('Lunas','Belum Lunas') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Belum Lunas',
  `payment_method` enum('Tunai','Transfer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Tunai',
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nama bank jika Transfer',
  `note` text COLLATE utf8mb4_unicode_ci COMMENT 'Catatan tambahan',
  `user_id` bigint UNSIGNED DEFAULT NULL COMMENT 'User yang input',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_second_details`
--

CREATE TABLE `purchase_second_details` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_second_id` bigint UNSIGNED NOT NULL,
  `product_second_id` bigint UNSIGNED NOT NULL COMMENT 'ID dari productseconds (TANPA underscore)',
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `condition_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Catatan kondisi produk',
  `quantity` int NOT NULL DEFAULT '1' COMMENT 'Selalu 1 untuk produk bekas',
  `unit_price` bigint NOT NULL DEFAULT '0' COMMENT 'Harga beli per unit',
  `sub_total` bigint NOT NULL DEFAULT '0' COMMENT 'Total = unit_price * 1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Foreign key ke table customers',
  `date` date NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email customer (opsional)',
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nomor telepon customer (opsional)',
  `has_price_adjustment` tinyint(1) DEFAULT '0' COMMENT '1=ada item dengan harga diedit',
  `tax_percentage` int NOT NULL DEFAULT '0',
  `tax_amount` bigint NOT NULL DEFAULT '0',
  `discount_percentage` int NOT NULL DEFAULT '0',
  `discount_amount` bigint NOT NULL DEFAULT '0',
  `shipping_amount` bigint NOT NULL DEFAULT '0',
  `total_amount` bigint NOT NULL DEFAULT '0',
  `has_manual_input` tinyint(1) DEFAULT '0' COMMENT 'Flag ada input manual',
  `total_hpp` bigint NOT NULL DEFAULT '0',
  `total_profit` bigint NOT NULL DEFAULT '0',
  `paid_amount` bigint NOT NULL DEFAULT '0',
  `due_amount` bigint NOT NULL DEFAULT '0',
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `snap_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `midtrans_transaction_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `midtrans_payment_type` enum('gopay','shopeepay','qris','bank_transfer','credit_card','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `manual_input_count` int UNSIGNED DEFAULT '0' COMMENT 'Jumlah item dengan input manual',
  `manual_input_summary` json DEFAULT NULL COMMENT 'Summary item manual: [{"name":"...", "qty":1, "price":..., "reason":"..."}]',
  `is_manual_input_notified` tinyint(1) DEFAULT '0' COMMENT 'Flag: sudah notify owner?',
  `notified_at` timestamp NULL DEFAULT NULL COMMENT 'Waktu notifikasi owner'
) ;

--
-- Triggers `sales`
--
DELIMITER $$
CREATE TRIGGER `trg_sales_after_insert` AFTER INSERT ON `sales` FOR EACH ROW BEGIN
  INSERT INTO manual_input_summary_daily (`date`, `cashier_id`, `total_transactions`)
  VALUES (NEW.`date`, COALESCE(NEW.`user_id`, 0), 1)
  ON DUPLICATE KEY UPDATE `total_transactions` = `total_transactions` + 1;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sale_details`
--

CREATE TABLE `sale_details` (
  `id` bigint UNSIGNED NOT NULL,
  `sale_id` bigint UNSIGNED NOT NULL,
  `item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `productable_id` bigint UNSIGNED DEFAULT NULL,
  `productable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_type` enum('new','second','manual') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `manual_kind` enum('service','goods') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `price` bigint NOT NULL,
  `original_price` bigint DEFAULT NULL COMMENT 'Harga asli dari master product',
  `is_price_adjusted` tinyint(1) DEFAULT '0' COMMENT 'Flag: 1=ada perubahan harga, 0=tidak ada',
  `price_adjustment_amount` bigint DEFAULT '0' COMMENT 'Selisih: original_price - price (positif = diskon)',
  `price_adjustment_note` text COLLATE utf8mb4_unicode_ci COMMENT 'Catatan kasir (WAJIB jika ada diskon)',
  `adjusted_by` bigint UNSIGNED DEFAULT NULL COMMENT 'User ID kasir yang edit harga',
  `adjusted_at` timestamp NULL DEFAULT NULL COMMENT 'Waktu edit harga',
  `hpp` bigint NOT NULL DEFAULT '0',
  `manual_hpp` bigint DEFAULT NULL,
  `unit_price` bigint NOT NULL,
  `sub_total` bigint NOT NULL,
  `subtotal_profit` bigint NOT NULL DEFAULT '0',
  `product_discount_amount` bigint NOT NULL,
  `product_discount_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `product_tax_amount` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

--
-- Triggers `sale_details`
--
DELIMITER $$
CREATE TRIGGER `trg_sale_details_chk_bi` BEFORE INSERT ON `sale_details` FOR EACH ROW BEGIN
  -- Normalisasi dasar
  SET NEW.source_type = LOWER(IFNULL(NEW.source_type, ''));
  IF NEW.price IS NULL OR NEW.price < 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'price wajib diisi dan >= 0';
  END IF;
  IF NEW.quantity IS NULL OR NEW.quantity < 1 THEN
    SET NEW.quantity = 1;
  END IF;
  IF NEW.unit_price IS NULL OR NEW.unit_price = 0 THEN
    SET NEW.unit_price = NEW.price;
  END IF;
  IF NEW.sub_total IS NULL OR NEW.sub_total = 0 THEN
    SET NEW.sub_total = NEW.unit_price * NEW.quantity;
  END IF;

  -- Deteksi JASA (ServiceMaster) → paksa jadi manual/service
  IF NEW.productable_type IN ('Modules\Product\Entities\ServiceMaster','ModulesProductEntitiesServiceMaster')
     OR LEFT(NEW.product_code,4) = 'SRV-'
  THEN
    SET NEW.source_type = 'manual';
    SET NEW.manual_kind = IFNULL(NEW.manual_kind, 'service');
  END IF;

  -- Validasi per sumber
  IF NEW.source_type = 'new' THEN
    IF NEW.product_id IS NULL OR NEW.product_id = 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk Baru: product_id wajib';
    END IF;
    -- untuk produk baru: tidak pakai productable
    SET NEW.productable_id = NULL;
    SET NEW.productable_type = NULL;
    SET NEW.manual_kind = NULL;

  ELSEIF NEW.source_type = 'second' THEN
    SET NEW.quantity = 1; -- unit second selalu 1
    IF NEW.productable_id IS NULL OR NEW.productable_id = 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk Second: productable_id wajib';
    END IF;
    IF NOT EXISTS (
      SELECT 1 FROM product_seconds ps
      WHERE ps.id = NEW.productable_id AND ps.status = 'available'
    ) THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk Second: unit tidak available';
    END IF;
    SET NEW.product_id = NULL;
    SET NEW.manual_kind = NULL;

  ELSE
    -- manual (service/goods)
    SET NEW.source_type = 'manual';
    SET NEW.manual_kind = IFNULL(NEW.manual_kind, 'goods');
    -- manual tidak memakai products.id
    SET NEW.product_id = NULL;
    -- catatan: sekarang kita IJINKAN simpan link ke master (productable_type/id) bila ada
    -- tidak ada movement stok utk manual, jadi aman
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_sale_details_chk_bu` BEFORE UPDATE ON `sale_details` FOR EACH ROW BEGIN
  SET NEW.source_type = LOWER(IFNULL(NEW.source_type, ''));
  IF NEW.price IS NULL OR NEW.price < 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'price wajib diisi dan >= 0 (UPDATE)';
  END IF;
  IF NEW.quantity IS NULL OR NEW.quantity < 1 THEN
    SET NEW.quantity = 1;
  END IF;
  IF NEW.unit_price IS NULL OR NEW.unit_price = 0 THEN
    SET NEW.unit_price = NEW.price;
  END IF;
  IF NEW.sub_total IS NULL OR NEW.sub_total = 0 THEN
    SET NEW.sub_total = NEW.unit_price * NEW.quantity;
  END IF;

  -- Deteksi JASA → paksa manual/service
  IF NEW.productable_type IN ('Modules\Product\Entities\ServiceMaster','ModulesProductEntitiesServiceMaster')
     OR LEFT(NEW.product_code,4) = 'SRV-'
  THEN
    SET NEW.source_type = 'manual';
    SET NEW.manual_kind = IFNULL(NEW.manual_kind, 'service');
  END IF;

  IF NEW.source_type = 'new' THEN
    IF NEW.product_id IS NULL OR NEW.product_id = 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk Baru: product_id wajib (UPDATE)';
    END IF;
    SET NEW.productable_id = NULL;
    SET NEW.productable_type = NULL;
    SET NEW.manual_kind = NULL;

  ELSEIF NEW.source_type = 'second' THEN
    SET NEW.quantity = 1;
    IF NEW.productable_id IS NULL OR NEW.productable_id = 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk Second: productable_id wajib (UPDATE)';
    END IF;
    IF NEW.productable_id <> OLD.productable_id THEN
      IF NOT EXISTS (
        SELECT 1 FROM product_seconds ps
        WHERE ps.id = NEW.productable_id AND ps.status = 'available'
      ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk Second: unit baru tidak available (UPDATE)';
      END IF;
    END IF;
    SET NEW.product_id = NULL;
    SET NEW.manual_kind = NULL;

  ELSE
    -- manual
    SET NEW.source_type = 'manual';
    SET NEW.manual_kind = IFNULL(NEW.manual_kind, 'goods');
    SET NEW.product_id = NULL;
    -- pertahankan productable_type/id bila ada
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_sales_flags_ad_sd` AFTER DELETE ON `sale_details` FOR EACH ROW BEGIN
  UPDATE sales s
  SET
    s.has_price_adjustment =
      EXISTS (SELECT 1 FROM sale_details d WHERE d.sale_id = s.id AND d.is_price_adjusted = 1),
    s.manual_input_count =
      (SELECT COUNT(*) FROM sale_details d WHERE d.sale_id = s.id AND d.source_type = 'manual'),
    s.has_manual_input =
      (SELECT CASE WHEN EXISTS(SELECT 1 FROM sale_details d WHERE d.sale_id = s.id AND d.source_type = 'manual')
              THEN 1 ELSE 0 END)
  WHERE s.id = OLD.sale_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_sales_flags_ai_sd` AFTER INSERT ON `sale_details` FOR EACH ROW BEGIN
  UPDATE sales s
  SET
    s.has_price_adjustment =
      EXISTS (SELECT 1 FROM sale_details d WHERE d.sale_id = s.id AND d.is_price_adjusted = 1),
    s.manual_input_count =
      (SELECT COUNT(*) FROM sale_details d WHERE d.sale_id = s.id AND d.source_type = 'manual'),
    s.has_manual_input =
      (SELECT CASE WHEN EXISTS(SELECT 1 FROM sale_details d WHERE d.sale_id = s.id AND d.source_type = 'manual')
              THEN 1 ELSE 0 END)
  WHERE s.id = NEW.sale_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_sales_flags_au_sd` AFTER UPDATE ON `sale_details` FOR EACH ROW BEGIN
  UPDATE sales s
  SET
    s.has_price_adjustment =
      EXISTS (SELECT 1 FROM sale_details d WHERE d.sale_id = s.id AND d.is_price_adjusted = 1),
    s.manual_input_count =
      (SELECT COUNT(*) FROM sale_details d WHERE d.sale_id = s.id AND d.source_type = 'manual'),
    s.has_manual_input =
      (SELECT CASE WHEN EXISTS(SELECT 1 FROM sale_details d WHERE d.sale_id = s.id AND d.source_type = 'manual')
              THEN 1 ELSE 0 END)
  WHERE s.id = NEW.sale_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_sd_inv_ad` AFTER DELETE ON `sale_details` FOR EACH ROW BEGIN
  DECLARE v_user_id BIGINT UNSIGNED;
  DECLARE v_ref VARCHAR(191);

  SELECT s.user_id, s.reference INTO v_user_id, v_ref
  FROM sales s WHERE s.id = OLD.sale_id;

  IF OLD.source_type = 'new' THEN
    UPDATE products
      SET product_quantity = product_quantity + OLD.quantity
    WHERE id = OLD.product_id;

    INSERT INTO stock_movements
      (productable_type, productable_id, `type`, quantity, description, user_id, created_at, updated_at)
    VALUES
      ('Modules\Product\Entities\Product', OLD.product_id, 'in', OLD.quantity,
       CONCAT('Revert sale ', IFNULL(v_ref, OLD.sale_id)), v_user_id, NOW(), NOW());

  ELSEIF OLD.source_type = 'second' THEN
    UPDATE product_seconds
      SET status = 'available'
    WHERE id = OLD.productable_id AND status = 'sold';

    INSERT INTO stock_movements
      (productable_type, productable_id, `type`, quantity, description, user_id, created_at, updated_at)
    VALUES
      ('Modules\Product\Entities\ProductSecond', OLD.productable_id, 'in', 1,
       CONCAT('Revert sale ', IFNULL(v_ref, OLD.sale_id)), v_user_id, NOW(), NOW());
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_sd_inv_ai` AFTER INSERT ON `sale_details` FOR EACH ROW BEGIN
  DECLARE v_user_id BIGINT UNSIGNED;
  DECLARE v_ref VARCHAR(191);

  SELECT s.user_id, s.reference INTO v_user_id, v_ref
  FROM sales s WHERE s.id = NEW.sale_id;

  IF NEW.source_type = 'new' THEN
    UPDATE products
      SET product_quantity = product_quantity - NEW.quantity
    WHERE id = NEW.product_id;

    INSERT INTO stock_movements
      (productable_type, productable_id, product_id, ref_id, ref_type, `type`,
       quantity, description, user_id, created_at, updated_at)
    VALUES
      ('Modules\Product\Entities\Product', NEW.product_id, NEW.product_id,
       NEW.sale_id, 'sale', 'out', NEW.quantity,
       CONCAT('Sale ', COALESCE(v_ref, NEW.sale_id)), v_user_id, NOW(), NOW());

  ELSEIF NEW.source_type = 'second' THEN
    UPDATE product_seconds
      SET status = 'sold'
    WHERE id = NEW.productable_id AND status = 'available';

    INSERT INTO stock_movements
      (productable_type, productable_id, ref_id, ref_type, `type`,
       quantity, description, user_id, created_at, updated_at)
    VALUES
      ('Modules\Product\Entities\ProductSecond', NEW.productable_id,
       NEW.sale_id, 'sale', 'out', 1,
       CONCAT('Sale ', COALESCE(v_ref, NEW.sale_id)), v_user_id, NOW(), NOW());
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_sd_inv_au` AFTER UPDATE ON `sale_details` FOR EACH ROW BEGIN
  DECLARE v_user_id BIGINT UNSIGNED;
  DECLARE v_ref VARCHAR(191);
  DECLARE v_change BOOL DEFAULT FALSE;

  IF (OLD.source_type <> NEW.source_type)
     OR (IFNULL(OLD.product_id,0) <> IFNULL(NEW.product_id,0))
     OR (IFNULL(OLD.productable_id,0) <> IFNULL(NEW.productable_id,0))
     OR (OLD.quantity <> NEW.quantity) THEN
    SET v_change = TRUE;
  END IF;

  IF v_change THEN
    SELECT s.user_id, s.reference INTO v_user_id, v_ref
    FROM sales s WHERE s.id = NEW.sale_id;

    /* Revert OLD */
    IF OLD.source_type = 'new' THEN
      UPDATE products
        SET product_quantity = product_quantity + OLD.quantity
      WHERE id = OLD.product_id;

      INSERT INTO stock_movements
        (productable_type, productable_id, `type`, quantity, description, user_id, created_at, updated_at)
      VALUES
        ('Modules\Product\Entities\Product', OLD.product_id, 'in', OLD.quantity,
         CONCAT('Adjust (revert) sale ', IFNULL(v_ref, NEW.sale_id)), v_user_id, NOW(), NOW());

    ELSEIF OLD.source_type = 'second' THEN
      UPDATE product_seconds
        SET status = 'available'
      WHERE id = OLD.productable_id AND status = 'sold';

      INSERT INTO stock_movements
        (productable_type, productable_id, `type`, quantity, description, user_id, created_at, updated_at)
      VALUES
        ('Modules\Product\Entities\ProductSecond', OLD.productable_id, 'in', 1,
         CONCAT('Adjust (revert) sale ', IFNULL(v_ref, NEW.sale_id)), v_user_id, NOW(), NOW());
    END IF;

    /* Apply NEW */
    IF NEW.source_type = 'new' THEN
      UPDATE products
        SET product_quantity = product_quantity - NEW.quantity
      WHERE id = NEW.product_id;

      INSERT INTO stock_movements
        (productable_type, productable_id, `type`, quantity, description, user_id, created_at, updated_at)
      VALUES
        ('Modules\Product\Entities\Product', NEW.product_id, 'out', NEW.quantity,
         CONCAT('Adjust sale ', IFNULL(v_ref, NEW.sale_id)), v_user_id, NOW(), NOW());

    ELSEIF NEW.source_type = 'second' THEN
      UPDATE product_seconds
        SET status = 'sold'
      WHERE id = NEW.productable_id AND status = 'available';

      INSERT INTO stock_movements
        (productable_type, productable_id, `type`, quantity, description, user_id, created_at, updated_at)
      VALUES
        ('Modules\Product\Entities\ProductSecond', NEW.productable_id, 'out', 1,
         CONCAT('Adjust sale ', IFNULL(v_ref, NEW.sale_id)), v_user_id, NOW(), NOW());
    END IF;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_sd_stock_guard_bi` BEFORE INSERT ON `sale_details` FOR EACH ROW BEGIN
  DECLARE v_qty INT DEFAULT 0;
  DECLARE v_exists INT DEFAULT 0;

  /* normalisasi */
  SET NEW.source_type = LOWER(NEW.source_type);

  IF NEW.source_type = 'new' THEN
    IF NEW.product_id IS NULL OR NEW.product_id = 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk Baru: product_id wajib';
    END IF;

    IF NEW.quantity IS NULL OR NEW.quantity < 1 THEN
      SET NEW.quantity = 1;
    END IF;

    SELECT product_quantity INTO v_qty
    FROM products
    WHERE id = NEW.product_id
    LIMIT 1;

    IF v_qty IS NULL THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk Baru: produk tidak ditemukan';
    END IF;

    IF v_qty < NEW.quantity THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok tidak mencukupi (BEFORE INSERT)';
    END IF;

  ELSEIF NEW.source_type = 'second' THEN
    SET NEW.quantity = 1;

    SELECT COUNT(*)
      INTO v_exists
    FROM product_seconds ps
    WHERE ps.id = NEW.productable_id
      AND ps.status = 'available';

    IF v_exists = 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk Second: unit tidak available';
    END IF;

  ELSE
    /* manual */
    SET NEW.quantity = 1;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_sd_stock_guard_bu` BEFORE UPDATE ON `sale_details` FOR EACH ROW BEGIN
  DECLARE v_old INT DEFAULT 0;
  DECLARE v_new INT DEFAULT 0;
  DECLARE v_stock INT DEFAULT 0;
  DECLARE v_stock_new INT DEFAULT 0;
  DECLARE v_stock_old INT DEFAULT 0;
  DECLARE v_exists INT DEFAULT 0;

  /* normalisasi */
  SET NEW.source_type = LOWER(NEW.source_type);

  IF NEW.source_type = 'new' THEN
    SET v_old = IFNULL(OLD.quantity,0);
    SET v_new = IFNULL(NEW.quantity,0);

    IF NEW.product_id IS NULL OR NEW.product_id = 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk Baru: product_id wajib (UPDATE)';
    END IF;

    IF v_new < 1 THEN
      SET NEW.quantity = 1;
      SET v_new = 1;
    END IF;

    IF NEW.product_id = OLD.product_id THEN
      SELECT product_quantity INTO v_stock FROM products WHERE id = NEW.product_id;
      /* setelah revert v_old dan apply v_new, stok tidak boleh minus */
      IF v_stock + v_old - v_new < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok tidak cukup setelah UPDATE';
      END IF;
    ELSE
      /* ganti produk: cek stok produk baru, dan produk lama akan direvert di AFTER */
      SELECT product_quantity INTO v_stock_new FROM products WHERE id = NEW.product_id;
      IF v_stock_new - v_new < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok tidak cukup (produk baru) setelah UPDATE';
      END IF;
    END IF;

  ELSEIF NEW.source_type = 'second' THEN
    SET NEW.quantity = 1;

    IF NEW.productable_id <> OLD.productable_id THEN
      SELECT COUNT(*) INTO v_exists
      FROM product_seconds ps
      WHERE ps.id = NEW.productable_id
        AND ps.status = 'available';
      IF v_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Unit second baru tidak available (UPDATE)';
      END IF;
    END IF;

  ELSE
    /* manual */
    SET NEW.quantity = 1;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sale_payments`
--

CREATE TABLE `sale_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `sale_id` bigint UNSIGNED NOT NULL,
  `amount` bigint NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `service_masters`
--

CREATE TABLE `service_masters` (
  `id` bigint UNSIGNED NOT NULL,
  `service_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama jasa: Pasang Ban, Balancing, dll',
  `standard_price` bigint UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Harga standar (dalam rupiah)',
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'service' COMMENT 'Kategori: service|goods|custom',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Deskripsi jasa',
  `status` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT '1=aktif, 0=nonaktif',
  `price_before` bigint UNSIGNED DEFAULT NULL COMMENT 'Harga sebelum diubah',
  `price_after` bigint UNSIGNED DEFAULT NULL COMMENT 'Harga setelah diubah',
  `price_updated_at` timestamp NULL DEFAULT NULL COMMENT 'Waktu perubahan harga',
  `updated_by` bigint UNSIGNED DEFAULT NULL COMMENT 'User ID yang ubah harga',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Master data jasa dengan harga standar';

-- --------------------------------------------------------

--
-- Table structure for table `service_master_audits`
--

CREATE TABLE `service_master_audits` (
  `id` bigint UNSIGNED NOT NULL,
  `service_master_id` bigint UNSIGNED NOT NULL,
  `old_price` bigint UNSIGNED NOT NULL COMMENT 'Harga lama',
  `new_price` bigint UNSIGNED NOT NULL COMMENT 'Harga baru',
  `reason` text COLLATE utf8mb4_unicode_ci COMMENT 'Alasan perubahan harga',
  `changed_by` bigint UNSIGNED NOT NULL COMMENT 'User ID yang mengubah',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit log perubahan harga standar jasa';

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_currency_id` bigint UNSIGNED NOT NULL,
  `default_currency_position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `thousand_separator` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `decimal_separator` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notification_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `footer_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint UNSIGNED NOT NULL,
  `productable_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `productable_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `ref_id` bigint UNSIGNED DEFAULT NULL,
  `ref_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'adjustment',
  `type` enum('in','out') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `out_key` varchar(255) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS ((case when (`type` = _utf8mb4'out') then concat(`productable_type`,_utf8mb4'#',`productable_id`) else NULL end)) STORED,
  `second_out_key` varchar(255) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS ((case when ((`type` = _utf8mb4'out') and (`productable_type` = _utf8mb4'Modules\\Product\\Entities\\ProductSecond')) then concat(`productable_type`,_utf8mb4'#',`productable_id`) else NULL end)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_opnames`
--

CREATE TABLE `stock_opnames` (
  `id` bigint UNSIGNED NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SO-YYYYMMDD-#####',
  `opname_date` date NOT NULL COMMENT 'Tanggal pelaksanaan',
  `status` enum('draft','in_progress','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `scope_type` enum('all','category','custom') COLLATE utf8mb4_unicode_ci DEFAULT 'all',
  `scope_ids` json DEFAULT NULL COMMENT 'Array ID kategori jika scope=category',
  `pic_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Petugas yang menghitung',
  `supervisor_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Supervisor yang approve',
  `approved_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `total_items` int UNSIGNED DEFAULT '0' COMMENT 'Jumlah item yang dihitung',
  `total_variance` int DEFAULT '0' COMMENT 'Total selisih (bisa +/-)',
  `variance_value` decimal(15,2) DEFAULT '0.00' COMMENT 'Nilai rupiah selisih',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_opname_items`
--

CREATE TABLE `stock_opname_items` (
  `id` bigint UNSIGNED NOT NULL,
  `stock_opname_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `system_qty` int NOT NULL DEFAULT '0' COMMENT 'Stok di sistem saat mulai hitung',
  `actual_qty` int DEFAULT NULL COMMENT 'Hasil hitung fisik (NULL = belum dihitung)',
  `variance_qty` int GENERATED ALWAYS AS ((`actual_qty` - `system_qty`)) STORED COMMENT 'Selisih',
  `variance_type` varchar(10) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS ((case when (`actual_qty` is null) then _utf8mb4'pending' when (`actual_qty` > `system_qty`) then _utf8mb4'surplus' when (`actual_qty` < `system_qty`) then _utf8mb4'shortage' else _utf8mb4'match' end)) STORED,
  `variance_reason` text COLLATE utf8mb4_unicode_ci COMMENT 'Alasan selisih (jika ada)',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `counted_at` timestamp NULL DEFAULT NULL COMMENT 'Waktu item ini dihitung',
  `counted_by` bigint UNSIGNED DEFAULT NULL,
  `adjustment_id` bigint UNSIGNED DEFAULT NULL COMMENT 'ID adjustment jika auto-generated',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `stock_opname_items`
--
DELIMITER $$
CREATE TRIGGER `trg_opname_items_after_delete` AFTER DELETE ON `stock_opname_items` FOR EACH ROW BEGIN
  UPDATE stock_opnames so
  SET 
    so.total_items = (
      SELECT COUNT(*) 
      FROM stock_opname_items 
      WHERE stock_opname_id = OLD.stock_opname_id 
        AND actual_qty IS NOT NULL
    ),
    so.total_variance = (
      SELECT IFNULL(SUM(variance_qty), 0)
      FROM stock_opname_items
      WHERE stock_opname_id = OLD.stock_opname_id
        AND actual_qty IS NOT NULL
    ),
    so.variance_value = (
      SELECT IFNULL(SUM(p.product_cost * soi.variance_qty), 0)
      FROM stock_opname_items soi
      JOIN products p ON p.id = soi.product_id
      WHERE soi.stock_opname_id = OLD.stock_opname_id
        AND soi.actual_qty IS NOT NULL
    )
  WHERE so.id = OLD.stock_opname_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_opname_items_after_insert` AFTER INSERT ON `stock_opname_items` FOR EACH ROW BEGIN
  UPDATE stock_opnames so
  SET 
    so.total_items = (
      SELECT COUNT(*) 
      FROM stock_opname_items 
      WHERE stock_opname_id = NEW.stock_opname_id 
        AND actual_qty IS NOT NULL
    ),
    so.total_variance = (
      SELECT IFNULL(SUM(variance_qty), 0)
      FROM stock_opname_items
      WHERE stock_opname_id = NEW.stock_opname_id
        AND actual_qty IS NOT NULL
    ),
    so.variance_value = (
      SELECT IFNULL(SUM(p.product_cost * soi.variance_qty), 0)
      FROM stock_opname_items soi
      JOIN products p ON p.id = soi.product_id
      WHERE soi.stock_opname_id = NEW.stock_opname_id
        AND soi.actual_qty IS NOT NULL
    )
  WHERE so.id = NEW.stock_opname_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_opname_items_after_update` AFTER UPDATE ON `stock_opname_items` FOR EACH ROW BEGIN
  UPDATE stock_opnames so
  SET 
    so.total_items = (
      SELECT COUNT(*) 
      FROM stock_opname_items 
      WHERE stock_opname_id = NEW.stock_opname_id 
        AND actual_qty IS NOT NULL
    ),
    so.total_variance = (
      SELECT IFNULL(SUM(variance_qty), 0)
      FROM stock_opname_items
      WHERE stock_opname_id = NEW.stock_opname_id
        AND actual_qty IS NOT NULL
    ),
    so.variance_value = (
      SELECT IFNULL(SUM(p.product_cost * soi.variance_qty), 0)
      FROM stock_opname_items soi
      JOIN products p ON p.id = soi.product_id
      WHERE soi.stock_opname_id = NEW.stock_opname_id
        AND soi.actual_qty IS NOT NULL
    )
  WHERE so.id = NEW.stock_opname_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `stock_opname_logs`
--

CREATE TABLE `stock_opname_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `stock_opname_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'created, started, item_counted, completed, approved, cancelled',
  `old_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint UNSIGNED NOT NULL,
  `supplier_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Indonesia',
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers_backup`
--

CREATE TABLE `suppliers_backup` (
  `id` bigint UNSIGNED NOT NULL DEFAULT '0',
  `supplier_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operator` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operation_value` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `id` bigint UNSIGNED NOT NULL,
  `folder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `supervisor_pin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'PIN 6 digit encrypted',
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `login_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_logs`
--

CREATE TABLE `user_activity_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'create_invoice, void_sale, override_price, dll',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` json DEFAULT NULL COMMENT 'data detail aktivitas',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_adjustment_summary`
-- (See below for the actual view)
--
CREATE TABLE `v_adjustment_summary` (
`adjustment_id` bigint unsigned
,`reference` varchar(255)
,`date` date
,`status` enum('pending','approved','rejected')
,`reason` enum('Rusak','Hilang','Kadaluarsa','Lainnya')
,`total_value` decimal(10,2)
,`requester_name` varchar(255)
,`approver_name` varchar(255)
,`total_products` bigint
,`total_items` decimal(32,0)
,`last_activity` varchar(24)
);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adjusted_products`
--
ALTER TABLE `adjusted_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `adjusted_products_adjustment_id_foreign` (`adjustment_id`),
  ADD KEY `adjusted_products_product_id_foreign` (`product_id`),
  ADD KEY `idx_adjustment_product` (`adjustment_id`,`product_id`),
  ADD KEY `idx_ap_deleted_at` (`deleted_at`),
  ADD KEY `idx_ap_adjustment_id` (`adjustment_id`),
  ADD KEY `idx_ap_product_id` (`product_id`);

--
-- Indexes for table `adjustments`
--
ALTER TABLE `adjustments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_adjustments_reference` (`reference`),
  ADD KEY `idx_adjustments_status` (`status`),
  ADD KEY `idx_adjustments_user` (`requester_id`,`created_at`),
  ADD KEY `idx_status_requester` (`status`,`requester_id`),
  ADD KEY `idx_adjustments_date_status` (`date`,`status`),
  ADD KEY `idx_adj_deleted_at` (`deleted_at`),
  ADD KEY `idx_adj_requester` (`requester_id`),
  ADD KEY `idx_adj_approver` (`approver_id`);

--
-- Indexes for table `adjustment_files`
--
ALTER TABLE `adjustment_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_adjfile_adj` (`adjustment_id`);

--
-- Indexes for table `adjustment_logs`
--
ALTER TABLE `adjustment_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_adj_id` (`adjustment_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `deleted_at` (`deleted_at`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brands_name_unique` (`name`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_category_code_unique` (`category_code`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_email_unique` (`customer_email`),
  ADD KEY `idx_deleted_at` (`deleted_at`),
  ADD KEY `idx_city` (`city`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_category_id_foreign` (`category_id`),
  ADD KEY `expenses_user_id_foreign` (`user_id`),
  ADD KEY `expenses_date_category_idx` (`date`,`category_id`),
  ADD KEY `expenses_date_method_bank_idx` (`date`,`payment_method`,`bank_name`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fontee_config`
--
ALTER TABLE `fontee_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_config_key` (`config_key`);

--
-- Indexes for table `manual_input_details`
--
ALTER TABLE `manual_input_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sale_id` (`sale_id`),
  ADD KEY `idx_cashier_id` (`cashier_id`),
  ADD KEY `idx_item_type` (`item_type`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `fk_mid_sale_detail` (`sale_detail_id`);

--
-- Indexes for table `manual_input_logs`
--
ALTER TABLE `manual_input_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mil_supervisor` (`supervisor_id`),
  ADD KEY `idx_sale_id` (`sale_id`),
  ADD KEY `idx_cashier_id` (`cashier_id`),
  ADD KEY `idx_approval_status` (`approval_status`),
  ADD KEY `idx_owner_notified` (`owner_notified`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_milog_sale` (`sale_id`),
  ADD KEY `idx_milog_notified` (`owner_notified`),
  ADD KEY `idx_milog_owner_notif_id` (`owner_notification_id`),
  ADD KEY `idx_milog_approval` (`approval_status`),
  ADD KEY `fk_milog_sale_detail` (`sale_detail_id`);

--
-- Indexes for table `manual_input_summary_daily`
--
ALTER TABLE `manual_input_summary_daily`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_misd_date_cashier` (`date`,`cashier_id`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_cashier_id` (`cashier_id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_uuid_unique` (`uuid`),
  ADD KEY `media_model_type_model_id_index` (`model_type`,`model_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `owner_notifications`
--
ALTER TABLE `owner_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_on_sale` (`sale_id`),
  ADD KEY `idx_user_unread` (`user_id`,`is_read`),
  ADD KEY `idx_user_reviewed` (`user_id`,`is_reviewed`),
  ADD KEY `idx_severity` (`severity`),
  ADD KEY `idx_notification_type` (`notification_type`),
  ADD KEY `idx_fontee_status` (`fontee_status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_products_code` (`product_code`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_brand_id_foreign` (`brand_id`),
  ADD KEY `idx_products_year` (`product_year`);
ALTER TABLE `products` ADD FULLTEXT KEY `ft_products_search` (`product_name`,`product_code`,`product_size`,`ring`);

--
-- Indexes for table `product_seconds`
--
ALTER TABLE `product_seconds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ps_unique_code` (`unique_code`),
  ADD KEY `product_seconds_category_id_foreign` (`category_id`),
  ADD KEY `product_seconds_brand_id_foreign` (`brand_id`),
  ADD KEY `idx_product_seconds_status` (`status`),
  ADD KEY `idx_product_seconds_year` (`product_year`);
ALTER TABLE `product_seconds` ADD FULLTEXT KEY `ft_product_seconds_search` (`name`,`unique_code`,`size`,`ring`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchases_reference_unique` (`reference`),
  ADD KEY `purchases_supplier_id_foreign` (`supplier_id`),
  ADD KEY `idx_purchases_date` (`date`),
  ADD KEY `purchases_user_id_foreign` (`user_id`),
  ADD KEY `idx_purchases_date_payment` (`date`,`payment_status`),
  ADD KEY `idx_purchases_supplier` (`supplier_id`,`date`);

--
-- Indexes for table `purchase_details`
--
ALTER TABLE `purchase_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_details_purchase_id_foreign` (`purchase_id`),
  ADD KEY `purchase_details_product_id_foreign` (`product_id`);

--
-- Indexes for table `purchase_payments`
--
ALTER TABLE `purchase_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_payments_purchase_id_foreign` (`purchase_id`);

--
-- Indexes for table `purchase_seconds`
--
ALTER TABLE `purchase_seconds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_seconds_reference_unique` (`reference`),
  ADD KEY `purchase_seconds_date_index` (`date`),
  ADD KEY `purchase_seconds_status_index` (`status`),
  ADD KEY `purchase_seconds_payment_status_index` (`payment_status`),
  ADD KEY `purchase_seconds_user_id_foreign` (`user_id`);

--
-- Indexes for table `purchase_second_details`
--
ALTER TABLE `purchase_second_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_second_details_purchase_second_id_foreign` (`purchase_second_id`),
  ADD KEY `purchase_second_details_product_second_id_foreign` (`product_second_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_sales_reference` (`reference`),
  ADD KEY `sales_user_id_foreign` (`user_id`),
  ADD KEY `idx_sales_date` (`date`),
  ADD KEY `idx_sales_status` (`status`),
  ADD KEY `idx_sales_payment_status` (`payment_status`),
  ADD KEY `sales_date_user_status_payment_idx` (`date`,`user_id`,`status`,`payment_status`),
  ADD KEY `sales_user_date_index` (`user_id`,`date`),
  ADD KEY `idx_sales_has_adjustment` (`has_price_adjustment`,`date`),
  ADD KEY `idx_has_manual_input` (`has_manual_input`),
  ADD KEY `idx_is_notified` (`is_manual_input_notified`),
  ADD KEY `idx_created_user` (`created_at`,`user_id`),
  ADD KEY `idx_customer_id` (`customer_id`);

--
-- Indexes for table `sale_details`
--
ALTER TABLE `sale_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_details_sale_id_foreign` (`sale_id`),
  ADD KEY `sale_details_product_id_foreign` (`product_id`),
  ADD KEY `idx_sale_details_sale_created` (`sale_id`,`created_at`),
  ADD KEY `idx_sale_details_productable` (`productable_type`,`productable_id`),
  ADD KEY `sale_details_sale_product_index` (`sale_id`,`product_id`),
  ADD KEY `idx_sd_source_type` (`source_type`),
  ADD KEY `idx_sd_manual_kind` (`manual_kind`),
  ADD KEY `idx_sale_details_adjusted` (`is_price_adjusted`,`sale_id`),
  ADD KEY `idx_sale_details_adjuster` (`adjusted_by`),
  ADD KEY `idx_sale_details_source` (`source_type`,`product_id`,`productable_id`);

--
-- Indexes for table `sale_payments`
--
ALTER TABLE `sale_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sale_payments_reference_unique` (`reference`),
  ADD KEY `sale_payments_sale_id_foreign` (`sale_id`),
  ADD KEY `idx_sale_payments_date` (`date`),
  ADD KEY `idx_sale_payments_method` (`payment_method`),
  ADD KEY `sale_payments_date_method_bank_idx` (`date`,`payment_method`,`bank_name`),
  ADD KEY `sale_payments_sale_date_index` (`sale_id`,`date`);

--
-- Indexes for table `service_masters`
--
ALTER TABLE `service_masters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_name` (`service_name`),
  ADD KEY `idx_status_category` (`status`,`category`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `fk_service_masters_created_by` (`created_by`);

--
-- Indexes for table `service_master_audits`
--
ALTER TABLE `service_master_audits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_master_id` (`service_master_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `settings_default_currency_id_foreign` (`default_currency_id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_stock_movements_second_out_once` (`second_out_key`),
  ADD KEY `stock_movements_productable_type_productable_id_index` (`productable_type`,`productable_id`),
  ADD KEY `stock_movements_user_id_foreign` (`user_id`),
  ADD KEY `idx_ref_type` (`ref_type`,`ref_id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `deleted_at` (`deleted_at`);

--
-- Indexes for table `stock_opnames`
--
ALTER TABLE `stock_opnames`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `idx_opname_date` (`opname_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `pic_id` (`pic_id`),
  ADD KEY `supervisor_id` (`supervisor_id`);

--
-- Indexes for table `stock_opname_items`
--
ALTER TABLE `stock_opname_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_opname_product` (`stock_opname_id`,`product_id`),
  ADD KEY `idx_variance_type` (`variance_type`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `counted_by` (`counted_by`),
  ADD KEY `adjustment_id` (`adjustment_id`);

--
-- Indexes for table `stock_opname_logs`
--
ALTER TABLE `stock_opname_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_opname_id` (`stock_opname_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_supplier_name` (`supplier_name`),
  ADD KEY `idx_supplier_email` (`supplier_email`),
  ADD KEY `idx_city` (`city`),
  ADD KEY `idx_deleted_at` (`deleted_at`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_date` (`user_id`,`created_at`),
  ADD KEY `idx_action` (`action`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adjusted_products`
--
ALTER TABLE `adjusted_products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `adjustments`
--
ALTER TABLE `adjustments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `adjustment_files`
--
ALTER TABLE `adjustment_files`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `adjustment_logs`
--
ALTER TABLE `adjustment_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fontee_config`
--
ALTER TABLE `fontee_config`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `manual_input_details`
--
ALTER TABLE `manual_input_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `manual_input_logs`
--
ALTER TABLE `manual_input_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `manual_input_summary_daily`
--
ALTER TABLE `manual_input_summary_daily`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `owner_notifications`
--
ALTER TABLE `owner_notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_seconds`
--
ALTER TABLE `product_seconds`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_details`
--
ALTER TABLE `purchase_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_payments`
--
ALTER TABLE `purchase_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_seconds`
--
ALTER TABLE `purchase_seconds`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_second_details`
--
ALTER TABLE `purchase_second_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_details`
--
ALTER TABLE `sale_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_payments`
--
ALTER TABLE `sale_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_masters`
--
ALTER TABLE `service_masters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_master_audits`
--
ALTER TABLE `service_master_audits`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_opnames`
--
ALTER TABLE `stock_opnames`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_opname_items`
--
ALTER TABLE `stock_opname_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_opname_logs`
--
ALTER TABLE `stock_opname_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Structure for view `categories_view`
--
DROP TABLE IF EXISTS `categories_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `categories_view`  AS SELECT `c`.`id` AS `id`, `c`.`category_code` AS `category_code`, `c`.`category_name` AS `name`, `c`.`created_at` AS `created_at`, `c`.`updated_at` AS `updated_at` FROM `categories` AS `c` ;

-- --------------------------------------------------------

--
-- Structure for view `v_adjustment_summary`
--
DROP TABLE IF EXISTS `v_adjustment_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_adjustment_summary`  AS SELECT `a`.`id` AS `adjustment_id`, `a`.`reference` AS `reference`, `a`.`date` AS `date`, `a`.`status` AS `status`, `a`.`reason` AS `reason`, `a`.`total_value` AS `total_value`, `req`.`name` AS `requester_name`, `appr`.`name` AS `approver_name`, count(distinct `ap`.`product_id`) AS `total_products`, coalesce(sum(`ap`.`quantity`),0) AS `total_items`, date_format(max(`al`.`created_at`),'%Y-%m-%d %H:%i:%s') AS `last_activity` FROM ((((`adjustments` `a` left join `adjusted_products` `ap` on(((`ap`.`adjustment_id` = `a`.`id`) and (`ap`.`deleted_at` is null)))) left join `adjustment_logs` `al` on(((`al`.`adjustment_id` = `a`.`id`) and (`al`.`deleted_at` is null)))) left join `users` `req` on((`req`.`id` = `a`.`requester_id`))) left join `users` `appr` on((`appr`.`id` = `a`.`approver_id`))) GROUP BY `a`.`id` ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adjusted_products`
--
ALTER TABLE `adjusted_products`
  ADD CONSTRAINT `fk_ap_adjustment` FOREIGN KEY (`adjustment_id`) REFERENCES `adjustments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ap_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `adjustments`
--
ALTER TABLE `adjustments`
  ADD CONSTRAINT `adjustments_ibfk_1` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `adjustments_ibfk_2` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_adj_appr` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_adj_req` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `adjustment_files`
--
ALTER TABLE `adjustment_files`
  ADD CONSTRAINT `adjustment_files_ibfk_1` FOREIGN KEY (`adjustment_id`) REFERENCES `adjustments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_adjfile_adj` FOREIGN KEY (`adjustment_id`) REFERENCES `adjustments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `adjustment_logs`
--
ALTER TABLE `adjustment_logs`
  ADD CONSTRAINT `fk_adjl_adjustment` FOREIGN KEY (`adjustment_id`) REFERENCES `adjustments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_adjl_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_adjlog_adj` FOREIGN KEY (`adjustment_id`) REFERENCES `adjustments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_adjlog_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_exp_cat` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_exp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `manual_input_details`
--
ALTER TABLE `manual_input_details`
  ADD CONSTRAINT `fk_mid_cashier` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mid_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mid_sale_detail` FOREIGN KEY (`sale_detail_id`) REFERENCES `sale_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `manual_input_logs`
--
ALTER TABLE `manual_input_logs`
  ADD CONSTRAINT `fk_mil_cashier` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_mil_notification` FOREIGN KEY (`owner_notification_id`) REFERENCES `owner_notifications` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_mil_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mil_sale_detail` FOREIGN KEY (`sale_detail_id`) REFERENCES `sale_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mil_supervisor` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_milog_sale_detail` FOREIGN KEY (`sale_detail_id`) REFERENCES `sale_details` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `manual_input_summary_daily`
--
ALTER TABLE `manual_input_summary_daily`
  ADD CONSTRAINT `fk_misd_cashier` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `owner_notifications`
--
ALTER TABLE `owner_notifications`
  ADD CONSTRAINT `fk_on_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_on_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_prod_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_prod_cat` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `product_seconds`
--
ALTER TABLE `product_seconds`
  ADD CONSTRAINT `fk_ps_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ps_cat` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `product_seconds_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `product_seconds_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchases_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `purchase_details`
--
ALTER TABLE `purchase_details`
  ADD CONSTRAINT `fk_pdet_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pdet_pur` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_details_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_payments`
--
ALTER TABLE `purchase_payments`
  ADD CONSTRAINT `purchase_payments_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_second_details`
--
ALTER TABLE `purchase_second_details`
  ADD CONSTRAINT `purchase_second_details_product_second_id_foreign` FOREIGN KEY (`product_second_id`) REFERENCES `product_seconds` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `purchase_second_details_purchase_second_id_foreign` FOREIGN KEY (`purchase_second_id`) REFERENCES `purchase_seconds` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `fk_sales_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sales_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sale_details`
--
ALTER TABLE `sale_details`
  ADD CONSTRAINT `fk_sd_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_sd_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `fk_sdet_adjuster` FOREIGN KEY (`adjusted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sdet_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sdet_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sale_details_adjusted_by_foreign` FOREIGN KEY (`adjusted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sale_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sale_details_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_payments`
--
ALTER TABLE `sale_payments`
  ADD CONSTRAINT `fk_sp_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_spay_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `service_masters`
--
ALTER TABLE `service_masters`
  ADD CONSTRAINT `fk_service_masters_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `service_masters_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `service_master_audits`
--
ALTER TABLE `service_master_audits`
  ADD CONSTRAINT `service_master_audits_ibfk_1` FOREIGN KEY (`service_master_id`) REFERENCES `service_masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_master_audits_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `settings`
--
ALTER TABLE `settings`
  ADD CONSTRAINT `settings_default_currency_id_foreign` FOREIGN KEY (`default_currency_id`) REFERENCES `currencies` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_opnames`
--
ALTER TABLE `stock_opnames`
  ADD CONSTRAINT `stock_opnames_ibfk_1` FOREIGN KEY (`pic_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_opnames_ibfk_2` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_opname_items`
--
ALTER TABLE `stock_opname_items`
  ADD CONSTRAINT `stock_opname_items_ibfk_1` FOREIGN KEY (`stock_opname_id`) REFERENCES `stock_opnames` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_opname_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `stock_opname_items_ibfk_3` FOREIGN KEY (`counted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_opname_items_ibfk_4` FOREIGN KEY (`adjustment_id`) REFERENCES `adjustments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_opname_logs`
--
ALTER TABLE `stock_opname_logs`
  ADD CONSTRAINT `stock_opname_logs_ibfk_1` FOREIGN KEY (`stock_opname_id`) REFERENCES `stock_opnames` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_opname_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  ADD CONSTRAINT `fk_user_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
