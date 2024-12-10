-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 27, 2024 at 06:32 AM
-- Server version: 8.3.0
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shoplex_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `auction_history`
--

DROP TABLE IF EXISTS `auction_history`;
CREATE TABLE IF NOT EXISTS `auction_history` (
  `auction_id` bigint NOT NULL AUTO_INCREMENT,
  `product_id` bigint NOT NULL,
  `winning_bidder_id` bigint DEFAULT NULL,
  `starting_bid` decimal(10,2) DEFAULT NULL,
  `ending_bid` decimal(10,2) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `end_time` timestamp NULL DEFAULT NULL,
  `is_end` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`auction_id`),
  KEY `product_id_auction_history_FK` (`product_id`),
  KEY `winning_bidder_id_auction_history_FK` (`winning_bidder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `auction_history`
--

INSERT INTO `auction_history` (`auction_id`, `product_id`, `winning_bidder_id`, `starting_bid`, `ending_bid`, `start_time`, `end_time`, `is_end`) VALUES
(112, 121, NULL, 45000.00, NULL, '2024-11-23 18:30:00', '2024-11-24 09:09:14', 1),
(113, 121, 57, 45000.00, 45024.00, '2024-11-23 18:30:00', '2024-11-24 09:46:45', 1),
(114, 121, 57, 45000.00, 45019.00, '2024-11-23 18:30:00', '2024-11-24 09:47:57', 1),
(115, 121, 57, 45000.00, 45013.00, '2024-11-23 18:30:00', '2024-11-24 10:00:16', 1),
(116, 121, NULL, 45000.00, 45004.00, '2024-11-23 18:30:00', '2024-11-25 10:59:57', 1),
(117, 121, NULL, 45000.00, 45007.00, '2024-11-24 18:30:00', '2024-11-26 07:04:01', 1),
(120, 161, NULL, 25000.00, NULL, '2024-11-25 18:30:00', '2024-11-28 18:30:00', 0),
(121, 121, NULL, 45000.00, NULL, '2024-11-23 18:30:00', '2024-11-29 18:30:00', 0),
(122, 165, NULL, 65000.00, NULL, '2024-11-25 18:30:00', '2024-12-30 18:30:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `banner`
--

DROP TABLE IF EXISTS `banner`;
CREATE TABLE IF NOT EXISTS `banner` (
  `banner_id` int NOT NULL AUTO_INCREMENT,
  `banner_image` text NOT NULL,
  `is_activate` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `banner`
--

INSERT INTO `banner` (`banner_id`, `banner_image`, `is_activate`, `created_at`, `update_at`) VALUES
(24, '../images/slideshow-banner/shopping-concept-close-up-portrait-young-beautiful-attractive-redhair-girl-smiling-looking-camera (1) (1) (2).webp', 1, '2024-11-25 01:30:25', '2024-11-25 01:32:16'),
(25, '../images/slideshow-banner/vecteezy_online-shopping-on-phone-buy-sell-business-digital-web_4299833 (1).jpg', 1, '2024-11-25 01:30:49', '2024-11-25 01:32:16'),
(26, '../images/slideshow-banner/vecteezy_online-shopping-on-phone-buy-sell-business-digital-web_4299835 (1).jpg', 1, '2024-11-25 01:31:16', '2024-11-25 01:32:15'),
(27, '../images/slideshow-banner/vecteezy_paper-art-shopping-online-on-smartphone-and-new-buy-sale_6828785 (1).jpg', 1, '2024-11-25 01:31:29', '2024-11-25 01:32:14');

-- --------------------------------------------------------

--
-- Table structure for table `bidding_records`
--

DROP TABLE IF EXISTS `bidding_records`;
CREATE TABLE IF NOT EXISTS `bidding_records` (
  `bid_id` bigint NOT NULL AUTO_INCREMENT,
  `auction_id` bigint NOT NULL,
  `product_id` bigint NOT NULL,
  `bider_id` bigint NOT NULL,
  `bid_amount` decimal(10,2) NOT NULL,
  `bid_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`bid_id`),
  KEY `product_id_bidding_records_FK` (`product_id`),
  KEY `buyer_id_bidding_records_FK` (`bider_id`),
  KEY `auction_id_bidding_records_FK` (`auction_id`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bidding_records`
--

INSERT INTO `bidding_records` (`bid_id`, `auction_id`, `product_id`, `bider_id`, `bid_amount`, `bid_time`) VALUES
(95, 113, 121, 57, 45014.00, '2024-11-24 14:41:13'),
(96, 113, 121, 57, 45024.00, '2024-11-24 14:44:12'),
(97, 114, 121, 57, 45019.00, '2024-11-24 15:17:31'),
(98, 115, 121, 57, 45013.00, '2024-11-24 15:29:42'),
(103, 120, 161, 94, 25003.00, '2024-11-26 12:56:19');

-- --------------------------------------------------------

--
-- Table structure for table `buyer`
--

DROP TABLE IF EXISTS `buyer`;
CREATE TABLE IF NOT EXISTS `buyer` (
  `buyer_id` bigint NOT NULL,
  PRIMARY KEY (`buyer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `buyer`
--

INSERT INTO `buyer` (`buyer_id`) VALUES
(57),
(68),
(76),
(88),
(94);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `cart_id` bigint NOT NULL AUTO_INCREMENT,
  `buyer_id` bigint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `buyer_id_cart_FK` (`buyer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `buyer_id`, `created_at`, `updated_at`) VALUES
(5, 57, '2024-11-18 15:07:31', '2024-11-18 15:07:31'),
(12, 76, '2024-11-24 01:34:59', '2024-11-24 01:34:59'),
(16, 94, '2024-11-26 15:11:55', '2024-11-26 15:11:55');

-- --------------------------------------------------------

--
-- Table structure for table `cart_item`
--

DROP TABLE IF EXISTS `cart_item`;
CREATE TABLE IF NOT EXISTS `cart_item` (
  `cart_item_id` bigint NOT NULL AUTO_INCREMENT,
  `cart_id` bigint NOT NULL,
  `product_id` bigint NOT NULL,
  `quantity` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_item_id`),
  KEY `cart_id_cart_item_FK` (`cart_id`),
  KEY `product_id_cart_item_FK` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart_item`
--

INSERT INTO `cart_item` (`cart_item_id`, `cart_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(60, 5, 31, 5, '2024-11-24 15:41:47', '2024-11-24 15:41:47'),
(61, 5, 103, 1, '2024-11-24 15:57:10', '2024-11-24 15:57:10'),
(62, 5, 154, 1, '2024-11-25 04:34:59', '2024-11-25 04:34:59'),
(70, 16, 31, 1, '2024-11-26 16:02:01', '2024-11-26 16:02:01'),
(71, 16, 42, 1, '2024-11-26 16:02:20', '2024-11-26 16:02:20');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(150) NOT NULL,
  `parent_category_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`category_id`),
  KEY `parent_category_id_category_FK` (`parent_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`, `parent_category_id`, `created_at`, `updated_at`) VALUES
(1, 'Electronics', NULL, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(2, 'Clothing', NULL, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(3, 'Home & Kitchen', NULL, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(4, 'Books', NULL, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(5, 'Sports', NULL, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(6, 'Health & Beauty', NULL, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(7, 'Toys & Games', NULL, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(8, 'Automotive', NULL, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(9, 'Garden & Outdoors', NULL, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(10, 'Mobile Phones', 1, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(11, 'Laptops', 1, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(12, 'Cameras', 1, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(13, 'Tablets', 1, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(14, 'Televisions', 1, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(15, 'Headphones', 1, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(16, 'Wearable Tech', 1, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(17, 'Men\'s Clothing', 2, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(18, 'Women\'s Clothing', 2, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(19, 'Kids\' Clothing', 2, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(20, 'Shoes', 2, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(21, 'Accessories', 2, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(22, 'Furniture', 3, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(23, 'Appliances', 3, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(24, 'Cookware', 3, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(25, 'Home Decor', 3, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(26, 'Bedding', 3, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(27, 'Storage & Organization', 3, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(28, 'Fiction', 4, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(29, 'Non-Fiction', 4, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(30, 'Comics', 4, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(31, 'Children\'s Books', 4, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(32, 'Textbooks', 4, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(33, 'Magazines', 4, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(34, 'Outdoor Sports', 5, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(35, 'Indoor Sports', 5, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(36, 'Fitness Equipment', 5, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(37, 'Team Sports', 5, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(38, 'Water Sports', 5, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(39, 'Winter Sports', 5, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(40, 'Skin Care', 6, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(41, 'Hair Care', 6, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(42, 'Makeup', 6, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(43, 'Personal Care', 6, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(44, 'Vitamins & Supplements', 6, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(45, 'Action Figures', 7, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(46, 'Board Games', 7, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(47, 'Educational Toys', 7, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(48, 'Puzzles', 7, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(49, 'Outdoor Toys', 7, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(50, 'Car Accessories', 8, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(51, 'Motorcycle Accessories', 8, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(52, 'Tools & Equipment', 8, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(53, 'Car Care', 8, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(54, 'Car Electronics', 8, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(55, 'Plants & Seeds', 9, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(56, 'Outdoor Furniture', 9, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(57, 'Gardening Tools', 9, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(58, 'Outdoor Lighting', 9, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(59, 'Grills & Outdoor Cooking', 9, '2024-10-31 00:50:39', '2024-10-31 00:50:39'),
(60, 'PC Accessories', 1, '2024-11-25 22:02:41', '2024-11-25 22:02:41');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `message_id` bigint NOT NULL AUTO_INCREMENT,
  `sender_id` bigint DEFAULT NULL,
  `reciver_id` bigint DEFAULT NULL,
  `message_content` text NOT NULL,
  `reply_message` text,
  `message_type` enum('normal','suggestion','bid_alert') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`),
  KEY `sender_id_message_FK` (`sender_id`),
  KEY `receiver_id_message_FK` (`reciver_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`message_id`, `sender_id`, `reciver_id`, `message_content`, `reply_message`, `message_type`, `created_at`, `updated_at`) VALUES
(17, 94, NULL, 'Hi', 'Hi', 'normal', '2024-11-26 14:59:27', '2024-11-26 14:59:38');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` bigint NOT NULL AUTO_INCREMENT,
  `buyer_id` bigint DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `total_shipping_fee` decimal(10,2) NOT NULL,
  `ordered_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  KEY `buyer_id_orders_FK` (`buyer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=189 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `buyer_id`, `total_amount`, `total_shipping_fee`, `ordered_at`, `updated_at`) VALUES
