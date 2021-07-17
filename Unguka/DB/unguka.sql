-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 02, 2019 at 07:38 AM
-- Server version: 10.4.8-MariaDB
-- PHP Version: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `unguka`
--

-- --------------------------------------------------------

--
-- Table structure for table `cropfarms`
--

CREATE TABLE `cropfarms` (
  `id` int(10) UNSIGNED NOT NULL,
  `farmID` int(10) UNSIGNED NOT NULL,
  `seasonID` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `cropsID` int(10) UNSIGNED NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cropfarms`
--

INSERT INTO `cropfarms` (`id`, `farmID`, `seasonID`, `user_id`, `cropsID`, `status`, `created_at`, `updated_at`) VALUES
(38, 1, 2, 1, 1, 0, '2019-10-01 16:49:46', '2019-10-01 17:55:50'),
(39, 2, 1, 1, 1, 0, '2019-10-10 20:38:16', '2019-10-10 20:38:16'),
(40, 3, 1, 1, 3, 0, '2019-10-10 21:12:22', '2019-10-10 21:12:22'),
(41, 10, 1, 4, 2, 0, '2019-10-11 14:22:24', '2019-10-11 14:22:24'),
(42, 2, 1, 1, 1, 1, '2019-10-11 16:29:32', '2019-10-11 16:29:32'),
(49, 17, 1, 28, 3, 0, '2019-10-12 13:33:40', '2019-10-12 13:33:40'),
(50, 18, 1, 28, 1, 0, '2019-10-12 13:37:55', '2019-10-12 13:37:55'),
(51, 18, 1, 28, 2, 0, '2019-10-12 13:40:43', '2019-10-12 13:40:43'),
(53, 26, 1, 30, 1, 0, '2019-10-12 16:55:34', '2019-10-12 16:55:34');

-- --------------------------------------------------------

--
-- Table structure for table `crops`
--

CREATE TABLE `crops` (
  `id` int(10) UNSIGNED NOT NULL,
  `crops` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crops`
--

INSERT INTO `crops` (`id`, `crops`, `photo`, `created_at`, `updated_at`) VALUES
(1, 'Casava', 'http://res.cloudinary.com/hviewtech/image/upload/c_fit,h_119,w_183/c7z1rlxvszxrflzc2cgj.png', '2019-09-30 12:39:11', '2019-10-04 16:17:11'),
(2, 'Beans', 'http://res.cloudinary.com/hviewtech/image/upload/c_fit,h_408,w_612/dqgcfgxgndxo9jcwbfvz.png', '2019-09-30 12:40:45', '2019-09-30 12:40:45'),
(3, 'Maize', 'http://res.cloudinary.com/hviewtech/image/upload/c_fit,h_185,w_272/iyqkfw0uupccfmtuadga.png', '2019-09-30 12:41:09', '2019-09-30 12:41:09');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(10) UNSIGNED NOT NULL,
  `fname` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lname` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identity` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `fname`, `lname`, `phone`, `identity`, `photo`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Danat', 'Rwatubyaye', '07826363344', '1273737388449', 'http://res.cloudinary.com/hviewtech/image/upload/c_fit,h_640,w_640/blbgyn3qelfnjajv0dnq.png', 2, '2019-09-30 12:00:42', '2019-10-11 18:42:20'),
(4, 'Adrie', 'Muhire', '0781167270', '2381783909812343', 'http://res.cloudinary.com/hviewtech/image/upload/c_fit,h_868,w_720/btvii9svcz5bejvbheg9.png', 29, '2019-10-12 13:46:22', '2019-10-12 13:46:22'),
(5, 'danat', 'murego', '0782001122', '1996123112345621', 'http://res.cloudinary.com/hviewtech/image/upload/c_fit,h_800,w_480/qin6id4wvxfalqfzrdzr.png', 31, '2019-10-12 20:38:49', '2019-10-12 20:38:49');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(10) UNSIGNED NOT NULL,
  `farmID` int(10) UNSIGNED NOT NULL,
  `cropfarmID` int(11) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `titles` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `moneySpent` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `farmID`, `cropfarmID`, `user_id`, `titles`, `description`, `moneySpent`, `status`, `created_at`, `updated_at`) VALUES
