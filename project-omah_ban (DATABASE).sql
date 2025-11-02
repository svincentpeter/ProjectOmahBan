-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 02, 2025 at 11:18 AM
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
-- Dumping data for table `adjusted_products`
--

INSERT INTO `adjusted_products` (`id`, `adjustment_id`, `product_id`, `quantity`, `type`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 5, 2, 3, 'sub', '2025-11-02 10:17:49', '2025-11-02 10:17:49', NULL);

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
(5, '2025-11-02', 'ADJ-20251102-00001', 'Testing', 'approved', 2, 2, 'Rusak', 'TESTER', 'Lanjutkan', '2025-11-02 10:18:26', 2175000.00, '2025-11-02 10:17:49', '2025-11-02 10:18:26', NULL);

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
(1, 5, 'adjustment_files/n8EhBuhi3oxNUZ55w6TWGz1Fig3DtwqQr713WqwI.png', 'MM5ZoC53d3tIdCInJcYeDzIIDQOFYB7DjGNUm9GV.png', 1779177, 'image/png', '2025-11-02 10:17:49', '2025-11-02 10:17:49', NULL);

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
(1, 5, 2, 'created', NULL, 'pending', 'Testing', 1, '2025-11-02 10:17:49', '2025-11-02 11:17:49', NULL),
(2, 5, 2, 'approved', 'pending', 'approved', 'Lanjutkan', 1, '2025-11-02 10:18:26', '2025-11-02 11:18:26', NULL);

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
`category_code` varchar(255)
,`created_at` timestamp
,`id` bigint unsigned
,`name` varchar(255)
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
  `updated_at` timestamp NULL DEFAULT NULL
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
(157, 'approve_adjustments', 'web', '2025-11-01 04:20:39', '2025-11-01 04:20:39');

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
(1, 1, 2, 'Ban GT Savero', 'GT_Savero', 15, 5, 1280760, 1425000, 'PC', 2, NULL, NULL, NULL, '2025-08-06 02:17:51', '2025-10-10 02:50:49', NULL, 1, '31x10,5', '15', NULL),
(2, 1, 2, 'Ban Bridgestone Ecopia EP150 185/65 R15', 'BS-EP150-18565R15', 18, 20, 725000, 925000, 'PC', 4, NULL, NULL, NULL, '2025-08-17 05:04:07', '2025-08-17 05:04:07', NULL, 2, '185/65', '15', 2024),
(3, 1, 2, 'Ban Dunlop SP Touring R1 205/65 R16', 'DN-SPR1-20565R16', 22, 12, 890000, 1090000, 'PC', 3, NULL, NULL, NULL, '2025-08-17 05:04:07', '2025-08-17 05:04:07', NULL, 3, '205/65', '16', 2024),
(4, 1, 2, 'Ban GT Radial Champiro Eco 195/65 R15', 'GT-CE-19565R15', 16, 16, 640000, 835000, 'PC', 3, NULL, NULL, NULL, '2025-08-17 05:04:07', '2025-08-17 05:04:07', NULL, 1, '195/65', '15', 2024),
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
(1, 'Ban Bekas Dunlop AT3 265/65 R17 (80%)', 'SEC-DN-26565R17-001', 'Kondisi 80%, tahun 2021, tambalan 0, ban seragam, masih empuk', 600000, 850000, 'sold', '2025-08-17 05:04:07', '2025-08-21 02:08:13', NULL, 2, 3, '265/65', '17', 2021),
(2, 'Ban Bekas GT Radial Savero 235/70 R16 (70%)', 'SEC-GT-23570R16-001', 'Kondisi 70%, tahun 2020, ada serat halus, masih layak harian', 400000, 650000, 'sold', '2025-08-17 05:04:07', '2025-08-20 13:25:00', NULL, 2, 1, '235/70', '16', 2020),
(3, 'Velg Bekas HSR Ring 16 Black Polish', 'SEC-HSR-R16-BP-001', 'Cat mulus 90%, lurus, PCD 5x114.3, lebar 7J, ET42', 1800000, 2250000, 'sold', '2025-08-17 05:04:07', '2025-08-17 05:24:47', NULL, 3, 4, NULL, '16', 2022),
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
  `tax_percentage` int NOT NULL DEFAULT '0',
  `tax_amount` bigint NOT NULL DEFAULT '0',
  `discount_percentage` int NOT NULL DEFAULT '0',
  `discount_amount` bigint NOT NULL DEFAULT '0',
  `shipping_amount` bigint NOT NULL DEFAULT '0',
  `total_amount` bigint NOT NULL DEFAULT '0',
  `paid_amount` bigint NOT NULL DEFAULT '0',
  `due_amount` bigint NOT NULL DEFAULT '0',
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
  `price` bigint NOT NULL,
  `unit_price` bigint NOT NULL,
  `sub_total` bigint NOT NULL,
  `product_discount_amount` bigint NOT NULL,
  `product_discount_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `product_tax_amount` bigint NOT NULL,
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
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_returns`
--