(187, 94, 5320.00, 2500.00, '2024-11-26 16:02:05', '2024-11-26 16:02:05'),
(188, 94, 3750.00, 500.00, '2024-11-26 16:02:22', '2024-11-26 16:02:22');

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

DROP TABLE IF EXISTS `order_item`;
CREATE TABLE IF NOT EXISTS `order_item` (
  `order_item_id` bigint NOT NULL AUTO_INCREMENT,
  `order_id` bigint NOT NULL,
  `product_id` bigint DEFAULT NULL,
  `auction_id` bigint DEFAULT NULL,
  `quantity` int NOT NULL,
  `price_after_discount` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `shipping_fee` decimal(10,2) NOT NULL,
  `shipped_date` date DEFAULT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `delivered_date` date DEFAULT NULL,
  `status_id` int NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id_order_item_FK` (`order_id`),
  KEY `status_id_order_item_FK` (`status_id`),
  KEY `product_id_order_item_FK` (`product_id`),
  KEY `auction_id_order_item_FK` (`auction_id`)
) ENGINE=InnoDB AUTO_INCREMENT=197 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_item`
--

INSERT INTO `order_item` (`order_item_id`, `order_id`, `product_id`, `auction_id`, `quantity`, `price_after_discount`, `subtotal`, `shipping_fee`, `shipped_date`, `expected_delivery_date`, `delivered_date`, `status_id`) VALUES
(195, 187, 31, NULL, 1, 5320.00, 5320.00, 2500.00, NULL, NULL, NULL, 1),
(196, 188, 42, NULL, 1, 3750.00, 3750.00, 500.00, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_status`
--

DROP TABLE IF EXISTS `order_status`;
CREATE TABLE IF NOT EXISTS `order_status` (
  `status_id` int NOT NULL AUTO_INCREMENT,
  `status_name` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_status`
--

INSERT INTO `order_status` (`status_id`, `status_name`) VALUES
(1, 'Pending'),
(3, 'Shipped'),
(4, 'Delivered');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `product_id` bigint NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int NOT NULL,
  `discount` decimal(10,2) DEFAULT '0.00',
  `shipping_fee` decimal(10,2) DEFAULT '0.00',
  `bid_activate` tinyint(1) DEFAULT '0',
  `bid_starting_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`),
  KEY `product_id` (`product_id`),
  KEY `category_id_product_FK` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `category_id`, `product_name`, `description`, `price`, `stock`, `discount`, `shipping_fee`, `bid_activate`, `bid_starting_price`, `created_at`, `updated_at`) VALUES