(23, 1, 38, 1, 'Transport', 'Transport from  rwamagana to kibungo', '250', 0, '2019-10-10 16:47:35', '2019-10-10 16:47:35'),
(24, 1, 38, 1, 'Transport', 'Transport from  rwamagana to kibungo', '1500', 0, '2019-10-10 16:54:09', '2019-10-10 16:54:09'),
(25, 1, 38, 1, 'seeds', '46 kg', '1000', 0, '2019-10-10 17:03:31', '2019-10-10 17:03:31'),
(26, 1, 39, 1, 'Ifumbire', '24kg byidumbire', '2000', 0, '2019-10-10 20:39:16', '2019-10-10 20:39:16'),
(27, 3, 40, 1, 'Seeds', 'i bought 50 kg which cost 500 per kg', '1200', 0, '2019-10-10 21:13:29', '2019-10-10 21:13:29'),
(28, 3, 40, 1, 'Ifumbire', 'Ibiro birindwi byifumbire', '560', 0, '2019-10-10 21:14:21', '2019-10-10 21:14:21'),
(29, 10, 41, 4, 'Seed', 'I bough 45 kg of beans which cost 500 per kg', '5700', 0, '2019-10-11 14:23:36', '2019-10-11 14:23:36'),
(30, 10, 41, 4, 'Felterize', 'I bought 10kg of felterize which cost 1500 per kg', '15000', 0, '2019-10-11 14:27:02', '2019-10-11 14:27:02'),
(38, 17, 49, 28, 'Ifumbire', 'naguze ibiro bitanu byifumbire yo gukoresha ikiro kimwe ni 120', '6000', 0, '2019-10-12 13:34:46', '2019-10-12 13:34:46'),
(39, 18, 50, 28, 'Imbuto', 'Naguze imbuto', '2000', 0, '2019-10-12 13:38:21', '2019-10-12 13:38:21'),
(40, 18, 51, 28, 'Ifumbire', 'Yogukoresha', '2000', 0, '2019-10-12 13:41:02', '2019-10-12 13:41:02');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `farmers`
--

