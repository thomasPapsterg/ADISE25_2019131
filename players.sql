-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 27, 2025 at 06:45 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `xeri_api`
--

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `player_id` int(11) NOT NULL,
  `token` varchar(32) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`player_id`, `token`, `created_at`) VALUES
(1, '9d09560caf60257543d4bd92a832ccd1', '2025-12-03 18:59:24'),
(2, '32cf78d4f8583079d0f535cad9481769', '2025-12-03 18:59:56'),
(3, 'ccd4c7ba991409d05047c7d93746e0a7', '2025-12-03 19:15:47'),
(4, '3d9a50d4ed2486e6f1214b6c55e4fa39', '2025-12-03 19:15:53'),
(5, 'c29342972e18af9727e73fe1a8c97dd6', '2025-12-24 16:11:19'),
(6, '24aa15e2553608c4fb7b9502d32417b4', '2025-12-24 16:11:41'),
(7, '01775fa278dc89e94b1da152efa558e0', '2025-12-24 16:11:45'),
(8, '95a204de8217db4ccf5461982c105979', '2025-12-24 16:17:20'),
(9, 'cf1b05954f5c7cfc23f3535cb4f6a0c4', '2025-12-24 16:17:26'),
(10, 'b640e2ee37ede8ce668ef76bc5a1981b', '2025-12-25 19:28:53'),
(11, '1671e0992d3b5a900256d8c5440ac1e8', '2025-12-25 19:28:55'),
(12, 'a7e11a1eeac6bf294174b39715a3376a', '2025-12-25 19:39:21'),
(13, 'edf929e4f956fc233e871828932bb151', '2025-12-25 19:39:21'),
(14, '3b42463a9479b4ca61b210bf625993a4', '2025-12-25 19:41:27'),
(15, 'f863d1179331dcc67e68f035c1a9df50', '2025-12-25 19:41:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`player_id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
