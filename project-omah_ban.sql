-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 09, 2025 at 04:19 PM
-- Server version: 8.0.30
-- PHP Version: 8.2.26

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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `adjusted_products`
--

INSERT INTO `adjusted_products` (`id`, `adjustment_id`, `product_id`, `quantity`, `type`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 5, 'add', '2025-08-07 12:42:27', '2025-08-07 12:42:27');

-- --------------------------------------------------------

--
-- Table structure for table `adjustments`
--

CREATE TABLE `adjustments` (
  `id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `adjustments`
--

INSERT INTO `adjustments` (`id`, `date`, `reference`, `note`, `created_at`, `updated_at`) VALUES
(1, '2025-08-07', 'ADJ-00001', 'Ambil Dari SH', '2025-08-07 12:42:27', '2025-08-07 12:42:27');

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
(1, 'GT Radial', '2025-08-06 01:21:23', '2025-08-06 01:21:23');

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
(3, 'VELG', 'Velg Mobil', '2025-08-06 12:13:58', '2025-08-06 12:13:58'),
(4, 'SPR_BAN', 'Spooring Ban', '2025-08-06 12:14:08', '2025-08-06 12:16:27'),
(5, 'BLN_BAN', 'Balancing Ban', '2025-08-06 12:16:51', '2025-08-06 12:16:51');

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
(1, 'Rupiah', 'IDR', 'Rp', '.', ',', NULL, '2025-08-05 14:46:12', '2025-08-06 01:40:41');

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
  `amount` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
(49, '2025_08_09_213452_alter_money_columns_to_bigint_on_sales_tables', 15);

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
(2, 'App\\Models\\User', 1);

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
(1, 'edit_own_profile', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(2, 'access_user_management', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(3, 'show_total_stats', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(4, 'show_month_overview', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(5, 'show_weekly_sales_purchases', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(6, 'show_monthly_cashflow', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(7, 'show_notifications', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(8, 'access_products', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(9, 'create_products', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(10, 'show_products', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(11, 'edit_products', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(12, 'delete_products', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(13, 'access_product_categories', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(14, 'print_barcodes', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(15, 'access_adjustments', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(16, 'create_adjustments', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(17, 'show_adjustments', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(18, 'edit_adjustments', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(19, 'delete_adjustments', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(20, 'access_quotations', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(21, 'create_quotations', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(22, 'show_quotations', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(23, 'edit_quotations', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(24, 'delete_quotations', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(25, 'create_quotation_sales', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(26, 'send_quotation_mails', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(27, 'access_expenses', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(28, 'create_expenses', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(29, 'edit_expenses', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(30, 'delete_expenses', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(31, 'access_expense_categories', 'web', '2025-08-05 14:46:10', '2025-08-05 14:46:10'),
(32, 'access_customers', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(33, 'create_customers', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(34, 'show_customers', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(35, 'edit_customers', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(36, 'delete_customers', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(37, 'access_suppliers', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(38, 'create_suppliers', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(39, 'show_suppliers', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(40, 'edit_suppliers', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(41, 'delete_suppliers', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(42, 'access_sales', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(43, 'create_sales', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(44, 'show_sales', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(45, 'edit_sales', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(46, 'delete_sales', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(47, 'create_pos_sales', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(48, 'access_sale_payments', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(49, 'access_sale_returns', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(50, 'create_sale_returns', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(51, 'show_sale_returns', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(52, 'edit_sale_returns', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(53, 'delete_sale_returns', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(54, 'access_sale_return_payments', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(55, 'access_purchases', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(56, 'create_purchases', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(57, 'show_purchases', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(58, 'edit_purchases', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(59, 'delete_purchases', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(60, 'access_purchase_payments', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(61, 'access_purchase_returns', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(62, 'create_purchase_returns', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(63, 'show_purchase_returns', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(64, 'edit_purchase_returns', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(65, 'delete_purchase_returns', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(66, 'access_purchase_return_payments', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(67, 'access_reports', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(68, 'access_currencies', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(69, 'create_currencies', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(70, 'edit_currencies', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(71, 'delete_currencies', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(72, 'access_settings', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11'),
(73, 'access_units', 'web', '2025-08-05 14:46:11', '2025-08-05 14:46:11');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
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
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `product_size` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ring` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_year` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `product_name`, `product_code`, `product_quantity`, `stok_awal`, `product_cost`, `product_price`, `product_unit`, `product_stock_alert`, `product_order_tax`, `product_tax_type`, `product_note`, `created_at`, `updated_at`, `brand_id`, `product_size`, `ring`, `product_year`) VALUES
(1, 2, 'Ban GT Savero', 'GT_Savero', 15, 5, 128076000, 142500000, 'PC', 2, NULL, NULL, NULL, '2025-08-06 02:17:51', '2025-08-07 12:42:27', 1, '31x10,5', '15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_seconds`
--

CREATE TABLE `product_seconds` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `unique_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `condition_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `purchase_price` decimal(15,2) NOT NULL,
  `selling_price` decimal(15,2) NOT NULL,
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
  `tax_percentage` int NOT NULL DEFAULT '0',
  `tax_amount` int NOT NULL DEFAULT '0',
  `discount_percentage` int NOT NULL DEFAULT '0',
  `discount_amount` int NOT NULL DEFAULT '0',
  `shipping_amount` int NOT NULL DEFAULT '0',
  `total_amount` int NOT NULL,
  `paid_amount` int NOT NULL,
  `due_amount` int NOT NULL,
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
  `price` int NOT NULL,
  `unit_price` int NOT NULL,
  `sub_total` int NOT NULL,
  `product_discount_amount` int NOT NULL,
  `product_discount_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `product_tax_amount` int NOT NULL,
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
  `amount` int NOT NULL,
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
  `tax_amount` int NOT NULL DEFAULT '0',
  `discount_percentage` int NOT NULL DEFAULT '0',
  `discount_amount` int NOT NULL DEFAULT '0',
  `shipping_amount` int NOT NULL DEFAULT '0',
  `total_amount` int NOT NULL,
  `paid_amount` int NOT NULL,
  `due_amount` int NOT NULL,
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
  `price` int NOT NULL,
  `unit_price` int NOT NULL,
  `sub_total` int NOT NULL,
  `product_discount_amount` int NOT NULL,
  `product_discount_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `product_tax_amount` int NOT NULL,
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
  `amount` int NOT NULL,
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
  `tax_amount` int NOT NULL DEFAULT '0',
  `discount_percentage` int NOT NULL DEFAULT '0',
  `discount_amount` int NOT NULL DEFAULT '0',
  `shipping_amount` int NOT NULL DEFAULT '0',
  `total_amount` int NOT NULL,
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
  `price` int NOT NULL,
  `unit_price` int NOT NULL,
  `sub_total` int NOT NULL,
  `product_discount_amount` int NOT NULL,
  `product_discount_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `product_tax_amount` int NOT NULL,
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
(2, 'Super Admin', 'web', '2025-08-05 14:46:12', '2025-08-05 14:46:12');

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
(1, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_percentage` int NOT NULL DEFAULT '0',
  `tax_amount` decimal(18,2) DEFAULT NULL,
  `discount_percentage` int NOT NULL DEFAULT '0',
  `discount_amount` decimal(18,2) DEFAULT NULL,
  `shipping_amount` decimal(18,2) DEFAULT NULL,
  `total_amount` decimal(18,2) DEFAULT NULL,
  `total_hpp` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_profit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(18,2) DEFAULT NULL,
  `due_amount` decimal(18,2) DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `date`, `reference`, `tax_percentage`, `tax_amount`, `discount_percentage`, `discount_amount`, `shipping_amount`, `total_amount`, `total_hpp`, `total_profit`, `paid_amount`, `due_amount`, `status`, `payment_status`, `payment_method`, `bank_name`, `note`, `created_at`, `updated_at`) VALUES