(31, 60, 'XBOX Wireless Controller', 'Ergonomic wireless controller for Xbox consoles.', 5600.00, 96, 0.05, 2500.00, 0, 0.00, '2024-11-01 01:56:48', '2024-11-26 16:02:05'),
(33, 11, 'RGB Gaming Keyboard', 'Mechanical RGB keyboard for a perfect gaming experience.', 4200.00, 119, 0.05, 0.00, 0, 0.00, '2024-11-01 01:56:48', '2024-11-26 15:51:49'),
(42, 36, 'Dumbbell 10KG', 'www', 5000.00, 198, 0.25, 500.00, 0, NULL, '2024-11-20 21:06:18', '2024-11-26 16:02:22'),
(103, 17, 'Men\'s Black Hoodie', '100% Cotton', 3000.00, 138, 0.10, 200.00, 0, NULL, '2024-11-22 03:12:10', '2024-11-22 13:02:54'),
(121, 11, 'Xbox Console (Used)', 'Used gaming console', NULL, 16, 0.00, 0.00, 1, 45000.00, '2024-11-24 14:39:05', '2024-11-26 16:30:18'),
(154, 23, 'Dog Foods', 'Dog Foods', 1453.00, 2, 0.25, 100.00, 0, NULL, '2024-11-25 03:23:03', '2024-11-25 04:51:59'),
(161, 21, 'Longines Chronograph (Vintage)', 'Longines Chronograph 30CH in 18ct rose gold (1951)', NULL, 15, 0.00, 1350.00, 1, 25000.00, '2024-11-25 22:18:04', '2024-11-26 16:29:41'),
(162, 10, 'Galaxy Z Fold 5', '256GB storage, foldable AMOLED display, 12MP camera, 5G enabled.', 220000.00, 50, 0.20, 1500.00, 0, NULL, '2024-11-26 15:06:54', '2024-11-26 15:06:54'),
(163, 22, 'Modern Office Chair', 'An ergonomic office chair with adjustable height, armrests, and lumbar support. Perfect for long working hours.', 25000.00, 50, 0.05, 0.00, 0, NULL, '2024-11-26 16:20:49', '2024-11-26 16:20:49'),
(164, 15, 'Wireless Bluetooth Headphones', 'Over-ear Bluetooth headphones with noise-canceling technology, 20-hour battery life, and superior sound quality.', 8500.00, 100, 0.05, 0.00, 0, NULL, '2024-11-26 16:22:01', '2024-11-26 16:22:01'),
(165, 22, 'Antique Cupboard', '1880s Portuguese Colonial Old Cupboard', NULL, 1, 0.00, 2000.00, 1, 65000.00, '2024-11-26 16:28:04', '2024-11-26 16:28:46');

