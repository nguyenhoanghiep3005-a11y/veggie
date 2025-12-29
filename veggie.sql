-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 24, 2025 lúc 07:09 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `veggie`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(87, 6, 80, 5, '2025-11-30 02:04:27', '2025-11-30 02:04:27'),
(88, 6, 73, 1, '2025-11-30 02:04:27', '2025-11-30 02:04:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Rau củ', 'rau-cu', 'ngon', 'uploads/categories/rau-cu.png', '2025-11-06 06:17:44', '2025-11-28 03:44:02'),
(2, 'Trái cây', 'trai-cay', 'Trái cây sạch tươi ngon', 'uploads/categories/trai-cay.png', '2025-11-06 06:17:44', '2025-11-06 06:17:44'),
(3, 'Thịt', 'thit', 'Thịt tươi ngon, đảm bảo chất lượng', 'uploads/categories/thit.png', '2025-11-06 06:17:44', '2025-11-06 06:17:44'),
(4, 'Cá ', 'ca', 'Hải sản và cá tươi sống', 'uploads/categories/ca.png', '2025-11-06 06:17:44', '2025-11-06 06:17:44'),
(5, 'Thực phẩm khác ', 'thuc-pham-khac', 'Các loại thực phẩm khác', 'uploads/categories/thuc-pham-khac.png', '2025-11-06 06:17:44', '2025-11-06 06:17:44');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `message` varchar(255) NOT NULL,
  `is_replied` varchar(255) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `contacts`
--