(4, '2025-08-08', 'SL-00001', 0, 0.00, 0, 0.00, 0.00, 1425000.00, 0.00, 14250.00, 0.00, 1425000.00, 'Completed', 'Unpaid', 'Tunai', NULL, NULL, '2025-08-08 08:15:20', '2025-08-08 08:15:20'),
(19, '2025-08-09', 'SL-00005', 0, 0.00, 0, 0.00, 0.00, 1425000.00, 0.00, 0.00, 0.00, 1425000.00, 'Draft', 'Unpaid', 'Tunai', NULL, NULL, '2025-08-09 13:38:27', '2025-08-09 13:38:27'),
(20, '2025-08-09', 'SL-00020', 0, 0.00, 0, 0.00, 0.00, 1575000.00, 0.00, 0.00, 0.00, 1575000.00, 'Draft', 'Unpaid', 'Tunai', NULL, NULL, '2025-08-09 13:56:29', '2025-08-09 13:56:29'),
(21, '2025-08-09', 'SL-00021', 0, 0.00, 0, 0.00, 0.00, 142500000.00, 0.00, 0.00, 0.00, 142500000.00, 'Draft', 'Unpaid', 'Tunai', NULL, NULL, '2025-08-09 14:02:55', '2025-08-09 14:02:55'),
(22, '2025-08-09', 'SL-00022', 0, 0.00, 0, 0.00, 0.00, 582500000.00, 0.00, 0.00, 0.00, 582500000.00, 'Draft', 'Unpaid', 'Tunai', NULL, NULL, '2025-08-09 14:37:09', '2025-08-09 14:37:09'),
(23, '2025-08-09', 'SL-00023', 0, 0.00, 0, 0.00, 0.00, 142500000.00, 0.00, 0.00, 142500000.00, 0.00, 'Completed', 'Paid', 'Tunai', NULL, NULL, '2025-08-09 14:43:36', '2025-08-09 14:43:46'),
(24, '2025-08-09', 'SL-00024', 0, 0.00, 0, 0.00, 0.00, 142500000.00, 0.00, 0.00, 0.00, 142500000.00, 'Draft', 'Unpaid', 'Tunai', NULL, NULL, '2025-08-09 14:44:29', '2025-08-09 14:44:29'),
(25, '2025-08-09', 'SL-00025', 0, 0.00, 0, 0.00, 0.00, 142500000.00, 0.00, 0.00, 0.00, 142500000.00, 'Draft', 'Unpaid', 'Tunai', NULL, NULL, '2025-08-09 14:52:26', '2025-08-09 14:52:26'),
(26, '2025-08-09', 'SL-00026', 0, 0.00, 0, 0.00, 0.00, 15000000.00, 0.00, 0.00, 0.00, 15000000.00, 'Draft', 'Unpaid', 'Tunai', NULL, NULL, '2025-08-09 14:52:54', '2025-08-09 14:52:54'),
(27, '2025-08-09', 'SL-00027', 0, 0.00, 0, 0.00, 0.00, 142500000.00, 0.00, 0.00, 0.00, 142500000.00, 'Draft', 'Unpaid', 'Tunai', NULL, NULL, '2025-08-09 14:55:16', '2025-08-09 14:55:16'),
(28, '2025-08-09', 'SL-00028', 0, 0.00, 0, 0.00, 0.00, 142500000.00, 0.00, 0.00, 0.00, 142500000.00, 'Draft', 'Unpaid', 'Tunai', NULL, NULL, '2025-08-09 14:55:44', '2025-08-09 14:55:44'),
(29, '2025-08-09', 'SL-00029', 0, 0.00, 0, 0.00, 0.00, 142500000.00, 0.00, 0.00, 0.00, 142500000.00, 'Draft', 'Unpaid', 'Tunai', NULL, NULL, '2025-08-09 14:56:11', '2025-08-09 14:56:11'),
(30, '2025-08-09', 'SL-00030', 0, 0.00, 0, 0.00, 0.00, 142500000.00, 0.00, 0.00, 0.00, 142500000.00, 'Draft', 'Unpaid', 'Tunai', NULL, NULL, '2025-08-09 15:04:34', '2025-08-09 15:04:34'),
(31, '2025-08-09', 'SL-00031', 0, 0.00, 0, 0.00, 0.00, 142500000.00, 0.00, 0.00, 0.00, 142500000.00, 'Draft', 'Unpaid', 'Tunai', NULL, NULL, '2025-08-09 15:17:00', '2025-08-09 15:17:00'),
(32, '2025-08-09', 'SL-00032', 0, 0.00, 0, 0.00, 0.00, 142500000.00, 0.00, 0.00, 0.00, 142500000.00, 'Draft', 'Unpaid', 'Tunai', NULL, NULL, '2025-08-09 15:17:49', '2025-08-09 15:17:49'),
(33, '2025-08-09', 'SL-00033', 0, 0.00, 0, 0.00, 0.00, 142500000.00, 0.00, 0.00, 0.00, 142500000.00, 'Draft', 'Unpaid', 'Tunai', NULL, NULL, '2025-08-09 15:18:22', '2025-08-09 15:18:22');

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
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `price` bigint NOT NULL,
  `hpp` decimal(15,2) NOT NULL DEFAULT '0.00',
  `unit_price` bigint NOT NULL,
  `sub_total` bigint NOT NULL,
  `subtotal_profit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `product_discount_amount` bigint NOT NULL,
  `product_discount_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `product_tax_amount` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_details`
--

INSERT INTO `sale_details` (`id`, `sale_id`, `item_name`, `product_id`, `productable_id`, `productable_type`, `source_type`, `product_name`, `product_code`, `quantity`, `price`, `hpp`, `unit_price`, `sub_total`, `subtotal_profit`, `product_discount_amount`, `product_discount_type`, `product_tax_amount`, `created_at`, `updated_at`) VALUES
(2, 4, 'Ban GT Savero', NULL, NULL, NULL, 'new', 'Ban GT Savero', 'GT_Savero', 1, 1425000, 0.00, 1425000, 1425000, 1425000.00, 0, 'fixed', 0, '2025-08-08 08:15:20', '2025-08-08 08:15:20'),
(3, 19, 'Ban GT Savero', 1, NULL, NULL, 'new', 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1280760.00, 1425000, 1425000, 0.00, 0, 'fixed', 0, '2025-08-09 13:38:27', '2025-08-09 13:38:27'),
(4, 20, 'Ban GT Savero', 1, NULL, NULL, 'new', 'Ban GT Savero', 'GT_Savero', 1, 1425000, 1280760.00, 1425000, 1425000, 0.00, 0, 'fixed', 0, '2025-08-09 13:56:29', '2025-08-09 13:56:29'),
(5, 20, 'Spooring Ban', NULL, NULL, NULL, 'manual', 'Spooring Ban', 'SRV-1754751385', 1, 150000, 0.00, 150000, 150000, 0.00, 0, 'fixed', 0, '2025-08-09 13:56:29', '2025-08-09 13:56:29'),
(6, 21, 'Ban GT Savero', 1, NULL, NULL, 'new', 'Ban GT Savero', 'GT_Savero', 1, 142500000, 1280760.00, 142500000, 142500000, 0.00, 0, 'fixed', 0, '2025-08-09 14:02:56', '2025-08-09 14:02:56'),
(7, 22, 'Ban GT Savero', 1, NULL, NULL, 'new', 'Ban GT Savero', 'GT_Savero', 4, 142500000, 1280760.00, 142500000, 570000000, 0.00, 0, 'fixed', 0, '2025-08-09 14:37:09', '2025-08-09 14:37:09'),
(8, 22, 'Spooring Ban', NULL, NULL, NULL, 'manual', 'Spooring Ban', '-', 1, 12500000, 0.00, 12500000, 12500000, 0.00, 0, 'fixed', 0, '2025-08-09 14:37:09', '2025-08-09 14:37:09'),
(9, 23, 'Ban GT Savero', 1, NULL, NULL, 'new', 'Ban GT Savero', 'GT_Savero', 1, 142500000, 1280760.00, 142500000, 142500000, 0.00, 0, 'fixed', 0, '2025-08-09 14:43:36', '2025-08-09 14:43:36'),
(10, 24, 'Ban GT Savero', 1, NULL, NULL, 'new', 'Ban GT Savero', 'GT_Savero', 1, 142500000, 1280760.00, 142500000, 142500000, 0.00, 0, 'fixed', 0, '2025-08-09 14:44:29', '2025-08-09 14:44:29'),
(11, 25, 'Ban GT Savero', 1, NULL, NULL, 'new', 'Ban GT Savero', 'GT_Savero', 1, 142500000, 1280760.00, 142500000, 142500000, 0.00, 0, 'fixed', 0, '2025-08-09 14:52:26', '2025-08-09 14:52:26'),
(12, 26, 'Spooring Ban', NULL, NULL, NULL, 'manual', 'Spooring Ban', '-', 1, 15000000, 0.00, 15000000, 15000000, 0.00, 0, 'fixed', 0, '2025-08-09 14:52:54', '2025-08-09 14:52:54'),
(13, 27, 'Ban GT Savero', 1, NULL, NULL, 'new', 'Ban GT Savero', 'GT_Savero', 1, 142500000, 1280760.00, 142500000, 142500000, 0.00, 0, 'fixed', 0, '2025-08-09 14:55:16', '2025-08-09 14:55:16'),
(14, 28, 'Ban GT Savero', 1, NULL, NULL, 'new', 'Ban GT Savero', 'GT_Savero', 1, 142500000, 1280760.00, 142500000, 142500000, 0.00, 0, 'fixed', 0, '2025-08-09 14:55:44', '2025-08-09 14:55:44'),
(15, 29, 'Ban GT Savero', 1, NULL, NULL, 'new', 'Ban GT Savero', 'GT_Savero', 1, 142500000, 1280760.00, 142500000, 142500000, 0.00, 0, 'fixed', 0, '2025-08-09 14:56:11', '2025-08-09 14:56:11'),
(16, 30, 'Ban GT Savero', 1, NULL, NULL, 'new', 'Ban GT Savero', 'GT_Savero', 1, 142500000, 1280760.00, 142500000, 142500000, 0.00, 0, 'fixed', 0, '2025-08-09 15:04:34', '2025-08-09 15:04:34'),
(17, 31, 'Ban GT Savero', 1, NULL, NULL, 'new', 'Ban GT Savero', 'GT_Savero', 1, 142500000, 1280760.00, 142500000, 142500000, 0.00, 0, 'fixed', 0, '2025-08-09 15:17:00', '2025-08-09 15:17:00'),
(18, 32, 'Ban GT Savero', 1, NULL, NULL, 'new', 'Ban GT Savero', 'GT_Savero', 1, 142500000, 1280760.00, 142500000, 142500000, 0.00, 0, 'fixed', 0, '2025-08-09 15:17:49', '2025-08-09 15:17:49'),
(19, 33, 'Ban GT Savero', 1, NULL, NULL, 'new', 'Ban GT Savero', 'GT_Savero', 1, 142500000, 1280760.00, 142500000, 142500000, 0.00, 0, 'fixed', 0, '2025-08-09 15:18:22', '2025-08-09 15:18:22');

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
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_payments`
--

