-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 05, 2025 at 05:04 AM
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

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `drop_fk_if_exists` (IN `p_table` VARCHAR(64), IN `p_fk` VARCHAR(64))   BEGIN
  IF EXISTS (
    SELECT 1 FROM information_schema.table_constraints
    WHERE table_schema = DATABASE()
      AND table_name = p_table
      AND constraint_type = 'FOREIGN KEY'
      AND constraint_name = p_fk
  ) THEN
    SET @sql = CONCAT('ALTER TABLE `', p_table, '` DROP FOREIGN KEY `', p_fk, '`');
    PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
  END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `drop_index_if_exists` (IN `p_table` VARCHAR(64), IN `p_index` VARCHAR(64))   BEGIN
  IF EXISTS (
    SELECT 1 FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name = p_table
      AND index_name = p_index
  ) THEN
    SET @sql = CONCAT('DROP INDEX `', p_index, '` ON `', p_table, '`');
    PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
  END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `drop_primary_key_if_exists` (IN `p_table` VARCHAR(64))   BEGIN
  IF EXISTS (
    SELECT 1 FROM information_schema.table_constraints
    WHERE table_schema = DATABASE()
      AND table_name = p_table
      AND constraint_type = 'PRIMARY KEY'
  ) THEN
    SET @sql = CONCAT('ALTER TABLE `', p_table, '` DROP PRIMARY KEY');
    PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
  END IF;
END$$

DELIMITER ;

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
-- Dumping data for table `adjusted_products`
--

INSERT INTO `adjusted_products` (`id`, `adjustment_id`, `product_id`, `quantity`, `type`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, 5, 'add', '2025-11-17 03:27:04', '2025-11-17 03:27:04', NULL),
(2, 2, 2, 5, 'add', '2025-11-17 03:32:55', '2025-11-17 03:32:55', NULL);

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

--
-- Dumping data for table `adjustments`
--

INSERT INTO `adjustments` (`id`, `date`, `reference`, `note`, `status`, `requester_id`, `approver_id`, `reason`, `description`, `approval_notes`, `approval_date`, `total_value`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '2025-11-12', 'ADJ-20251117-00001', NULL, 'approved', 1, 1, 'Rusak', 'Testing barang', 'Diterima', '2025-11-17 03:27:30', 3625000.00, '2025-11-17 03:27:04', '2025-11-17 03:27:30', NULL),
(2, '2025-11-17', 'ADJ-20251117-00002', NULL, 'approved', 1, 1, 'Hilang', 'Testing barang', NULL, '2025-11-17 03:33:05', 3625000.00, '2025-11-17 03:32:55', '2025-11-17 03:33:05', NULL);

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

--
-- Dumping data for table `adjustment_files`
--

INSERT INTO `adjustment_files` (`id`, `adjustment_id`, `file_path`, `file_name`, `file_size`, `mime_type`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'adjustment_files/OO9sAoIPCSxSFg35CGlObjkr99wrnJ1zrfjDd8Fi.png', 'MM5ZoC53d3tIdCInJcYeDzIIDQOFYB7DjGNUm9GV.png', 1779177, 'image/png', '2025-11-17 03:27:04', '2025-11-17 03:27:04', NULL),
(2, 2, 'adjustment_files/eaM4RraHFipu3sMgHkWaUh63nWW9gcCZsc07qu5n.png', 'MM5ZoC53d3tIdCInJcYeDzIIDQOFYB7DjGNUm9GV.png', 1779177, 'image/png', '2025-11-17 03:32:55', '2025-11-17 03:32:55', NULL);

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

--
-- Dumping data for table `adjustment_logs`
--

INSERT INTO `adjustment_logs` (`id`, `adjustment_id`, `user_id`, `action`, `old_status`, `new_status`, `notes`, `locked`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'created', NULL, 'pending', 'Pengajuan baru', 1, '2025-11-17 03:27:04', '2025-11-17 04:27:04', NULL),
(2, 1, 1, 'approved', 'pending', 'approved', 'Diterima', 1, '2025-11-17 03:27:30', '2025-11-17 04:27:30', NULL),
(3, 2, 1, 'created', NULL, 'pending', 'Pengajuan baru', 1, '2025-11-17 03:32:55', '2025-11-17 04:32:55', NULL),
(4, 2, 1, 'approved', 'pending', 'approved', 'Approved', 1, '2025-11-17 03:33:05', '2025-11-17 04:33:05', NULL);

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

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'GT Radial', '2025-08-06 01:21:23', '2025-08-06 01:21:23'),
(2, 'Bridgestone', '2025-08-17 05:04:07', '2025-08-17 05:04:07'),
(3, 'Dunlop', '2025-08-17 05:04:07', '2025-08-17 05:04:07'),
(4, 'HSR', '2025-08-17 05:04:07', '2025-08-17 05:04:07'),
(5, 'OEM', '2025-08-17 05:04:07', '2025-08-17 05:04:07');

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

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_code`, `category_name`, `created_at`, `updated_at`) VALUES
(2, 'BAN', 'Ban Mobil', '2025-08-06 01:22:15', '2025-08-06 01:22:15'),
(3, 'VELG', 'Velg Mobil', '2025-08-06 12:13:58', '2025-08-06 12:13:58');

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

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `currency_name`, `code`, `symbol`, `thousand_separator`, `decimal_separator`, `exchange_rate`, `created_at`, `updated_at`) VALUES
(1, 'Rupiah', 'IDR', 'Rp', '.', ',', NULL, '2025-08-05 14:46:12', '2025-09-08 08:30:37');

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

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_name`, `customer_email`, `customer_phone`, `city`, `country`, `address`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Peter Vincent', 'peter@gmail.com', '082227863969', 'Semarang', 'Indonesia', 'Jalan Alvita Indah Timur', '2025-11-11 13:57:10', '2025-11-11 13:57:10', NULL),
(2, 'Peter', '', '', '-', 'Indonesia', '-', '2025-11-12 06:38:22', '2025-11-12 06:38:22', NULL),
(3, 'Peter', 'guest_1762933601@temp.com', '-', '-', 'Indonesia', '-', '2025-11-12 06:46:41', '2025-11-12 06:46:41', NULL);

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

--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `category_name`, `category_description`, `created_at`, `updated_at`) VALUES
(1, 'Bensin', NULL, '2025-08-23 06:16:46', '2025-08-23 06:16:46');

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

--
-- Dumping data for table `fontee_config`
--

INSERT INTO `fontee_config` (`id`, `config_key`, `config_value`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'fontee_api_key', 'YOUR_FONTEE_API_KEY_HERE', 'API Key dari Fontee Dashboard', 0, '2025-11-05 08:41:27', '2025-11-05 08:41:27'),
(2, 'fontee_channel_id', 'YOUR_CHANNEL_ID', 'Channel ID untuk WhatsApp/SMS', 0, '2025-11-05 08:41:27', '2025-11-05 08:41:27'),
(3, 'fontee_webhook_secret', 'YOUR_WEBHOOK_SECRET', 'Secret key untuk webhook validation', 0, '2025-11-05 08:41:27', '2025-11-05 08:41:27'),
(4, 'fontee_enabled', '1', 'Enable/disable Fontee integration', 1, '2025-11-05 08:41:27', '2025-11-05 08:41:27'),
(5, 'fontee_notification_type', 'whatsapp', 'Tipe notifikasi: whatsapp, sms, or both', 1, '2025-11-05 08:41:27', '2025-11-05 08:41:27'),
(6, 'fontee_retry_attempts', '3', 'Jumlah retry kalau gagal', 1, '2025-11-05 08:41:27', '2025-11-05 08:41:27'),
(7, 'fontee_retry_delay_seconds', '60', 'Delay antar retry (detik)', 1, '2025-11-05 08:41:27', '2025-11-05 08:41:27');

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
-- Dumping data for table `manual_input_details`
--

