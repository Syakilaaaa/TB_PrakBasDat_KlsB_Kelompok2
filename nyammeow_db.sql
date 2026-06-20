-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2026 at 09:59 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nyammeow_db`
--
CREATE DATABASE IF NOT EXISTS `nyammeow_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `nyammeow_db`;

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

DROP TABLE IF EXISTS `detail_pesanan`;
CREATE TABLE `detail_pesanan` (
  `id` int(11) NOT NULL,
  `pesanan_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id`, `pesanan_id`, `menu_id`, `jumlah`, `subtotal`) VALUES
(1, 1, 4, 1, 35000),
(2, 1, 10, 2, 44000),
(3, 1, 5, 2, 30000),
(4, 1, 8, 1, 14000),
(5, 2, 2, 1, 32000),
(6, 2, 8, 1, 14000),
(7, 3, 5, 1, 15000),
(8, 3, 8, 1, 14000),
(9, 3, 6, 1, 18000),
(10, 3, 9, 1, 25000),
(11, 3, 4, 1, 35000),
(12, 4, 4, 2, 70000),
(13, 4, 5, 1, 15000),
(14, 4, 6, 1, 18000),
(15, 4, 8, 1, 14000),
(16, 5, 4, 1, 35000),
(17, 5, 3, 2, 76000),
(18, 5, 5, 3, 45000),
(19, 6, 1, 1, 28000),
(20, 6, 4, 1, 35000),
(21, 7, 4, 1, 35000),
(22, 7, 9, 1, 25000),
(23, 7, 2, 1, 32000),
(24, 8, 4, 1, 35000);

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `kategori` enum('Makanan','Minuman') NOT NULL,
  `harga` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT 'default.jpg',
  `stok` int(11) DEFAULT 99
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `nama_menu`, `kategori`, `harga`, `gambar`, `stok`) VALUES
(1, 'Meow Chicken Rice', 'Makanan', 28000, 'default.jpg', 99),
(2, 'Tuna Fish Bowl', 'Makanan', 32000, 'default.jpg', 99),
(3, 'Salmon Purr-fect', 'Makanan', 38000, 'default.jpg', 99),
(4, 'Cat-lifornia Roll', 'Makanan', 35000, 'default.jpg', 99),
(5, 'Milk Meow-shake', 'Minuman', 15000, 'default.jpg', 99),
(6, 'Catpuccino', 'Minuman', 18000, 'default.jpg', 99),
(7, 'Whisker Tea', 'Minuman', 10000, 'default.jpg', 99),
(8, 'Purple Cat Juice', 'Minuman', 14000, 'default.jpg', 99),
(9, 'Nasi Goreng Meowkong', 'Makanan', 25000, 'default.jpg', 99),
(10, 'Mie Nyam-Nyam', 'Makanan', 22000, 'default.jpg', 99);

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

DROP TABLE IF EXISTS `pesanan`;
CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL,
  `no_meja` int(11) NOT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `total_harga` int(11) NOT NULL,
  `status` enum('Menunggu Pembayaran','Dibayar','Sedang Dimasak','Selesai') DEFAULT 'Menunggu Pembayaran',
  `catatan` text DEFAULT NULL,
  `tanggal_pesan` timestamp NOT NULL DEFAULT current_timestamp(),
  `metode_pembayaran` varchar(50) DEFAULT 'Cash',
  `waktu_dibayar` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id`, `no_meja`, `nama_pemesan`, `total_harga`, `status`, `catatan`, `tanggal_pesan`, `metode_pembayaran`, `waktu_dibayar`) VALUES
(1, 1, 'KILAAAA', 123000, '', '', '2026-05-20 15:23:41', 'Cash', NULL),
(2, 1, 'hvjh', 46000, '', '', '2026-05-20 15:40:31', 'Cash', NULL),
(3, 1, 'deaaa', 107000, '', 'saya mau pedass sekalii', '2026-05-21 03:04:01', 'Cash', NULL),
(4, 8, 'cici', 117000, '', 'less sugar yaa', '2026-05-30 03:26:07', 'Cash', NULL),
(5, 7, 'cikaa', 156000, '', '', '2026-05-30 04:08:27', 'Cash', NULL),
(6, 1, 'nyotnyot', 63000, '', '', '2026-06-04 05:47:33', 'QRIS', NULL),
(7, 6, 'hihi', 92000, '', '', '2026-06-11 05:29:43', 'Cash', NULL),
(8, 3, 'lolo', 35000, '', '', '2026-06-11 05:50:22', 'Cash', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pesanan_id` (`pesanan_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