INSERT INTO `sale_payments` (`id`, `sale_id`, `amount`, `date`, `reference`, `payment_method`, `note`, `created_at`, `updated_at`) VALUES
(1, 23, 142500000, '2025-08-09', 'INV/SL-00023/PMT-214346', 'Tunai', NULL, '2025-08-09 14:43:46', '2025-08-09 14:43:46');

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
  `tax_amount` int NOT NULL DEFAULT '0',
  `discount_percentage` int NOT NULL DEFAULT '0',
  `discount_amount` int NOT NULL DEFAULT '0',
  `shipping_amount` int NOT NULL DEFAULT '0',
  `total_amount` int NOT NULL,
  `paid_amount` int NOT NULL,
  `due_amount` int NOT NULL,
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
  `price` int NOT NULL,
  `unit_price` int NOT NULL,
  `sub_total` int NOT NULL,
  `product_discount_amount` int NOT NULL,
  `product_discount_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `product_tax_amount` int NOT NULL,
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
  `amount` int NOT NULL,
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
  `default_currency_id` int NOT NULL,
  `default_currency_position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notification_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `footer_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `company_name`, `company_email`, `company_phone`, `site_logo`, `default_currency_id`, `default_currency_position`, `notification_email`, `footer_text`, `company_address`, `created_at`, `updated_at`) VALUES
(1, 'Triangle POS', 'company@test.com', '012345678901', NULL, 1, 'prefix', 'notification@test.com', 'Triangle Pos © 2021 || Developed by <strong><a target=\"_blank\" href=\"https://fahimanzam.me\">Fahim Anzam</a></strong>', 'Tangail, Bangladesh', '2025-08-05 14:46:12', '2025-08-05 14:46:12');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint UNSIGNED NOT NULL,
  `productable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `productable_id` bigint UNSIGNED NOT NULL,
  `type` enum('in','out') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
  `is_active` tinyint(1) NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'super.admin@test.com', NULL, '$2y$10$SefjCt2y1ea0RvhAa7Y15etzlm/0AbhzYWguTxRnAx3Gzu7/DnqqG', 1, NULL, '2025-08-05 14:46:12', '2025-08-05 14:46:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adjusted_products`