INSERT INTO `manual_input_details` (`id`, `sale_id`, `sale_detail_id`, `cashier_id`, `item_type`, `item_name`, `quantity`, `price`, `manual_reason`, `cost_price`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 5, 'service', 'Balancing', 1, 20000, 'Velg besar', 0, '2025-11-06 19:10:20', '2025-11-06 19:10:20'),
(3, 4, 5, 5, 'service', 'Jasa Pasang Ban', 1, 30000, 'Pemasangan', 0, '2025-11-06 19:40:06', '2025-11-06 19:40:06'),
(4, 4, 6, 5, 'goods', 'Nitrogen', 1, 8000, 'Isi ulang', 0, '2025-11-06 19:40:08', '2025-11-06 19:40:08'),
(5, 6, 7, 5, 'service', 'Ngetestt', 1, 22222, 'TWESTTTTTINFGGGGG', 0, '2025-11-07 06:52:09', '2025-11-07 06:52:09'),
(6, 7, 8, 1, 'service', 'Spooring Ban', 1, 150000, NULL, 0, '2025-11-12 06:46:41', '2025-11-12 06:46:41');

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

--
-- Dumping data for table `manual_input_logs`
--

INSERT INTO `manual_input_logs` (`id`, `sale_id`, `sale_detail_id`, `cashier_id`, `input_type`, `item_name`, `quantity`, `standard_price`, `input_price`, `price_variance`, `variance_percent`, `reason_provided`, `supervisor_pin_required`, `supervisor_id`, `approval_status`, `approval_notes`, `owner_notified`, `owner_notification_id`, `owner_notified_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 5, 'price_override', 'EP150 185/65 R15', 1, 925000, 900000, -25000, -2.7, 'Diskon loyal pelanggan', 1, 4, 'approved', 'OK by supervisor', 0, NULL, NULL, '2025-11-06 19:05:10', '2025-11-06 19:05:10'),
(2, 2, 2, 5, 'manual_service', 'Balancing', 1, NULL, 20000, NULL, NULL, 'Input manual awal', 0, NULL, 'pending', NULL, 0, NULL, NULL, '2025-11-06 19:10:25', '2025-11-06 19:10:25'),
(3, 2, 2, 5, 'price_override', 'Balancing', 1, 20000, 25000, 5000, 25, 'SUV balancing (beban kerja lebih)', 1, 4, 'approved', 'Disetujui atasan', 1, 2, '2025-11-06 19:20:20', '2025-11-06 19:18:05', '2025-11-06 19:20:20'),
(5, 4, 5, 5, 'manual_service', 'Jasa Pasang Ban', 1, NULL, 30000, NULL, NULL, 'Pemasangan', 0, NULL, 'pending', NULL, 0, NULL, NULL, '2025-11-06 19:40:06', '2025-11-06 19:40:06'),
(6, 4, 6, 5, 'manual_item', 'Nitrogen', 1, NULL, 8000, NULL, NULL, 'Isi ulang', 0, NULL, 'pending', NULL, 0, NULL, NULL, '2025-11-06 19:40:08', '2025-11-06 19:40:08'),
(7, 6, 7, 5, 'manual_item', 'Ngetestt', 1, NULL, 22222, NULL, NULL, 'TWESTTTTTINFGGGGG', 0, NULL, 'pending', NULL, 1, 7, '2025-11-07 06:52:09', '2025-11-07 06:52:09', '2025-11-07 06:52:09'),
(8, 7, 8, 1, 'manual_item', 'Spooring Ban', 1, NULL, 150000, NULL, NULL, 'No reason provided', 0, NULL, 'pending', NULL, 1, 11, '2025-11-12 06:46:41', '2025-11-12 06:46:41', '2025-11-12 06:46:41');

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

--
-- Dumping data for table `manual_input_summary_daily`
--

INSERT INTO `manual_input_summary_daily` (`id`, `date`, `cashier_id`, `total_transactions`, `manual_input_count`, `total_manual_items`, `total_manual_value`, `pending_approvals`, `top_manual_items`, `created_at`, `updated_at`) VALUES
(1, '2025-11-07', 5, 5, 4, 4, 80222, 0, NULL, '2025-11-07 07:36:15', '2025-11-07 07:52:09'),
(12, '2025-11-12', 1, 1, 1, 1, 150000, 0, NULL, '2025-11-12 07:46:41', '2025-11-12 07:46:41'),
(14, '2025-11-16', 1, 1, 0, 0, 0, 0, NULL, '2025-11-15 18:09:00', '2025-11-15 18:09:00');

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

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `model_type`, `model_id`, `uuid`, `collection_name`, `name`, `file_name`, `mime_type`, `disk`, `conversions_disk`, `size`, `manipulations`, `custom_properties`, `generated_conversions`, `responsive_images`, `order_column`, `created_at`, `updated_at`) VALUES
(5, 'Modules\\Product\\Entities\\Product', 2, '282c7069-71b5-498f-9ddd-d5309597734d', 'images', '1761474762', '1761474762.jpg', 'image/jpeg', 'public', 'public', 62080, '[]', '[]', '{\"large\": true, \"thumb\": true, \"preview\": true, \"pos-grid\": true}', '[]', 1, '2025-10-26 09:32:43', '2025-10-26 11:00:57'),
(6, 'Modules\\Product\\Entities\\Product', 1, '47232eb1-7f2f-494a-9e8e-8386757e9c4e', 'images', '1761480536', '1761480536.jpg', 'image/jpeg', 'public', 'public', 64193, '[]', '[]', '{\"large\": true, \"thumb\": true, \"preview\": true, \"pos-grid\": true}', '[]', 1, '2025-10-26 11:08:57', '2025-10-26 11:08:59');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2021_07_14_145038_create_categories_table', 1),
(5, '2021_07_14_145047_create_products_table', 1),
(6, '2021_07_15_211319_create_media_table', 1),
(7, '2021_07_16_010005_create_uploads_table', 1),
(8, '2021_07_16_220524_create_permission_tables', 1),
(9, '2021_07_22_003941_create_adjustments_table', 1),
(10, '2021_07_22_004043_create_adjusted_products_table', 1),
(11, '2021_07_28_192608_create_expense_categories_table', 1),
(12, '2021_07_28_192616_create_expenses_table', 1),
(13, '2021_07_29_165419_create_customers_table', 1),
(14, '2021_07_29_165440_create_suppliers_table', 1),
(15, '2021_07_31_015923_create_currencies_table', 1),
(16, '2021_07_31_140531_create_settings_table', 1),
(17, '2021_07_31_201003_create_sales_table', 1),
(18, '2021_07_31_212446_create_sale_details_table', 1),
(19, '2021_08_07_192203_create_sale_payments_table', 1),
(20, '2021_08_08_021108_create_purchases_table', 1),
(21, '2021_08_08_021131_create_purchase_payments_table', 1),
(22, '2021_08_08_021713_create_purchase_details_table', 1),
(23, '2021_08_08_175345_create_sale_returns_table', 1),
(24, '2021_08_08_175358_create_sale_return_details_table', 1),
(25, '2021_08_08_175406_create_sale_return_payments_table', 1),
(26, '2021_08_08_222603_create_purchase_returns_table', 1),
(27, '2021_08_08_222612_create_purchase_return_details_table', 1),
(28, '2021_08_08_222646_create_purchase_return_payments_table', 1),
(29, '2021_08_16_015031_create_quotations_table', 1),
(30, '2021_08_16_155013_create_quotation_details_table', 1),
(31, '2023_07_01_184221_create_units_table', 1),
(32, '2025_08_05_203617_create_products_second_table', 1),
(33, '2025_08_05_203701_add_new_columns_to_sale_details_table', 1),
(34, '2025_08_05_203908_create_stock_movements_table', 1),
(35, '2025_08_05_230827_create_brands_table', 2),
(36, '2025_08_05_230858_add_brand_id_to_products_table', 2),
(37, '2025_08_06_085644_add_product_category_id_to_products_table', 3),
(38, '2025_08_07_101131_add_size_columns_to_products_table', 4),
(39, '2025_08_07_111015_add_size_and_year_to_products_table', 5),
(40, '2025_08_07_122651_add_ring_and_initial_stock_to_products_table', 6),
(41, '2025_08_07_173024_remove_barcode_symbology_from_products_table', 7),
(42, '2025_08_07_174120_add_category_and_brand_to_product_seconds_table', 8),
(43, '2025_08_07_183146_add_specs_to_product_seconds_table', 9),
(44, '2025_08_07_184344_remove_photo_path_from_product_seconds_table', 10),
(45, '2025_08_08_125854_AddBankNameToSalesTable', 11),
(46, '2025_08_08_133851_RemoveCustomerIdFromSalesTable', 12),
(47, '2025_08_08_150335_remove_customer_name_from_sales_table', 13),
(48, '2025_08_08_151424_add_hpp_and_profit_to_sales_table', 14),
(49, '2025_08_09_213452_alter_money_columns_to_bigint_on_sales_tables', 15),
(51, '2025_08_10_121940_add_user_id_to_sales_table', 16),
(52, '2025_08_14_202349_alter_sale_payments_amount_to_decimal', 17),
(53, '2025_08_14_210716_normalize_money_to_bigint', 18),
(54, '2025_08_19_150401_add_bank_name_to_sale_payments_table', 19),
(55, '2025_08_23_115755_add_operational_fields_to_expenses_table', 20),
(56, '2025_09_08_133151_normalize_money_to_idr', 21),
(57, '2025_09_08_150741_align_settings_currency_to_idr', 22),
(58, '2025_10_21_134216_add_midtrans_columns_to_sales_table', 23);

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

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(3, 'App\\Models\\User', 1),
(4, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 4),
(5, 'App\\Models\\User', 5),
(5, 'App\\Models\\User', 6);

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

--
-- Dumping data for table `owner_notifications`
--

INSERT INTO `owner_notifications` (`id`, `user_id`, `sale_id`, `notification_type`, `title`, `message`, `data`, `severity`, `is_read`, `is_reviewed`, `read_at`, `reviewed_at`, `reviewed_by`, `review_notes`, `fontee_message_id`, `fontee_status`, `fontee_sent_at`, `fontee_error_message`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'price_adjustment', '✅ Penyesuaian Harga - Inv OB2-00001', 'Harga EP150 disesuaikan dari Rp 925.000 menjadi Rp 900.000 (-2.70%).', '{\"items\": [{\"qty\": 1, \"name\": \"EP150 185/65 R15\", \"price\": 900000}], \"cashier_id\": 5, \"invoice_no\": \"OB2-00001\", \"cashier_name\": \"Ani (Kasir 1)\"}', 'info', 1, 0, '2025-11-08 06:09:39', NULL, NULL, NULL, NULL, 'sent', NULL, NULL, '2025-11-06 19:06:00', '2025-11-08 06:09:39', NULL),
(2, 1, 2, 'manual_input_alert', '⚠️ Input Manual Disesuaikan - Inv OB2-00002', 'Balancing naik dari Rp 20.000 ➜ Rp 25.000 (approved).', '{\"items\": [{\"name\": \"Balancing\", \"type\": \"service\", \"price\": 25000, \"reason\": \"Velg besar\", \"quantity\": 1}], \"cashier_id\": 5, \"invoice_no\": \"OB2-00002\", \"items_count\": 1, \"cashier_name\": \"Ani (Kasir 1)\", \"total_amount\": 25000}', 'info', 0, 0, NULL, NULL, NULL, NULL, NULL, 'sent', NULL, NULL, '2025-11-06 19:20:25', '2025-11-06 19:20:25', NULL),
(3, 1, 4, 'manual_input_alert', '⚠️ Input Manual (2 item) - Inv OB2-00004', 'Transaksi memuat 2 item manual: Jasa Pasang Ban (30.000), Nitrogen (8.000).', '{\"items\": [{\"name\": \"Jasa Pasang Ban\", \"type\": \"service\", \"price\": 30000, \"quantity\": 1}, {\"name\": \"Nitrogen\", \"type\": \"goods\", \"price\": 8000, \"quantity\": 1}], \"cashier_id\": 5, \"invoice_no\": \"OB2-00004\", \"items_count\": 2, \"cashier_name\": \"Ani (Kasir 1)\", \"total_amount\": 38000}', 'warning', 0, 0, NULL, NULL, NULL, NULL, NULL, 'sent', NULL, NULL, '2025-11-06 19:40:12', '2025-11-06 19:40:12', NULL),
(4, 1, 6, 'manual_input_alert', '⚠️ Input Manual - Inv OB2-00005', 'Kasir Ani (Kasir 1) membuat transaksi dengan 1 item input manual:\n\nNgetestt (1x @ Rp 22.222)\n\nTotal: Rp 22.222\nInvoice: OB2-00005\nWaktu: 07-11-2025 13:52:09', '{\"items\": [{\"name\": \"Ngetestt\", \"type\": \"service\", \"price\": 22222, \"reason\": \"TWESTTTTTINFGGGGG\", \"quantity\": 1}], \"cashier_id\": 5, \"invoice_no\": \"OB2-00005\", \"items_count\": 1, \"cashier_name\": \"Ani (Kasir 1)\", \"total_amount\": 22222}', 'info', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-07 06:52:09', '2025-11-07 06:52:09', NULL),
(5, 2, 6, 'manual_input_alert', '⚠️ Input Manual - Inv OB2-00005', 'Kasir Ani (Kasir 1) membuat transaksi dengan 1 item input manual:\n\nNgetestt (1x @ Rp 22.222)\n\nTotal: Rp 22.222\nInvoice: OB2-00005\nWaktu: 07-11-2025 13:52:09', '{\"items\": [{\"name\": \"Ngetestt\", \"type\": \"service\", \"price\": 22222, \"reason\": \"TWESTTTTTINFGGGGG\", \"quantity\": 1}], \"cashier_id\": 5, \"invoice_no\": \"OB2-00005\", \"items_count\": 1, \"cashier_name\": \"Ani (Kasir 1)\", \"total_amount\": 22222}', 'info', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-07 06:52:09', '2025-11-07 06:52:09', NULL),
(6, 3, 6, 'manual_input_alert', '⚠️ Input Manual - Inv OB2-00005', 'Kasir Ani (Kasir 1) membuat transaksi dengan 1 item input manual:\n\nNgetestt (1x @ Rp 22.222)\n\nTotal: Rp 22.222\nInvoice: OB2-00005\nWaktu: 07-11-2025 13:52:09', '{\"items\": [{\"name\": \"Ngetestt\", \"type\": \"service\", \"price\": 22222, \"reason\": \"TWESTTTTTINFGGGGG\", \"quantity\": 1}], \"cashier_id\": 5, \"invoice_no\": \"OB2-00005\", \"items_count\": 1, \"cashier_name\": \"Ani (Kasir 1)\", \"total_amount\": 22222}', 'info', 1, 1, '2025-11-07 07:18:39', '2025-11-07 07:18:41', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-07 06:52:09', '2025-11-07 07:18:41', NULL),
(7, 4, 6, 'manual_input_alert', '⚠️ Input Manual - Inv OB2-00005', 'Kasir Ani (Kasir 1) membuat transaksi dengan 1 item input manual:\n\nNgetestt (1x @ Rp 22.222)\n\nTotal: Rp 22.222\nInvoice: OB2-00005\nWaktu: 07-11-2025 13:52:09', '{\"items\": [{\"name\": \"Ngetestt\", \"type\": \"service\", \"price\": 22222, \"reason\": \"TWESTTTTTINFGGGGG\", \"quantity\": 1}], \"cashier_id\": 5, \"invoice_no\": \"OB2-00005\", \"items_count\": 1, \"cashier_name\": \"Ani (Kasir 1)\", \"total_amount\": 22222}', 'info', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-07 06:52:09', '2025-11-07 06:52:09', NULL),
(8, 1, 7, 'manual_input_alert', '⚠️ Input Manual - Inv OB2-00007', 'Kasir Administrator membuat transaksi dengan 1 item input manual:\n\nSpooring Ban (1x @ Rp 150.000)\n\nTotal: Rp 150.000\nInvoice: OB2-00007\nWaktu: 12-11-2025 13:46:41', '{\"items\": [{\"name\": \"Spooring Ban\", \"type\": \"service\", \"price\": 150000, \"reason\": null, \"quantity\": 1}], \"cashier_id\": 1, \"invoice_no\": \"OB2-00007\", \"items_count\": 1, \"cashier_name\": \"Administrator\", \"total_amount\": 150000}', 'info', 1, 0, '2025-12-04 13:36:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 06:46:41', '2025-12-04 13:36:53', NULL),
(9, 2, 7, 'manual_input_alert', '⚠️ Input Manual - Inv OB2-00007', 'Kasir Administrator membuat transaksi dengan 1 item input manual:\n\nSpooring Ban (1x @ Rp 150.000)\n\nTotal: Rp 150.000\nInvoice: OB2-00007\nWaktu: 12-11-2025 13:46:41', '{\"items\": [{\"name\": \"Spooring Ban\", \"type\": \"service\", \"price\": 150000, \"reason\": null, \"quantity\": 1}], \"cashier_id\": 1, \"invoice_no\": \"OB2-00007\", \"items_count\": 1, \"cashier_name\": \"Administrator\", \"total_amount\": 150000}', 'info', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 06:46:41', '2025-11-12 06:46:41', NULL),
(10, 3, 7, 'manual_input_alert', '⚠️ Input Manual - Inv OB2-00007', 'Kasir Administrator membuat transaksi dengan 1 item input manual:\n\nSpooring Ban (1x @ Rp 150.000)\n\nTotal: Rp 150.000\nInvoice: OB2-00007\nWaktu: 12-11-2025 13:46:41', '{\"items\": [{\"name\": \"Spooring Ban\", \"type\": \"service\", \"price\": 150000, \"reason\": null, \"quantity\": 1}], \"cashier_id\": 1, \"invoice_no\": \"OB2-00007\", \"items_count\": 1, \"cashier_name\": \"Administrator\", \"total_amount\": 150000}', 'info', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 06:46:41', '2025-11-12 06:46:41', NULL),
(11, 4, 7, 'manual_input_alert', '⚠️ Input Manual - Inv OB2-00007', 'Kasir Administrator membuat transaksi dengan 1 item input manual:\n\nSpooring Ban (1x @ Rp 150.000)\n\nTotal: Rp 150.000\nInvoice: OB2-00007\nWaktu: 12-11-2025 13:46:41', '{\"items\": [{\"name\": \"Spooring Ban\", \"type\": \"service\", \"price\": 150000, \"reason\": null, \"quantity\": 1}], \"cashier_id\": 1, \"invoice_no\": \"OB2-00007\", \"items_count\": 1, \"cashier_name\": \"Administrator\", \"total_amount\": 150000}', 'info', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-12 06:46:41', '2025-11-12 06:46:41', NULL);

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

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(74, 'edit_own_profile', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(75, 'access_user_management', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(76, 'show_total_stats', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(77, 'show_month_overview', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(78, 'show_weekly_sales_purchases', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(79, 'show_monthly_cashflow', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(80, 'show_notifications', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(81, 'access_products', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(82, 'create_products', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(83, 'show_products', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(84, 'edit_products', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(85, 'delete_products', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(86, 'access_product_categories', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(87, 'print_barcodes', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(88, 'access_adjustments', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(89, 'create_adjustments', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(90, 'show_adjustments', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(91, 'edit_adjustments', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(92, 'delete_adjustments', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(93, 'access_quotations', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(94, 'create_quotations', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(95, 'show_quotations', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(96, 'edit_quotations', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(97, 'delete_quotations', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(98, 'create_quotation_sales', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(99, 'send_quotation_mails', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(100, 'access_expenses', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(101, 'create_expenses', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(102, 'edit_expenses', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(103, 'delete_expenses', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(104, 'access_expense_categories', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(105, 'access_customers', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(106, 'create_customers', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(107, 'show_customers', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(108, 'edit_customers', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(109, 'delete_customers', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(110, 'access_suppliers', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(111, 'create_suppliers', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(112, 'show_suppliers', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(113, 'edit_suppliers', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(114, 'delete_suppliers', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(115, 'access_sales', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(116, 'create_sales', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(117, 'show_sales', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(118, 'edit_sales', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(119, 'delete_sales', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(120, 'create_pos_sales', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(121, 'access_sale_payments', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(122, 'access_sale_returns', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(123, 'create_sale_returns', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(124, 'show_sale_returns', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(125, 'edit_sale_returns', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(126, 'delete_sale_returns', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(127, 'access_sale_return_payments', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(128, 'access_purchases', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(129, 'create_purchases', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(130, 'show_purchases', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(131, 'edit_purchases', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(132, 'delete_purchases', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(133, 'access_purchase_payments', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(134, 'access_purchase_returns', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(135, 'create_purchase_returns', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(136, 'show_purchase_returns', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(137, 'edit_purchase_returns', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(138, 'delete_purchase_returns', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(139, 'access_purchase_return_payments', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(140, 'access_reports', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(141, 'access_currencies', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(142, 'create_currencies', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(143, 'edit_currencies', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(144, 'delete_currencies', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(145, 'access_settings', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(146, 'access_units', 'web', '2025-08-15 14:36:27', '2025-08-15 14:36:27'),
(147, 'pos.override_price_limit', 'web', '2025-10-29 14:39:20', '2025-10-29 14:39:20'),
(148, 'pos.approve_discount', 'web', '2025-10-29 14:39:20', '2025-10-29 14:39:20'),
(149, 'pos.view_cost_price', 'web', '2025-10-29 14:39:20', '2025-10-29 14:39:20'),
(150, 'inventory.edit_hpp', 'web', '2025-10-29 14:39:20', '2025-10-29 14:39:20'),
(151, 'inventory.approve_hpp_override', 'web', '2025-10-29 14:39:20', '2025-10-29 14:39:20'),
(152, 'report.view_deviation', 'web', '2025-10-29 14:39:20', '2025-10-29 14:39:20'),
(153, 'report.view_activity_log', 'web', '2025-10-29 14:39:20', '2025-10-29 14:39:20'),
(154, 'report.export_sensitive', 'web', '2025-10-29 14:39:20', '2025-10-29 14:39:20'),
(155, 'settings.manage_service_standards', 'web', '2025-10-29 14:39:20', '2025-10-29 14:39:20'),
(156, 'settings.view_system_log', 'web', '2025-10-29 14:39:20', '2025-10-29 14:39:20'),
(157, 'approve_adjustments', 'web', '2025-11-01 04:20:39', '2025-11-01 04:20:39'),
(159, 'access_stock_opname', 'web', '2025-12-04 15:04:12', '2025-12-04 15:04:12'),
(160, 'create_stock_opname', 'web', '2025-12-04 15:04:12', '2025-12-04 15:04:12'),
(161, 'edit_stock_opname', 'web', '2025-12-04 15:04:12', '2025-12-04 15:04:12'),
(162, 'show_stock_opname', 'web', '2025-12-04 15:04:12', '2025-12-04 15:04:12'),
(163, 'delete_stock_opname', 'web', '2025-12-04 15:04:12', '2025-12-04 15:04:12');

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

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `is_active`, `category_id`, `product_name`, `product_code`, `product_quantity`, `stok_awal`, `product_cost`, `product_price`, `product_unit`, `product_stock_alert`, `product_order_tax`, `product_tax_type`, `product_note`, `created_at`, `updated_at`, `deleted_at`, `brand_id`, `product_size`, `ring`, `product_year`) VALUES
(1, 1, 2, 'Ban GT Savero', 'GT_Savero', 51, 5, 1280760, 1425000, 'PC', 2, NULL, NULL, NULL, '2025-08-06 02:17:51', '2025-10-10 02:50:49', NULL, 1, '31x10,5', '15', NULL),
(2, 1, 2, 'Ban Bridgestone Ecopia EP150 185/65 R15', 'BS-EP150-18565R15', 23, 20, 725000, 925000, 'PC', 4, NULL, NULL, NULL, '2025-08-17 05:04:07', '2025-08-17 05:04:07', NULL, 2, '185/65', '15', 2024),
(3, 1, 2, 'Ban Dunlop SP Touring R1 205/65 R16', 'DN-SPR1-20565R16', 23, 12, 890000, 1090000, 'PC', 3, NULL, NULL, NULL, '2025-08-17 05:04:07', '2025-08-17 05:04:07', NULL, 3, '205/65', '16', 2024),
(4, 1, 2, 'Ban GT Radial Champiro Eco 195/65 R15', 'GT-CE-19565R15', 23, 16, 640000, 835000, 'PC', 3, NULL, NULL, NULL, '2025-08-17 05:04:07', '2025-11-06 09:43:29', NULL, 1, '195/65', '15', 2024),
(5, 1, 3, 'Velg HSR Samurai Ring 17 5x114.3', 'HSR-SAM-R17-51143', 8, 8, 2450000, 3050000, 'PC', 2, NULL, NULL, 'Finish Black Polish', '2025-08-17 05:04:07', '2025-08-17 05:04:07', NULL, 4, NULL, '17', 2024),
(6, 1, 3, 'Velg OEM Toyota Innova Ring 16', 'OEM-INV-R16', 6, 6, 1200000, 1600000, 'PC', 2, NULL, NULL, 'Kondisi Baru OEM', '2025-08-17 05:04:07', '2025-08-17 05:04:07', NULL, 5, NULL, '16', 2023);

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

--
-- Dumping data for table `product_seconds`
--

INSERT INTO `product_seconds` (`id`, `name`, `unique_code`, `condition_notes`, `purchase_price`, `selling_price`, `status`, `created_at`, `updated_at`, `deleted_at`, `category_id`, `brand_id`, `size`, `ring`, `product_year`) VALUES
(1, 'Ban Bekas Dunlop AT3 265/65 R17 (80%)', 'SEC-DN-26565R17-001', 'Kondisi 80%, tahun 2021, tambalan 0, ban seragam, masih empuk', 600000, 850000, 'available', '2025-08-17 05:04:07', '2025-11-16 07:02:57', NULL, 2, 3, '265/65', '17', 2021),
(2, 'Ban Bekas GT Radial Savero 235/70 R16 (70%)', 'SEC-GT-23570R16-001', 'Kondisi 70%, tahun 2020, ada serat halus, masih layak harian', 400000, 650000, 'available', '2025-08-17 05:04:07', '2025-08-20 13:25:00', NULL, 2, 1, '235/70', '16', 2020),
(3, 'Velg Bekas HSR Ring 16 Black Polish', 'SEC-HSR-R16-BP-001', 'Cat mulus 90%, lurus, PCD 5x114.3, lebar 7J, ET42', 1800000, 2250000, 'available', '2025-08-17 05:04:07', '2025-08-17 05:24:47', NULL, 3, 4, NULL, '16', 2022),
(4, 'Velg Bekas OEM Ertiga Ring 15', 'SEC-OEM-ERT-R15-001', 'OEM Suzuki Ertiga, kondisi 85%, ada baret tipis, lurus', 1000000, 1400000, 'available', '2025-08-17 05:04:07', '2025-08-17 05:04:07', NULL, 3, 5, NULL, '15', 2019);

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

--
-- Dumping data for table `purchase_seconds`
--

INSERT INTO `purchase_seconds` (`id`, `date`, `reference`, `customer_name`, `customer_phone`, `total_amount`, `paid_amount`, `due_amount`, `status`, `payment_status`, `payment_method`, `bank_name`, `note`, `user_id`, `created_at`, `updated_at`) VALUES
(1, '2025-11-08', 'PBS-20251108-0001', 'Test Customer', NULL, 1000000, 1000000, 0, 'Completed', 'Lunas', 'Tunai', NULL, NULL, 1, '2025-11-08 16:41:31', '2025-11-08 16:41:31');

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

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(2, 'Super Admin', 'web', '2025-08-05 14:46:12', '2025-08-05 14:46:12'),
(3, 'Owner', 'web', '2025-10-29 14:39:12', '2025-10-29 14:39:12'),
(4, 'Supervisor', 'web', '2025-10-29 14:39:12', '2025-10-29 14:39:12'),
(5, 'Kasir', 'web', '2025-10-29 14:39:12', '2025-10-29 14:39:12');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(81, 1),
(82, 1),
(83, 1),
(84, 1),
(85, 1),
(86, 1),
(87, 1),
(88, 1),
(89, 1),
(90, 1),
(91, 1),
(92, 1),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(97, 1),
(98, 1),
(99, 1),
(100, 1),
(101, 1),
(102, 1),
(103, 1),
(104, 1),
(105, 1),
(106, 1),
(107, 1),
(108, 1),
(109, 1),
(110, 1),
(111, 1),
(112, 1),
(113, 1),
(114, 1),
(115, 1),
(116, 1),
(117, 1),
(118, 1),
(119, 1),
(120, 1),
(121, 1),
(122, 1),
(123, 1),
(124, 1),
(125, 1),
(126, 1),
(127, 1),
(128, 1),
(129, 1),
(130, 1),
(131, 1),
(132, 1),
(133, 1),
(134, 1),
(135, 1),
(136, 1),
(137, 1),
(138, 1),
(139, 1),
(140, 1),
(141, 1),
(145, 1),
(146, 1),
(157, 1),
(157, 2),
(74, 3),
(75, 3),
(76, 3),
(77, 3),
(78, 3),
(79, 3),
(80, 3),
(81, 3),
(82, 3),
(83, 3),
(84, 3),
(85, 3),
(86, 3),
(87, 3),
(88, 3),
(89, 3),
(90, 3),
(91, 3),
(92, 3),
(93, 3),
(94, 3),
(95, 3),
(96, 3),
(97, 3),
(98, 3),
(99, 3),
(100, 3),
(101, 3),
(102, 3),
(103, 3),
(104, 3),
(105, 3),
(106, 3),
(107, 3),
(108, 3),
(109, 3),
(110, 3),
(111, 3),
(112, 3),
(113, 3),
(114, 3),
(115, 3),
(116, 3),
(117, 3),
(118, 3),
(119, 3),
(120, 3),
(121, 3),
(122, 3),
(123, 3),
(124, 3),
(125, 3),
(126, 3),
(127, 3),
(128, 3),
(129, 3),
(130, 3),
(131, 3),
(132, 3),
(133, 3),
(134, 3),
(135, 3),
(136, 3),
(137, 3),
(138, 3),
(139, 3),
(140, 3),
(141, 3),
(142, 3),
(143, 3),
(144, 3),
(145, 3),
(146, 3),
(147, 3),
(148, 3),
(149, 3),
(150, 3),
(151, 3),
(152, 3),
(153, 3),
(154, 3),
(155, 3),
(156, 3),
(157, 3),
(159, 3),
(160, 3),
(161, 3),
(162, 3),
(163, 3),
(74, 4),
(76, 4),
(77, 4),
(78, 4),
(79, 4),
(80, 4),
(81, 4),
(82, 4),
(83, 4),
(84, 4),
(85, 4),
(86, 4),
(87, 4),
(88, 4),
(89, 4),
(90, 4),
(91, 4),
(92, 4),
(93, 4),
(94, 4),
(95, 4),
(96, 4),
(97, 4),
(98, 4),
(99, 4),
(100, 4),
(101, 4),
(102, 4),
(103, 4),
(104, 4),
(105, 4),
(106, 4),
(107, 4),
(108, 4),
(109, 4),
(110, 4),
(111, 4),
(112, 4),
(113, 4),
(114, 4),
(115, 4),
(116, 4),
(117, 4),
(118, 4),
(119, 4),
(120, 4),
(121, 4),
(122, 4),
(123, 4),
(124, 4),
(125, 4),
(126, 4),
(127, 4),
(128, 4),
(129, 4),
(130, 4),
(131, 4),
(132, 4),
(133, 4),
(134, 4),
(135, 4),
(136, 4),
(137, 4),
(138, 4),
(139, 4),
(140, 4),
(141, 4),
(142, 4),
(143, 4),
(144, 4),
(145, 4),
(146, 4),
(147, 4),
(148, 4),
(149, 4),
(150, 4),
(151, 4),
(152, 4),
(153, 4),
(154, 4),
(155, 4),
(156, 4),
(157, 4),
(88, 5),
(89, 5),
(120, 5);

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
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `customer_id`, `date`, `reference`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `has_price_adjustment`, `tax_percentage`, `tax_amount`, `discount_percentage`, `discount_amount`, `shipping_amount`, `total_amount`, `has_manual_input`, `total_hpp`, `total_profit`, `paid_amount`, `due_amount`, `status`, `payment_status`, `paid_at`, `payment_method`, `snap_token`, `midtrans_transaction_id`, `midtrans_payment_type`, `bank_name`, `note`, `created_at`, `updated_at`, `deleted_at`, `manual_input_count`, `manual_input_summary`, `is_manual_input_notified`, `notified_at`) VALUES
(1, NULL, '2025-11-07', 'OB2-00001', 5, 'Walk-in', NULL, NULL, 1, 0, 0, 0, 0, 0, 900000, 0, 725000, 175000, 900000, 0, 'Completed', 'Paid', NULL, 'Transfer', NULL, NULL, NULL, 'BCA', NULL, '2025-11-06 19:00:00', '2025-11-06 19:20:00', NULL, 0, NULL, 0, NULL),
(2, NULL, '2025-11-07', 'OB2-00002', 5, 'Walk-in', NULL, NULL, 1, 0, 0, 0, 0, 0, 25000, 1, 0, 25000, 25000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-11-06 19:10:00', '2025-11-06 19:20:30', NULL, 1, '[{\"name\": \"Balancing\", \"type\": \"service\", \"price\": 25000, \"reason\": \"Velg besar\", \"quantity\": 1}]', 1, '2025-11-06 19:20:30'),
(3, NULL, '2025-11-07', 'OB2-00003', 5, 'Walk-in', NULL, NULL, 0, 0, 0, 0, 0, 0, 835000, 0, 640000, 195000, 845000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-11-06 19:30:00', '2025-11-06 19:32:30', NULL, 0, NULL, 0, NULL),
(4, NULL, '2025-11-07', 'OB2-00004', 5, 'Walk-in', NULL, NULL, 0, 0, 0, 0, 0, 0, 38000, 1, 0, 38000, 38000, 0, 'Completed', 'Paid', NULL, 'QRIS', NULL, NULL, NULL, NULL, NULL, '2025-11-06 19:40:00', '2025-11-06 19:40:00', NULL, 2, '[{\"name\": \"Jasa Pasang Ban\", \"type\": \"service\", \"price\": 30000, \"reason\": \"Pemasangan\", \"quantity\": 1}, {\"name\": \"Nitrogen\", \"type\": \"goods\", \"price\": 8000, \"reason\": \"Isi ulang\", \"quantity\": 1}]', 1, '2025-11-06 19:40:10'),
(6, NULL, '2025-11-07', 'OB2-00005', 5, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 22222, 1, 0, 22222, 22222, 0, 'Completed', 'Paid', NULL, 'QRIS', NULL, NULL, NULL, NULL, NULL, '2025-11-07 06:52:09', '2025-11-07 06:52:15', NULL, 1, '\"[{\\\"name\\\":\\\"Ngetestt\\\",\\\"quantity\\\":1,\\\"price\\\":22222,\\\"reason\\\":\\\"TWESTTTTTINFGGGGG\\\",\\\"type\\\":\\\"service\\\"}]\"', 1, '2025-11-07 06:52:09'),
(7, 3, '2025-11-12', 'OB2-00007', 1, 'Peter', NULL, NULL, 0, 0, 0, 0, 0, 0, 150000, 1, 0, 150000, 0, 150000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-11-12 06:46:41', '2025-11-12 06:46:41', NULL, 1, '\"[{\\\"name\\\":\\\"Spooring Ban\\\",\\\"quantity\\\":1,\\\"price\\\":150000,\\\"reason\\\":null,\\\"type\\\":\\\"service\\\"}]\"', 1, '2025-11-12 06:46:41'),
(8, 1, '2025-11-16', 'OB2-00008', 1, 'Peter Vincent', 'peter@gmail.com', '082227863969', 1, 0, 0, 0, 0, 0, 990000, 0, 890000, 100000, 0, 990000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-11-15 17:09:00', '2025-11-15 17:09:00', NULL, 0, NULL, 0, NULL);

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
-- Dumping data for table `sale_details`
--

INSERT INTO `sale_details` (`id`, `sale_id`, `item_name`, `product_id`, `productable_id`, `productable_type`, `source_type`, `manual_kind`, `product_name`, `product_code`, `quantity`, `price`, `original_price`, `is_price_adjusted`, `price_adjustment_amount`, `price_adjustment_note`, `adjusted_by`, `adjusted_at`, `hpp`, `manual_hpp`, `unit_price`, `sub_total`, `subtotal_profit`, `product_discount_amount`, `product_discount_type`, `product_tax_amount`, `created_at`, `updated_at`) VALUES
(1, 1, 'Ban Bridgestone Ecopia EP150 185/65 R15', 2, NULL, NULL, 'new', NULL, 'Ban Bridgestone Ecopia EP150 185/65 R15', 'BS-EP150-18565R15', 1, 900000, 925000, 1, 25000, 'Diskon loyal pelanggan', 5, '2025-11-06 19:05:00', 725000, NULL, 900000, 900000, 175000, 0, 'fixed', 0, '2025-11-06 19:00:00', '2025-11-06 19:20:00'),
(2, 2, 'Balancing', NULL, NULL, NULL, 'manual', 'service', 'Balancing', 'SRV-BAL', 1, 25000, 20000, 1, -5000, 'SUV balancing (butuh waktu & beban lebih)', 4, '2025-11-06 19:18:00', 0, 0, 25000, 25000, 25000, 0, 'fixed', 0, '2025-11-06 19:10:10', '2025-11-06 19:10:10'),
(3, 3, 'GT Champiro Eco 195/65 R15', 4, NULL, NULL, 'new', NULL, 'GT Champiro Eco 195/65 R15', 'GT-CE-19565R15', 1, 835000, 835000, 0, 0, NULL, NULL, NULL, 640000, NULL, 835000, 835000, 195000, 0, 'fixed', 0, '2025-11-06 19:30:10', '2025-11-06 19:30:10'),
(5, 4, 'Jasa Pasang Ban', NULL, NULL, NULL, 'manual', 'service', 'Jasa Pasang Ban', 'SRV-PASANG', 1, 30000, 30000, 0, 0, NULL, NULL, NULL, 0, 0, 30000, 30000, 30000, 0, 'fixed', 0, '2025-11-06 19:40:05', '2025-11-06 19:40:05'),
(6, 4, 'Nitrogen', NULL, NULL, NULL, 'manual', 'goods', 'Nitrogen', 'GD-N2', 1, 8000, 8000, 0, 0, NULL, NULL, NULL, 0, 0, 8000, 8000, 8000, 0, 'fixed', 0, '2025-11-06 19:40:07', '2025-11-06 19:40:07'),
(7, 6, 'Ngetestt', NULL, NULL, NULL, 'manual', 'service', 'Ngetestt', '-', 1, 22222, 22222, 0, 0, NULL, NULL, NULL, 0, NULL, 22222, 22222, 22222, 0, 'fixed', 0, '2025-11-07 06:52:09', '2025-11-07 06:52:09'),
(8, 7, 'Spooring Ban', NULL, 1, 'Modules\\Product\\Entities\\ServiceMaster', 'manual', 'service', 'Spooring Ban', 'SRV-1', 1, 150000, 150000, 0, 0, NULL, NULL, NULL, 0, NULL, 150000, 150000, 150000, 0, 'fixed', 0, '2025-11-12 06:46:41', '2025-11-12 06:46:41'),
(9, 8, 'Ban Dunlop SP Touring R1 205/65 R16', 3, NULL, NULL, 'new', NULL, 'Ban Dunlop SP Touring R1 205/65 R16', 'DN-SPR1-20565R16', 1, 990000, 1090000, 1, 100000, 'tesssssssssssss', 1, '2025-11-15 17:09:00', 890000, NULL, 990000, 990000, 100000, 0, 'fixed', 0, '2025-11-15 17:09:00', '2025-11-15 17:09:00');

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

--
-- Dumping data for table `sale_payments`
--

INSERT INTO `sale_payments` (`id`, `sale_id`, `amount`, `date`, `reference`, `payment_method`, `bank_name`, `note`, `created_at`, `updated_at`, `deleted_at`) VALUES
(36, 6, 22222, '2025-11-07', 'INV/OB2-00005', 'QRIS', NULL, NULL, '2025-11-07 06:52:15', '2025-11-07 06:52:15', NULL);

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

--
-- Dumping data for table `service_masters`
--

INSERT INTO `service_masters` (`id`, `service_name`, `standard_price`, `category`, `description`, `status`, `price_before`, `price_after`, `price_updated_at`, `updated_by`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Spooring Ban', 150000, 'service', 'Penstabilan Roda', 1, 150000, 150000, '2025-11-04 08:22:21', 1, NULL, '2025-11-04 07:26:02', '2025-11-04 08:22:21', NULL),
(2, 'Pasang Ban', 25000, 'service', NULL, 1, NULL, NULL, NULL, NULL, 9001, '2025-11-06 09:40:35', '2025-11-06 09:40:35', NULL),
(3, 'Balancing', 20000, 'service', NULL, 1, NULL, NULL, NULL, NULL, 9001, '2025-11-06 09:40:35', '2025-11-06 09:40:35', NULL);

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

--
-- Dumping data for table `service_master_audits`
--

INSERT INTO `service_master_audits` (`id`, `service_master_id`, `old_price`, `new_price`, `reason`, `changed_by`, `created_at`, `updated_at`) VALUES
(1, 1, 150000, 150000, NULL, 1, '2025-11-04 07:50:20', '2025-11-04 07:50:20'),
(2, 1, 150000, 150000, NULL, 1, '2025-11-04 08:22:21', '2025-11-04 08:22:21');

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

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `company_name`, `company_email`, `company_phone`, `site_logo`, `default_currency_id`, `default_currency_position`, `thousand_separator`, `decimal_separator`, `notification_email`, `footer_text`, `company_address`, `created_at`, `updated_at`) VALUES
(1, 'Omah Ban 2', 'vincentpeter789@gmail.com', '085325579921', NULL, 1, 'prefix', '.', ',', 'vincentpeter789@gmail.com', 'Triangle Pos © 2021 || Developed by <strong><a target=\"_blank\" href=\"https://fahimanzam.me\">Fahim Anzam</a></strong>', 'Jl. Empu Sendok 2A, Gedawang (Banyumanik), Semarang', '2025-08-05 14:46:12', '2025-09-08 08:30:37');

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

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `productable_type`, `productable_id`, `product_id`, `ref_id`, `ref_type`, `type`, `quantity`, `description`, `user_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(11, NULL, NULL, 2, 5, 'adjustment', 'out', 3, 'Adjustment ADJ-20251102-00001 - sub', 2, '2025-11-02 10:18:26', '2025-11-02 10:18:26', NULL),
(16, 'Modules\\Product\\Entities\\Product', 4, NULL, NULL, 'adjustment', 'out', 1, 'Sale OB2-00089', 5, '2025-11-06 10:43:05', '2025-11-06 10:43:05', NULL),
(17, 'Modules\\Product\\Entities\\Product', 4, NULL, NULL, 'adjustment', 'out', 1, 'Sale #OB2-00089', 5, '2025-11-06 09:43:29', '2025-11-06 09:43:29', NULL),
(18, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00001', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(19, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00005', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(20, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00020', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(21, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00021', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(22, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 4, 'Revert sale SL-00022', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(23, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00023', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(24, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00024', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(25, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00025', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(26, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00027', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(27, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00028', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(28, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00029', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(29, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00030', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(30, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00031', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(31, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00032', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(32, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00033', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(33, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00034', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(34, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00035', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(35, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00036', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(36, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00037', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(37, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00041', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(38, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00045', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(39, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00048', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(40, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00049', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(41, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00050', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(42, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00051', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(43, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00052', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(44, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00054', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(45, 'Modules\\Product\\Entities\\ProductSecond', 3, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00055', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(46, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00057', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(47, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-00056', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(48, 'Modules\\Product\\Entities\\ProductSecond', 2, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale SL-20250820-202448-68a5dab0793ff', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(49, 'Modules\\Product\\Entities\\ProductSecond', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00060', NULL, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(50, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00061', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(51, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00064', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(52, 'Modules\\Product\\Entities\\Product', 4, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00065', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(53, 'Modules\\Product\\Entities\\Product', 2, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00066', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(54, 'Modules\\Product\\Entities\\Product', 4, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00067', 2, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(55, 'Modules\\Product\\Entities\\Product', 4, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00068', 2, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(56, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00069', 2, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(57, 'Modules\\Product\\Entities\\Product', 2, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00070', 2, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(58, 'Modules\\Product\\Entities\\Product', 2, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00071', 2, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(59, 'Modules\\Product\\Entities\\Product', 4, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00072', 2, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(60, 'Modules\\Product\\Entities\\Product', 4, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00073', 2, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(61, 'Modules\\Product\\Entities\\Product', 3, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00074', 2, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(62, 'Modules\\Product\\Entities\\Product', 4, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00075', 2, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(63, 'Modules\\Product\\Entities\\Product', 2, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00077', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(64, 'Modules\\Product\\Entities\\Product', 4, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00078', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(65, 'Modules\\Product\\Entities\\Product', 4, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00079', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(66, 'Modules\\Product\\Entities\\Product', 4, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00080', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(67, 'Modules\\Product\\Entities\\Product', 3, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00081', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(68, 'Modules\\Product\\Entities\\Product', 2, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00082', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(69, 'Modules\\Product\\Entities\\Product', 2, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00083', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(70, 'Modules\\Product\\Entities\\Product', 1, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00084', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(71, 'Modules\\Product\\Entities\\ProductSecond', 4, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00085', 1, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(72, 'Modules\\Product\\Entities\\Product', 4, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00089', 5, '2025-11-07 04:43:16', '2025-11-07 04:43:16', NULL),
(74, 'Modules\\Product\\Entities\\Product', 2, NULL, NULL, 'adjustment', 'out', 1, 'Sale OB2-00001', 5, '2025-11-07 04:48:10', '2025-11-07 04:48:10', NULL),
(75, 'Modules\\Product\\Entities\\Product', 2, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00001', 5, '2025-11-07 04:48:42', '2025-11-07 04:48:42', NULL),
(76, 'Modules\\Product\\Entities\\Product', 2, NULL, NULL, 'adjustment', 'out', 1, 'Sale OB2-00001', 5, '2025-11-07 04:53:14', '2025-11-07 04:53:14', NULL),
(77, 'Modules\\Product\\Entities\\Product', 4, NULL, NULL, 'adjustment', 'out', 1, 'Sale OB2-00003', 5, '2025-11-07 04:53:26', '2025-11-07 04:53:26', NULL),
(78, 'Modules\\Product\\Entities\\Product', 2, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00001', 5, '2025-11-07 07:35:14', '2025-11-07 07:35:14', NULL),
(79, 'Modules\\Product\\Entities\\Product', 4, NULL, NULL, 'adjustment', 'in', 1, 'Revert sale OB2-00003', 5, '2025-11-07 07:35:14', '2025-11-07 07:35:14', NULL),
(80, 'Modules\\Product\\Entities\\Product', 2, NULL, NULL, 'adjustment', 'out', 1, 'Sale OB2-00001', 5, '2025-11-07 07:36:15', '2025-11-07 07:36:15', NULL),
(81, 'Modules\\Product\\Entities\\Product', 4, NULL, NULL, 'adjustment', 'out', 1, 'Sale OB2-00003', 5, '2025-11-07 07:36:26', '2025-11-07 07:36:26', NULL),
(82, 'Modules\\Product\\Entities\\Product', 3, 3, 8, 'sale', 'out', 1, 'Sale OB2-00008', 1, '2025-11-15 18:09:00', '2025-11-15 18:09:00', NULL),
(83, NULL, NULL, 2, 1, 'adjustment', 'in', 5, 'Adjustment ADJ-20251117-00001 - add', 1, '2025-11-17 03:27:30', '2025-11-17 03:27:30', NULL),
(84, NULL, NULL, 2, 2, 'adjustment', 'in', 5, 'Adjustment ADJ-20251117-00002 - add', 1, '2025-11-17 03:33:05', '2025-11-17 03:33:05', NULL);

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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
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

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `name`, `short_name`, `operator`, `operation_value`, `created_at`, `updated_at`) VALUES
(1, 'Piece', 'PC', '*', 1, '2025-08-05 14:46:12', '2025-08-05 14:46:12');

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

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` (`id`, `folder`, `filename`, `created_at`, `updated_at`) VALUES
(1, '68dbea0a9aeb5-1759242762', '1759242762.jpg', '2025-09-30 13:32:43', '2025-09-30 13:32:43');

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
  `is_active` tinyint(1) NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `supervisor_pin`, `phone_number`, `last_login_at`, `login_ip`, `is_active`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Administrator', 'super.admin@test.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NULL, 1, NULL, '2025-08-05 14:46:12', '2025-08-05 14:46:12', NULL),
(2, 'Vincent Peter', 'peter@gmail.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWWG/igi', NULL, NULL, NULL, 1, 'MsMYL6l4lOkrJbP9ltu41XhNDJDqSPcGJYLZVvVdedAAwHLwRqOCeKD1q0eM', '2025-09-30 13:32:44', '2025-09-30 13:32:44', NULL),
(3, 'Budi (Owner)', 'budi.owner@omahban.test', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '08123456789', NULL, NULL, 1, NULL, '2025-11-01 04:20:39', '2025-11-01 04:20:39', NULL),
(4, 'Siti (Supervisor)', 'siti.sup@omahban.test', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '08198765432', NULL, NULL, 1, NULL, '2025-11-01 04:20:39', '2025-11-01 04:20:39', NULL),
(5, 'Ani (Kasir 1)', 'ani.kasir@omahban.test', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '08112233445', NULL, NULL, 1, NULL, '2025-11-01 04:20:39', '2025-11-01 04:20:39', NULL),
(6, 'Rina (Kasir 2)', 'rina.kasir@omahban.test', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '08556677889', NULL, NULL, 1, NULL, '2025-11-01 04:20:39', '2025-11-01 04:20:39', NULL),
(9001, 'Owner OmahBan', 'owner@example.com', NULL, '$2y$10$hashdummy', NULL, NULL, NULL, NULL, 1, NULL, '2025-11-06 09:40:35', '2025-11-06 09:40:35', NULL),
(9002, 'Kasir Demo', 'kasir@example.com', NULL, '$2y$10$hashdummy', NULL, NULL, NULL, NULL, 1, NULL, '2025-11-06 09:40:35', '2025-11-06 09:40:35', NULL),
(9003, 'Owner', 'owner@ob.test', NULL, '$2y$10$OGGliCSV1Ksj.cl/IL5KfOMhUkYyuzGzJqsEN1CwB6tK1x2cMSVBu', NULL, NULL, NULL, NULL, 1, NULL, '2025-11-06 13:42:16', '2025-11-06 13:42:16', NULL),
(9004, 'Kasir 1', 'kasir1@ob.test', NULL, '$2y$10$D6H6R2B21KNGkbDOounpWOk6boBureT1hSDZnI8op.lUj9pe.BnKW', NULL, NULL, NULL, NULL, 1, NULL, '2025-11-06 13:42:16', '2025-11-06 13:42:16', NULL);

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `adjustments`
--
ALTER TABLE `adjustments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `adjustment_files`
--
ALTER TABLE `adjustment_files`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `adjustment_logs`
--
ALTER TABLE `adjustment_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fontee_config`
--
ALTER TABLE `fontee_config`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `manual_input_details`
--
ALTER TABLE `manual_input_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `manual_input_logs`
--
ALTER TABLE `manual_input_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `manual_input_summary_daily`
--
ALTER TABLE `manual_input_summary_daily`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `owner_notifications`
--
ALTER TABLE `owner_notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `product_seconds`
--
ALTER TABLE `product_seconds`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_second_details`
--
ALTER TABLE `purchase_second_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `service_master_audits`
--
ALTER TABLE `service_master_audits`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9005;

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