CREATE TABLE `farmers` (
  `id` int(10) UNSIGNED NOT NULL,
  `fname` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lname` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identity` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `farmers`
--

INSERT INTO `farmers` (`id`, `fname`, `lname`, `phone`, `identity`, `photo`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Kwizera', 'Emmanuel', '0783849999', '119914567388090', 'http://res.cloudinary.com/hviewtech/image/upload/c_fit,h_3264,w_4928/jjnrhktxi9qvkr1iofok.png', 1, '2019-09-30 12:11:08', '2019-10-11 19:51:09'),
(2, 'Donat', 'Murego', '0781167278', '1253738496489000', 'http://res.cloudinary.com/hviewtech/image/upload/c_fit,h_185,w_272/yyqkgjw0pppunelhof8m.png', 4, '2019-09-30 14:29:26', '2019-10-11 14:53:28'),
(8, 'Felix', 'Niyonzima', '0781167271', '1278367234675621', 'http://res.cloudinary.com/hviewtech/image/upload/c_fit,h_640,w_640/hn9sf03dsfigxdeiffkd.png', 28, '2019-10-12 13:27:53', '2019-10-12 13:27:53'),
(9, 'Maic', 'sebakara', '0786091893', '1234567865421547', 'http://res.cloudinary.com/hviewtech/image/upload/c_fit,h_800,w_480/zzy3ryzxhnjz2nppns2u.png', 30, '2019-10-12 15:39:26', '2019-10-12 15:39:26');

-- --------------------------------------------------------

--
-- Table structure for table `farms`
--

CREATE TABLE `farms` (
  `id` int(10) UNSIGNED NOT NULL,
  `UPI` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plotsize` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `farms`
--

INSERT INTO `farms` (`id`, `UPI`, `location`, `plotsize`, `user_id`, `status`, `created_at`, `updated_at`) VALUES
(1, '234', 'Kigali/Kicukiro', '4000m', 1, 1, '2019-09-30 12:11:21', '2019-09-30 12:57:11'),
(2, '267', 'Rwamagana/Musha', '49000m', 1, 1, '2019-09-30 17:13:30', '2019-09-30 17:13:30'),
(3, '122', 'rusizi', '2345m', 1, 1, '2019-10-01 20:36:57', '2019-10-07 11:59:24'),
(10, '890', 'Kayonza/Murambi', '7000', 4, 1, '2019-10-11 14:21:43', '2019-10-11 14:21:43'),
(17, 'UPI/128/277/09', 'Kayonza/Murambi', '2500m', 28, 1, '2019-10-12 13:28:15', '2019-10-12 13:28:15'),
(18, 'UPI/128/277/10', 'Kayonza/Rwagitima', '2900m', 28, 1, '2019-10-12 13:37:39', '2019-10-12 13:37:39'),
(26, 'UPI/12/023/01', 'Rwamagana/musha', '12', 30, 1, '2019-10-12 16:55:22', '2019-10-12 16:55:22');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_09_13_090757_create_farmers_table', 1),
(5, '2019_09_13_090914_create_customers_table', 1),
(6, '2019_09_13_090915_create_seasons_table', 1),
(7, '2019_09_13_090951_create_farms_table', 1),
(8, '2019_09_13_091539_create_payments_table', 1),
(9, '2019_09_24_091641_create_crops_table', 1),
(10, '2019_09_24_091642_create_stocks_table', 1),
(11, '2019_09_24_091643_create_orders_table', 1),
(12, '2019_09_24_091644_create_expenses_table', 1),
(13, '2019_09_30_131555_create_cropfarms_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `stockID` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `stockID`, `quantity`, `user_id`, `status`, `created_at`, `updated_at`) VALUES
(16, 30, 50, 2, '3', '2019-10-10 20:40:45', '2019-10-14 14:04:14'),
(17, 31, 30, 2, '0', '2019-10-10 20:48:13', '2019-10-14 14:02:22'),
(18, 30, 67, 2, '3', '2019-10-10 21:20:41', '2019-10-14 14:02:50'),
(19, 32, 23, 2, '1', '2019-10-10 23:04:12', '2019-10-14 14:03:01'),
(21, 33, 140, 2, '0', '2019-10-11 14:32:51', '2019-10-14 13:53:20'),
(22, 32, 33, 2, '0', '2019-10-11 18:22:27', '2019-10-14 14:03:14'),
(24, 31, 41, 2, '3', '2019-10-11 23:55:07', '2019-10-14 14:02:34'),
(25, 40, 42, 29, '1', '2019-10-12 13:47:30', '2019-10-12 13:47:30'),
(26, 32, 30, 29, '3', '2019-10-12 14:14:47', '2019-10-12 14:14:47');

-- --------------------------------------------------------

--
-- Table structure for table `passwordresets`
--

CREATE TABLE `passwordresets` (
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `amount` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seasons`
--

CREATE TABLE `seasons` (
  `id` int(10) UNSIGNED NOT NULL,
  `seasonLenght` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seasons`
--

INSERT INTO `seasons` (`id`, `seasonLenght`, `status`, `created_at`, `updated_at`) VALUES
(1, '9-12', '1', '2019-09-19 11:28:32', '2019-09-17 08:07:22'),
(2, '1-6', '', '2019-09-14 14:47:03', '2019-09-14 14:47:03');

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `id` int(10) UNSIGNED NOT NULL,
  `quantity` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cropfarmID` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stocks`
--

INSERT INTO `stocks` (`id`, `quantity`, `price`, `status`, `cropfarmID`, `user_id`, `created_at`, `updated_at`) VALUES
(30, '315', '350', '0', 39, 1, '2019-10-10 20:39:46', '2019-10-10 20:39:46'),
(31, '230', '300', '1', 38, 1, '2019-10-10 20:47:26', '2019-10-10 20:47:26'),
(32, '453', '175', '1', 40, 1, '2019-10-10 21:14:58', '2019-10-14 12:48:01'),
(33, '140', '300', '1', 41, 4, '2019-10-11 14:27:32', '2019-10-11 14:27:32'),
(39, '125', '250', '0', 49, 28, '2019-10-12 13:35:37', '2019-10-12 13:35:37'),
(40, '88', '100', '1', 50, 28, '2019-10-12 13:38:47', '2019-10-12 13:43:42'),
(41, '300', '120', '0', 51, 28, '2019-10-12 13:41:27', '2019-10-12 13:41:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `fullname` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phoneverifiedat` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `phone`, `phoneverifiedat`, `password`, `level`, `status`, `code`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Kwizera', '0781167275', NULL, '$2y$10$VUXyhr78oyEyGMj2Nb6p.uhVC5OD3at3Q8/UPFlppjyXYDWV/KzwS', 1, 1, NULL, NULL, '2019-09-30 11:44:46', '2019-10-07 21:15:04'),
(2, 'Kwizera', '0781167276', NULL, '$2y$10$6BIgCWW6MxPPHEjUrkBujO.JU2fbN76yLUYjwFyhGuK9BdRkYFvSy', 2, 1, NULL, NULL, '2019-09-30 12:00:15', '2019-10-11 15:58:00'),
(4, 'Kwizera', '0781167278', NULL, '$2y$10$gF9wYhmOlv7vHEmTUhfKj.uobaw7seQI11O0RaWwL9lV6iLxVLM0C', 1, 1, NULL, NULL, '2019-09-30 12:54:40', '2019-09-30 12:54:40'),
(28, 'Felix', '0781167271', NULL, '$2y$10$BiH74PyFTfhoD3BQR31QXuDdUgIjyzQRIVJlsSgFRtYdppie12TVG', 1, 1, NULL, NULL, '2019-10-12 13:27:08', '2019-10-12 13:27:08'),
(29, 'Adrie', '0781167270', NULL, '$2y$10$JKaGeJxmB3cn1M4dP4GKbuDCfn/7lq5L8lOl5Ka6yjdPrx7S1l7Wa', 2, 1, NULL, NULL, '2019-10-12 13:45:29', '2019-10-12 13:45:29'),
(30, 'maic', '0786091893', NULL, '$2y$10$DPZtl2Hhxynv1H8KyJqYsObjD1/DEvgjYF.z.NsNGphDxJOI7WkxW', 1, 1, NULL, NULL, '2019-10-12 14:57:37', '2019-10-12 14:57:37'),
(31, 'Emiliano', '0726140850', NULL, '$2y$10$0kWcKpwo0F9JWL8RfTaMJe05ahAGP2Ez9npWPMjFLdZXxj8Qr91De', 2, 1, NULL, NULL, '2019-10-12 20:11:39', '2019-10-12 20:11:39'),
(32, 'muhoza', '0781167279', NULL, '$2y$10$ZGn3B96r1/mv0lXhFl6l.ubia4SWDolHsNfuTQlyEZ5MhvhWIy5nW', 3, 1, NULL, NULL, '2019-10-15 11:34:41', '2019-10-15 11:34:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cropfarms`
--
ALTER TABLE `cropfarms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cropfarms_farmid_foreign` (`farmID`),
  ADD KEY `cropfarms_seasonid_foreign` (`seasonID`),
  ADD KEY `cropfarms_user_id_foreign` (`user_id`),
  ADD KEY `cropfarms_cropsid_foreign` (`cropsID`);

--
-- Indexes for table `crops`
--
ALTER TABLE `crops`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_identity_unique` (`identity`),
  ADD KEY `customers_user_id_foreign` (`user_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_user_id_foreign` (`user_id`),
  ADD KEY `cropsID` (`farmID`),
  ADD KEY `cropfarmID` (`cropfarmID`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `farmers`
--
ALTER TABLE `farmers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `farmers_user_id_foreign` (`user_id`);

--
-- Indexes for table `farms`
--
ALTER TABLE `farms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `farms_user_id_foreign` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_stockid_foreign` (`stockID`),
  ADD KEY `orders_user_id_foreign` (`user_id`);

--
-- Indexes for table `passwordresets`
--
ALTER TABLE `passwordresets`
  ADD KEY `passwordresets_phone_index` (`phone`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_user_id_foreign` (`user_id`);

--
-- Indexes for table `seasons`
--
ALTER TABLE `seasons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stocks_user_id_foreign` (`user_id`),
  ADD KEY `farmID` (`cropfarmID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cropfarms`
--
ALTER TABLE `cropfarms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `crops`
--
ALTER TABLE `crops`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `farmers`
--
ALTER TABLE `farmers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `farms`
--
ALTER TABLE `farms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seasons`
--
ALTER TABLE `seasons`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cropfarms`
--
ALTER TABLE `cropfarms`
  ADD CONSTRAINT `cropfarms_cropsid_foreign` FOREIGN KEY (`cropsID`) REFERENCES `crops` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `cropfarms_farmid_foreign` FOREIGN KEY (`farmID`) REFERENCES `farms` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `cropfarms_seasonid_foreign` FOREIGN KEY (`seasonID`) REFERENCES `seasons` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `cropfarms_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`farmID`) REFERENCES `farms` (`id`),
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`cropfarmID`) REFERENCES `cropfarms` (`id`),
  ADD CONSTRAINT `expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `farmers`
--
ALTER TABLE `farmers`
  ADD CONSTRAINT `farmers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `farms`
--
ALTER TABLE `farms`
  ADD CONSTRAINT `farms_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_stockid_foreign` FOREIGN KEY (`stockID`) REFERENCES `stocks` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `stocks`
--
ALTER TABLE `stocks`
  ADD CONSTRAINT `stocks_ibfk_1` FOREIGN KEY (`cropfarmID`) REFERENCES `cropfarms` (`id`),
  ADD CONSTRAINT `stocks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