--
ALTER TABLE `adjusted_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `adjusted_products_adjustment_id_foreign` (`adjustment_id`);

--
-- Indexes for table `adjustments`
--
ALTER TABLE `adjustments`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `expenses_category_id_foreign` (`category_id`);

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
  ADD KEY `products_brand_id_foreign` (`brand_id`);

--
-- Indexes for table `product_seconds`
--
ALTER TABLE `product_seconds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_seconds_unique_code_unique` (`unique_code`),
  ADD KEY `product_seconds_category_id_foreign` (`category_id`),
  ADD KEY `product_seconds_brand_id_foreign` (`brand_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchases_supplier_id_foreign` (`supplier_id`);

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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sale_details`
--
ALTER TABLE `sale_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_details_sale_id_foreign` (`sale_id`),
  ADD KEY `sale_details_product_id_foreign` (`product_id`);

--
-- Indexes for table `sale_payments`
--
ALTER TABLE `sale_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_payments_sale_id_foreign` (`sale_id`);

--
-- Indexes for table `sale_returns`
--
ALTER TABLE `sale_returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_returns_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `sale_return_details`
--
ALTER TABLE `sale_return_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_return_details_sale_return_id_foreign` (`sale_return_id`),
  ADD KEY `sale_return_details_product_id_foreign` (`product_id`);

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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_movements_productable_type_productable_id_index` (`productable_type`,`productable_id`),
  ADD KEY `stock_movements_user_id_foreign` (`user_id`);

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `sale_details`
--
ALTER TABLE `sale_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `sale_payments`
--
ALTER TABLE `sale_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adjusted_products`
--
ALTER TABLE `adjusted_products`
  ADD CONSTRAINT `adjusted_products_adjustment_id_foreign` FOREIGN KEY (`adjustment_id`) REFERENCES `adjustments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`) ON DELETE RESTRICT;

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
-- Constraints for table `sale_details`
--
ALTER TABLE `sale_details`
  ADD CONSTRAINT `sale_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sale_details_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_payments`
--
ALTER TABLE `sale_payments`
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
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