CREATE TABLE `purchase_returns` (
  `id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `supplier_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_percentage` int NOT NULL DEFAULT '0',
  `tax_amount` bigint NOT NULL DEFAULT '0',
  `discount_percentage` int NOT NULL DEFAULT '0',
  `discount_amount` bigint NOT NULL DEFAULT '0',
  `shipping_amount` bigint NOT NULL DEFAULT '0',
  `total_amount` bigint NOT NULL,
  `paid_amount` bigint NOT NULL,
  `due_amount` bigint NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_details`
--

CREATE TABLE `purchase_return_details` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_return_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `price` bigint UNSIGNED DEFAULT NULL,
  `unit_price` bigint NOT NULL,
  `sub_total` bigint UNSIGNED DEFAULT NULL,
  `product_discount_amount` bigint UNSIGNED DEFAULT NULL,
  `product_discount_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `product_tax_amount` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_payments`
--

CREATE TABLE `purchase_return_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_return_id` bigint UNSIGNED NOT NULL,
  `amount` bigint NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotations`
--

CREATE TABLE `quotations` (
  `id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_percentage` int NOT NULL DEFAULT '0',
  `tax_amount` bigint NOT NULL DEFAULT '0',
  `discount_percentage` int NOT NULL DEFAULT '0',
  `discount_amount` bigint NOT NULL DEFAULT '0',
  `shipping_amount` bigint NOT NULL DEFAULT '0',
  `total_amount` bigint NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotation_details`
--

CREATE TABLE `quotation_details` (
  `id` bigint UNSIGNED NOT NULL,
  `quotation_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `price` bigint NOT NULL,
  `unit_price` bigint NOT NULL,
  `sub_total` bigint NOT NULL,
  `product_discount_amount` bigint NOT NULL,
  `product_discount_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `product_tax_amount` bigint NOT NULL,
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
(89, 5);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `has_price_adjustment` tinyint(1) DEFAULT '0' COMMENT '1=ada item dengan harga diedit',
  `tax_percentage` int NOT NULL DEFAULT '0',
  `tax_amount` bigint NOT NULL DEFAULT '0',
  `discount_percentage` int NOT NULL DEFAULT '0',
  `discount_amount` bigint NOT NULL DEFAULT '0',
  `shipping_amount` bigint NOT NULL DEFAULT '0',
  `total_amount` bigint NOT NULL DEFAULT '0',
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
  `deleted_at` timestamp NULL DEFAULT NULL
) ;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `date`, `reference`, `user_id`, `customer_name`, `has_price_adjustment`, `tax_percentage`, `tax_amount`, `discount_percentage`, `discount_amount`, `shipping_amount`, `total_amount`, `total_hpp`, `total_profit`, `paid_amount`, `due_amount`, `status`, `payment_status`, `paid_at`, `payment_method`, `snap_token`, `midtrans_transaction_id`, `midtrans_payment_type`, `bank_name`, `note`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, '2025-08-08', 'SL-00001', NULL, NULL, 0, 0, 0, 0, 0, 0, 1425000, 0, 1425000, 0, 1425000, 'Completed', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-08 08:15:20', '2025-08-08 08:15:20', NULL),
(19, '2025-08-09', 'SL-00005', NULL, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 0, 1425000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-09 13:38:27', '2025-08-09 13:38:27', NULL),
(20, '2025-08-09', 'SL-00020', NULL, NULL, 0, 0, 0, 0, 0, 0, 1575000, 1280760, 294240, 0, 1575000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-09 13:56:29', '2025-08-09 13:56:29', NULL),
(21, '2025-08-09', 'SL-00021', NULL, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 0, 1425000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-09 14:02:55', '2025-08-09 14:02:55', NULL),
(22, '2025-08-09', 'SL-00022', NULL, NULL, 0, 0, 0, 0, 0, 0, 18200000, 5123040, 13076960, 0, 18200000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-09 14:37:09', '2025-08-09 14:37:09', NULL),
(23, '2025-08-09', 'SL-00023', NULL, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 1425000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-09 14:43:36', '2025-08-09 14:43:46', NULL),
(24, '2025-08-09', 'SL-00024', NULL, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 0, 1425000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-09 14:44:29', '2025-08-09 14:44:29', NULL),
(25, '2025-08-09', 'SL-00025', NULL, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 0, 1425000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-09 14:52:26', '2025-08-09 14:52:26', NULL),
(26, '2025-08-09', 'SL-00026', NULL, NULL, 0, 0, 0, 0, 0, 0, 15000000, 0, 15000000, 0, 15000000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-09 14:52:54', '2025-08-09 14:52:54', NULL),
(27, '2025-08-09', 'SL-00027', NULL, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 0, 1425000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-09 14:55:16', '2025-08-09 14:55:16', NULL),
(28, '2025-08-09', 'SL-00028', NULL, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 0, 1425000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-09 14:55:44', '2025-08-09 14:55:44', NULL),
(29, '2025-08-09', 'SL-00029', NULL, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 0, 1425000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-09 14:56:11', '2025-08-09 14:56:11', NULL),
(30, '2025-08-09', 'SL-00030', NULL, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 0, 1425000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-09 15:04:34', '2025-08-09 15:04:34', NULL),
(31, '2025-08-09', 'SL-00031', NULL, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 0, 1425000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-09 15:17:00', '2025-08-09 15:17:00', NULL),
(32, '2025-08-09', 'SL-00032', NULL, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 0, 1425000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-09 15:17:49', '2025-08-09 15:17:49', NULL),
(33, '2025-08-09', 'SL-00033', NULL, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 0, 1425000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-09 15:18:22', '2025-08-09 15:18:22', NULL),
(34, '2025-08-10', 'SL-00034', NULL, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 0, 1425000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-10 03:42:31', '2025-08-10 03:42:31', NULL),
(35, '2025-08-10', 'SL-00035', NULL, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 0, 1425000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-10 04:02:39', '2025-08-10 04:02:39', NULL),
(36, '2025-08-10', 'SL-00036', NULL, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 0, 1425000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-10 04:13:37', '2025-08-10 04:13:37', NULL),
(37, '2025-08-10', 'SL-00037', NULL, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 1425000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-10 04:33:26', '2025-08-20 11:32:45', NULL),
(38, '2025-08-10', 'SL-00038', NULL, NULL, 0, 0, 0, 0, 0, 0, 12500000, 0, 12500000, 0, 12500000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-10 04:58:25', '2025-08-10 04:58:25', NULL),
(39, '2025-08-10', 'SL-00039', NULL, NULL, 0, 0, 0, 0, 0, 0, 15000000, 0, 15000000, 0, 15000000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-10 04:59:06', '2025-08-10 04:59:06', NULL),
(40, '2025-08-10', 'SL-00040', NULL, NULL, 0, 0, 0, 0, 0, 0, 15000000, 0, 15000000, 0, 15000000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-10 04:59:58', '2025-08-10 04:59:58', NULL),
(44, '2025-08-10', 'SL-00041', 1, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 0, 1425000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-10 06:08:39', '2025-08-10 06:08:39', NULL),
(45, '2025-08-10', 'SL-00045', 1, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 1425000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-10 06:11:54', '2025-08-10 06:11:56', NULL),
(46, '2025-08-10', 'SL-00046', 1, NULL, 0, 0, 0, 0, 0, 0, 150000, 0, 150000, 150000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-10 06:12:16', '2025-08-20 11:32:59', NULL),
(47, '2025-08-10', 'SL-00047', 1, NULL, 0, 0, 0, 0, 0, 0, 200000, 0, 200000, 200000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-10 06:19:30', '2025-08-18 16:18:52', NULL),
(48, '2025-08-10', 'SL-00048', 1, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 1425000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-10 06:20:10', '2025-08-20 12:18:55', NULL),
(49, '2025-08-10', 'SL-00049', 1, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 1425000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-10 06:20:39', '2025-08-20 12:27:33', NULL),
(50, '2025-08-10', 'SL-00050', 1, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 1425000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-10 06:28:26', '2025-08-20 12:27:59', NULL),
(51, '2025-08-10', 'SL-00051', 1, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 1425000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-10 06:29:20', '2025-08-21 04:20:36', NULL),
(52, '2025-08-10', 'SL-00052', 1, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 1425000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-10 06:33:33', '2025-08-10 06:34:17', NULL),
(53, '2025-08-12', 'SL-00053', 1, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 100000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-12 04:14:18', '2025-08-18 15:52:12', NULL),
(54, '2025-08-12', 'SL-00054', 1, NULL, 0, 0, 0, 0, 0, 0, 1425000, 0, 0, 1425000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-12 04:16:16', '2025-08-20 11:32:24', NULL),
(55, '2025-08-17', 'SL-00055', 1, NULL, 0, 0, 0, 0, 0, 0, 2400000, 0, 2400000, 2400000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-17 05:24:44', '2025-08-18 13:41:51', NULL),
(56, '2025-08-19', 'SL-00056', 1, NULL, 0, 0, 0, 0, 0, 0, 1450000, 1280760, 169240, 1450000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-19 04:58:30', '2025-08-19 06:49:21', NULL),
(57, '2025-08-19', 'SL-00057', 1, NULL, 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 1425000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-19 05:23:41', '2025-08-20 10:07:07', NULL),
(58, '2025-08-19', 'SL-00058', 1, NULL, 0, 0, 0, 0, 0, 0, 125000, 0, 125000, 125000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-19 06:17:01', '2025-08-19 06:17:47', NULL),
(59, '2025-08-20', 'SL-20250820-202448-68a5dab0793ff', NULL, NULL, 0, 0, 0, 0, 0, 0, 650000, 0, 0, 650000, 0, 'Completed', 'Paid', NULL, 'Transfer', NULL, NULL, NULL, 'BCA', NULL, '2025-08-20 13:24:48', '2025-08-20 13:25:00', NULL),
(60, '2025-08-21', 'OB2-00060', NULL, NULL, 0, 0, 0, 0, 0, 0, 850000, 0, 0, 850000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-21 02:08:07', '2025-08-21 02:08:13', NULL),
(61, '2025-08-23', 'OB2-00061', 1, NULL, 0, 0, 0, 0, 0, 0, 1550000, 0, 0, 1550000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-23 15:12:31', '2025-08-23 15:12:37', NULL),
(62, '2025-08-23', 'OB2-00062', 1, NULL, 0, 0, 0, 0, 0, 0, 150000, 0, 0, 150000, 0, 'Completed', 'Paid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-08-23 15:12:56', '2025-08-23 15:12:59', NULL),
(63, '2025-08-23', 'OB2-00063', 1, NULL, 0, 0, 0, 0, 0, 0, 50000, 0, 0, 50000, 0, 'Completed', 'Paid', NULL, 'Transfer', NULL, NULL, NULL, 'BCA', NULL, '2025-08-23 15:13:36', '2025-08-23 15:13:49', NULL),
(64, '2025-10-14', 'OB2-00064', 1, NULL, 0, 0, 0, 0, 0, 0, 14250, 0, 0, 0, 14250, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-10-14 08:02:39', '2025-10-14 08:02:39', NULL),
(65, '2025-10-14', 'OB2-00065', 1, NULL, 0, 0, 0, 0, 0, 0, 8350, 0, 0, 0, 8350, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-10-14 08:21:22', '2025-10-14 08:21:22', NULL),
(66, '2025-10-14', 'OB2-00066', 1, 'PT. OMAH BAN JAYA', 0, 0, 0, 0, 0, 0, 9250, 7250, 2000, 0, 9250, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-10-14 08:32:14', '2025-10-14 08:32:14', NULL),
(67, '2025-10-21', 'OB2-00067', 2, NULL, 0, 0, 0, 0, 0, 0, 835000, 640000, 195000, 0, 835000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-10-21 14:24:18', '2025-10-21 14:24:18', NULL),
(68, '2025-10-21', 'OB2-00068', 2, 'Peter', 0, 0, 0, 0, 0, 0, 835000, 640000, 195000, 0, 835000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-10-21 15:41:04', '2025-10-21 15:41:04', NULL),
(69, '2025-10-21', 'OB2-00069', 2, 'PT. OMAH BAN JAYA', 0, 0, 0, 0, 0, 0, 1425000, 12807600, 0, 0, 1425000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-10-21 15:45:35', '2025-10-21 15:45:35', NULL),
(70, '2025-10-21', 'OB2-00070', 2, 'PT. OMAH BAN JAYA', 0, 0, 0, 0, 0, 0, 925000, 725000, 200000, 0, 925000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-10-21 15:46:06', '2025-10-21 15:46:06', NULL),
(71, '2025-10-21', 'OB2-00071', 2, 'Peter', 0, 0, 0, 0, 0, 0, 925000, 725000, 200000, 0, 925000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-10-21 15:46:39', '2025-10-21 15:46:39', NULL),
(72, '2025-10-21', 'OB2-00072', 2, 'Peter', 0, 0, 0, 0, 0, 0, 835000, 640000, 195000, 0, 835000, 'Draft', 'Unpaid', NULL, 'Tunai', '45c7b31a-45c7-4f37-a128-fa01f14bdfe3', NULL, NULL, NULL, NULL, '2025-10-21 15:47:40', '2025-10-21 16:20:47', NULL),
(73, '2025-10-21', 'OB2-00073', 2, 'PT. OMAH BAN JAYA', 0, 0, 0, 0, 0, 0, 835000, 640000, 195000, 0, 835000, 'Draft', 'Unpaid', NULL, 'Tunai', 'af0daa05-799f-432e-bb60-31a9781edfcd', NULL, NULL, NULL, NULL, '2025-10-21 16:20:55', '2025-10-21 16:21:51', NULL),
(74, '2025-10-22', 'OB2-00074', 2, 'Peter', 0, 0, 0, 0, 0, 0, 1090000, 890000, 200000, 0, 1090000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-10-22 14:51:59', '2025-10-22 14:51:59', NULL),
(76, '2025-10-23', 'OB2-00075', 2, 'PT. OMAH BAN JAYA', 0, 0, 0, 0, 0, 0, 835000, 640000, 195000, 0, 835000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-10-23 14:30:03', '2025-10-23 14:30:03', NULL),
(77, '2025-10-27', 'OB2-00077', 1, 'Peter', 0, 0, 0, 0, 0, 0, 925000, 725000, 200000, 0, 925000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-10-27 08:49:47', '2025-10-27 08:49:47', NULL),
(78, '2025-10-27', 'OB2-00078', 1, 'Peter', 0, 0, 0, 0, 0, 0, 835000, 640000, 195000, 0, 835000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-10-27 08:58:08', '2025-10-27 08:58:08', NULL),
(79, '2025-10-27', 'OB2-00079', 1, 'PT. OMAH BAN JAYA', 0, 0, 0, 0, 0, 0, 835000, 640000, 195000, 0, 835000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-10-27 08:58:22', '2025-10-27 08:58:22', NULL),
(80, '2025-10-27', 'OB2-00080', 1, 'PT. OMAH BAN JAYA', 0, 0, 0, 0, 0, 0, 835000, 640000, 195000, 0, 835000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-10-27 09:34:00', '2025-10-27 09:34:00', NULL),
(81, '2025-10-27', 'OB2-00081', 1, 'Peter', 0, 0, 0, 0, 0, 0, 1090000, 890000, 200000, 0, 1090000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-10-27 09:47:29', '2025-10-27 09:47:29', NULL),
(82, '2025-10-27', 'OB2-00082', 1, 'Peter', 0, 0, 0, 0, 0, 0, 925000, 725000, 200000, 0, 925000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-10-27 09:48:34', '2025-10-27 09:48:34', NULL),
(83, '2025-10-27', 'OB2-00083', 1, 'Peter', 0, 0, 0, 0, 0, 0, 925000, 725000, 200000, 0, 925000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-10-27 09:49:18', '2025-10-27 09:49:18', NULL),
(84, '2025-10-27', 'OB2-00084', 1, 'Peter', 0, 0, 0, 0, 0, 0, 1425000, 1280760, 144240, 0, 1425000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-10-27 09:49:39', '2025-10-27 09:49:39', NULL),
(85, '2025-10-27', 'OB2-00085', 1, 'Peter', 0, 0, 0, 0, 0, 0, 1200000, 1000000, 200000, 0, 1200000, 'Draft', 'Unpaid', NULL, 'Tunai', NULL, NULL, NULL, NULL, NULL, '2025-10-27 12:50:55', '2025-10-27 12:50:55', NULL);

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
(2, 4, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 0, NULL, 1425000, 1425000, 1425000, 0, 'fixed', 0, '2025-08-08 08:15:20', '2025-08-08 08:15:20'),
(3, 19, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-09 13:38:27', '2025-08-09 13:38:27'),
(4, 20, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-09 13:56:29', '2025-08-09 13:56:29'),
(5, 20, 'Spooring Ban', NULL, NULL, NULL, 'manual', 'service', 'Spooring Ban', 'SRV-1754751385', 1, 150000, 150000, 0, 0, NULL, NULL, NULL, 0, NULL, 150000, 150000, 150000, 0, 'fixed', 0, '2025-08-09 13:56:29', '2025-08-09 13:56:29'),
(6, 21, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-09 14:02:56', '2025-08-09 14:02:56'),
(7, 22, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 4, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 5700000, 576960, 0, 'fixed', 0, '2025-08-09 14:37:09', '2025-08-09 14:37:09'),
(8, 22, 'Spooring Ban', NULL, NULL, NULL, 'manual', 'service', 'Spooring Ban', '-', 1, 12500000, 12500000, 0, 0, NULL, NULL, NULL, 0, NULL, 12500000, 12500000, 12500000, 0, 'fixed', 0, '2025-08-09 14:37:09', '2025-08-09 14:37:09'),
(9, 23, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-09 14:43:36', '2025-08-09 14:43:36'),
(10, 24, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-09 14:44:29', '2025-08-09 14:44:29'),
(11, 25, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-09 14:52:26', '2025-08-09 14:52:26'),
(12, 26, 'Spooring Ban', NULL, NULL, NULL, 'manual', 'service', 'Spooring Ban', '-', 1, 15000000, 15000000, 0, 0, NULL, NULL, NULL, 0, NULL, 15000000, 15000000, 15000000, 0, 'fixed', 0, '2025-08-09 14:52:54', '2025-08-09 14:52:54'),
(13, 27, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-09 14:55:16', '2025-08-09 14:55:16'),
(14, 28, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-09 14:55:44', '2025-08-09 14:55:44'),
(15, 29, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-09 14:56:11', '2025-08-09 14:56:11'),
(16, 30, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-09 15:04:34', '2025-08-09 15:04:34'),
(17, 31, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-09 15:17:00', '2025-08-09 15:17:00'),
(18, 32, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-09 15:17:49', '2025-08-09 15:17:49'),
(19, 33, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-09 15:18:22', '2025-08-09 15:18:22'),
(20, 34, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-10 03:42:31', '2025-08-10 03:42:31'),
(21, 35, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-10 04:02:39', '2025-08-10 04:02:39'),
(22, 36, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-10 04:13:37', '2025-08-10 04:13:37'),
(23, 37, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-10 04:33:26', '2025-08-10 04:33:26'),
(24, 38, 'Spooring Ban', NULL, NULL, NULL, 'manual', 'service', 'Spooring Ban', '-', 1, 12500000, 12500000, 0, 0, NULL, NULL, NULL, 0, NULL, 12500000, 12500000, 12500000, 0, 'fixed', 0, '2025-08-10 04:58:25', '2025-08-10 04:58:25'),
(25, 39, 'Spooring Ban', NULL, NULL, NULL, 'manual', 'service', 'Spooring Ban', '-', 1, 15000000, 15000000, 0, 0, NULL, NULL, NULL, 0, NULL, 15000000, 15000000, 15000000, 0, 'fixed', 0, '2025-08-10 04:59:06', '2025-08-10 04:59:06'),
(26, 40, 'Spooring Ban', NULL, NULL, NULL, 'manual', 'service', 'Spooring Ban', '-', 1, 15000000, 15000000, 0, 0, NULL, NULL, NULL, 0, NULL, 15000000, 15000000, 15000000, 0, 'fixed', 0, '2025-08-10 04:59:58', '2025-08-10 04:59:58'),
(27, 44, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-10 06:08:39', '2025-08-10 06:08:39'),
(28, 45, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-10 06:11:54', '2025-08-10 06:11:54'),
(29, 46, 'Spooring Ban', NULL, NULL, NULL, 'manual', 'service', 'Spooring Ban', '-', 1, 150000, 150000, 0, 0, NULL, NULL, NULL, 0, NULL, 150000, 150000, 150000, 0, 'fixed', 0, '2025-08-10 06:12:16', '2025-08-10 06:12:16'),
(31, 48, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-10 06:20:10', '2025-08-10 06:20:10'),
(32, 49, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-10 06:20:39', '2025-08-10 06:20:39'),
(33, 50, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-10 06:28:26', '2025-08-10 06:28:26'),
(34, 51, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-10 06:29:20', '2025-08-10 06:29:20'),
(35, 52, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-10 06:33:33', '2025-08-10 06:33:33'),
(37, 54, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 0, 0, 'fixed', 0, '2025-08-12 04:16:16', '2025-08-12 04:16:16'),
(43, 55, 'Velg Bekas HSR Ring 16 Black Polish', NULL, 3, 'Modules\\Product\\Entities\\ProductSecond', 'second', NULL, 'Velg Bekas HSR Ring 16 Black Polish', 'SEC-HSR-R16-BP-001', 1, 2250000, 2250000, 0, 0, NULL, NULL, NULL, 0, NULL, 2250000, 2250000, 2250000, 0, 'fixed', 0, '2025-08-18 13:41:51', '2025-08-18 13:41:51'),
(44, 55, 'Balancing', NULL, NULL, NULL, 'manual', 'service', 'Balancing', '-', 1, 25000, 25000, 0, 0, NULL, NULL, NULL, 0, NULL, 25000, 25000, 25000, 0, 'fixed', 0, '2025-08-18 13:41:51', '2025-08-18 13:41:51'),
(45, 55, 'Spooring Ban', NULL, NULL, NULL, 'manual', 'service', 'Spooring Ban', '-', 1, 125000, 125000, 0, 0, NULL, NULL, NULL, 0, NULL, 125000, 125000, 125000, 0, 'fixed', 0, '2025-08-18 13:41:51', '2025-08-18 13:41:51'),
(49, 47, 'Spooring Ban', NULL, NULL, NULL, 'manual', 'service', 'Spooring Ban', '-', 1, 150000, 150000, 0, 0, NULL, NULL, NULL, 0, NULL, 150000, 150000, 150000, 0, 'fixed', 0, '2025-08-18 16:18:52', '2025-08-18 16:18:52'),
(50, 47, 'Balancing Ban', NULL, NULL, NULL, 'manual', 'service', 'Balancing Ban', '-', 2, 25000, 25000, 0, 0, NULL, NULL, NULL, 0, NULL, 25000, 50000, 50000, 0, 'fixed', 0, '2025-08-18 16:18:52', '2025-08-18 16:18:52'),
(53, 57, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-19 05:23:41', '2025-08-19 05:23:41'),
(54, 58, 'Spooring Ban', NULL, NULL, NULL, 'manual', 'service', 'Spooring Ban', '-', 1, 125000, 125000, 0, 0, NULL, NULL, NULL, 0, NULL, 125000, 125000, 125000, 0, 'fixed', 0, '2025-08-19 06:17:01', '2025-08-19 06:17:01'),
(55, 56, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-19 06:49:21', '2025-08-19 06:49:21'),
(56, 56, 'Balancing Ban', NULL, NULL, NULL, 'manual', 'service', 'Balancing Ban', '-', 1, 25000, 25000, 0, 0, NULL, NULL, NULL, 0, NULL, 25000, 25000, 25000, 0, 'fixed', 0, '2025-08-19 06:49:21', '2025-08-19 06:49:21'),
(57, 59, 'Ban Bekas GT Radial Savero 235/70 R16 (70%)', NULL, 2, 'Modules\\Product\\Entities\\ProductSecond', 'second', NULL, 'Ban Bekas GT Radial Savero 235/70 R16 (70%)', 'SEC-GT-23570R16-001', 1, 650000, 650000, 0, 0, NULL, NULL, NULL, 400000, NULL, 650000, 650000, 250000, 0, 'fixed', 0, '2025-08-20 13:24:48', '2025-08-20 13:24:48'),
(58, 60, 'Ban Bekas Dunlop AT3 265/65 R17 (80%)', NULL, 1, 'Modules\\Product\\Entities\\ProductSecond', 'second', NULL, 'Ban Bekas Dunlop AT3 265/65 R17 (80%)', 'SEC-DN-26565R17-001', 1, 850000, 850000, 0, 0, NULL, NULL, NULL, 600000, NULL, 850000, 850000, 250000, 0, 'fixed', 0, '2025-08-21 02:08:07', '2025-08-21 02:08:07'),
(59, 61, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-08-23 15:12:31', '2025-08-23 15:12:31'),
(60, 61, 'Spooring Ban', NULL, NULL, NULL, 'manual', 'service', 'Spooring Ban', '-', 1, 125000, 125000, 0, 0, NULL, NULL, NULL, 0, NULL, 125000, 125000, 125000, 0, 'fixed', 0, '2025-08-23 15:12:31', '2025-08-23 15:12:31'),
(61, 62, 'Spooring Ban', NULL, NULL, NULL, 'manual', 'service', 'Spooring Ban', '-', 1, 150000, 150000, 0, 0, NULL, NULL, NULL, 0, NULL, 150000, 150000, 150000, 0, 'fixed', 0, '2025-08-23 15:12:56', '2025-08-23 15:12:56'),
(62, 63, 'Balancing Ban', NULL, NULL, NULL, 'manual', 'service', 'Balancing Ban', '-', 1, 50000, 50000, 0, 0, NULL, NULL, NULL, 0, NULL, 50000, 50000, 50000, 0, 'fixed', 0, '2025-08-23 15:13:36', '2025-08-23 15:13:36'),
(63, 64, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 14250, 14250, 0, 0, NULL, NULL, NULL, 128076, NULL, 14250, 14250, 0, 0, 'fixed', 0, '2025-10-14 08:02:39', '2025-10-14 08:02:39'),
(64, 65, 'Ban GT Radial Champiro Eco 195/65 R15', 4, NULL, NULL, 'new', NULL, 'Ban GT Radial Champiro Eco 195/65 R15', 'GT-CE-19565R15', 1, 8350, 8350, 0, 0, NULL, NULL, NULL, 6400, NULL, 8350, 8350, 1950, 0, 'fixed', 0, '2025-10-14 08:21:22', '2025-10-14 08:21:22'),
(65, 66, 'Ban Bridgestone Ecopia EP150 185/65 R15', 2, NULL, NULL, 'new', NULL, 'Ban Bridgestone Ecopia EP150 185/65 R15', 'BS-EP150-18565R15', 1, 9250, 9250, 0, 0, NULL, NULL, NULL, 7250, NULL, 9250, 9250, 2000, 0, 'fixed', 0, '2025-10-14 08:32:14', '2025-10-14 08:32:14'),
(66, 67, 'Ban GT Radial Champiro Eco 195/65 R15', 4, NULL, NULL, 'new', NULL, 'Ban GT Radial Champiro Eco 195/65 R15', 'GT-CE-19565R15', 1, 835000, 835000, 0, 0, NULL, NULL, NULL, 640000, NULL, 835000, 835000, 195000, 0, 'fixed', 0, '2025-10-21 14:24:18', '2025-10-21 14:24:18'),
(67, 68, 'Ban GT Radial Champiro Eco 195/65 R15', 4, NULL, NULL, 'new', NULL, 'Ban GT Radial Champiro Eco 195/65 R15', 'GT-CE-19565R15', 1, 835000, 835000, 0, 0, NULL, NULL, NULL, 640000, NULL, 835000, 835000, 195000, 0, 'fixed', 0, '2025-10-21 15:41:04', '2025-10-21 15:41:04'),
(68, 69, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 12807600, NULL, 1425000, 1425000, 0, 0, 'fixed', 0, '2025-10-21 15:45:35', '2025-10-21 15:45:35'),
(69, 70, 'Ban Bridgestone Ecopia EP150 185/65 R15', 2, NULL, NULL, 'new', NULL, 'Ban Bridgestone Ecopia EP150 185/65 R15', 'BS-EP150-18565R15', 1, 925000, 925000, 0, 0, NULL, NULL, NULL, 725000, NULL, 925000, 925000, 200000, 0, 'fixed', 0, '2025-10-21 15:46:06', '2025-10-21 15:46:06'),
(70, 71, 'Ban Bridgestone Ecopia EP150 185/65 R15', 2, NULL, NULL, 'new', NULL, 'Ban Bridgestone Ecopia EP150 185/65 R15', 'BS-EP150-18565R15', 1, 925000, 925000, 0, 0, NULL, NULL, NULL, 725000, NULL, 925000, 925000, 200000, 0, 'fixed', 0, '2025-10-21 15:46:39', '2025-10-21 15:46:39'),
(71, 72, 'Ban GT Radial Champiro Eco 195/65 R15', 4, NULL, NULL, 'new', NULL, 'Ban GT Radial Champiro Eco 195/65 R15', 'GT-CE-19565R15', 1, 835000, 835000, 0, 0, NULL, NULL, NULL, 640000, NULL, 835000, 835000, 195000, 0, 'fixed', 0, '2025-10-21 15:47:40', '2025-10-21 15:47:40'),
(72, 73, 'Ban GT Radial Champiro Eco 195/65 R15', 4, NULL, NULL, 'new', NULL, 'Ban GT Radial Champiro Eco 195/65 R15', 'GT-CE-19565R15', 1, 835000, 835000, 0, 0, NULL, NULL, NULL, 640000, NULL, 835000, 835000, 195000, 0, 'fixed', 0, '2025-10-21 16:20:55', '2025-10-21 16:20:55'),
(73, 74, 'Ban Dunlop SP Touring R1 205/65 R16', 3, NULL, NULL, 'new', NULL, 'Ban Dunlop SP Touring R1 205/65 R16', 'DN-SPR1-20565R16', 1, 1090000, 1090000, 0, 0, NULL, NULL, NULL, 890000, NULL, 1090000, 1090000, 200000, 0, 'fixed', 0, '2025-10-22 14:51:59', '2025-10-22 14:51:59'),
(75, 76, 'Ban GT Radial Champiro Eco 195/65 R15', 4, NULL, NULL, 'new', NULL, 'Ban GT Radial Champiro Eco 195/65 R15', 'GT-CE-19565R15', 1, 835000, 835000, 0, 0, NULL, NULL, NULL, 640000, NULL, 835000, 835000, 195000, 0, 'fixed', 0, '2025-10-23 14:30:03', '2025-10-23 14:30:03'),
(76, 77, 'Ban Bridgestone Ecopia EP150 185/65 R15', 2, NULL, NULL, 'new', NULL, 'Ban Bridgestone Ecopia EP150 185/65 R15', 'BS-EP150-18565R15', 1, 925000, 925000, 0, 0, NULL, NULL, NULL, 725000, NULL, 925000, 925000, 200000, 0, 'fixed', 0, '2025-10-27 08:49:47', '2025-10-27 08:49:47'),
(77, 78, 'Ban GT Radial Champiro Eco 195/65 R15', 4, NULL, NULL, 'new', NULL, 'Ban GT Radial Champiro Eco 195/65 R15', 'GT-CE-19565R15', 1, 835000, 835000, 0, 0, NULL, NULL, NULL, 640000, NULL, 835000, 835000, 195000, 0, 'fixed', 0, '2025-10-27 08:58:08', '2025-10-27 08:58:08'),
(78, 79, 'Ban GT Radial Champiro Eco 195/65 R15', 4, NULL, NULL, 'new', NULL, 'Ban GT Radial Champiro Eco 195/65 R15', 'GT-CE-19565R15', 1, 835000, 835000, 0, 0, NULL, NULL, NULL, 640000, NULL, 835000, 835000, 195000, 0, 'fixed', 0, '2025-10-27 08:58:22', '2025-10-27 08:58:22'),
(79, 80, 'Ban GT Radial Champiro Eco 195/65 R15', 4, NULL, NULL, 'new', NULL, 'Ban GT Radial Champiro Eco 195/65 R15', 'GT-CE-19565R15', 1, 835000, 835000, 0, 0, NULL, NULL, NULL, 640000, NULL, 835000, 835000, 195000, 0, 'fixed', 0, '2025-10-27 09:34:00', '2025-10-27 09:34:00'),
(80, 81, 'Ban Dunlop SP Touring R1 205/65 R16', 3, NULL, NULL, 'new', NULL, 'Ban Dunlop SP Touring R1 205/65 R16', 'DN-SPR1-20565R16', 1, 1090000, 1090000, 0, 0, NULL, NULL, NULL, 890000, NULL, 1090000, 1090000, 200000, 0, 'fixed', 0, '2025-10-27 09:47:29', '2025-10-27 09:47:29'),
(81, 82, 'Ban Bridgestone Ecopia EP150 185/65 R15', 2, NULL, NULL, 'new', NULL, 'Ban Bridgestone Ecopia EP150 185/65 R15', 'BS-EP150-18565R15', 1, 925000, 925000, 0, 0, NULL, NULL, NULL, 725000, NULL, 925000, 925000, 200000, 0, 'fixed', 0, '2025-10-27 09:48:34', '2025-10-27 09:48:34'),
(82, 83, 'Ban Bridgestone Ecopia EP150 185/65 R15', 2, NULL, NULL, 'new', NULL, 'Ban Bridgestone Ecopia EP150 185/65 R15', 'BS-EP150-18565R15', 1, 925000, 925000, 0, 0, NULL, NULL, NULL, 725000, NULL, 925000, 925000, 200000, 0, 'fixed', 0, '2025-10-27 09:49:18', '2025-10-27 09:49:18'),
(83, 84, 'Ban GT Savero', 1, NULL, NULL, 'new', NULL, 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1425000, 0, 0, NULL, NULL, NULL, 1280760, NULL, 1425000, 1425000, 144240, 0, 'fixed', 0, '2025-10-27 09:49:39', '2025-10-27 09:49:39'),
(84, 85, 'Velg Bekas OEM Ertiga Ring 15', NULL, 4, 'Modules\\Product\\Entities\\ProductSecond', 'second', NULL, 'Velg Bekas OEM Ertiga Ring 15', 'SEC-OEM-ERT-R15-001', 1, 1200000, NULL, 0, 0, NULL, NULL, NULL, 1000000, NULL, 1200000, 1200000, 200000, 0, 'fixed', 0, '2025-10-27 12:50:55', '2025-10-27 12:50:55');

--
-- Triggers `sale_details`
--
DELIMITER $$
CREATE TRIGGER `trg_sale_details_chk_bi` BEFORE INSERT ON `sale_details` FOR EACH ROW BEGIN
  SET NEW.source_type = LOWER(NEW.source_type);

  IF NEW.source_type NOT IN ('new','second','manual') THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'source_type invalid';
  END IF;

  IF NEW.price IS NULL OR NEW.price < 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'price wajib diisi dan >= 0';
  END IF;

  IF NEW.source_type = 'new' THEN
    IF NEW.product_id IS NULL OR NEW.product_id = 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk Baru: product_id wajib';
    END IF;
    IF NEW.quantity IS NULL OR NEW.quantity < 1 THEN
      SET NEW.quantity = 1;
    END IF;
    SET NEW.productable_id = NULL;
    SET NEW.manual_kind = NULL;

  ELSEIF NEW.source_type = 'second' THEN
    SET NEW.quantity = 1;
    IF NEW.productable_id IS NULL OR NEW.productable_id = 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk Second: productable_id wajib';
    END IF;
    IF NOT EXISTS(
      SELECT 1 FROM product_seconds ps
      WHERE ps.id = NEW.productable_id AND ps.status = 'available'
    ) THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk Second: unit tidak available';
    END IF;
    SET NEW.product_id = NULL;
    SET NEW.manual_kind = NULL;

  ELSE -- manual
    SET NEW.quantity = 1;
    SET NEW.product_id = NULL;
    SET NEW.productable_id = NULL;

    IF NEW.manual_kind IS NULL OR NEW.manual_kind NOT IN ('service','goods') THEN
      SET NEW.manual_kind = 'goods';
    END IF;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_sale_details_chk_bu` BEFORE UPDATE ON `sale_details` FOR EACH ROW BEGIN
  SET NEW.source_type = LOWER(NEW.source_type);

  IF NEW.source_type NOT IN ('new','second','manual') THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'source_type invalid (UPDATE)';
  END IF;

  IF NEW.price IS NULL OR NEW.price < 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'price wajib diisi dan >= 0 (UPDATE)';
  END IF;

  IF NEW.source_type = 'new' THEN
    IF NEW.product_id IS NULL OR NEW.product_id = 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk Baru: product_id wajib (UPDATE)';
    END IF;
    IF NEW.quantity IS NULL OR NEW.quantity < 1 THEN
      SET NEW.quantity = 1;
    END IF;
    SET NEW.productable_id = NULL;
    SET NEW.manual_kind = NULL;

  ELSEIF NEW.source_type = 'second' THEN
    SET NEW.quantity = 1;
    IF NEW.productable_id IS NULL OR NEW.productable_id = 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk Second: productable_id wajib (UPDATE)';
    END IF;
    IF NEW.productable_id <> OLD.productable_id THEN
      IF NOT EXISTS(
        SELECT 1 FROM product_seconds ps
        WHERE ps.id = NEW.productable_id AND ps.status = 'available'
      ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Produk Second: unit baru tidak available (UPDATE)';
      END IF;
    END IF;
    SET NEW.product_id = NULL;
    SET NEW.manual_kind = NULL;

  ELSE -- manual
    SET NEW.quantity = 1;
    SET NEW.product_id = NULL;
    SET NEW.productable_id = NULL;

    IF NEW.manual_kind IS NULL OR NEW.manual_kind NOT IN ('service','goods') THEN
      SET NEW.manual_kind = 'goods';
    END IF;
  END IF;
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
  DECLARE v_qty INT;

  SELECT s.user_id, s.reference INTO v_user_id, v_ref
  FROM sales s WHERE s.id = NEW.sale_id;

  IF NEW.source_type = 'new' THEN
    UPDATE products
      SET product_quantity = product_quantity - NEW.quantity
    WHERE id = NEW.product_id;

    SELECT product_quantity INTO v_qty FROM products WHERE id = NEW.product_id;
    IF v_qty < 0 THEN
      UPDATE products
        SET product_quantity = product_quantity + NEW.quantity
      WHERE id = NEW.product_id;
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok produk tidak mencukupi';
    END IF;

    INSERT INTO stock_movements
      (productable_type, productable_id, `type`, quantity, description, user_id, created_at, updated_at)
    VALUES
      ('Modules\Product\Entities\Product', NEW.product_id, 'out', NEW.quantity,
       CONCAT('Sale ', IFNULL(v_ref, NEW.sale_id)), v_user_id, NOW(), NOW());

  ELSEIF NEW.source_type = 'second' THEN
    UPDATE product_seconds
      SET status = 'sold'
    WHERE id = NEW.productable_id AND status = 'available';

    IF ROW_COUNT() = 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Unit second tidak available saat insert';
    END IF;

    INSERT INTO stock_movements
      (productable_type, productable_id, `type`, quantity, description, user_id, created_at, updated_at)
    VALUES
      ('Modules\Product\Entities\ProductSecond', NEW.productable_id, 'out', 1,
       CONCAT('Sale ', IFNULL(v_ref, NEW.sale_id)), v_user_id, NOW(), NOW());
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_sd_inv_au` AFTER UPDATE ON `sale_details` FOR EACH ROW BEGIN
  DECLARE v_user_id BIGINT UNSIGNED;
  DECLARE v_ref VARCHAR(191);
  DECLARE v_qty INT;
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

    -- Revert OLD
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

    -- Apply NEW
    IF NEW.source_type = 'new' THEN
      UPDATE products
        SET product_quantity = product_quantity - NEW.quantity
      WHERE id = NEW.product_id;

      SELECT product_quantity INTO v_qty FROM products WHERE id = NEW.product_id;
      IF v_qty < 0 THEN
        UPDATE products
          SET product_quantity = product_quantity + NEW.quantity
        WHERE id = NEW.product_id;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok tidak cukup setelah UPDATE';
      END IF;

      INSERT INTO stock_movements
        (productable_type, productable_id, `type`, quantity, description, user_id, created_at, updated_at)
      VALUES
        ('Modules\Product\Entities\Product', NEW.product_id, 'out', NEW.quantity,
         CONCAT('Adjust sale ', IFNULL(v_ref, NEW.sale_id)), v_user_id, NOW(), NOW());

    ELSEIF NEW.source_type = 'second' THEN
      UPDATE product_seconds
        SET status = 'sold'
      WHERE id = NEW.productable_id AND status = 'available';

      IF ROW_COUNT() = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Unit second tidak available saat UPDATE';
      END IF;

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
(1, 23, 1425000, '2025-08-09', 'INV/SL-00023/PMT-214346', 'Tunai', NULL, NULL, '2025-08-09 14:43:46', '2025-08-09 14:43:46', NULL),
(6, 45, 1425000, '2025-08-10', 'INV/SL-00045', 'Tunai', NULL, NULL, '2025-08-10 06:11:56', '2025-08-10 06:11:56', NULL),
(7, 47, 150000, '2025-08-10', 'INV/SL-00047', 'Tunai', NULL, NULL, '2025-08-10 06:19:53', '2025-08-10 06:19:53', NULL),
(8, 52, 1425000, '2025-08-10', 'INV/SL-00052', 'Tunai', NULL, NULL, '2025-08-10 06:34:17', '2025-08-10 06:34:17', NULL),
(9, 53, 1000, '2025-08-12', 'INV/SL-00053', 'Tunai', NULL, NULL, '2025-08-12 04:14:41', '2025-08-12 04:14:41', NULL),
(10, 55, 2275000, '2025-08-17', 'INV/SL-00055', 'Transfer', NULL, NULL, '2025-08-17 05:24:47', '2025-08-17 05:24:47', NULL),
(11, 55, 125000, '2025-08-18', 'ADJ-20250818161158', 'Tunai', NULL, 'Penyesuaian pembayaran saat edit', '2025-08-18 09:11:58', '2025-08-18 09:11:58', NULL),
(12, 47, 148500, '2025-08-18', 'SP-20250818-230945-SFXCN', 'Tunai', NULL, 'Penyesuaian saat edit (refund)', '2025-08-18 16:09:45', '2025-08-18 16:09:45', NULL),
(13, 47, 50000, '2025-08-18', 'SP-20250818-231852-GTOSJ', 'Tunai', NULL, 'Penyesuaian saat edit (+)', '2025-08-18 16:18:52', '2025-08-18 16:18:52', NULL),
(14, 58, 125000, '2025-08-19', 'INV/SL-00058', 'Tunai', NULL, NULL, '2025-08-19 06:17:47', '2025-08-19 06:17:47', NULL),
(15, 56, 1450000, '2025-08-19', 'SP-20250819-134921-LYGUG', 'Tunai', NULL, 'Penyesuaian saat edit (+)', '2025-08-19 06:49:21', '2025-08-19 06:49:21', NULL),
(16, 57, 1425000, '2025-08-20', 'INV/SL-00057/PMT-170707', 'Transfer', 'Mandiri', NULL, '2025-08-20 10:07:07', '2025-08-20 10:07:07', NULL),
(17, 54, 1425000, '2025-08-20', 'SP-00017', 'Tunai', NULL, 'Pelunasan', '2025-08-20 11:32:24', '2025-08-20 11:32:24', NULL),
(18, 37, 1425000, '2025-08-20', 'SP-00018', 'Tunai', NULL, NULL, '2025-08-20 11:32:45', '2025-08-20 11:32:45', NULL),
(19, 46, 150000, '2025-08-20', 'SP-00019', 'Tunai', NULL, 'Pelunasan', '2025-08-20 11:32:59', '2025-08-20 11:32:59', NULL),
(20, 48, 1425000, '2025-08-20', 'SP-00020', 'Transfer', 'Mandiri', NULL, '2025-08-20 12:18:55', '2025-08-20 12:18:55', NULL),
(21, 49, 1425000, '2025-08-20', 'SP-00021', 'Transfer', 'BCA', NULL, '2025-08-20 12:27:33', '2025-08-20 12:27:33', NULL),
(22, 50, 1425000, '2025-08-20', 'SP-00022', 'QRIS', 'BCA', NULL, '2025-08-20 12:27:59', '2025-08-20 12:27:59', NULL),
(23, 59, 650000, '2025-08-20', 'INV/SL-20250820-202448-68a5dab0793ff', 'Transfer', 'BCA', NULL, '2025-08-20 13:25:00', '2025-08-20 13:25:00', NULL),
(24, 60, 850000, '2025-08-21', 'INV/OB2-00060', 'Tunai', NULL, NULL, '2025-08-21 02:08:13', '2025-08-21 02:08:13', NULL),
(25, 51, 1425000, '2025-08-21', 'SP-00025', 'Tunai', NULL, NULL, '2025-08-21 04:20:36', '2025-08-21 04:20:36', NULL),
(26, 61, 1550000, '2025-08-23', 'INV/OB2-00061', 'Tunai', NULL, NULL, '2025-08-23 15:12:37', '2025-08-23 15:12:37', NULL),
(27, 62, 150000, '2025-08-23', 'INV/OB2-00062', 'Tunai', NULL, NULL, '2025-08-23 15:12:59', '2025-08-23 15:12:59', NULL),
(28, 63, 50000, '2025-08-23', 'INV/OB2-00063', 'Transfer', NULL, 'BCA', '2025-08-23 15:13:49', '2025-08-23 15:13:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sale_returns`
--

CREATE TABLE `sale_returns` (
  `id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_percentage` int NOT NULL DEFAULT '0',
  `tax_amount` bigint NOT NULL DEFAULT '0',
  `discount_percentage` int NOT NULL DEFAULT '0',
  `discount_amount` bigint NOT NULL DEFAULT '0',
  `shipping_amount` bigint NOT NULL DEFAULT '0',
  `total_amount` bigint NOT NULL,
  `paid_amount` bigint NOT NULL,
  `due_amount` bigint NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_return_details`
--

CREATE TABLE `sale_return_details` (
  `id` bigint UNSIGNED NOT NULL,
  `sale_return_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `price` bigint UNSIGNED DEFAULT NULL,
  `unit_price` bigint NOT NULL,
  `sub_total` bigint UNSIGNED DEFAULT NULL,
  `product_discount_amount` bigint UNSIGNED DEFAULT NULL,
  `product_discount_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `product_tax_amount` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_return_payments`
--

CREATE TABLE `sale_return_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `sale_return_id` bigint UNSIGNED NOT NULL,
  `amount` bigint NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(11, NULL, NULL, 2, 5, 'adjustment', 'out', 3, 'Adjustment ADJ-20251102-00001 - sub', 2, '2025-11-02 10:18:26', '2025-11-02 10:18:26', NULL);

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
(2, 'Vincent Peter', 'peter@gmail.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWWG/igi', NULL, NULL, NULL, 1, '5hZ3CWkyl8cCit1tNc7qtR0WHhW2RiXxgg7HiCAYa3BaDy5mOTE0VmQZ00AY', '2025-09-30 13:32:44', '2025-09-30 13:32:44', NULL),
(3, 'Budi (Owner)', 'budi.owner@omahban.test', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '08123456789', NULL, NULL, 1, NULL, '2025-11-01 04:20:39', '2025-11-01 04:20:39', NULL),
(4, 'Siti (Supervisor)', 'siti.sup@omahban.test', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '08198765432', NULL, NULL, 1, NULL, '2025-11-01 04:20:39', '2025-11-01 04:20:39', NULL),
(5, 'Ani (Kasir 1)', 'ani.kasir@omahban.test', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '08112233445', NULL, NULL, 1, NULL, '2025-11-01 04:20:39', '2025-11-01 04:20:39', NULL),
(6, 'Rina (Kasir 2)', 'rina.kasir@omahban.test', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '08556677889', NULL, NULL, 1, NULL, '2025-11-01 04:20:39', '2025-11-01 04:20:39', NULL);

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
,`approver_name` varchar(255)
,`date` date
,`last_activity` timestamp
,`reason` enum('Rusak','Hilang','Kadaluarsa','Lainnya')
,`reference` varchar(255)
,`requester_name` varchar(255)
,`status` enum('pending','approved','rejected')
,`total_items` decimal(32,0)
,`total_products` bigint
,`total_value` decimal(10,2)
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
  ADD KEY `deleted_at` (`deleted_at`);

--
-- Indexes for table `adjustments`
--
ALTER TABLE `adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approver_id` (`approver_id`),
  ADD KEY `idx_adjustments_status` (`status`),
  ADD KEY `idx_adjustments_user` (`requester_id`,`created_at`),
  ADD KEY `idx_status_requester` (`status`,`requester_id`),
  ADD KEY `idx_adjustments_date_status` (`date`,`status`),
  ADD KEY `deleted_at` (`deleted_at`);

--
-- Indexes for table `adjustment_files`
--
ALTER TABLE `adjustment_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `adjustment_id` (`adjustment_id`);

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
  ADD PRIMARY KEY (`id`);

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
  ADD UNIQUE KEY `products_product_code_unique` (`product_code`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_brand_id_foreign` (`brand_id`),
  ADD KEY `idx_products_year` (`product_year`);
ALTER TABLE `products` ADD FULLTEXT KEY `ft_products_search` (`product_name`,`product_code`,`product_size`,`ring`);

--
-- Indexes for table `product_seconds`
--
ALTER TABLE `product_seconds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_seconds_unique_code_unique` (`unique_code`),
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
  ADD KEY `idx_purchases_date` (`date`);

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
-- Indexes for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_returns_reference_unique` (`reference`),
  ADD KEY `purchase_returns_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `purchase_return_details`
--
ALTER TABLE `purchase_return_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_return_details_purchase_return_id_foreign` (`purchase_return_id`),
  ADD KEY `purchase_return_details_product_id_foreign` (`product_id`);

--
-- Indexes for table `purchase_return_payments`
--
ALTER TABLE `purchase_return_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_return_payments_purchase_return_id_foreign` (`purchase_return_id`);

--
-- Indexes for table `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quotations_reference_unique` (`reference`),
  ADD KEY `quotations_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `quotation_details`
--
ALTER TABLE `quotation_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quotation_details_quotation_id_foreign` (`quotation_id`),
  ADD KEY `quotation_details_product_id_foreign` (`product_id`);

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
  ADD UNIQUE KEY `sales_reference_unique` (`reference`),
  ADD KEY `sales_user_id_foreign` (`user_id`),
  ADD KEY `idx_sales_date` (`date`),
  ADD KEY `idx_sales_status` (`status`),
  ADD KEY `idx_sales_payment_status` (`payment_status`),
  ADD KEY `sales_date_user_status_payment_idx` (`date`,`user_id`,`status`,`payment_status`),
  ADD KEY `sales_user_date_index` (`user_id`,`date`),
  ADD KEY `idx_sales_has_adjustment` (`has_price_adjustment`,`date`);

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
-- Indexes for table `sale_returns`
--
ALTER TABLE `sale_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sale_returns_reference_unique` (`reference`),
  ADD KEY `sale_returns_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `sale_return_details`
--
ALTER TABLE `sale_return_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_return_details_sale_return_id_foreign` (`sale_return_id`),
  ADD KEY `sale_return_details_product_id_foreign` (`product_id`),
  ADD KEY `srd_return_product_index` (`sale_return_id`,`product_id`);

--
-- Indexes for table `sale_return_payments`
--
ALTER TABLE `sale_return_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_return_payments_sale_return_id_foreign` (`sale_return_id`);

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
  ADD KEY `idx_ref_type_id` (`ref_type`,`ref_id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `deleted_at` (`deleted_at`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `adjustments`
--
ALTER TABLE `adjustments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `adjustment_files`
--
ALTER TABLE `adjustment_files`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `adjustment_logs`
--
ALTER TABLE `adjustment_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

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
-- AUTO_INCREMENT for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_return_details`
--
ALTER TABLE `purchase_return_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_return_payments`
--
ALTER TABLE `purchase_return_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotation_details`
--
ALTER TABLE `quotation_details`
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
-- AUTO_INCREMENT for table `sale_returns`
--
ALTER TABLE `sale_returns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_return_details`
--
ALTER TABLE `sale_return_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_return_payments`
--
ALTER TABLE `sale_return_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY INVOKER VIEW `categories_view`  AS SELECT `categories`.`id` AS `id`, `categories`.`category_code` AS `category_code`, `categories`.`category_name` AS `name`, `categories`.`created_at` AS `created_at`, `categories`.`updated_at` AS `updated_at` FROM `categories` ;

-- --------------------------------------------------------

--
-- Structure for view `v_adjustment_summary`
--
DROP TABLE IF EXISTS `v_adjustment_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_adjustment_summary`  AS SELECT `a`.`id` AS `adjustment_id`, `a`.`reference` AS `reference`, `a`.`date` AS `date`, `a`.`status` AS `status`, `a`.`reason` AS `reason`, `a`.`total_value` AS `total_value`, `u1`.`name` AS `requester_name`, `u2`.`name` AS `approver_name`, count(`ap`.`id`) AS `total_products`, sum(`ap`.`quantity`) AS `total_items`, max(`al`.`created_at`) AS `last_activity` FROM ((((`adjustments` `a` left join `adjusted_products` `ap` on((`a`.`id` = `ap`.`adjustment_id`))) left join `adjustment_logs` `al` on((`a`.`id` = `al`.`adjustment_id`))) left join `users` `u1` on((`a`.`requester_id` = `u1`.`id`))) left join `users` `u2` on((`a`.`approver_id` = `u2`.`id`))) GROUP BY `a`.`id`, `a`.`reference`, `a`.`date`, `a`.`status`, `a`.`reason`, `a`.`total_value`, `u1`.`name`, `u2`.`name` ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adjusted_products`
--
ALTER TABLE `adjusted_products`
  ADD CONSTRAINT `adjusted_products_adjustment_id_foreign` FOREIGN KEY (`adjustment_id`) REFERENCES `adjustments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `adjusted_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `adjustments`
--
ALTER TABLE `adjustments`
  ADD CONSTRAINT `adjustments_ibfk_1` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `adjustments_ibfk_2` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `adjustment_files`
--
ALTER TABLE `adjustment_files`
  ADD CONSTRAINT `adjustment_files_ibfk_1` FOREIGN KEY (`adjustment_id`) REFERENCES `adjustments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `adjustment_logs`
--
ALTER TABLE `adjustment_logs`
  ADD CONSTRAINT `fk_al_adj` FOREIGN KEY (`adjustment_id`) REFERENCES `adjustments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_al_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `product_seconds`
--
ALTER TABLE `product_seconds`
  ADD CONSTRAINT `product_seconds_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `product_seconds_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_details`
--
ALTER TABLE `purchase_details`
  ADD CONSTRAINT `purchase_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_details_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_payments`
--
ALTER TABLE `purchase_payments`
  ADD CONSTRAINT `purchase_payments_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD CONSTRAINT `purchase_returns_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_return_details`
--
ALTER TABLE `purchase_return_details`
  ADD CONSTRAINT `purchase_return_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_return_details_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_return_payments`
--
ALTER TABLE `purchase_return_payments`
  ADD CONSTRAINT `purchase_return_payments_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quotations`
--
ALTER TABLE `quotations`
  ADD CONSTRAINT `quotations_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `quotation_details`
--
ALTER TABLE `quotation_details`
  ADD CONSTRAINT `quotation_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `quotation_details_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sale_details`
--
ALTER TABLE `sale_details`
  ADD CONSTRAINT `fk_sd_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_sd_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_details_adjusted_by_foreign` FOREIGN KEY (`adjusted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sale_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sale_details_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_payments`
--
ALTER TABLE `sale_payments`
  ADD CONSTRAINT `fk_sp_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_payments_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_returns`
--
ALTER TABLE `sale_returns`
  ADD CONSTRAINT `sale_returns_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sale_return_details`
--
ALTER TABLE `sale_return_details`
  ADD CONSTRAINT `sale_return_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sale_return_details_sale_return_id_foreign` FOREIGN KEY (`sale_return_id`) REFERENCES `sale_returns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_return_payments`
--
ALTER TABLE `sale_return_payments`
  ADD CONSTRAINT `sale_return_payments_sale_return_id_foreign` FOREIGN KEY (`sale_return_id`) REFERENCES `sale_returns` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  ADD CONSTRAINT `fk_user_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