INSERT INTO `contacts` (`id`, `full_name`, `phone_number`, `email`, `message`, `is_replied`, `created_at`, `updated_at`) VALUES
(1, 'Hiep so 1', '0388536385', 'nguyenhoanghiep3005@gmail.com', 'ádasda', '0', '2025-11-30 08:10:15', '2025-11-30 08:10:15');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_10_16_053521_create_roles_table', 1),
(2, '2025_10_16_053647_create_permissions_table', 1),
(3, '2025_10_16_053717_create_role_prermissions_table', 1),
(4, '2025_10_16_053918_create_users_table', 1),
(5, '2025_10_16_053941_create_categories_table', 1),
(6, '2025_10_16_054000_create_products_table', 1),
(7, '2025_10_16_054022_create_products_images_table', 1),
(8, '2025_10_16_054123_create_shipping_addresses_table', 1),
(9, '2025_10_16_054147_create_oder_table', 1),
(10, '2025_10_16_054200_create_oder_items_table', 1),
(11, '2025_10_16_054213_create_payments_table', 1),
(12, '2025_10_16_054234_create_wishlists_table', 1),
(13, '2025_10_16_054254_create_reviews_table', 1),
(14, '2025_10_16_054312_create_notifications_table', 1),
(15, '2025_10_16_054338_create_contacts_table', 1),
(16, '2025_10_16_054408_create_role_order_status_history_table', 1),
(17, '2025_10_16_054437_create_role_cart_items_table', 1),
(18, '2025_10_16_054530_create_role_password_reset_tokens_table', 1),
(19, '2025_11_28_142941_create_cache_table', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `shipping_address_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `shipping_address_id`, `created_at`, `updated_at`) VALUES
(37, 20, 57000.00, 'pending', 9, '2025-12-03 00:02:01', '2025-12-03 00:02:01'),
(38, 20, 57000.00, 'pending', 9, '2025-12-03 00:02:29', '2025-12-03 00:02:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `product_id`, `order_id`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
(36, 70, 37, 1, 6000.00, '2025-12-03 00:02:01', '2025-12-03 00:02:01'),
(37, 71, 37, 1, 11000.00, '2025-12-03 00:02:01', '2025-12-03 00:02:01'),
(38, 72, 37, 1, 15000.00, '2025-12-03 00:02:01', '2025-12-03 00:02:01');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_status_history`
--

CREATE TABLE `order_status_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pendung','processing','shipped','completed','cancelled') NOT NULL,
  `note` text DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `payment_method` enum('cash','paypal') NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `payment_method`, `transaction_id`, `status`, `paid_at`, `amount`, `created_at`, `updated_at`) VALUES
(20, 37, 'cash', NULL, 'pending', NULL, 57000.00, '2025-12-03 00:02:01', '2025-12-03 00:02:01'),
(21, 38, 'paypal', '47Y39908T4008084L', 'completed', '2025-12-03 00:02:29', 57000.00, '2025-12-03 00:02:29', '2025-12-03 00:02:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'manage_user', '2025-10-16 03:49:53', '2025-10-16 03:49:53'),
(2, 'manage_products', '2025-10-16 03:49:53', '2025-10-16 03:49:53'),
(3, 'manage_order', '2025-10-16 03:49:53', '2025-10-16 03:49:53'),
(4, 'manage_categories', '2025-10-16 03:49:53', '2025-10-16 03:49:53'),
(5, 'manage_contacts', '2025-10-16 03:49:53', '2025-10-16 03:49:53');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'in_stock',
  `unit` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `category_id`, `description`, `price`, `stock`, `status`, `unit`, `created_at`, `updated_at`) VALUES
(65, 'Ổi Đài Loan', 'oi-dai-loan-1764490363', 2, 'Ăn ngon hơn khi ướp lạnh, có thể làm nước ép,..', 13500.00, 28, 'int_stock', 'kg', '2025-11-30 01:12:43', '2025-12-02 22:17:57'),
(66, 'Dừa xiêm tiện lợi', 'dua-xiem-tien-loi-1764490844', 2, 'Sản phẩm có sẵn chỗ cắm ống hút và có tặng kèm thêm ống hút', 13500.00, 9, 'int_stock', 'trái', '2025-11-30 01:20:44', '2025-12-02 09:55:54'),
(67, 'Thanh long ruột đỏ', 'thanh-long-ruot-do-1764490933', 2, 'thanh long ngọt', 24000.00, 3, 'int_stock', 'kg', '2025-11-30 01:22:13', '2025-11-30 01:22:13'),
(68, 'Cải bẹ xanh 300gr', 'cai-be-xanh-300gr-1764491032', 1, 'cải tươi', 17000.00, 6, 'int_stock', 'bịch', '2025-11-30 01:23:52', '2025-12-01 02:52:33'),
(70, 'Gừng củ 100g', 'gung-cu-100g-1764491461', 1, 'Gừng củ từ 50g trở lên', 6000.00, 6, 'int_stock', 'kg', '2025-11-30 01:31:01', '2025-12-03 00:02:01'),
(71, 'Hành tây 300gr', 'hanh-tay-300gr-1764491541', 1, 'hành tây tươi', 11000.00, 16, 'int_stock', 'kg', '2025-11-30 01:32:21', '2025-12-03 00:02:01'),
(72, 'Mướp hương trái từ 150g trở lên', 'muop-huong-trai-tu-150g-tro-len-1764491734', 1, 'Mướp hương có màu xanh đậm hoặc xanh nhạt, vỏ thô ráp. Mướp hương ngọt, ngon và thơm nhẹ.', 15000.00, 18, 'int_stock', 'trái', '2025-11-30 01:35:34', '2025-12-03 00:02:01'),
(73, 'Cà rốt', 'ca-rot-1764491975', 1, 'Cà rốt củ từ 150g trở lên', 15000.00, 10, 'int_stock', 'kg', '2025-11-30 01:39:35', '2025-11-30 01:39:35'),
(74, 'Thịt heo xay', 'thit-heo-xay-1764492105', 3, 'thịt ngon', 45000.00, 8, 'int_stock', 'kg', '2025-11-30 01:41:45', '2025-11-30 01:41:45'),
(75, 'Nạc thăn heo', 'nac-than-heo-1764492232', 3, 'nạc tươi', 58000.00, 10, 'int_stock', 'kg', '2025-11-30 01:43:52', '2025-11-30 01:43:52'),
(76, 'Cá diêu hồng', 'ca-dieu-hong-1764492373', 4, 'có giá trị dinh dưỡng cao, giàu protein, vitamin A, B, D và chất khoáng', 33000.00, 8, 'int_stock', 'kg', '2025-11-30 01:46:13', '2025-11-30 01:46:13'),
(77, 'Cá hường', 'ca-huong-1764492591', 4, 'Cá hường làm sạch 500gr', 44000.00, 8, 'int_stock', 'vỉ', '2025-11-30 01:49:51', '2025-11-30 01:49:51'),
(78, 'Cá basa', 'ca-basa-1764492652', 4, 'Cá basa cắt khúc', 30000.00, 20, 'int_stock', 'kg', '2025-11-30 01:50:52', '2025-11-30 01:50:52'),
(79, 'Cá lóc', 'ca-loc-1764492711', 4, 'Cá lóc đã làm sạch', 35000.00, 4, 'int_stock', 'kg', '2025-11-30 01:51:51', '2025-11-30 01:51:51'),
(80, 'Nấm kim châm', 'nam-kim-cham-1764492802', 1, 'Nấm kim châm nội địa Trung 150g', 9000.00, 6, 'int_stock', 'kg', '2025-11-30 01:53:22', '2025-11-30 01:53:22'),
(81, 'Chả giò gia đình tôm mực Cầu Tre', 'cha-gio-gia-dinh-tom-muc-cau-tre-1764492924', 5, 'Chả giò gia đình tôm mực Cầu Tre gói 440g', 40000.00, 7, 'int_stock', 'túi', '2025-11-30 01:55:24', '2025-11-30 01:55:24'),
(82, 'Nem nướng', 'nem-nuong-1764493001', 5, 'Nem nướng MVP 250g', 49900.00, 10, 'int_stock', 'túi', '2025-11-30 01:56:41', '2025-11-30 01:56:41'),
(83, 'Đùi gà góc tư', 'dui-ga-goc-tu-1764493124', 3, 'thịt gà ngon', 23000.00, 7, 'int_stock', 'kg', '2025-11-30 01:58:44', '2025-11-30 01:58:44'),
(86, 'Lòng gà 250gr', 'long-ga-250gr-1764509513', 3, 'Nấu chín trước khi sử dụng', 27500.00, 20, 'int_stock', 'kg', '2025-11-30 06:31:53', '2025-11-30 06:31:53'),
(87, 'Thịt heo', 'thit-heo-1764644497', 3, 'thịt heo tươi', 50000.00, 10, 'int_stock', 'kg', '2025-12-01 20:01:37', '2025-12-01 20:01:37');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image`, `created_at`, `updated_at`) VALUES
(49, 65, 'uploads/products/1764490363_692bfc7bcc1cd.jpg', '2025-11-30 01:12:43', '2025-11-30 01:12:43'),
(50, 65, 'uploads/products/1764490363_692bfc7bcf8fb.jpg', '2025-11-30 01:12:43', '2025-11-30 01:12:43'),
(51, 65, 'uploads/products/1764490363_692bfc7bd0b35.jpg', '2025-11-30 01:12:43', '2025-11-30 01:12:43'),
(54, 67, 'uploads/products/1764490933_692bfeb5636c1.jpg', '2025-11-30 01:22:13', '2025-11-30 01:22:13'),
(55, 67, 'uploads/products/1764490933_692bfeb56690c.jpg', '2025-11-30 01:22:13', '2025-11-30 01:22:13'),
(56, 67, 'uploads/products/1764490933_692bfeb56890a.jpg', '2025-11-30 01:22:13', '2025-11-30 01:22:13'),
(57, 68, 'uploads/products/1764491032_692bff184a013.jpg', '2025-11-30 01:23:52', '2025-11-30 01:23:52'),
(58, 68, 'uploads/products/1764491032_692bff184dd9e.jpg', '2025-11-30 01:23:52', '2025-11-30 01:23:52'),
(59, 68, 'uploads/products/1764491032_692bff184f754.jpg', '2025-11-30 01:23:52', '2025-11-30 01:23:52'),
(63, 70, 'uploads/products/1764491461_692c00c5331f0.jpg', '2025-11-30 01:31:01', '2025-11-30 01:31:01'),
(64, 70, 'uploads/products/1764491461_692c00c537734.jpg', '2025-11-30 01:31:01', '2025-11-30 01:31:01'),
(65, 70, 'uploads/products/1764491461_692c00c539265.jpg', '2025-11-30 01:31:01', '2025-11-30 01:31:01'),
(66, 71, 'uploads/products/1764491541_692c011580d16.jpg', '2025-11-30 01:32:21', '2025-11-30 01:32:21'),
(67, 71, 'uploads/products/1764491541_692c011583a5d.jpg', '2025-11-30 01:32:21', '2025-11-30 01:32:21'),
(68, 72, 'uploads/products/1764491734_692c01d618142.jpg', '2025-11-30 01:35:34', '2025-11-30 01:35:34'),
(69, 72, 'uploads/products/1764491734_692c01d61cb4b.jpg', '2025-11-30 01:35:34', '2025-11-30 01:35:34'),
(70, 72, 'uploads/products/1764491734_692c01d61ef8d.jpg', '2025-11-30 01:35:34', '2025-11-30 01:35:34'),
(71, 73, 'uploads/products/1764491975_692c02c7cb6d5.jpg', '2025-11-30 01:39:35', '2025-11-30 01:39:35'),
(72, 73, 'uploads/products/1764491975_692c02c7cf144.jpg', '2025-11-30 01:39:35', '2025-11-30 01:39:35'),
(73, 73, 'uploads/products/1764491975_692c02c7d13d6.jpg', '2025-11-30 01:39:35', '2025-11-30 01:39:35'),
(74, 74, 'uploads/products/1764492105_692c0349425cb.jpg', '2025-11-30 01:41:45', '2025-11-30 01:41:45'),
(75, 74, 'uploads/products/1764492105_692c03494607a.jpg', '2025-11-30 01:41:45', '2025-11-30 01:41:45'),
(76, 75, 'uploads/products/1764492232_692c03c8800da.jpg', '2025-11-30 01:43:52', '2025-11-30 01:43:52'),
(77, 75, 'uploads/products/1764492232_692c03c884044.jpg', '2025-11-30 01:43:52', '2025-11-30 01:43:52'),
(78, 75, 'uploads/products/1764492232_692c03c885c06.jpg', '2025-11-30 01:43:52', '2025-11-30 01:43:52'),
(79, 76, 'uploads/products/1764492373_692c04556fd96.jpg', '2025-11-30 01:46:13', '2025-11-30 01:46:13'),
(80, 76, 'uploads/products/1764492373_692c045573ec0.jpg', '2025-11-30 01:46:13', '2025-11-30 01:46:13'),
(81, 77, 'uploads/products/1764492591_692c052f69f64.jpg', '2025-11-30 01:49:51', '2025-11-30 01:49:51'),
(82, 77, 'uploads/products/1764492591_692c052f6f564.jpg', '2025-11-30 01:49:51', '2025-11-30 01:49:51'),
(83, 78, 'uploads/products/1764492652_692c056c5bd98.jpg', '2025-11-30 01:50:52', '2025-11-30 01:50:52'),
(84, 78, 'uploads/products/1764492652_692c056c61337.jpg', '2025-11-30 01:50:52', '2025-11-30 01:50:52'),
(85, 79, 'uploads/products/1764492711_692c05a756faa.jpg', '2025-11-30 01:51:51', '2025-11-30 01:51:51'),
(86, 79, 'uploads/products/1764492711_692c05a75ad53.jpg', '2025-11-30 01:51:51', '2025-11-30 01:51:51'),
(87, 79, 'uploads/products/1764492711_692c05a75ee20.jpg', '2025-11-30 01:51:51', '2025-11-30 01:51:51'),
(88, 80, 'uploads/products/1764492802_692c0602c3d5c.jpg', '2025-11-30 01:53:22', '2025-11-30 01:53:22'),
(89, 80, 'uploads/products/1764492802_692c0602c7fd2.jpg', '2025-11-30 01:53:22', '2025-11-30 01:53:22'),
(90, 81, 'uploads/products/1764492924_692c067c7ec41.jpg', '2025-11-30 01:55:24', '2025-11-30 01:55:24'),
(91, 81, 'uploads/products/1764492924_692c067c8404d.jpg', '2025-11-30 01:55:24', '2025-11-30 01:55:24'),
(92, 81, 'uploads/products/1764492924_692c067c86839.jpg', '2025-11-30 01:55:24', '2025-11-30 01:55:24'),
(93, 82, 'uploads/products/1764493001_692c06c927c49.jpg', '2025-11-30 01:56:41', '2025-11-30 01:56:41'),
(94, 82, 'uploads/products/1764493001_692c06c92b4a8.jpg', '2025-11-30 01:56:41', '2025-11-30 01:56:41'),
(95, 82, 'uploads/products/1764493001_692c06c92cea0.jpg', '2025-11-30 01:56:41', '2025-11-30 01:56:41'),
(96, 83, 'uploads/products/1764493124_692c07449861f.jpg', '2025-11-30 01:58:44', '2025-11-30 01:58:44'),
(97, 83, 'uploads/products/1764493124_692c07449c2bf.jpg', '2025-11-30 01:58:44', '2025-11-30 01:58:44'),
(98, 83, 'uploads/products/1764493124_692c07449ed95.jpeg', '2025-11-30 01:58:44', '2025-11-30 01:58:44'),
(105, 66, 'uploads/products/1764509281_692c46611af5e.jpg', '2025-11-30 06:28:01', '2025-11-30 06:28:01'),
(106, 86, 'uploads/products/1764509513_692c4749cd6e6.jpg', '2025-11-30 06:31:53', '2025-11-30 06:31:53'),
(107, 87, 'uploads/products/1764644497_692e5691e4758.jpg', '2025-12-01 20:01:40', '2025-12-01 20:01:40'),
(108, 87, 'uploads/products/1764644500_692e5694201e3.jpg', '2025-12-01 20:01:40', '2025-12-01 20:01:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `rating`, `comment`, `created_at`, `updated_at`) VALUES
(16, 20, 68, 5, 'cai ngon tuoi', '2025-11-30 06:23:49', '2025-11-30 06:23:49'),
(17, 20, 65, 4, 'oi ngot', '2025-11-30 06:24:48', '2025-11-30 06:24:48');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'admin', '2025-10-16 04:17:07', '2025-10-16 04:17:07'),
(2, 'staff', '2025-10-16 04:17:07', '2025-10-16 04:17:07'),
(3, 'customer', '2025-10-16 04:17:07', '2025-10-16 04:17:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 1, 2, NULL, NULL),
(3, 1, 3, NULL, NULL),
(4, 1, 4, NULL, NULL),
(5, 1, 5, NULL, NULL),
(6, 2, 2, NULL, NULL),
(7, 2, 5, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shipping_addresses`
--

CREATE TABLE `shipping_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `default` varchar(255) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `shipping_addresses`
--

INSERT INTO `shipping_addresses` (`id`, `user_id`, `full_name`, `phone`, `address`, `city`, `default`, `created_at`, `updated_at`) VALUES
(9, 20, 'Hiep 666', '0388536385', 'ấp lộc trị , Hưng Thuận, Thị Xã Trảng bàng', 'Tây Ninh', '1', '2025-12-03 00:01:41', '2025-12-03 00:01:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('pending','active','banned','deleted') NOT NULL DEFAULT 'pending',
  `phone_number` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `activation_token` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `status`, `phone_number`, `avatar`, `address`, `role_id`, `activation_token`, `google_id`, `created_at`, `updated_at`) VALUES
(3, 'Nguyen Van A', 'nguyenvana@example.com', '$2y$12$GzZUZbV0nJNynHvw2hTLDubTPqALv0ewGru.fCqJ9bxfK7dQ9KABG', 'active', '0123456789', NULL, 'Da Nang, Vietnam', 3, NULL, NULL, '2025-10-16 04:19:53', '2025-11-25 08:46:44'),
(4, 'Tran Thi B', 'tranthib@example.com', '$2y$12$hVe4SvT/71yqHy34mc/AYejPZO7RQ5OAY6EMvCprGUiITBmLfUxvu', 'pending', '0987654321', NULL, 'Gia Lai, Vietnam', 2, NULL, NULL, '2025-10-16 04:19:53', '2025-10-16 04:19:53'),
(5, 'Nguyen Hoang Hiep', 'nguyenhoanghiep@example.com', '$2y$12$DQ1EGMJzqF7wLQ7euLL9Zu4x6qLXsUFb/hzdFYNkjsqhZSVOUAZ3a', 'active', '0987654321', NULL, 'Ho Chi Minh, Vietnam', 3, NULL, NULL, '2025-10-16 04:19:53', '2025-12-01 19:59:11'),
(6, 'Admin', 'admin@example.com', '$2y$12$Z0ybvU7fChSJv8HhwrH.7ODBlnrMpKJTHP5upAV5agvFcL3ojyEkC', 'active', '019999999', NULL, 'Da Nang, Vietnam', 1, NULL, NULL, '2025-10-16 04:19:54', '2025-10-16 04:19:54'),
(7, 'Staff', 'staff@example.com', '$2y$12$3RBC..Mgtn0FbZRPBTi8.u5LJ6rFXFk32sCoViIXB1VAFIIIKM60y', 'active', '018889999', NULL, 'Da Nang, Vietnam', 2, NULL, NULL, '2025-10-16 04:19:54', '2025-11-25 08:00:46'),
(11, 'Hiep Hoang', 'hiepo@gmail.com', '$2y$12$b.0hqnxYVM.o9kRSRCAxeu4GT2eXkQLChqvaBbxsMRYL85Y4PDo8G', 'active', NULL, NULL, NULL, 2, 'G9ekyQH9KvzUJz3UUdZj15tHu7DIxaLUQf3Cv2KtTHD1i3Z68A2Wp3bYTOYVS8zD', NULL, '2025-10-23 01:48:12', '2025-12-01 19:58:51'),
(20, 'Nguyễn Hoàng Hiệp', 'nguyenhoanghiep3005@gmail.com', '$2y$12$14w6D3BhEjl2yu6/35qmx.zoUOyoS90bFaPraipUU9sk1erD/qkx2', 'active', '0388536385', 'uploads/users/1764040066_69251d82a87f8.jpg', '457 kênh tân hóa, Tân Phú, HCM', 3, NULL, NULL, '2025-10-27 20:15:27', '2025-12-02 08:09:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wishlists`
--

CREATE TABLE `wishlists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `wishlists`
--

INSERT INTO `wishlists` (`id`, `user_id`, `product_id`, `created_at`, `updated_at`) VALUES
(14, 20, 71, '2025-12-01 02:51:40', '2025-12-01 02:51:40'),
(15, 20, 80, '2025-12-01 02:51:55', '2025-12-01 02:51:55');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Chỉ mục cho bảng `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Chỉ mục cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_items_user_id_foreign` (`user_id`),
  ADD KEY `cart_items_product_id_foreign` (`product_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_name_unique` (`name`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Chỉ mục cho bảng `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `fk_order_items_products` (`product_id`);

--
-- Chỉ mục cho bảng `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_status_history_order_id_foreign` (`order_id`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_order_id_foreign` (`order_id`);

--
-- Chỉ mục cho bảng `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_slug_unique` (`slug`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_images_product_id_foreign` (`product_id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_user_id_foreign` (`user_id`),
  ADD KEY `reviews_product_id_foreign` (`product_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Chỉ mục cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_permissions_role_id_foreign` (`role_id`),
  ADD KEY `role_permissions_permission_id_foreign` (`permission_id`);

--
-- Chỉ mục cho bảng `shipping_addresses`
--
ALTER TABLE `shipping_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shipping_addresses_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- Chỉ mục cho bảng `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wishlists_user_id_foreign` (`user_id`),
  ADD KEY `wishlists_product_id_foreign` (`product_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT cho bảng `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT cho bảng `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `shipping_addresses`
--
ALTER TABLE `shipping_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD CONSTRAINT `order_status_history_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `shipping_addresses`
--
ALTER TABLE `shipping_addresses`
  ADD CONSTRAINT `shipping_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
