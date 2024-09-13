-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 09, 2024 at 02:16 PM
-- Server version: 8.0.39
-- PHP Version: 8.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
Database: `crust_house`
--

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_initials` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_web_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `max_discount_percentage` decimal(8,2) DEFAULT '20.00',
  `receipt_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `feedback` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_tagline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `riderOption` tinyint(1) DEFAULT NULL,
  `onlineDeliveryOption` tinyint(1) DEFAULT NULL,
  `DiningOption` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `branch_state`, `branch_city`, `company_name`, `branch_initials`, `branch_name`, `branch_code`, `branch_address`, `branch_web_address`, `max_discount_percentage`, `receipt_message`, `feedback`, `receipt_tagline`, `riderOption`, `onlineDeliveryOption`, `DiningOption`, `created_at`, `updated_at`) VALUES
(1, 'Capital', 'Islamabad', 'Crust-House', 'CH', 'CrustHouse-ISB-01', 'ISB-162', 'Itehhad Center 1st Floor Shop#2, Main Muree Rd, opposite Attock Pump, ISB', 'www.crusthouse.com.pk', 15.00, 'THANK YOU FOR YOUR VISIT', 'SHARE YOUR FEEDBACk', 'ENJOY YOUR MEAL', 1, 1, 1, '2024-08-05 10:40:45', '2024-08-19 14:16:30'),
(4, 'Punjab', 'Wah Cantonment', 'Tehzeeb Bakers', 'TB', 'Tehzeeb Bakers - Wah', 'WAH-913', 'Opposite Keyani Restaurant, Main GT Road, Wah', 'BabuShona.com', 15.00, 'Thank You Babu', 'Babu', 'Meray babu ny thana thaya', 0, 1, 1, '2024-08-08 06:16:52', '2024-08-21 10:34:26');

-- --------------------------------------------------------

--
-- Table structure for table `branch_categories`
--

CREATE TABLE `branch_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch_categories`
--

INSERT INTO `branch_categories` (`id`, `category_id`, `branch_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2024-07-18 21:36:24', '2024-07-18 21:36:24'),
(2, 2, 1, '2024-07-18 21:36:39', '2024-07-18 21:36:39'),
(3, 3, 1, '2024-07-18 21:37:04', '2024-07-18 21:37:04'),
(4, 4, 1, '2024-07-18 21:37:13', '2024-07-18 21:37:13'),
(6, 6, 1, '2024-07-21 14:47:57', '2024-07-21 14:47:57'),
(7, 7, 1, '2024-07-21 14:54:58', '2024-07-21 14:54:58'),
(8, 8, 1, '2024-07-21 15:11:01', '2024-07-21 15:11:01'),
(9, 9, 1, '2024-07-21 15:44:39', '2024-07-21 15:44:39'),
(10, 10, 1, '2024-07-21 15:54:15', '2024-07-21 15:54:15'),
(11, 11, 1, '2024-07-21 15:54:25', '2024-07-21 15:54:25'),
(12, 12, 1, '2024-07-21 16:11:43', '2024-07-21 16:11:43'),
(13, 13, 1, '2024-07-21 16:23:13', '2024-07-21 16:23:13'),
(14, 14, 4, '2024-08-08 07:18:11', '2024-08-08 07:18:11'),
(15, 15, 4, '2024-08-08 07:20:44', '2024-08-08 07:20:44'),
(26, 26, 4, '2024-08-08 07:26:43', '2024-08-08 07:26:43'),
(27, 27, 4, '2024-08-08 07:26:56', '2024-08-08 07:26:56'),
(28, 28, 4, '2024-08-08 07:27:16', '2024-08-08 07:27:16');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` bigint UNSIGNED NOT NULL,
  `salesman_id` bigint UNSIGNED DEFAULT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `productName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `productPrice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `productAddon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addonPrice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `productVariation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `VariationPrice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `drinkFlavour` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `drinkFlavourPrice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `productQuantity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `totalPrice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `salesman_id`, `branch_id`, `product_id`, `productName`, `productPrice`, `productAddon`, `addonPrice`, `productVariation`, `VariationPrice`, `drinkFlavour`, `drinkFlavourPrice`, `productQuantity`, `totalPrice`, `created_at`, `updated_at`) VALUES
(47, 28, 4, 168, 'Small Vanilla Ice Cream', 'Rs. 80', '', NULL, 'Small', 'Rs. 80', '', NULL, '1', 'Rs. 80', '2024-08-08 12:15:28', '2024-08-08 12:15:28'),
(48, 28, 4, 170, '1 Scoop Vanilla', 'Rs. 120', '', NULL, '1 Scoop', 'Rs. 120', '', NULL, '1', 'Rs. 120', '2024-08-08 12:15:30', '2024-08-08 12:15:30'),
(58, 25, 4, 170, '1 Scoop Vanilla', 'Rs. 120', '', NULL, '1 Scoop', 'Rs. 120', '', NULL, '4', 'Rs. 480', '2024-08-21 12:24:48', '2024-08-21 12:24:48');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `categoryImage` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoryName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `categoryImage`, `categoryName`, `branch_id`, `created_at`, `updated_at`) VALUES
(1, '1722858547.jpeg', 'Appetizer', 1, '2024-07-18 21:36:24', '2024-08-05 11:49:07'),
(2, '1722858558.jpg', 'Burger', 1, '2024-07-18 21:36:39', '2024-08-05 11:49:18'),
(3, '1722858570.jpg', 'Pizza', 1, '2024-07-18 21:37:04', '2024-08-05 11:49:30'),
(4, '1722858588.jpg', 'Fries', 1, '2024-07-18 21:37:13', '2024-08-05 11:49:48'),
(6, '1722858607.jpg', 'Baked Pasta', 1, '2024-07-21 14:47:57', '2024-08-05 11:50:07'),
(7, '1722858629.jpg', 'Sandwich', 1, '2024-07-21 14:54:58', '2024-08-05 11:50:29'),
(8, '1722858642.jpeg', 'Spin Roll', 1, '2024-07-21 15:11:01', '2024-08-05 11:50:42'),
(9, '1722858663.png', 'Addons', 1, '2024-07-21 15:44:39', '2024-08-05 11:51:03'),
(10, '1722858700.jpeg', 'Platter', 1, '2024-07-21 15:54:15', '2024-08-05 11:51:40'),
(11, '1722858714.jpg', 'Others', 1, '2024-07-21 15:54:25', '2024-08-05 11:51:54'),
(12, '1722858735.jpeg', 'Chicken Pieces', 1, '2024-07-21 16:11:43', '2024-08-05 11:52:15'),
(13, '1722858757.png', 'Drinks', 1, '2024-07-21 16:23:13', '2024-08-05 11:52:37'),
(14, '1723101491.png', 'Cake', 4, '2024-08-08 07:18:11', '2024-08-08 07:18:11'),
(15, '1723101682.png', 'Pastry', 4, '2024-08-08 07:20:44', '2024-08-08 07:21:38'),
(26, '1723102003.png', 'Ice Cream', 4, '2024-08-08 07:26:43', '2024-08-08 07:26:43'),
(27, '1723102016.png', 'Cookies', 4, '2024-08-08 07:26:56', '2024-08-08 07:26:56'),
(28, '1723102036.png', 'Croissant', 4, '2024-08-08 07:27:16', '2024-08-08 07:27:16');

-- --------------------------------------------------------

--
-- Table structure for table `deals`
--