-- --------------------------------------------------------

--
-- Table structure for table `product_picture`
--

DROP TABLE IF EXISTS `product_picture`;
CREATE TABLE IF NOT EXISTS `product_picture` (
  `product_picture_id` bigint NOT NULL AUTO_INCREMENT,
  `product_id` bigint NOT NULL,
  `picture_path` text NOT NULL,
  `default_picture` tinyint DEFAULT NULL,
  PRIMARY KEY (`product_picture_id`),
  KEY `product_id_product_picture_FK` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product_picture`
--

INSERT INTO `product_picture` (`product_picture_id`, `product_id`, `picture_path`, `default_picture`) VALUES
(123, 154, '/images/product-images/6743ed976ff93_11.webp', 1),
(124, 154, '/images/product-images/6743ed97701b2_6.webp', 0),
(142, 42, '/images/product-images/6744b245642a3_Bounce-Dumbbell-01.jpg', 1),
(143, 42, '/images/product-images/6744b24564472_1rd025-1rd50-jpg-webp.webp', 0),
(145, 33, '/images/product-images/6744b2d725ed7_CORSAIR-K70-RGB-copy.png', 1),
(146, 103, '/images/product-images/6744b3877fa08_Black_Oversized_Hoodies_Men_1_800x.webp', 1),
(151, 121, '/images/product-images/6744bedcdaf26_xbox_series_x_console_with_controller.webp', 1),
(152, 121, '/images/product-images/6744bedcde917_XboxSeriesXTech_Inline1.jpg', 0),
(153, 121, '/images/product-images/6744bedcdea44_64377728-IMG-002.webp', 0),
(154, 31, '/images/product-images/6744f54828e6a_LD0006033481.jpg', 1),
(155, 31, '/images/product-images/6744f5482906d_Xbox-Wireless-Controller-Velocity-Green.webp', 0),
(156, 161, '/images/product-images/6744f79c6b9ee_hero02_aa0c3ccb-58f4-4368-b1e6-8c96cbefc872_1024x1024.webp', 1),
(157, 162, '/images/product-images/6745e40ef0533_Galaxy-Z-Fold5-7.jpg', 1),
(158, 162, '/images/product-images/6745e40ef068c_Samsung-Galaxy-Z-Fold-5-2.jpg', 0),
(159, 162, '/images/product-images/6745e40ef07ca_fold5-colours_bolq94.jpg', 0),
(160, 163, '/images/product-images/6745f561b354d_images.jpg', 1),
(161, 164, '/images/product-images/6745f5a9acfef_1-104-3.webp', 1),
(162, 165, '/images/product-images/6745f7143916a_Antique-Cupboards-Poruguese-Colonial-Mahogany-Cupboard-The-Past-Perfect-Collection-Singapore-CUP-635-1L.jpg.webp', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_review`
--

DROP TABLE IF EXISTS `product_review`;
CREATE TABLE IF NOT EXISTS `product_review` (
  `review_id` int NOT NULL AUTO_INCREMENT,
  `product_id` bigint NOT NULL,
  `buyer_id` bigint NOT NULL,
  `rating` tinyint NOT NULL,
  `review_content` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  UNIQUE KEY `product_id` (`product_id`),
  KEY `buyer_id_product_review_FK` (`buyer_id`)
) ;

--
-- Dumping data for table `product_review`
--

INSERT INTO `product_review` (`review_id`, `product_id`, `buyer_id`, `rating`, `review_content`, `created_at`, `updated_at`) VALUES
(20, 121, 57, 5, 'Good', '2024-11-24 15:18:24', '2024-11-24 15:18:24');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` bigint NOT NULL AUTO_INCREMENT,
  `user_type_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `profile_picture` text,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `user_type_id_user_FK` (`user_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_type_id`, `name`, `email`, `password`, `profile_picture`, `last_login`, `created_at`, `updated_at`) VALUES
(57, 4, 'Azeez', 'a@a.com', '$2y$10$QNYr.8mGZrUEGS2Dx1h6Ku7LJTUduYTeoOO8/6YP.sKS5m/pprKmW', '67449666ed4da-igor-karimov-AGf_KACMHJ8-unsplash.jpg', '2024-11-26 14:56:53', '2024-10-26 02:21:29', '2024-11-26 14:56:53'),
(68, 1, 'John jds', 'john@j.com', '$2y$10$33QWurr531XXGDnZ/dk/GeqVN248F/LWQQDzc5FzV8.44JYWV7D2S', NULL, '2024-11-04 07:56:48', '2024-11-04 07:56:26', '2024-11-04 07:56:48'),
(72, 1, 'Kevin', 'kevin@k.com', '$2y$10$pwavEZZxicrr2eOxGUZOh..xZsYh.WpgMQRH5NbnJappytUkPbboe', NULL, '2024-11-22 13:37:32', '2024-11-22 13:36:18', '2024-11-22 13:37:32'),
(74, 1, 'sdfds', 'sdf@f.com', '$2y$10$Eviw5j/MletGlPmRl0vLiOdDMsB2QSaQpxByDKWQ3lirte6yNklmm', NULL, NULL, '2024-11-22 13:42:06', '2024-11-22 13:42:06'),
(76, 1, 'Rose J', 'r@r.com', '$2y$10$z3ye4ethnuBf9jPR.9C.6eu3.29vomBQ5jbHvRGWTsCoUc/sJP9wm', NULL, '2024-11-24 01:34:41', '2024-11-24 01:34:21', '2024-11-24 01:34:41'),
(81, 4, 'David', 'd@d.com', '$2y$10$9J7EWHa9FlEeTJpHY94p/uot0kK//NLmO5eVkrAOhblwSTqVPDtUW', NULL, NULL, '2024-11-24 22:46:17', '2024-11-24 23:09:32'),
(82, 4, 'Edward', 'ed@ed.com', '$2y$10$ektAWfH/rDEy2TYsZWbTl.O3Tc/OAIcchza5NVwW73VqpHfWShVYq', NULL, NULL, '2024-11-24 23:10:56', '2024-11-24 23:19:56'),
(83, 4, 'ghj', 'vv@vv.com', '$2y$10$P0zvxj4ljbH0MTo6EBl42OFsFK/GTaTBAqj65HeZ4D28Dbsb6xHOK', '../images/user-dp/tote (1).webp', NULL, '2024-11-24 23:25:06', '2024-11-24 23:25:06'),
(84, 4, 'dgfg', 'h@v.cim', '$2y$10$uYPNU9HZp2RU3T/UONnsTelZyx3xfmtw.10XErziHA6L/WDPkHiVG', '../images/user-dp/shoplex-favicon-black.png', NULL, '2024-11-25 21:11:05', '2024-11-25 21:11:05'),
(85, 1, 'zxvz', 'er@df.ck', '$2y$10$8AFb4C4ieS.p//l/1gaU1uB.E6tT28MAB9ocE6DUL1So1ZjB2vJMi', 'tote (1).webp', NULL, '2024-11-25 21:13:19', '2024-11-25 21:13:19'),
(86, 4, 'Test 1 //', 't@t.com', '$2y$10$OyxLf3KHT8x9gegvWeUWR.3XcNgBKU89gtSvrR.yyackqB3ap7zyS', NULL, '2024-11-25 23:09:09', '2024-11-25 22:42:11', '2024-11-25 23:09:33'),
(88, 1, 'Test buyer', 'h@h.com', '$2y$10$BlZFmQe1oOTc5B22bwJG7uzk5RurVwM59Ey63KQ/pk6J58m6HYM76', '6745133790702-Xbox-Wireless-Controller-Velocity-Green.webp', '2024-11-25 23:47:40', '2024-11-25 23:12:43', '2024-11-26 00:15:51'),
(92, 1, 'jjkk', 'kk@kk.com', '$2y$10$pvpSgpEy3.xq.aGCM0/OFuyxAtKR8Pl2j6ldK.1onKqjo/lZbkAVm', 'LD0006033481.jpg', '2024-11-26 00:19:48', '2024-11-26 00:19:04', '2024-11-26 00:19:48'),
(94, 1, 'Jackson', 'jk@jk.com', '$2y$10$VGm5uhVyvMy3CgPGiRYZ4ugMMcL.wOjJw7lgqeJI9x5ay2RGNG./6', 'automation.png', '2024-11-26 12:56:02', '2024-11-26 12:55:33', '2024-11-26 12:56:02'),
(95, 4, 'Default Admin', 'root@root.com', '$2y$10$sJd5SuEe5Kc/RBcOsnhn8.usEOqgzq5MeCi3LTrTvY0KNQ6Zi3s8C', NULL, '2024-11-26 14:58:21', '2024-11-26 14:57:53', '2024-11-26 14:58:21');

-- --------------------------------------------------------

--
-- Table structure for table `user_type`
--

DROP TABLE IF EXISTS `user_type`;
CREATE TABLE IF NOT EXISTS `user_type` (
  `user_type_id` int NOT NULL AUTO_INCREMENT,
  `type_name` varchar(20) NOT NULL,
  PRIMARY KEY (`user_type_id`),
  UNIQUE KEY `type_name` (`type_name`),
  KEY `user_type_id` (`user_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_type`
--

INSERT INTO `user_type` (`user_type_id`, `type_name`) VALUES
(4, 'admin'),
(1, 'buyer');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auction_history`
--
ALTER TABLE `auction_history`
  ADD CONSTRAINT `product_id_auction_history_FK` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `winning_bidder_id_auction_history_FK` FOREIGN KEY (`winning_bidder_id`) REFERENCES `buyer` (`buyer_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `bidding_records`
--
ALTER TABLE `bidding_records`
  ADD CONSTRAINT `auction_id_bidding_records_FK` FOREIGN KEY (`auction_id`) REFERENCES `auction_history` (`auction_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `buyer_id_bidding_records_FK` FOREIGN KEY (`bider_id`) REFERENCES `buyer` (`buyer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_id_bidding_records_FK` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `buyer`
--
ALTER TABLE `buyer`
  ADD CONSTRAINT `buyer_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `buyer_id_cart_FK` FOREIGN KEY (`buyer_id`) REFERENCES `buyer` (`buyer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD CONSTRAINT `cart_id_cart_item_FK` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `product_id_cart_item_FK` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `parent_category_id_category_FK` FOREIGN KEY (`parent_category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `receiver_id_message_FK` FOREIGN KEY (`reciver_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sender_id_message_FK` FOREIGN KEY (`sender_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `buyer_id_orders_FK` FOREIGN KEY (`buyer_id`) REFERENCES `buyer` (`buyer_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `auction_id_order_item_FK` FOREIGN KEY (`auction_id`) REFERENCES `auction_history` (`auction_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_id_order_item_FK` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_id_order_item_FK` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `status_id_order_item_FK` FOREIGN KEY (`status_id`) REFERENCES `order_status` (`status_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `category_id_product_FK` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_picture`
--
ALTER TABLE `product_picture`
  ADD CONSTRAINT `product_id_product_picture_FK` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_review`
--
ALTER TABLE `product_review`
  ADD CONSTRAINT `buyer_id_product_review_FK` FOREIGN KEY (`buyer_id`) REFERENCES `buyer` (`buyer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_id_product_review_FK` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_type_id_user_FK` FOREIGN KEY (`user_type_id`) REFERENCES `user_type` (`user_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
