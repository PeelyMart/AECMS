-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 31, 2026 at 07:46 AM
-- Server version: 8.0.43
-- PHP Version: 8.5.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ITPROG`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders_header`
--

CREATE TABLE `orders_header` (
  `id` int NOT NULL,
  `ext_id` varchar(100) NOT NULL,
  `platform` enum('lazada','shopee','tiktok') NOT NULL,
  `buyer_username` varchar(150) DEFAULT NULL,
  `total_worth` decimal(10,2) DEFAULT '0.00',
  `assigned_to` int DEFAULT NULL,
  `status` enum('pending','packed','shipped') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders_header`
--

INSERT INTO `orders_header` (`id`, `ext_id`, `platform`, `buyer_username`, `total_worth`, `assigned_to`, `status`, `created_at`) VALUES
(45, 'ORDLZK-0001', 'lazada', 'JayTheJay123', 900.00, 1, 'pending', '2026-03-30 16:09:27'),
(49, 'LZAADA-ORDER12312', 'shopee', 'Philip123', 3000.00, 1, 'pending', '2026-03-30 16:15:53'),
(50, 'LZAADA-ORDER12311', 'shopee', 'James123', 7700.00, 1, 'pending', '2026-03-30 16:15:53');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `external_sku` varchar(100) NOT NULL,
  `qty` int NOT NULL,
  `unit_price_snapshot` decimal(10,2) DEFAULT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `external_sku`, `qty`, `unit_price_snapshot`, `sub_total`, `created_at`) VALUES
(72, 45, 1, 'LZ-0001', 1, 500.00, 500.00, '2026-03-30 16:09:27'),
(73, 45, 2, 'LZ-0008', 1, 400.00, 400.00, '2026-03-30 16:09:27'),
(78, 49, 1, 'SK-0001', 2, 500.00, 1000.00, '2026-03-30 16:15:53'),
(79, 49, 2, 'SK-0008', 5, 400.00, 2000.00, '2026-03-30 16:15:53'),
(80, 50, 1, 'SK-0001', 9, 500.00, 4500.00, '2026-03-30 16:15:53'),
(81, 50, 2, 'SK-0008', 8, 400.00, 3200.00, '2026-03-30 16:15:53');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(150) NOT NULL,
  `l_sku` varchar(100) DEFAULT NULL,
  `s_sku` varchar(100) DEFAULT NULL,
  `t_sku` varchar(100) DEFAULT NULL,
  `qty` int DEFAULT '0',
  `remarks` text,
  `unit_price` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `l_sku`, `s_sku`, `t_sku`, `qty`, `remarks`, `unit_price`, `created_at`) VALUES
(1, 'Red Coat', 'LZ-0001', 'SK-0001', 'TK-0001', 90, 'its a red coat', 500.00, '2026-03-28 16:18:15'),
(2, 'BlackCup', 'LZ-0008', 'SK-0008', 'TK-0008', 600, 'Its a black cup', 400.00, '2026-03-28 16:19:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(50) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `contactNumber` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('PENDING','ACTIVE','INACTIVE') DEFAULT 'PENDING',
  `role` enum('NORMAL','ADMIN') DEFAULT 'NORMAL',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `firstName`, `lastName`, `contactNumber`, `password`, `status`, `role`, `created_at`) VALUES
(1, 'philipmartinantolihao@gmail.com', 'Philip', 'Antolihao', '09950740100', '$2y$12$siJ/VdYlWiOzz0k51Ks65uCwjRW4r3AhjTtmcge5/OXm855nrWMDi', 'PENDING', 'NORMAL', '2026-03-30 11:18:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders_header`
--
ALTER TABLE `orders_header`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_order` (`ext_id`,`platform`),
  ADD KEY `idx_orders_assigned_to` (`assigned_to`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_items_order_id` (`order_id`),
  ADD KEY `idx_order_items_product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_l_sku` (`l_sku`),
  ADD UNIQUE KEY `uniq_s_sku` (`s_sku`),
  ADD UNIQUE KEY `uniq_t_sku` (`t_sku`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders_header`
--
ALTER TABLE `orders_header`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders_header`
--
ALTER TABLE `orders_header`
  ADD CONSTRAINT `orders_header_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders_header` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