CREATE TABLE `deals` (
  `id` bigint UNSIGNED NOT NULL,
  `dealImage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dealTitle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dealStatus` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dealActualPrice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dealDiscountedPrice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dealEndDate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `deals`
--

INSERT INTO `deals` (`id`, `dealImage`, `dealTitle`, `dealStatus`, `dealActualPrice`, `dealDiscountedPrice`, `dealEndDate`, `branch_id`, `created_at`, `updated_at`) VALUES
(3, '1722858819.jpeg', 'Double Deal', 'not active', '1238 Pkr', '1099 Pkr', '2024-08-30', 1, '2024-07-21 16:25:29', '2024-09-03 13:26:22'),
(4, '1722858830.jpeg', 'Yummy Deal', 'not active', '1788 Pkr', '1299 Pkr', '2024-08-10', 1, '2024-07-21 16:26:48', '2024-08-13 16:12:03'),
(5, '1722858848.jpg', 'Super Deal', 'not active', '2548 Pkr', '2099 Pkr', '2024-08-10', 1, '2024-07-21 16:27:59', '2024-08-13 16:12:03'),
(6, '1722858866.jpeg', 'Family Deal', 'not active', '5947 Pkr', '4949 Pkr', '2024-08-10', 1, '2024-07-21 16:30:03', '2024-08-13 16:12:03'),
(7, '1722859069.jpeg', 'Student Deal', 'not active', '828 Pkr', '789 Pkr', '2024-08-10', 1, '2024-07-21 16:33:06', '2024-08-13 16:12:03'),
(8, '1722859095.jpeg', 'Crunch Deal', 'not active', '1137 Pkr', '999 Pkr', '2024-08-10', 1, '2024-07-21 16:36:29', '2024-08-13 16:12:03'),
(9, '1722859124.jpeg', 'Saver Deal', 'not active', '1735 Pkr', '1549 Pkr', '2024-08-10', 1, '2024-07-21 16:38:28', '2024-08-13 16:12:03'),
(10, '1722859196.jpeg', 'Big Deal', 'not active', '2842 Pkr', '2599 Pkr', '2024-08-24', 1, '2024-07-21 16:41:39', '2024-09-03 13:26:22'),
(11, '1722859250.webp', 'Good Deal', 'not active', '2698 Pkr', '2449 Pkr', '2024-08-17', 1, '2024-07-21 16:43:52', '2024-08-19 14:34:36'),
(12, '1722859265.jpeg', 'classic Deal', 'active', '2245 Pkr', '2049 Pkr', '2024-11-02', 1, '2024-07-21 16:47:21', '2024-08-05 12:01:05'),
(17, '1724220837.png', 'FUN ONE', 'not active', '2520 Pkr', '2700 Pkr', '2024-08-23', 4, '2024-08-21 11:13:57', '2024-09-03 13:26:22'),
(18, '1724220939.png', 'fun one n', 'not active', '2060 Pkr', '1950 Pkr', '2024-08-23', 4, '2024-08-21 11:15:39', '2024-09-03 13:26:22'),
(19, '1725352049.jpg', 'Deal-1', 'active', '1298 Pkr', '1200 Pkr', '2024-09-30', 1, '2024-09-03 13:27:29', '2024-09-03 13:28:34');

-- --------------------------------------------------------

--
-- Table structure for table `dine_in_tables`
--

CREATE TABLE `dine_in_tables` (
  `id` bigint UNSIGNED NOT NULL,
  `table_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `max_sitting_capacity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `table_status` decimal(8,2) DEFAULT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dine_in_tables`
--

INSERT INTO `dine_in_tables` (`id`, `table_number`, `max_sitting_capacity`, `table_status`, `branch_id`, `created_at`, `updated_at`) VALUES
(2, 'H1T1', '4 Chairs', 1.00, 1, '2024-08-19 15:21:14', '2024-08-19 15:21:14'),
(3, '1', '4', 1.00, 4, '2024-08-21 11:58:39', '2024-08-21 11:58:39'),
(4, '2', '5', 1.00, 4, '2024-08-21 11:58:58', '2024-08-21 11:58:58'),
(5, 'H1T2', '6 Chairs', 1.00, 1, '2024-09-03 13:33:50', '2024-09-03 13:33:50');

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` bigint UNSIGNED NOT NULL,
  `discount_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `discounts`
--

INSERT INTO `discounts` (`id`, `discount_reason`, `branch_id`, `created_at`, `updated_at`) VALUES
(1, 'Family Discount', 1, '2024-07-22 01:01:06', '2024-07-22 01:01:12'),
(2, 'General Discount', 1, '2024-07-25 10:43:58', '2024-07-25 10:43:58'),
(3, 'Family', 4, '2024-08-08 10:32:11', '2024-08-08 10:32:11');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `handlers`
--

CREATE TABLE `handlers` (
  `id` bigint UNSIGNED NOT NULL,
  `deal_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `product_quantity` int DEFAULT NULL,
  `product_total_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `handlers`
--

INSERT INTO `handlers` (`id`, `deal_id`, `product_id`, `product_quantity`, `product_total_price`, `created_at`, `updated_at`) VALUES
(7, 3, 57, 2, '998.00 Pkr', NULL, NULL),
(9, 4, 54, 1, '1299.00 Pkr', NULL, NULL),
(10, 4, 18, 1, '249.00 Pkr', NULL, NULL),
(12, 5, 83, 1, '1649.00 Pkr', NULL, NULL),
(13, 5, 1, 1, '399.00 Pkr', NULL, NULL),
(15, 6, 79, 3, '4947.00 Pkr', NULL, NULL),
(17, 7, 5, 1, '399.00 Pkr', NULL, NULL),
(18, 7, 18, 1, '249.00 Pkr', NULL, NULL),
(20, 8, 5, 2, '798.00 Pkr', NULL, NULL),
(21, 8, 18, 1, '249.00 Pkr', NULL, NULL),
(23, 9, 5, 2, '798.00 Pkr', NULL, NULL),
(24, 9, 18, 1, '249.00 Pkr', NULL, NULL),
(25, 9, 135, 2, '498.00 Pkr', NULL, NULL),
(27, 10, 5, 4, '1596.00 Pkr', NULL, NULL),
(28, 10, 135, 4, '996.00 Pkr', NULL, NULL),
(30, 11, 87, 1, '1649.00 Pkr', NULL, NULL),
(31, 11, 34, 1, '799.00 Pkr', NULL, NULL),
(33, 12, 5, 5, '1995.00 Pkr', NULL, NULL),
(36, 7, 162, 1, '90.00 Pkr', '2024-08-05 11:57:39', '2024-08-05 11:57:39'),
(37, 3, 155, 1, '120.00 Pkr', '2024-08-05 12:27:18', '2024-08-05 12:27:18'),
(38, 4, 151, 1, '120.00 Pkr', '2024-08-05 12:28:44', '2024-08-05 12:28:44'),
(39, 5, 157, 1, '250.00 Pkr', '2024-08-05 12:29:16', '2024-08-05 12:29:16'),
(40, 6, 157, 2, '500.00 Pkr', '2024-08-05 12:35:46', '2024-08-05 12:35:46'),
(50, 17, 169, 2, '300.00 Pkr', NULL, NULL),
(51, 17, 172, 3, '720.00 Pkr', NULL, NULL),
(52, 17, 167, 1, '1500.00 Pkr', NULL, NULL),
(53, 18, 168, 1, '80.00 Pkr', NULL, NULL),
(54, 18, 169, 2, '300.00 Pkr', NULL, NULL),
(55, 18, 171, 1, '180.00 Pkr', NULL, NULL),
(56, 18, 167, 1, '1500.00 Pkr', '2024-08-21 11:28:30', '2024-08-21 11:28:30'),
(57, 19, 24, 1, '399.00 Pkr', NULL, NULL),
(58, 19, 13, 1, '649.00 Pkr', NULL, NULL),
(59, 19, 165, 1, '250.00 Pkr', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2024_04_05_072955_create_branches_table', 1),
(4, '2024_04_05_072956_create_users_table', 1),
(5, '2024_04_15_063251_create_categories_table', 1),
(6, '2024_04_15_113706_create_products_table', 1),
(7, '2024_04_16_180743_create_deals_table', 1),
(8, '2024_04_17_115459_create_handlers_table', 1),
(9, '2024_04_18_120307_create_stocks_table', 1),
(10, '2024_04_25_175949_create_recipes_table', 1),
(11, '2024_05_17_153449_create_notifications_table', 1),
(12, '2024_05_21_154456_create_orders_table', 1),
(13, '2024_05_21_154709_create_order_items_table', 1),
(14, '2024_06_03_084319_create_carts_table', 1),
(15, '2024_06_07_163852_create_stock_histories_table', 1),
(16, '2024_07_03_163720_create_branch_categories_table', 1),
(17, '2024_07_12_130544_create_taxes_table', 1),
(18, '2024_07_12_175536_create_discounts_table', 1),
(19, '2024_07_23_152003_create_owner_settings_table', 1),
(20, '2024_07_25_103142_create_payment_methods_table', 1),
(21, '2024_08_19_140850_add_field_to_branch', 2),
(22, '2024_08_19_141008_add_field_to_order', 2),
(23, '2024_08_19_141354_create_dine_in_tables_table', 3),
(24, '2024_08_23_114609_add_field_to_user', 4),
(25, '2024_08_26_160532_add_field_to_orders', 4),
(26, '2024_08_27_152145_create_online_notifications_table', 4),
(27, '2024_08_30_145221_add_field_to_user', 5),
(28, '2024_08_30_145355_add_field_to_user', 6);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_notifications`
--

CREATE TABLE `online_notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `toast` int DEFAULT '0',
  `message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `salesman_id` bigint UNSIGNED DEFAULT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `total_bill` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `taxes` decimal(8,2) DEFAULT '0.00',
  `delivery_charge` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount` decimal(8,2) DEFAULT '0.00',
  `discount_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'None',
  `discount_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'None',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `received_cash` decimal(8,2) DEFAULT NULL,
  `return_change` decimal(8,2) DEFAULT NULL,
  `ordertype` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int DEFAULT '2',
  `order_cancel_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `customer_id`, `salesman_id`, `branch_id`, `total_bill`, `taxes`, `delivery_charge`, `discount`, `discount_reason`, `discount_type`, `payment_method`, `received_cash`, `return_change`, `ordertype`, `order_address`, `status`, `order_cancel_by`, `created_at`, `updated_at`) VALUES
(7, 'CH-100', NULL, 10, 1, 'Rs. 1474.545', 37.43, NULL, 4.00, 'General Discount', '%', 'NayaPAY', 1600.00, 65.58, 'Takeaway', NULL, 1, NULL, '2024-08-06 12:17:11', '2024-08-06 12:17:11'),
(8, 'TZ-101', NULL, 25, 4, 'Rs. 3200', 0.00, NULL, NULL, NULL, NULL, 'Cash', 5000.00, 1800.00, 'Dine-In', NULL, 1, NULL, '2024-08-08 10:21:42', '2024-08-08 10:25:18'),
(9, 'TZ-102', NULL, 25, 4, 'Rs. 300', 0.00, NULL, NULL, NULL, '%', 'Cash', 400.00, 100.00, 'Dine-In', NULL, 3, NULL, '2024-08-08 10:24:14', '2024-08-08 10:24:41'),
(10, 'TZ-103', NULL, 25, 4, 'Rs. 240', 0.00, NULL, NULL, NULL, '%', 'Cash', 240.00, 0.00, 'Dine-In', NULL, 1, NULL, '2024-08-08 10:27:21', '2024-08-08 10:27:36'),
(11, 'TZ-104', NULL, 25, 4, 'Rs. 1640', 40.00, NULL, NULL, NULL, '%', 'Cash', 2000.00, 360.00, 'dine-in', NULL, 1, NULL, '2024-08-08 10:52:57', '2024-08-08 10:53:25'),
(12, 'TZ-105', NULL, 25, 4, 'Rs. 123', 3.00, NULL, NULL, NULL, '%', 'Cash', 125.00, 2.00, 'dine-in', NULL, 1, NULL, '2024-08-08 10:53:14', '2024-08-08 10:53:28'),
(13, 'TZ-106', NULL, 25, 4, 'Rs. 144', 4.00, NULL, 20.00, 'Family', '-', 'Cash', 170.00, 6.00, 'dine-in', NULL, 1, NULL, '2024-08-08 10:55:57', '2024-08-08 11:07:17'),
(14, 'TZ-107', NULL, 25, 4, 'Rs. -954', 6.00, NULL, 500.00, 'none', '%', 'Cash', 500.00, 254.00, 'dine-in', NULL, 1, NULL, '2024-08-08 10:57:05', '2024-08-08 11:07:27'),
(15, 'TZ-108', NULL, 25, 4, 'Rs. 111', 3.00, NULL, 10.00, 'none', '%', 'Cash', 150.00, 27.00, 'dine-in', NULL, 1, NULL, '2024-08-08 10:57:56', '2024-08-08 11:07:18'),
(16, 'TZ-109', NULL, 28, 4, 'Rs. 103', 3.00, NULL, 20.00, 'Family', '-', 'Jazzcash', 150.00, 27.00, 'dine-in', NULL, 1, NULL, '2024-08-08 11:01:15', '2024-08-08 11:07:25'),
(17, 'TZ-110', NULL, 28, 4, 'Rs. 74', 2.00, NULL, 10.00, 'Family', '%', 'Jazzcash', 100.00, 18.00, 'dine-in', NULL, 1, NULL, '2024-08-08 11:05:15', '2024-08-08 11:07:21'),
(18, 'TZ-111', NULL, 28, 4, 'Rs. 246', 6.00, NULL, NULL, NULL, '%', 'Cash', 1000.00, 754.00, 'dine-in', NULL, 1, NULL, '2024-08-08 11:06:25', '2024-08-08 11:07:24'),
(19, 'TZ-112', NULL, 28, 4, 'Rs. 123', 3.00, NULL, NULL, NULL, NULL, 'Cash', 123.00, 0.00, 'takeaway', NULL, 1, NULL, '2024-08-08 11:09:02', '2024-08-08 11:09:13'),
(20, 'TZ-113', NULL, 25, 4, 'Rs. 525', 25.00, NULL, 500.00, 'none', '-', 'Cash', 1500.00, 475.00, 'dine-in', NULL, 1, NULL, '2024-08-08 11:31:24', '2024-08-08 11:31:34'),
(21, 'TZ-114', NULL, 25, 4, 'Rs. 820', 20.00, NULL, NULL, NULL, '%', 'Cash', 1000.00, 180.00, 'dine-in', NULL, 1, NULL, '2024-08-08 11:33:35', '2024-08-08 11:39:05'),
(22, 'TZ-115', NULL, 25, 4, 'Rs. 222', 6.00, NULL, 10.00, 'none', '%', 'Cash', 300.00, 54.00, 'dine-in', NULL, 1, NULL, '2024-08-08 11:33:55', '2024-08-08 11:39:08'),
(23, 'TZ-116', NULL, 28, 4, 'Rs. 259', 7.00, NULL, 10.00, 'Family', '%', 'Jazzcash', 300.00, 13.00, 'dine-in', NULL, 1, NULL, '2024-08-08 11:35:15', '2024-08-08 11:39:10'),
(24, 'TZ-117', NULL, 28, 4, 'Rs. 123', 3.00, NULL, NULL, NULL, '%', 'Cash', 150.00, 27.00, 'takeaway', NULL, 1, NULL, '2024-08-08 11:35:55', '2024-08-08 11:39:13'),
(25, 'TZ-118', NULL, 28, 4, 'Rs. 385', 11.00, NULL, 15.00, 'Family', '%', 'Cash', 400.00, 16.65, 'takeaway', NULL, 1, NULL, '2024-08-08 11:55:45', '2024-08-08 12:18:52'),
(26, 'TZ-119', NULL, 28, 4, 'Rs. 1700', 44.75, NULL, 134.75, 'none', '-', 'Cash', 1700.00, 0.00, 'dine-in', NULL, 1, NULL, '2024-08-08 12:01:59', '2024-08-08 12:18:53'),
(29, 'CH-120', NULL, 10, 1, 'Rs. 1348.675', 38.68, NULL, 237.00, 'Family Discount', '-', 'Cash', 1500.00, 152.00, 'Takeaway', NULL, 3, 'Hassan Ali', '2024-08-19 16:12:33', '2024-08-19 16:19:26'),
(31, 'TB-122', NULL, 25, 4, 'Rs. 1722', 42.00, NULL, NULL, NULL, '%', 'Cash', 1800.00, 78.00, 'takeaway', NULL, 3, 'hassan', '2024-08-21 12:04:28', '2024-08-21 12:08:36'),
(32, 'TB-123', NULL, 25, 4, 'Rs. 105', 3.00, NULL, 18.00, 'Family', '-', 'Cash', 110.00, 5.00, 'takeaway', NULL, 2, NULL, '2024-08-21 12:06:33', '2024-08-21 12:06:33'),
(43, 'CH-121', NULL, 10, 1, 'Rs. 7599.15', 194.85, NULL, 5.00, 'Family Discount', '%', 'NayaPAY', 7588.00, 0.00, 'Takeaway', NULL, 1, NULL, '2024-09-03 13:41:18', '2024-09-03 13:44:25'),
(44, 'OL-ORD-100', 46, 10, 1, 'Rs.2447', 0.00, '0', 0.00, 'None', 'None', 'Cash On Delivery', NULL, NULL, 'online', 'Cb 656 sajjadtown asifabad wah cantt', 5, NULL, '2024-09-06 21:25:46', '2024-09-06 21:49:05'),
(45, 'OL-ORD-101', 46, NULL, NULL, 'Rs.1098', 0.00, '0', 0.00, 'None', 'None', 'Cash On Delivery', NULL, NULL, 'online', 'Cb 656 sajjadtown asifabad wah cantt', 2, NULL, '2024-09-06 21:31:43', '2024-09-06 21:31:43');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_variation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `addons` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_price` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_quantity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_price` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `order_number`, `product_id`, `product_name`, `product_variation`, `addons`, `product_price`, `product_quantity`, `total_price`, `created_at`, `updated_at`) VALUES
(8, 7, 'CH-100', 5, 'Only Burger Zinger Burger', 'Only Burger', '', 'Rs. 399', '2', 'Rs. 798', '2024-08-06 12:17:11', '2024-08-06 12:17:11'),
(9, 7, 'CH-100', 38, 'Regular Pizza Sandwich', 'Regular', '', 'Rs. 699', '1', 'Rs. 699', '2024-08-06 12:17:11', '2024-08-06 12:17:11'),
(10, 8, 'TZ-101', 166, '1 Pound Chocolate Cake', '1 Pound', '', 'Rs. 800', '4', 'Rs. 3200', '2024-08-08 10:21:42', '2024-08-08 10:21:42'),
(11, 9, 'TZ-102', 169, 'Large Vanilla Ice Cream', 'Large', '', 'Rs. 150', '2', 'Rs. 300', '2024-08-08 10:24:14', '2024-08-08 10:24:14'),
(12, 10, 'TZ-103', 172, '3 Scoop Vanilla', '3 Scoop', '', 'Rs. 240', '1', 'Rs. 240', '2024-08-08 10:27:21', '2024-08-08 10:27:21'),
(13, 11, 'TZ-104', 166, '1 Pound Chocolate Cake', '1 Pound', '', 'Rs. 800', '2', 'Rs. 1600', '2024-08-08 10:52:57', '2024-08-08 10:52:57'),
(14, 12, 'TZ-105', 170, '1 Scoop Vanilla', '1 Scoop', '', 'Rs. 120', '1', 'Rs. 120', '2024-08-08 10:53:14', '2024-08-08 10:53:14'),
(15, 13, 'TZ-106', 168, 'Small Vanilla Ice Cream', 'Small', '', 'Rs. 80', '2', 'Rs. 160', '2024-08-08 10:55:57', '2024-08-08 10:55:57'),
(16, 14, 'TZ-107', 170, '1 Scoop Vanilla', '1 Scoop', '', 'Rs. 120', '2', 'Rs. 240', '2024-08-08 10:57:05', '2024-08-08 10:57:05'),
(17, 15, 'TZ-108', 170, '1 Scoop Vanilla', '1 Scoop', '', 'Rs. 120', '1', 'Rs. 120', '2024-08-08 10:57:56', '2024-08-08 10:57:56'),
(18, 16, 'TZ-109', 170, '1 Scoop Vanilla', '1 Scoop', '', 'Rs. 120', '1', 'Rs. 120', '2024-08-08 11:01:15', '2024-08-08 11:01:15'),
(19, 17, 'TZ-110', 168, 'Small Vanilla Ice Cream', 'Small', '', 'Rs. 80', '1', 'Rs. 80', '2024-08-08 11:05:15', '2024-08-08 11:05:15'),
(20, 18, 'TZ-111', 172, '3 Scoop Vanilla', '3 Scoop', '', 'Rs. 240', '1', 'Rs. 240', '2024-08-08 11:06:25', '2024-08-08 11:06:25'),
(21, 19, 'TZ-112', 170, '1 Scoop Vanilla', '1 Scoop', '', 'Rs. 120', '1', 'Rs. 120', '2024-08-08 11:09:02', '2024-08-08 11:09:02'),
(22, 20, 'TZ-113', 166, '1 Pound Chocolate Cake', '1 Pound', '', 'Rs. 800', '1', 'Rs. 800', '2024-08-08 11:31:24', '2024-08-08 11:31:24'),
(23, 20, 'TZ-113', 168, 'Small Vanilla Ice Cream', 'Small', '', 'Rs. 80', '1', 'Rs. 80', '2024-08-08 11:31:24', '2024-08-08 11:31:24'),
(24, 20, 'TZ-113', 170, '1 Scoop Vanilla', '1 Scoop', '', 'Rs. 120', '1', 'Rs. 120', '2024-08-08 11:31:24', '2024-08-08 11:31:24'),
(25, 21, 'TZ-114', 166, '1 Pound Chocolate Cake', '1 Pound', '', 'Rs. 800', '1', 'Rs. 800', '2024-08-08 11:33:35', '2024-08-08 11:33:35'),
(26, 22, 'TZ-115', 170, '1 Scoop Vanilla', '1 Scoop', '', 'Rs. 120', '2', 'Rs. 240', '2024-08-08 11:33:55', '2024-08-08 11:33:55'),
(27, 23, 'TZ-116', 170, '1 Scoop Vanilla', '1 Scoop', '', 'Rs. 120', '1', 'Rs. 120', '2024-08-08 11:35:15', '2024-08-08 11:35:15'),
(28, 23, 'TZ-116', 168, 'Small Vanilla Ice Cream', 'Small', '', 'Rs. 80', '2', 'Rs. 160', '2024-08-08 11:35:15', '2024-08-08 11:35:15'),
(29, 24, 'TZ-117', 170, '1 Scoop Vanilla', '1 Scoop', '', 'Rs. 120', '1', 'Rs. 120', '2024-08-08 11:35:56', '2024-08-08 11:35:56'),
(30, 25, 'TZ-118', 14, 'FUN ONE', NULL, '', 'Rs. 220', '2', 'Rs. 440', '2024-08-08 11:55:45', '2024-08-08 11:55:45'),
(31, 26, 'TZ-119', 166, '1 Pound Chocolate Cake', '1 Pound', '', 'Rs. 800', '1', 'Rs. 800', '2024-08-08 12:01:59', '2024-08-08 12:01:59'),
(32, 26, 'TZ-119', 169, 'Large Vanilla Ice Cream', 'Large', '', 'Rs. 150', '3', 'Rs. 450', '2024-08-08 12:01:59', '2024-08-08 12:01:59'),
(33, 26, 'TZ-119', 171, '2 Scoop Vanilla', '2 Scoop', '', 'Rs. 180', '3', 'Rs. 540', '2024-08-08 12:01:59', '2024-08-08 12:01:59'),
(36, 29, 'CH-120', 13, 'Small Square Pizza', 'Small', '', 'Rs. 649', '1', 'Rs. 649', '2024-08-19 16:12:33', '2024-08-19 16:12:33'),
(37, 29, 'CH-120', 24, '6 Pieces Peri Peri Wings', '6 Pieces', '', 'Rs. 399', '1', 'Rs. 399', '2024-08-19 16:12:33', '2024-08-19 16:12:33'),
(38, 29, 'CH-120', 31, 'Small Mughlai', 'Small', '', 'Rs. 499', '1', 'Rs. 499', '2024-08-19 16:12:33', '2024-08-19 16:12:33'),
(41, 31, 'TB-122', 168, 'Small Vanilla Ice Cream', 'Small', '', 'Rs. 80', '1', 'Rs. 80', '2024-08-21 12:04:28', '2024-08-21 12:04:28'),
(42, 31, 'TB-122', 166, '1 Pound Chocolate Cake', '1 Pound', '', 'Rs. 800', '2', 'Rs. 1600', '2024-08-21 12:04:28', '2024-08-21 12:04:28'),
(43, 32, 'TB-123', 170, '1 Scoop Vanilla', '1 Scoop', '', 'Rs. 120', '1', 'Rs. 120', '2024-08-21 12:06:33', '2024-08-21 12:06:33'),
(61, 43, 'CH-121', 95, 'XLarge Deluxe', 'XLarge', 'Cheese Topping ', 'Rs. 2598', '3', 'Rs. 7794', '2024-09-03 13:41:18', '2024-09-03 13:41:18'),
(62, 44, 'OL-ORD-100', NULL, 'Peri Peri Wings', '12 Pieces', '', '399', '1', '749', '2024-09-06 21:25:46', '2024-09-06 21:25:46'),
(63, 44, 'OL-ORD-100', NULL, 'Chicken petty Burger', NULL, '', '349', '1', '349', '2024-09-06 21:25:46', '2024-09-06 21:25:46'),
(64, 44, 'OL-ORD-100', NULL, '1 6 Pieces Peri Peri Wings, 1 Small Chicken Tandoori pizza, 1 1.5 Liter Sprite', 'Chicken Topping', '', '1349', '1', '1349', '2024-09-06 21:25:46', '2024-09-06 21:25:46'),
(65, 45, 'OL-ORD-101', NULL, 'Peri Peri Wings', '12 Pieces', '', '399', '1', '749', '2024-09-06 21:31:43', '2024-09-06 21:31:43'),
(66, 45, 'OL-ORD-101', NULL, 'Chicken petty Burger', NULL, '', '349', '1', '349', '2024-09-06 21:31:43', '2024-09-06 21:31:43');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` bigint UNSIGNED NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `payment_method`, `order_type`, `discount_type`, `branch_id`, `created_at`, `updated_at`) VALUES
(1, 'Cash', NULL, NULL, 1, '2024-07-25 10:43:09', '2024-07-25 10:43:09'),
(2, 'Jazzcash', NULL, NULL, 1, '2024-07-25 10:43:15', '2024-07-25 10:43:26'),
(3, 'Sadapay', NULL, NULL, 1, '2024-07-25 10:43:33', '2024-07-25 10:43:33'),
(4, 'Easypaisa', NULL, NULL, 1, '2024-07-25 10:43:41', '2024-07-25 10:43:41'),
(5, NULL, NULL, 'Fixed', 1, '2024-07-25 10:44:09', '2024-07-26 00:38:56'),
(6, NULL, NULL, 'Percentage', 1, '2024-07-25 10:44:16', '2024-07-26 00:39:03'),
(7, NULL, 'Dine-In', NULL, 1, '2024-07-25 10:44:24', '2024-07-26 00:39:12'),
(8, NULL, 'Takeaway', NULL, 1, '2024-07-25 10:44:33', '2024-07-26 00:39:20'),
(9, 'NayaPAY', NULL, NULL, 1, '2024-07-31 04:38:44', '2024-07-31 04:38:44'),
(11, 'Cash', NULL, NULL, 4, '2024-08-08 10:20:35', '2024-08-08 10:20:35'),
(12, 'Jazzcash', NULL, NULL, 4, '2024-08-08 10:20:43', '2024-08-08 10:20:43'),
(13, NULL, NULL, 'Fixed', 4, '2024-08-08 10:35:02', '2024-08-08 10:35:02'),
(14, NULL, NULL, 'Percentage', 4, '2024-08-08 10:35:09', '2024-08-08 10:35:09'),
(15, NULL, 'dine-in', NULL, 4, '2024-08-08 10:41:33', '2024-08-08 10:41:33'),
(16, NULL, 'takeaway', NULL, 4, '2024-08-08 10:41:47', '2024-08-08 10:41:47'),
(17, 'CASH', NULL, NULL, 4, '2024-08-08 11:56:03', '2024-08-08 11:56:03'),
(18, 'sadapay', NULL, NULL, 4, '2024-08-08 11:56:25', '2024-08-08 11:56:25'),
(19, 'easypaisa', NULL, NULL, 4, '2024-08-08 11:56:31', '2024-08-08 11:56:31');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `productImage` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `productName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `productVariation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `productPrice` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `productImage`, `productName`, `productVariation`, `productPrice`, `category_name`, `category_id`, `branch_id`, `created_at`, `updated_at`) VALUES
(1, '17229412274.jpg', 'Oven Baked Wings', '6 Pieces', '399', 'Appetizer', 1, 1, '2024-07-18 21:38:59', '2024-08-06 10:47:07'),
(2, '17229412274.jpg', 'Oven Baked Wings', '12 Pieces', '749', 'Appetizer', 1, 1, '2024-07-18 21:38:59', '2024-08-06 10:47:07'),
(3, '17229412393.jpg', 'Bar B Q Wings', '6 Pieces', '399', 'Appetizer', 1, 1, '2024-07-18 21:39:33', '2024-08-06 10:47:19'),
(4, '17229412393.jpg', 'Bar B Q Wings', '12 Pieces', '749', 'Appetizer', 1, 1, '2024-07-18 21:39:33', '2024-08-06 10:47:19'),
(5, '17228595329.jpeg', 'Zinger Burger', 'Only Burger', '399', 'Burger', 2, 1, '2024-07-18 21:40:13', '2024-08-05 12:05:32'),
(6, '17228595625.jpg', 'Grill Burger', 'Only Burger', '449', 'Burger', 2, 1, '2024-07-18 21:40:40', '2024-08-05 12:06:02'),
(7, '17228600357.jpg', 'Crown Crust', 'Medium', '1299', 'Pizza', 3, 1, '2024-07-18 21:42:26', '2024-08-05 12:13:55'),
(8, '17228600357.jpg', 'Crown Crust', 'Large', '1749', 'Pizza', 3, 1, '2024-07-18 21:42:26', '2024-08-05 12:13:55'),
(9, '17228600357.jpg', 'Crown Crust', 'XLarge', '2299', 'Pizza', 3, 1, '2024-07-18 21:42:26', '2024-08-05 12:13:55'),
(13, '17228600522.jpg', 'Square Pizza', 'Small', '649', 'Pizza', 3, 1, '2024-07-18 21:44:00', '2024-08-05 12:14:12'),
(14, '17228600522.jpg', 'Square Pizza', 'Medium', '1299', 'Pizza', 3, 1, '2024-07-18 21:44:00', '2024-08-05 12:14:12'),
(15, '17228600522.jpg', 'Square Pizza', 'Large', '1699', 'Pizza', 3, 1, '2024-07-18 21:44:00', '2024-08-05 12:14:12'),
(17, '172285988510.jpeg', 'Plain Fries', 'Large', '349', 'Fries', 4, 1, '2024-07-18 21:44:52', '2024-08-05 12:11:25'),
(18, '172285988510.jpeg', 'Plain Fries', 'Regular', '249', 'Fries', 4, 1, '2024-07-18 21:44:52', '2024-08-05 12:11:25'),
(24, '17228593905.jpg', 'Peri Peri Wings', '6 Pieces', '399', 'Appetizer', 1, 1, '2024-07-21 14:35:19', '2024-08-05 12:03:10'),
(25, '17228593905.jpg', 'Peri Peri Wings', '12 Pieces', '749', 'Appetizer', 1, 1, '2024-07-21 14:35:19', '2024-08-05 12:03:10'),
(26, '17228594065.jpeg', 'Sweet Chilli Wings', '6 Pieces', '399', 'Appetizer', 1, 1, '2024-07-21 14:46:00', '2024-08-05 12:03:26'),
(27, '17228594065.jpeg', 'Sweet Chilli Wings', '12 Pieces', '749', 'Appetizer', 1, 1, '2024-07-21 14:46:00', '2024-08-05 12:03:26'),
(28, '172285945510.jpeg', 'Crunchy', 'Large', '799', 'Baked Pasta', 6, 1, '2024-07-21 14:49:06', '2024-08-05 12:04:15'),
(29, '17228594769.jpeg', 'Fettucine', 'Small', '499', 'Baked Pasta', 6, 1, '2024-07-21 14:50:19', '2024-08-05 12:04:36'),
(30, '17228594769.jpeg', 'Fettucine', 'Large', '799', 'Baked Pasta', 6, 1, '2024-07-21 14:50:19', '2024-08-05 12:04:36'),
(31, '17228594937.jpeg', 'Mughlai', 'Small', '499', 'Baked Pasta', 6, 1, '2024-07-21 14:51:53', '2024-08-05 12:04:53'),
(32, '17228594937.jpeg', 'Mughlai', 'Large', '799', 'Baked Pasta', 6, 1, '2024-07-21 14:51:53', '2024-08-05 12:04:53'),
(33, '17228595057.jpeg', 'Chef Special Pasta', 'Small', '499', 'Baked Pasta', 6, 1, '2024-07-21 14:53:37', '2024-08-05 12:05:05'),
(34, '17228595057.jpeg', 'Chef Special Pasta', 'Large', '799', 'Baked Pasta', 6, 1, '2024-07-21 14:53:37', '2024-08-05 12:05:05'),
(35, '172286059010.jpg', 'Chicken Grill Sandwich', 'Regular', '699', 'Sandwich', 7, 1, '2024-07-21 15:01:05', '2024-08-05 12:23:10'),
(36, '17228606245.jpg', 'Mexico Sandwich', 'Regular', '699', 'Sandwich', 7, 1, '2024-07-21 15:02:29', '2024-08-05 12:23:44'),
(37, '17228606416.jpg', 'Mughlai Sandwich', 'Regular', '699', 'Sandwich', 7, 1, '2024-07-21 15:04:51', '2024-08-05 12:24:01'),
(38, '17228606574.jpg', 'Pizza Sandwich', 'Regular', '699', 'Sandwich', 7, 1, '2024-07-21 15:05:43', '2024-08-05 12:24:17'),
(39, '17228595735.jpeg', 'Chicken petty Burger', 'Only Burger', '349', 'Burger', 2, 1, '2024-07-21 15:07:10', '2024-08-05 12:06:13'),
(40, '17228596093.jpeg', 'Double Decker Burger', 'Only Burger', '699', 'Burger', 2, 1, '2024-07-21 15:07:37', '2024-08-05 12:06:49'),
(41, '17228596347.jpg', 'Smash Beef Burger', 'Only Burger', '649', 'Burger', 2, 1, '2024-07-21 15:08:55', '2024-08-05 12:07:14'),
(42, '17228606854.jpeg', 'Bihari Roll', 'Regular', '699', 'Spin Roll', 8, 1, '2024-07-21 15:12:38', '2024-08-05 12:24:45'),
(43, '17228607079.jpeg', 'Kabab Roll', 'Regular', '399', 'Spin Roll', 8, 1, '2024-07-21 15:15:29', '2024-08-05 12:25:07'),
(44, '17228607344.png', 'Mughali Roll', 'Regular', '699', 'Spin Roll', 8, 1, '2024-07-21 15:15:57', '2024-08-05 12:25:34'),
(45, '17228607588.jpg', 'Peri Peri Roll', 'Regular', '699', 'Spin Roll', 8, 1, '2024-07-21 15:16:25', '2024-08-05 12:25:58'),
(46, '17228607767.jpeg', 'Zinger Roll', 'Regular', '399', 'Spin Roll', 8, 1, '2024-07-21 15:16:53', '2024-08-05 12:26:16'),
(47, '17228607867.jpeg', 'Crust House Special', 'Regular', '699', 'Spin Roll', 8, 1, '2024-07-21 15:17:35', '2024-08-05 12:26:26'),
(48, '17228600746.jpeg', 'Malai Boti', 'Medium', '1199', 'Pizza', 3, 1, '2024-07-21 15:20:17', '2024-08-05 12:14:34'),
(49, '17228600746.jpeg', 'Malai Boti', 'Large', '1749', 'Pizza', 3, 1, '2024-07-21 15:20:17', '2024-08-05 12:14:34'),
(50, '17228600746.jpeg', 'Malai Boti', 'XLarge', '2299', 'Pizza', 3, 1, '2024-07-21 15:20:17', '2024-08-05 12:14:34'),
(51, '17228601031.jpeg', 'Cheese Stuffer', 'Medium', '1299', 'Pizza', 3, 1, '2024-07-21 15:21:18', '2024-08-05 12:15:03'),
(52, '17228601031.jpeg', 'Cheese Stuffer', 'Large', '1849', 'Pizza', 3, 1, '2024-07-21 15:21:18', '2024-08-05 12:15:03'),
(53, '17228601031.jpeg', 'Cheese Stuffer', 'XLarge', '2349', 'Pizza', 3, 1, '2024-07-21 15:21:18', '2024-08-05 12:15:03'),
(54, '172286014210.jpg', 'Kabab Stuffer', 'Medium', '1299', 'Pizza', 3, 1, '2024-07-21 15:22:18', '2024-08-05 12:15:42'),
(55, '172286014210.jpg', 'Kabab Stuffer', 'Large', '1849', 'Pizza', 3, 1, '2024-07-21 15:22:18', '2024-08-05 12:15:42'),
(56, '172286014210.jpg', 'Kabab Stuffer', 'XLarge', '2349', 'Pizza', 3, 1, '2024-07-21 15:22:18', '2024-08-05 12:15:42'),
(57, '17228601747.jpeg', 'Chicken tikka pizza', 'Small', '499', 'Pizza', 3, 1, '2024-07-21 15:24:12', '2024-08-05 12:16:14'),
(58, '17228601747.jpeg', 'Chicken tikka pizza', 'Medium', '1099', 'Pizza', 3, 1, '2024-07-21 15:24:12', '2024-08-05 12:16:14'),
(59, '17228601747.jpeg', 'Chicken tikka pizza', 'Large', '1649', 'Pizza', 3, 1, '2024-07-21 15:24:12', '2024-08-05 12:16:14'),
(60, '17228601747.jpeg', 'Chicken tikka pizza', 'X-Large', '2049', 'Pizza', 3, 1, '2024-07-21 15:24:12', '2024-08-05 12:16:14'),
(61, '17228601968.jpg', 'Chicken Fajitta', 'Small', '499', 'Pizza', 3, 1, '2024-07-21 15:24:59', '2024-08-05 12:16:36'),
(62, '17228601968.jpg', 'Chicken Fajitta', 'Medium', '1099', 'Pizza', 3, 1, '2024-07-21 15:24:59', '2024-08-05 12:16:36'),
(63, '17228601968.jpg', 'Chicken Fajitta', 'Large', '1649', 'Pizza', 3, 1, '2024-07-21 15:24:59', '2024-08-05 12:16:36'),
(64, '17228601968.jpg', 'Chicken Fajitta', 'X-Large', '2049', 'Pizza', 3, 1, '2024-07-21 15:24:59', '2024-08-05 12:16:36'),
(65, '17228602274.jpeg', 'Chicken Supereme', 'Small', '499', 'Pizza', 3, 1, '2024-07-21 15:28:45', '2024-08-05 12:17:07'),
(66, '17228602274.jpeg', 'Chicken Supereme', 'Medium', '1099', 'Pizza', 3, 1, '2024-07-21 15:28:45', '2024-08-05 12:17:07'),
(67, '17228602274.jpeg', 'Chicken Supereme', 'Large', '1649', 'Pizza', 3, 1, '2024-07-21 15:28:45', '2024-08-05 12:17:07'),
(68, '17228602274.jpeg', 'Chicken Supereme', 'X-Large', '2049', 'Pizza', 3, 1, '2024-07-21 15:28:45', '2024-08-05 12:17:07'),
(69, '17228602509.jpeg', 'Chicken Tandoori', 'Small', '499', 'Pizza', 3, 1, '2024-07-21 15:29:49', '2024-08-05 12:17:30'),
(70, '17228602509.jpeg', 'Chicken Tandoori', 'Medium', '1099', 'Pizza', 3, 1, '2024-07-21 15:29:49', '2024-08-05 12:17:30'),
(71, '17228602509.jpeg', 'Chicken Tandoori', 'Large', '1649', 'Pizza', 3, 1, '2024-07-21 15:29:49', '2024-08-05 12:17:30'),
(72, '17228602509.jpeg', 'Chicken Tandoori', 'X-Large', '2049', 'Pizza', 3, 1, '2024-07-21 15:29:49', '2024-08-05 12:17:30'),
(73, '17228602734.jpeg', 'Cheese Lover', 'Small', '499', 'Pizza', 3, 1, '2024-07-21 15:30:48', '2024-08-05 12:17:53'),
(74, '17228602734.jpeg', 'Cheese Lover', 'Medium', '1099', 'Pizza', 3, 1, '2024-07-21 15:30:48', '2024-08-05 12:17:53'),
(75, '17228602734.jpeg', 'Cheese Lover', 'Large', '1649', 'Pizza', 3, 1, '2024-07-21 15:30:48', '2024-08-05 12:17:53'),
(76, '17228602734.jpeg', 'Cheese Lover', 'X-Large', '2049', 'Pizza', 3, 1, '2024-07-21 15:30:48', '2024-08-05 12:17:53'),
(77, '17228602976.jpeg', 'Euro', 'Small', '499', 'Pizza', 3, 1, '2024-07-21 15:31:59', '2024-08-05 12:18:17'),
(78, '17228602976.jpeg', 'Euro', 'Medium', '1099', 'Pizza', 3, 1, '2024-07-21 15:31:59', '2024-08-05 12:18:17'),
(79, '17228602976.jpeg', 'Euro', 'Large', '1649', 'Pizza', 3, 1, '2024-07-21 15:31:59', '2024-08-05 12:18:17'),
(80, '17228602976.jpeg', 'Euro', 'X-Large', '2049', 'Pizza', 3, 1, '2024-07-21 15:31:59', '2024-08-05 12:18:17'),
(81, '17228603478.jpeg', 'Hot N Spicy', 'Small', '499', 'Pizza', 3, 1, '2024-07-21 15:32:35', '2024-08-05 12:19:07'),
(82, '17228603478.jpeg', 'Hot N Spicy', 'Medium', '1099', 'Pizza', 3, 1, '2024-07-21 15:32:35', '2024-08-05 12:19:07'),
(83, '17228603478.jpeg', 'Hot N Spicy', 'Large', '1649', 'Pizza', 3, 1, '2024-07-21 15:32:35', '2024-08-05 12:19:07'),
(84, '17228603478.jpeg', 'Hot N Spicy', 'X-Large', '2049', 'Pizza', 3, 1, '2024-07-21 15:32:35', '2024-08-05 12:19:07'),
(85, '17228603621.jpeg', 'Veggle Lover', 'Small', '499', 'Pizza', 3, 1, '2024-07-21 15:33:17', '2024-08-05 12:19:22'),
(86, '17228603621.jpeg', 'Veggle Lover', 'Medium', '1099', 'Pizza', 3, 1, '2024-07-21 15:33:17', '2024-08-05 12:19:22'),
(87, '17228603621.jpeg', 'Veggle Lover', 'Large', '1649', 'Pizza', 3, 1, '2024-07-21 15:33:17', '2024-08-05 12:19:22'),
(88, '17228603621.jpeg', 'Veggle Lover', 'X-Large', '2049', 'Pizza', 3, 1, '2024-07-21 15:33:18', '2024-08-05 12:19:22'),
(89, '172286037910.jpeg', 'Bone Fire', 'Small', '549', 'Pizza', 3, 1, '2024-07-21 15:38:00', '2024-08-05 12:19:39'),
(90, '172286037910.jpeg', 'Bone Fire', 'Medium', '1199', 'Pizza', 3, 1, '2024-07-21 15:38:00', '2024-08-05 12:19:39'),
(91, '172286037910.jpeg', 'Bone Fire', 'Large', '1749', 'Pizza', 3, 1, '2024-07-21 15:38:00', '2024-08-05 12:19:39'),
(92, '172286037910.jpeg', 'Bone Fire', 'X-Large', '2199', 'Pizza', 3, 1, '2024-07-21 15:38:00', '2024-08-05 12:19:39'),
(93, '17228604376.jpeg', 'Deluxe', 'Medium', '1199', 'Pizza', 3, 1, '2024-07-21 15:38:46', '2024-08-05 12:20:37'),
(94, '17228604376.jpeg', 'Deluxe', 'Large', '1749', 'Pizza', 3, 1, '2024-07-21 15:38:46', '2024-08-05 12:20:37'),
(95, '17228604376.jpeg', 'Deluxe', 'XLarge', '2199', 'Pizza', 3, 1, '2024-07-21 15:38:46', '2024-08-05 12:20:37'),
(96, '17228604634.jpeg', 'Half & Half', 'Medium', '1199', 'Pizza', 3, 1, '2024-07-21 15:40:06', '2024-08-05 12:21:03'),
(97, '17228604634.jpeg', 'Half & Half', 'Large', '1749', 'Pizza', 3, 1, '2024-07-21 15:40:06', '2024-08-05 12:21:03'),
(98, '17228604634.jpeg', 'Half & Half', 'XLarge', '2199', 'Pizza', 3, 1, '2024-07-21 15:40:06', '2024-08-05 12:21:03'),
(99, '17228604793.jpg', 'Mughali Pizza', 'Small', '549', 'Pizza', 3, 1, '2024-07-21 15:40:56', '2024-08-05 12:21:19'),
(100, '17228604793.jpg', 'Mughali Pizza', 'Medium', '1199', 'Pizza', 3, 1, '2024-07-21 15:40:56', '2024-08-05 12:21:19'),
(101, '17228604793.jpg', 'Mughali Pizza', 'Large', '1749', 'Pizza', 3, 1, '2024-07-21 15:40:56', '2024-08-05 12:21:19'),
(102, '17228604793.jpg', 'Mughali Pizza', 'X-Large', '2199', 'Pizza', 3, 1, '2024-07-21 15:40:56', '2024-08-05 12:21:19'),
(103, '17228604972.jpeg', 'Pepperoni Pizza', 'Small', '549', 'Pizza', 3, 1, '2024-07-21 15:41:41', '2024-08-05 12:21:37'),
(104, '17228604972.jpeg', 'Pepperoni Pizza', 'Medium', '1199', 'Pizza', 3, 1, '2024-07-21 15:41:41', '2024-08-05 12:21:37'),
(105, '17228604972.jpeg', 'Pepperoni Pizza', 'Large', '1749', 'Pizza', 3, 1, '2024-07-21 15:41:41', '2024-08-05 12:21:37'),
(106, '17228604972.jpeg', 'Pepperoni Pizza', 'X-Large', '2199', 'Pizza', 3, 1, '2024-07-21 15:41:41', '2024-08-05 12:21:37'),
(107, '17228605184.jpeg', 'Seekh Kabab', 'Small', '549', 'Pizza', 3, 1, '2024-07-21 15:42:23', '2024-08-05 12:21:58'),
(108, '17228605184.jpeg', 'Seekh Kabab', 'Medium', '1199', 'Pizza', 3, 1, '2024-07-21 15:42:23', '2024-08-05 12:21:58'),
(109, '17228605184.jpeg', 'Seekh Kabab', 'Large', '1749', 'Pizza', 3, 1, '2024-07-21 15:42:23', '2024-08-05 12:21:58'),
(110, '17228605184.jpeg', 'Seekh Kabab', 'X-Large', '2199', 'Pizza', 3, 1, '2024-07-21 15:42:23', '2024-08-05 12:21:58'),
(111, '17228605326.jpeg', 'Special Crust House', 'Small', '549', 'Pizza', 3, 1, '2024-07-21 15:44:05', '2024-08-05 12:22:12'),
(112, '17228605326.jpeg', 'Special Crust House', 'Medium', '1199', 'Pizza', 3, 1, '2024-07-21 15:44:05', '2024-08-05 12:22:12'),
(113, '17228605326.jpeg', 'Special Crust House', 'Large', '1749', 'Pizza', 3, 1, '2024-07-21 15:44:05', '2024-08-05 12:22:12'),
(114, '17228605326.jpeg', 'Special Crust House', 'X-Large', '2199', 'Pizza', 3, 1, '2024-07-21 15:44:05', '2024-08-05 12:22:12'),
(115, '17228593279.jpeg', 'Chicken Topping', 'Small', '149', 'Addons', 9, 1, '2024-07-21 15:47:22', '2024-08-05 12:02:07'),
(116, '17228593279.jpeg', 'Chicken Topping', 'Medium', '249', 'Addons', 9, 1, '2024-07-21 15:47:22', '2024-08-05 12:02:07'),
(117, '17228593279.jpeg', 'Chicken Topping', 'Large', '299', 'Addons', 9, 1, '2024-07-21 15:47:22', '2024-08-05 12:02:07'),
(118, '17228593279.jpeg', 'Chicken Topping', 'X-Large', '399', 'Addons', 9, 1, '2024-07-21 15:47:22', '2024-08-05 12:02:07'),
(119, '17228593372.jpeg', 'Cheese Topping', 'Small', '149', 'Addons', 9, 1, '2024-07-21 15:48:27', '2024-08-05 12:02:17'),
(120, '17228593372.jpeg', 'Cheese Topping', 'Medium', '249', 'Addons', 9, 1, '2024-07-21 15:48:27', '2024-08-05 12:02:17'),
(121, '17228593372.jpeg', 'Cheese Topping', 'Large', '299', 'Addons', 9, 1, '2024-07-21 15:48:27', '2024-08-05 12:02:17'),
(122, '17228593372.jpeg', 'Cheese Topping', 'X-Large', '399', 'Addons', 9, 1, '2024-07-21 15:48:27', '2024-08-05 12:02:17'),
(123, '17228605548.jpeg', 'Special Platter', 'Regular', '1149', 'Platter', 10, 1, '2024-07-21 15:55:07', '2024-08-05 12:22:34'),
(124, '17228600013.jpg', 'CalZone', 'Small', '699', 'Others', 11, 1, '2024-07-21 15:56:47', '2024-08-05 12:13:21'),
(125, '17228600013.jpg', 'CalZone', 'Medium', '1299', 'Others', 11, 1, '2024-07-21 15:56:47', '2024-08-05 12:13:21'),
(126, '17228600013.jpg', 'CalZone', 'Large', '1799', 'Others', 11, 1, '2024-07-21 15:56:47', '2024-08-05 12:13:21'),
(127, '172285993610.webp', 'Special Fries', 'Large', '499', 'Fries', 4, 1, '2024-07-21 15:59:03', '2024-08-05 12:12:16'),
(128, '17228599658.jpeg', 'Pizza Fries', 'Large', '599', 'Fries', 4, 1, '2024-07-21 15:59:52', '2024-08-05 12:12:45'),
(129, '17228599833.jpeg', 'Pepperoni Fries', 'Large', '599', 'Fries', 4, 1, '2024-07-21 16:00:27', '2024-08-05 12:13:03'),
(130, '172285965310.jpeg', 'Fried Wings', '5 Pieces', '349', 'Chicken Pieces', 12, 1, '2024-07-21 16:15:47', '2024-08-05 12:07:33'),
(131, '172285965310.jpeg', 'Fried Wings', '10 Pieces', '599', 'Chicken Pieces', 12, 1, '2024-07-21 16:15:47', '2024-08-05 12:07:33'),
(132, '172285966710.jpg', 'Nuggets', '5 Pieces', '349', 'Chicken Pieces', 12, 1, '2024-07-21 16:16:33', '2024-08-05 12:07:47'),
(133, '172285966710.jpg', 'Nuggets', '10 Pieces', '599', 'Chicken Pieces', 12, 1, '2024-07-21 16:16:33', '2024-08-05 12:07:47'),
(134, '172285972310.jpg', 'Full Bucked Chicken pcs', '10 Pieces', '2199', 'Chicken Pieces', 12, 1, '2024-07-21 16:17:35', '2024-08-05 12:08:43'),
(135, '17228597452.jpeg', 'Fried chicken Pieces', '1 Piece', '249', 'Chicken Pieces', 12, 1, '2024-07-21 16:18:46', '2024-08-05 12:09:05'),
(136, '17228597595.jpeg', 'Hot Shot 20 Pieces', '10 Pieces', '699', 'Chicken Pieces', 12, 1, '2024-07-21 16:20:31', '2024-08-05 12:09:19'),
(137, '17228597746.jpg', 'Dip souce', '1 Piece', '79', 'Chicken Pieces', 12, 1, '2024-07-21 16:21:06', '2024-08-05 12:09:34'),
(138, '17228600176.jpg', 'Mineral Water', '500 ML', '70', 'Others', 11, 1, '2024-07-21 16:22:22', '2024-08-05 12:13:37'),
(139, '17228600176.jpg', 'Mineral Water', '1.5 Liter', '120', 'Others', 11, 1, '2024-07-21 16:22:22', '2024-08-05 12:13:37'),
(146, '17228597879.jpg', 'Sprite', '350 ML', '90', 'Drinks', 13, 1, '2024-07-31 00:36:41', '2024-08-05 12:09:47'),
(147, '17228597879.jpg', 'Sprite', '500 ML', '120', 'Drinks', 13, 1, '2024-07-31 00:36:41', '2024-08-05 12:09:47'),
(148, '17228597879.jpg', 'Sprite', '1 Liter', '190', 'Drinks', 13, 1, '2024-07-31 00:36:41', '2024-08-05 12:09:47'),
(149, '17228597879.jpg', 'Sprite', '1.5 Liter', '250', 'Drinks', 13, 1, '2024-07-31 00:36:41', '2024-08-05 12:09:47'),
(150, '17228598027.jpg', 'Coca Cola', '350 ML', '90', 'Drinks', 13, 1, '2024-07-31 00:38:00', '2024-08-05 12:10:02'),
(151, '17228598027.jpg', 'Coca Cola', '500 ML', '120', 'Drinks', 13, 1, '2024-07-31 00:38:00', '2024-08-05 12:10:02'),
(152, '17228598027.jpg', 'Coca Cola', '1 Liter', '190', 'Drinks', 13, 1, '2024-07-31 00:38:00', '2024-08-05 12:10:02'),
(153, '17228598027.jpg', 'Coca Cola', '1.5 Liter', '250', 'Drinks', 13, 1, '2024-07-31 00:38:00', '2024-08-05 12:10:02'),
(154, '17228598164.jpeg', 'Pepsi', '350 ML', '90', 'Drinks', 13, 1, '2024-07-31 00:38:59', '2024-08-05 12:10:16'),
(155, '17228598164.jpeg', 'Pepsi', '500 ML', '120', 'Drinks', 13, 1, '2024-07-31 00:38:59', '2024-08-05 12:10:16'),
(156, '17228598164.jpeg', 'Pepsi', '1 Liter', '190', 'Drinks', 13, 1, '2024-07-31 00:38:59', '2024-08-05 12:10:16'),
(157, '17228598164.jpeg', 'Pepsi', '1.5 Liter', '250', 'Drinks', 13, 1, '2024-07-31 00:38:59', '2024-08-05 12:10:16'),
(158, '17228598346.jpeg', '7up', '350 ML', '90', 'Drinks', 13, 1, '2024-07-31 00:39:40', '2024-08-05 12:10:34'),
(159, '17228598346.jpeg', '7up', '500 ML', '120', 'Drinks', 13, 1, '2024-07-31 00:39:40', '2024-08-05 12:10:34'),
(160, '17228598346.jpeg', '7up', '1 Liter', '190', 'Drinks', 13, 1, '2024-07-31 00:39:40', '2024-08-05 12:10:34'),
(161, '17228598346.jpeg', '7up', '1.5 Liter', '250', 'Drinks', 13, 1, '2024-07-31 00:39:40', '2024-08-05 12:10:34'),
(162, '17228598587.jpeg', 'Dew', '350 ML', '90', 'Drinks', 13, 1, '2024-07-31 00:41:08', '2024-08-05 12:10:58'),
(163, '17228598587.jpeg', 'Dew', '500 ML', '120', 'Drinks', 13, 1, '2024-07-31 00:41:08', '2024-08-05 12:10:58'),
(164, '17228598587.jpeg', 'Dew', '1 Liter', '190', 'Drinks', 13, 1, '2024-07-31 00:41:08', '2024-08-05 12:10:58'),
(165, '17228598587.jpeg', 'Dew', '1.5 Liter', '250', 'Drinks', 13, 1, '2024-07-31 00:41:08', '2024-08-05 12:10:58'),
(166, '1723108425_0_4080.png', 'Chocolate Cake', '1 Pound', '800', 'Cake', 14, 4, '2024-08-08 09:13:45', '2024-08-08 09:13:45'),
(167, '1723108425_1_7336.png', 'Chocolate Cake', '2 Pound', '1500', 'Cake', 14, 4, '2024-08-08 09:13:45', '2024-08-08 09:13:45'),
(168, '1723108473_0_9988.png', 'Vanilla Ice Cream', 'Small', '80', 'Pastry', 15, 4, '2024-08-08 09:14:33', '2024-08-08 09:14:33'),
(169, '1723108473_1_2709.png', 'Vanilla Ice Cream', 'Large', '150', 'Pastry', 15, 4, '2024-08-08 09:14:33', '2024-08-08 09:14:33'),
(170, '1723108545_0_7936.png', 'Vanilla', '1 Scoop', '120', 'Ice Cream', 26, 4, '2024-08-08 09:15:45', '2024-08-08 09:15:45'),
(171, '1723108545_1_9667.png', 'Vanilla', '2 Scoop', '180', 'Ice Cream', 26, 4, '2024-08-08 09:15:45', '2024-08-08 09:15:45'),
(172, '1723108545_2_3624.png', 'Vanilla', '3 Scoop', '240', 'Ice Cream', 26, 4, '2024-08-08 09:15:45', '2024-08-08 09:15:45');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `stock_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `category_id`, `product_id`, `stock_id`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 3, 7, 1, '2 kg', '2024-08-05 12:37:31', '2024-08-05 12:37:31'),
(2, 14, 166, 2, '0.4 kg', '2024-08-08 09:21:53', '2024-08-08 09:21:53');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1CsFtb9eL1NhukkSr8SGAB009DcbHZtSMiKnfXZc', NULL, '167.71.33.123', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaFZMb01NZEQ3U1BnOTVBbW1Telp1NGl0Vzg3aHlCdFl2Qnd1dGxIVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1725848213),
('1P6CivXfyRaWK6A5EGTlSbpVbi5j3z8IyABe4QXv', NULL, '52.167.144.22', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib0RPcUxsRGlTZ1NXdG8zWGdKUWxQamFOYXhHZWplNXJ4YUR1SmRQdSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vd3d3LmNydXN0aG91c2UuY29tLnBrL29ubGluZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1725803807),
('2EzuwNgegRhcSXCQQWqacscftiRTPT0fwY4E3B35', NULL, '193.186.4.116', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Mobile Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoibDFIOENrc0xLdFNQcDBqb0lObUlVMzNMWnBGMG9xZ09TcjNDbFRSYiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725832788),
('2LAjMnywvUobPrI2ingjrGEEFVPw1i2ne1Hn4h2b', NULL, '116.202.221.212', 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOXljRVg3NVpWVk9DMzVuSnVkZjVkTXRlcmE1VjBoYVVUdmsyVnRqQSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjQ6Imh0dHA6Ly9jcnVzdGhvdXNlLmNvbS5wayI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1725884688),
('2OowIomvIlTU0wxMTZ8LeKuWd2POTPPRQf4fKsDW', NULL, '192.99.36.126', 'Mozilla/5.0 (compatible; MJ12bot/v1.4.8; http://mj12bot.com/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTEtVWGRnOGp6UUh5R3lwRGhjNXNTU0NRejg1WHhnTW9tZmY1ejdvaiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsvb25saW5lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725801184),
('59ZIbHcgJoy4rhJ2bkAMZG0ipsT2B94oQhW3H6fL', NULL, '172.176.75.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.6478.36 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOHRaUWF0YW5rU2JmQUxheHd3VEZGUGc1WlFEeVJyeTQzS1doazBRYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsvb25saW5lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725888978),
('72x1jeVRWD6P6OCHQx07qxc4vO3La2qTn4ACkTXf', NULL, '40.77.167.17', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiemlpdmFyZ2M0ZTRvcXVuelBiRkpqdmk2NGVFNzRkR2xiZFBLUmE5TCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1725865624),
('7j9cbMdM6iUfChJSMFxOGClZjLCYOYrObOmR8Q1P', NULL, '40.77.167.59', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibURZaUlVVWNvYzgyN3JlM2daYWx6YzdjVUhqUE5CMmE4RW9CTFdUYSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDY6Imh0dHBzOi8vd3d3LmNydXN0aG91c2UuY29tLnBrL3ZpZXdSZWdpc3RlclBhZ2UiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1725775113),
('bdFYZz8apirWm1VwtPYMh1wMHzBk8e5gvW1nLxz6', NULL, '20.169.168.224', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.6422.143 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid2R2a3d4eXp2ckhwRE5WMkxXaEhvOU53ajJjWXVvR011WTllVDNiYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1725799369),
('bMHdjDK9w7QmAz9bbOYctwPyyEfo4KIfmiC1YNfP', NULL, '66.249.65.70', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.6613.113 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibkp5eW1yUEY5cG50NUdXbnh3ZVhVR2x0dGNCZXloYnZOT0RXZkNzZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly93d3cuY3J1c3Rob3VzZS5jb20ucGsiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1725879973),
('BYXK0qk5HWkswV6wJl6GR6Ps3nwQXasKuH4u1iUa', NULL, '152.32.205.193', 'curl/7.29.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR2MxQUVzS0xxYTQ4RGlDTkRHb3owRThKV0JZUmRVVVpsZGFrazdReCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjQ6Imh0dHA6Ly9jcnVzdGhvdXNlLmNvbS5wayI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1725815085),
('CBKKJCNZ9O5GtzaKGREbbX54uVHaizr91pLEERTw', NULL, '66.249.70.133', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.6613.113 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTGNhcTdQcnM0ZklPWkVJczNoRTJ2NGRKSjJpdDdvN3dKN0lKMURTUyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vd3d3LmNydXN0aG91c2UuY29tLnBrIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725792013),
('cDkpVxh0JqCfQWn5VIH18mIGRpRVylCgayaZqyEI', NULL, '205.210.31.27', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicndGRGZ5SW5adXZkYVZBWFp0RVQ2UUM2YnZnYWlIWHgxYm9sSDFaTCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly9tYWlsLmNydXN0aG91c2UuY29tLnBrIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725808085),
('ClH4i1fX15IbicDAMfhbQKmfF1sBV3BHi5cNlmT0', NULL, '101.36.106.89', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 8_1_1) AppleWebKit/600.43 (KHTML, like Gecko) Chrome/53.0.418 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSldaZVhmZGpXT09HOXdiY0l6MHVKRzh4RkJLckVzYmxuSHYzVnhjaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1725818464),
('e5IqPBEURr6Lu4UNpfGMFk4dM78aTnP6bPl6knhr', NULL, '66.249.73.225', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.6613.113 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWTdhOGMzaTNwSVljeG1xN2R3SEdZYkRadDEwdjBvUXhvYjY3Y2hxRCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1725833252),
('eiib775fMN7HQG57VOeVuA8rnteEFCrg5aoKfyky', NULL, '172.176.75.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.6422.114 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUjNZUEpyc0FIZTdZbldEbWtiRnRiMHRvWG93N3VTQkx2RjVRT1B6WSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1725835691),
('GgiW3pTHawQ3OmupYg0eHypaEldHKIV4Ym5vTruI', NULL, '72.14.201.57', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiZDhnNlN1Y1k4ZVg3dUVqTVdydmxqeXhRck1vR1FDeW04TVBHMTdsNSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725826207),
('GOY2t0mopmicgo4rsikO3kv1Yx2ZvvYOBTPcSZbW', NULL, '20.169.168.224', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.6422.78 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSmlRZzJGREFJZTNDYlJseDY4Z1RFeTBrekV1amdEcndJaU9JUk1ZaSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1725888919),
('GY6Vvfc8gzqILZxarP8a6ywNuwr55b7QfFeVwLgC', NULL, '69.171.231.2', 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidGQyRmdNaUs1Tm5sckRDd0czMVk4YkNOZ3Y1dWJ4Qm5HNE9nV2RIOSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vd3d3LmNydXN0aG91c2UuY29tLnBrIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725817783),
('haUjpQSnsyx2EjSgTFTnjuOD4UApnWAL5RsSKOz4', NULL, '54.36.148.159', 'Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidGFWemIyeFBYblJUU3N2Nks4eXNOWThha1dRckJ4MGJDZGNKYjRSUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1725859671),
('he75ElRWNyq1mOFwuaAtRwLiidY4KuPelJnTVwBX', NULL, '156.252.23.189', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaExibE9Ja2VzUFRIc1VzYmFxTk9kdzkyaFgyYlBITU1DVGY1d0kxdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1725818372),
('iqCkvMj04IUIJBxFDK0aXjdJLaROATjl66I74VgL', NULL, '103.53.162.41', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia3dFOXVlZ21yaW5NdHBEMm1nZEFwS016SHJrUHhFeVg0WkZoOXVRUSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vd3d3LmNydXN0aG91c2UuY29tLnBrIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725826027),
('IQwS8CdxVHAzPAXRlNHKk6WEk4omYrzA1c3vpEWm', NULL, '178.25.242.225', 'Mozilla/5.0 (compatible; MJ12bot/v1.4.8; http://mj12bot.com/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRlhaaUw1eW1QbmJTcFR1VjVybll5WW80eXVXdTVaMnVYSUpJTnl2SyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly9jcnVzdGhvdXNlLmNvbS5way92aWV3Rm9yZ290UGFzc3dvcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1725819603),
('jglj7IGHVTYZDhr9K8raMD83OHEKc004RGEc9FcX', NULL, '20.169.168.224', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.6422.143 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRDZWNUZQdldUVHpGYUR1YnlzcFlBSXVHaUJ3Y3d0RlFZalNsSjN5eCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsvb25saW5lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725889112),
('knRECcaY5CZ09Q2Iwfwn1z1o0iMMocO1W9npMXHk', NULL, '182.187.151.37', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/128.0.6613.98 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWEl5TzJVWDY5Tkdrb0dOTWFnQkFad3paZlJPN0FqVlFmMzY3c2d5VyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmNydXN0aG91c2UuY29tLnBrL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725817944),
('lHvJar5x5wJTWMBJfKp9AupBtU7ZyBf2CFEniUOK', NULL, '172.176.75.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.6422.142 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR2JMamdlSnFOR3JVU3RQQWZ2NG1tbzY3bnNGcThCcnZpQlNSd0ZuRSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsvb25saW5lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725835792),
('LR1lWXiXNTsN0oFhLmVhm195IcRJ3oEXbJMPQ3MJ', NULL, '192.99.15.34', 'Mozilla/5.0 (compatible; MJ12bot/v1.4.8; http://mj12bot.com/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR0hDYW5XMTBHVjZ6aUhRYnBuUzhyWnF1NlgwZk5IZzF5ZmVQdEF4RSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9jcnVzdGhvdXNlLmNvbS5way8/Y2F0PTEiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1725794028),
('NLQ2waBo4OZEQOQTSgkI3vdmS0tMW6m5MdxJl3Bc', NULL, '66.249.64.4', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.6613.113 Mobile Safari/537.36 (compatible; GoogleOther)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMEpqYjdtYnFPNkhneWt6OHJlUWdabE5sR042WHBXamxTdFI5dnhUbSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsvY3VzdG9tZXJGb3Jnb3RQYXNzd29yZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1725786145),
('nQdcHLmB2BPN9BOLfQ1EtYBHMbgIfVwMoUiHeE28', NULL, '72.14.201.61', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Mobile Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiR1pEQjhPRzRXTzhmNDBFQVlSVFc5SUxsRUJSOU9VbmNycmxZaDVONCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725832495),
('OMBhyTEItr8Fn9knn89AbuIwsAIs4WGkbmBFTttx', NULL, '172.176.75.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.6523.4 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiV1BtSnFFdE9WdTNPTUhGQUwxYmxxakgwS0FKdlBPWk9zbkhFMVhkcCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1725888790),
('opcS1j2R8lEWadmxHCE1Dg46qVi0exMSyZ36cH1K', NULL, '203.99.181.88', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoieGo2ZTg5NVZzelpVdVJYTjA4WmNPRmpEb25xVk1zREV3c1RSanhrTCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjExOiJwcm9maWxlX3BpYyI7czoxNDoiMTcyNTY0MTQ3OC5wbmciO3M6MTU6InRvdGFsQ2F0ZWdvcmllcyI7aToxMjtzOjEzOiJ0b3RhbFByb2R1Y3RzIjtpOjE1MDtzOjExOiJ0b3RhbFN0b2NrcyI7aToxO3M6MTI6InRvdGFsUmV2ZW51ZSI7czo0OiI3LjZrIjtzOjEzOiJOb3RpZmljYXRpb25zIjtPOjM5OiJJbGx1bWluYXRlXERhdGFiYXNlXEVsb3F1ZW50XENvbGxlY3Rpb24iOjI6e3M6ODoiACoAaXRlbXMiO2E6MDp7fXM6Mjg6IgAqAGVzY2FwZVdoZW5DYXN0aW5nVG9TdHJpbmciO2I6MDt9fQ==', 1725882148),
('ozJIp9nHx3phANdRqPn81T1kY3w1CJMHjLyEYBj5', NULL, '66.249.64.4', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.6613.113 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNU91QUM4N1JDTXljMTBHVTYza09oNDB6Z2NrODUwaEE1aXRmWlE2ZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsvb25saW5lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725772636),
('PO5SVELUJASdcyTFSHbTrW9lELAeLs1D9Bwu2dzr', NULL, '192.99.36.126', 'Mozilla/5.0 (compatible; MJ12bot/v1.4.8; http://mj12bot.com/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidjRjRTd0bmVUaWJqVVlQNEhOalFoNHM4OGU0azlWME5mVnZNMmIyRSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly9jcnVzdGhvdXNlLmNvbS5way9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1725801162),
('PzEL2VpIp6tgHmrlUDBkcA746Icj9waJZrYBGce2', NULL, '192.99.15.34', 'Mozilla/5.0 (compatible; MJ12bot/v1.4.8; http://mj12bot.com/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYlZ5bXdwN1Azelc1UmJHM0IxN3JzWlM5TFJ3N0pFTEhzR2U0bWltZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly9jcnVzdGhvdXNlLmNvbS5way8/cD0xIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725794032),
('q34D8MSPmlD0Yv3GRDqDttuWpAwGD1ZnGx0yxmTJ', NULL, '115.186.190.27', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_6_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.6 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRXdpZks4SHdUZjRuclN1cFJrRmZlQ0FvWEh5SmpudEZPTTZNeTAyUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsvcmVnaXN0ZXJlZEN1c3RvbWVyIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725803331),
('q64q2dIFGui2z0TmliAz7qgcUEBhQbYgDBmY3Ena', NULL, '111.68.98.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNmVSWWNqYmJHVXcyV3dRNFpQbGRSTlBtWXRic3g2NkpUbXd6MjhSVCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjQ6Imh0dHA6Ly9jcnVzdGhvdXNlLmNvbS5wayI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1725859046),
('QgOUhCIELXOeUvGYOykevwg5LB4RpCJtWQ1oGuen', NULL, '20.169.168.224', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.6523.4 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT25xdHZ2eVFvTjYwU1M5UGFqUnh1eWJFeXJHY2hoemF3V0Y1QlQ1ViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsvb25saW5lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725799480),
('r9FZgUOg8FG33SFkTukGeooiH7GdJGy91Ce8GS2e', NULL, '66.249.64.2', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.6613.113 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNWhsV09mM3V3YVBseVZLd1JJcUtTNzFJc2VNS0RoQ3RCeUF6RTF3dCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsvY3VzdG9tZXJGb3Jnb3RQYXNzd29yZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1725786557),
('RcKPqnRUvf3ArPDfIzo576o92p8bSygcwrdyslKT', NULL, '93.158.91.247', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:109.0) Gecko/20100101 Firefox/115', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSkdYZUQ2TklJN0Npa3NzYTZnRjVhYW5XbFlEdm1YWUNFS2xiekpkRCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjQ6Imh0dHA6Ly9jcnVzdGhvdXNlLmNvbS5wayI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1725816958),
('rNqZNsiwiPAhZwPFf2si6oRoQVOgFy2ds0FuSleM', NULL, '193.186.4.61', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Mobile Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiT3BLME1JYTYyVGxZcnNhV0l6bDFVM1JwbllNWWVzbUFYWXMxVzJBUiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725832511),
('TepfWohnOVma1YYozBpSK9Wy4vkXdakOFn5oiwm3', NULL, '193.186.4.116', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Mobile Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiTjJ6OFhhZGE0bU5rcGtwVW1vMTgzeWYwU01KQWhVVlBYcks3UG4yRCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725891058),
('tinT8eWpfYwvl2EIFqRRRvHOpJCLHwOZcx5aGohb', NULL, '192.99.36.126', 'Mozilla/5.0 (compatible; MJ12bot/v1.4.8; http://mj12bot.com/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVnRHV2trd21zd3RhQ2JiVlVId0RZUGtHUExNVzVkZm9KZkVwVFRNVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9jcnVzdGhvdXNlLmNvbS5way9vbmxpbmUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1725801169),
('TkeuLmtdddev1ztpvnkLET1dbfC9xDxsPW1lBbLd', NULL, '39.40.80.202', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) GSA/331.0.665236494 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicU9kR1RFb3Q4MGJjbVJ4WE9pbWd1WkhPUmtWR1ZHNXhCbnFyRld5byI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1725805281),
('TrkHei1Bt1Y8GQyjACM3jaYERP66ynUlew0I9mXm', NULL, '193.186.4.61', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Mobile Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiTEwwNDUyWmZ0UUZvemNldll6UjZhSDBEOUJLUFo5ZVdsaXYzSm5yTCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725782719),
('ue0pRcuGgLYgDjkyMivA8AtYvJbYybLscJs8UdSM', NULL, '152.32.205.193', 'curl/7.29.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMERPeXVETFNwWHp2S2xPb2p6Q0ZNU3dNVnJxQlVjUlRtTndYb0d2bCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1725815088),
('UQmZeKziFLdTd3z0VHfQQ0iekChC0D9bvBG6FNjV', NULL, '167.71.33.123', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNzRkVzZFZ3gwTmh5NnZoVUEzc2RKa3BZTE1qMkx0aHBjbUI0dmUzWSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjQ6Imh0dHA6Ly9jcnVzdGhvdXNlLmNvbS5wayI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1725848213),
('XdP10CdZAeewuxhW2HUGb4bAetzxKfHm1bGGoeU9', NULL, '193.186.4.61', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Mobile Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiTHVRMXNLVVlqS3dqaTNJN0J0R2tCcEFubTJ0SlhaeVJ6Rk4wbE55SiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725825906),
('Xmsvqn3Ol1GT8XYGNqi5vYmP6fx8IFhdbovLqAFu', NULL, '192.99.15.34', 'Mozilla/5.0 (compatible; MJ12bot/v1.4.8; http://mj12bot.com/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicGZLZWhwQVhVSk4zamh6Q2VTeUE4TUdVdUE0ZmVxQ2pQWUlmNHZQSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsvP3A9MSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1725794033),
('xMTSjoCHsIum9iWMaDApAtNBTn9vMZMtxJuvOjWU', NULL, '152.32.128.85', 'Mozilla/5.0 (Windows NT 8_1_1; Win64; x64) AppleWebKit/553.48 (KHTML, like Gecko) Chrome/96.0.445 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWjNCc2xKSmJncVVBazU0cFJTVXE0VmQ0dm5zQ1QyWVpmcm9nZkw1YiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjQ6Imh0dHA6Ly9jcnVzdGhvdXNlLmNvbS5wayI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1725818491),
('yQcd3HSNVRVZIXNEKZXwt3fIVBEe5iOezQoKEn93', NULL, '66.249.65.69', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.6613.113 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieGRUTGd6TExNWUtMMGVGTTdpYzA5YklYOFdlOU9nTTVYS0VtcTRsSiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHBzOi8vd3d3LmNydXN0aG91c2UuY29tLnBrL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725876382),
('YwJ5yGBlhKT0BO71HHnz7rhvFeFSr5BQjSgZiZgm', NULL, '111.68.98.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic3Rva2VnOEZKVklqWUlHYUdwZjRVNnQ5Q2p5YzBUMVpkQTZyMTN6VCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjQ6Imh0dHA6Ly9jcnVzdGhvdXNlLmNvbS5wayI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1725859039),
('ZLkMI3B1QgwtAf7AXdKirtUcJ1CPLPYeUyso3sWM', NULL, '192.99.15.34', 'Mozilla/5.0 (compatible; MJ12bot/v1.4.8; http://mj12bot.com/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNG5vdWUzeWVST2FxNEU0cXVHMEVmcUxHeVJvcExLVld5SjBtckc4OSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY3J1c3Rob3VzZS5jb20ucGsvP2NhdD0xIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1725794030);

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `id` bigint UNSIGNED NOT NULL,
  `itemName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `itemQuantity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mimimumItemQuantity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unitPrice` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stocks`
--

INSERT INTO `stocks` (`id`, `itemName`, `itemQuantity`, `mimimumItemQuantity`, `unitPrice`, `branch_id`, `created_at`, `updated_at`) VALUES
(1, 'All-purpose flour', '4 kg', '1.00 kg', '100.00 Pkr', 1, '2024-08-05 12:37:13', '2024-08-05 12:48:46'),
(2, 'Flour123', '96.9 kg', '20kg', '100 Pkr', 4, '2024-08-08 09:19:23', '2024-08-21 12:08:36');

-- --------------------------------------------------------

--
-- Table structure for table `stock_histories`
--

CREATE TABLE `stock_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `itemName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `itemQuantity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mimimumItemQuantity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unitPrice` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_histories`
--

INSERT INTO `stock_histories` (`id`, `itemName`, `itemQuantity`, `mimimumItemQuantity`, `unitPrice`, `branch_id`, `created_at`, `updated_at`) VALUES
(1, 'All-purpose flour', '10.00 kg', '1.00 kg', '100.00 Pkr', 1, '2024-08-05 12:37:13', '2024-08-05 12:37:13'),
(2, 'Flour123', '100.50 kg', '20.00 kg', '100.00 Pkr', 4, '2024-08-08 09:19:23', '2024-08-08 09:19:23');

-- --------------------------------------------------------

--
-- Table structure for table `taxes`
--

CREATE TABLE `taxes` (
  `id` bigint UNSIGNED NOT NULL,
  `tax_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_value` decimal(8,2) NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taxes`
--

INSERT INTO `taxes` (`id`, `tax_name`, `tax_value`, `branch_id`, `created_at`, `updated_at`) VALUES
(2, 'GST', 2.50, 1, '2024-08-06 05:47:47', '2024-08-06 05:47:47'),
(3, 'GST', 2.50, 4, '2024-08-08 10:36:33', '2024-08-08 10:36:33');

-- --------------------------------------------------------

--
-- Table structure for table `theme_settings`
--

CREATE TABLE `theme_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `pos_logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pos_primary_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pos_secondary_color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `theme_settings`
--

INSERT INTO `theme_settings` (`id`, `pos_logo`, `pos_primary_color`, `pos_secondary_color`, `branch_id`, `created_at`, `updated_at`) VALUES
(1, '1725352575.jpg', '#ff4000', '#ffffff', 1, '2024-08-05 11:46:34', '2024-09-03 13:36:15'),
(3, '1724219522.png', '#000000', '#000000', 4, '2024-08-08 10:43:31', '2024-08-21 10:52:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'branchManager',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `profile_picture`, `name`, `email`, `phone_number`, `role`, `email_verified_at`, `password`, `branch_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, '1724055028.png', 'Danish Ejaz', 'anonymouscode786@gmail.com', '+923028933706', 'owner', '2024-08-30 14:56:12', '$2y$12$MYIoQ5GiP9IjlQAv4dqKG.SpSfupScHEzuBxM1GBqEyHwgjXKwrhK', NULL, NULL, '2024-07-19 07:27:28', '2024-09-06 22:01:25'),
(2, '1725641478.png', 'Muhammad Ali', 'muhammadali@gmail.com', NULL, 'branchManager', NULL, '$2y$12$v6QNQ7zoY7wf2IMnOnYc3u3GMH7q8exRzmmcullWZpxX8DU3/zici', 1, 'IUWSbenCKT14Jjzue7GrqCVZi7JBwKHv', '2024-08-05 10:40:45', '2024-09-06 21:51:18'),
(10, '1722927006.jpeg', 'Hassan Ali', 'hassanali@gmail.com', NULL, 'salesman', NULL, '$2y$12$pB004wYDn2X0.wrohPA1UuEE9tIun95rSWTp4kc70EBOLGerGUI.O', 1, NULL, '2024-08-06 06:50:06', '2024-08-29 11:44:07'),
(12, '1722931250.jpg', 'Salesman ISB', 'chefisb@gmail.com', NULL, 'chef', NULL, '$2y$12$/AwShzv7kxgVNPASRNiddej6r.kyvdgnDfW6jSPvhCRr22H393xaS', 1, NULL, '2024-08-06 08:00:50', '2024-08-19 15:53:11'),
(13, NULL, 'Muneeb Malik', 'anonkhan2k14@gmail.com', NULL, NULL, NULL, '$2y$12$SeIPsqVDLod8gPpIAlLZbOiKN856zgneSUcpBl9YvU.9erSfIFTFe', NULL, NULL, '2024-08-07 06:31:56', '2024-08-07 06:31:56'),
(14, NULL, 'sikander kiani', 'sikanderkiani999@yahoo.com', NULL, NULL, NULL, '$2y$12$XxL5lEYkpITmZkoQas42pes.a.Hg35/ENV0KBrgECC8ZTBquPOKJK', NULL, NULL, '2024-08-07 14:03:40', '2024-08-07 14:03:40'),
(16, '1724219426.png', 'jaanu maanu', 'hammad@gmail.com', NULL, 'branchManager', NULL, '$2y$12$zuM3ZIoFRmIdnW/.kE/iNej3gIWi21Ez3Kz3ZjwlNdnPiZ5eALRQS', 4, NULL, '2024-08-08 06:16:52', '2024-08-21 10:50:26'),
(25, '1723109351.png', 'hassan', 'hassan@gmail.com', NULL, 'salesman', NULL, '$2y$12$rT/FCZHOAr/fMp3uY3YgwuHRLXN4x21aU8quYPCsu1FrCgCgzvp1y', 4, NULL, '2024-08-08 09:29:11', '2024-08-08 09:29:11'),
(26, '1723109378.png', 'ahmed', 'ahmed@gmail.com', NULL, 'chef', NULL, '$2y$12$OZrJcHptLOXjkLgaXM49Gex5vDh4OWE4loD5pl9G1aUBqOnvgNeZy', 4, NULL, '2024-08-08 09:29:38', '2024-08-08 10:15:47'),
(28, '1723109772.png', 'ayesha', 'ayesha@gmail.com', NULL, 'salesman', NULL, '$2y$12$ZZ0p.jiCIG30hTMPqcrxwObVRtxWsRYtX4wQ44bppjpJF7LCwg/72', 4, NULL, '2024-08-08 09:36:13', '2024-08-08 09:36:13'),
(32, NULL, 'Muzamil', 'manobilli6355@gmail.com', NULL, NULL, NULL, '$2y$12$exdJM9riWT/xLW2T2gTKoeCmMzg8KE7nDu5ZwON6cKvl5ifVBe8gK', NULL, NULL, '2024-08-28 16:16:33', '2024-08-28 16:16:33'),
(46, NULL, 'Danish Ejaz', 'm512d786@gmail.com', '+923028933706', 'customer', '2024-09-06 21:21:00', '$2y$12$XuT0HF8iSZe76v47o5npfuMNL5F.VmJbVzDnfm9NKCN.so5Jba0vm', NULL, NULL, '2024-09-06 21:20:48', '2024-09-06 21:39:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_branch_code_unique` (`branch_code`);

--
-- Indexes for table `branch_categories`
--
ALTER TABLE `branch_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_categories_category_id_foreign` (`category_id`),
  ADD KEY `branch_categories_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carts_salesman_id_foreign` (`salesman_id`),
  ADD KEY `carts_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `deals`
--
ALTER TABLE `deals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deals_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `dine_in_tables`
--
ALTER TABLE `dine_in_tables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dine_in_tables_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discounts_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `handlers`
--
ALTER TABLE `handlers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `handlers_deal_id_foreign` (`deal_id`),
  ADD KEY `handlers_product_id_foreign` (`product_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `online_notifications`
--
ALTER TABLE `online_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_branch_id_foreign` (`branch_id`),
  ADD KEY `orders_salesman_id_foreign` (`salesman_id`),
  ADD KEY `orders_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_methods_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipes_category_id_foreign` (`category_id`),
  ADD KEY `recipes_product_id_foreign` (`product_id`),
  ADD KEY `recipes_stock_id_foreign` (`stock_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stocks_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `stock_histories`
--
ALTER TABLE `stock_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_histories_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `taxes`
--
ALTER TABLE `taxes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `taxes_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `theme_settings`
--
ALTER TABLE `theme_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `theme_settings_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_branch_id_foreign` (`branch_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `branch_categories`
--
ALTER TABLE `branch_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `deals`
--
ALTER TABLE `deals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `dine_in_tables`
--
ALTER TABLE `dine_in_tables`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `handlers`
--
ALTER TABLE `handlers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_notifications`
--
ALTER TABLE `online_notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `stock_histories`
--
ALTER TABLE `stock_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `taxes`
--
ALTER TABLE `taxes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `theme_settings`
--
ALTER TABLE `theme_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `branch_categories`
--
ALTER TABLE `branch_categories`
  ADD CONSTRAINT `branch_categories_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `branch_categories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carts_salesman_id_foreign` FOREIGN KEY (`salesman_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `deals`
--
ALTER TABLE `deals`
  ADD CONSTRAINT `deals_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dine_in_tables`
--
ALTER TABLE `dine_in_tables`
  ADD CONSTRAINT `dine_in_tables_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discounts`
--
ALTER TABLE `discounts`
  ADD CONSTRAINT `discounts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `handlers`
--
ALTER TABLE `handlers`
  ADD CONSTRAINT `handlers_deal_id_foreign` FOREIGN KEY (`deal_id`) REFERENCES `deals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `handlers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_salesman_id_foreign` FOREIGN KEY (`salesman_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD CONSTRAINT `payment_methods_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipes_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stocks`
--
ALTER TABLE `stocks`
  ADD CONSTRAINT `stocks_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_histories`
--
ALTER TABLE `stock_histories`
  ADD CONSTRAINT `stock_histories_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `taxes`
--
ALTER TABLE `taxes`
  ADD CONSTRAINT `taxes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `theme_settings`
--
ALTER TABLE `theme_settings`
  ADD CONSTRAINT `theme_settings_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
