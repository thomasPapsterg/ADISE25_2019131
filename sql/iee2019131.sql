-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Εξυπηρετητής: 127.0.0.1
-- Χρόνος δημιουργίας: 06 Φεβ 2026 στις 11:57:13
-- Έκδοση διακομιστή: 10.4.32-MariaDB
-- Έκδοση PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Βάση δεδομένων: `iee2019131`
--

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `games`
--

CREATE TABLE `games` (
  `game_id` int(11) NOT NULL,
  `player1_id` int(11) NOT NULL,
  `player2_id` int(11) DEFAULT NULL,
  `status` enum('waiting','active','ended') NOT NULL DEFAULT 'waiting',
  `current_turn` int(11) DEFAULT NULL,
  `board_state` text NOT NULL,
  `last_move` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `games`
--

INSERT INTO `games` (`game_id`, `player1_id`, `player2_id`, `status`, `current_turn`, `board_state`, `last_move`) VALUES
(8, 94, 95, 'active', 94, '{\"deck\":[\"4H\",\"2D\",\"4S\",\"2C\",\"JS\",\"KH\",\"AH\",\"3H\",\"10D\",\"8C\",\"QH\",\"6H\",\"AS\",\"8H\",\"9H\",\"9D\",\"JD\",\"3S\",\"JH\",\"10S\",\"QD\",\"QC\",\"3C\",\"8S\",\"AD\",\"7D\",\"3D\",\"2H\",\"10H\",\"5C\",\"8D\",\"QS\",\"6S\",\"9C\",\"KC\",\"5H\"],\"player1_hand\":[\"7H\",\"KS\",\"AC\",\"2S\",\"4D\",\"4C\"],\"player2_hand\":[\"6D\",\"7C\",\"9S\",\"5S\",\"JC\",\"KD\"],\"table_pile\":[\"10C\",\"5D\",\"7S\",\"6C\"],\"player1_collected\":[],\"player2_collected\":[],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":0,\"last_collector_id\":null,\"game_rounds_left\":6,\"player1_id\":94,\"player2_id\":95}', '2026-02-04 18:23:28'),
(9, 96, 97, 'active', 96, '{\"deck\":[\"10D\",\"AC\",\"6H\",\"10C\",\"QC\",\"QH\",\"10S\",\"2C\",\"4D\",\"3S\",\"2H\",\"7H\",\"8C\",\"JH\",\"2S\",\"AD\",\"2D\",\"9C\",\"10H\",\"3H\",\"7D\",\"9H\",\"JS\",\"AS\",\"8D\",\"6D\",\"JD\",\"KC\",\"KH\",\"5D\",\"5S\",\"7C\",\"4S\",\"3D\",\"7S\",\"JC\"],\"player1_hand\":[\"8H\",\"3C\",\"KD\",\"6S\",\"QD\",\"5C\"],\"player2_hand\":[\"4C\",\"AH\",\"9D\",\"5H\",\"QS\",\"KS\"],\"table_pile\":[\"4H\",\"8S\",\"6C\",\"9S\"],\"player1_collected\":[],\"player2_collected\":[],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":0,\"last_collector_id\":null,\"game_rounds_left\":6,\"player1_id\":96,\"player2_id\":97}', '2026-02-04 18:33:37'),
(10, 98, NULL, 'waiting', 98, '{\"deck\":[\"KD\",\"2C\",\"7C\",\"3C\",\"8C\",\"8D\",\"7H\",\"KS\",\"9H\",\"3S\",\"7S\",\"QD\",\"KC\",\"7D\",\"QS\",\"9C\",\"6D\",\"QC\",\"QH\",\"JH\",\"5C\",\"5S\",\"2D\",\"4D\",\"10H\",\"AD\",\"3H\",\"8S\",\"JS\",\"5H\",\"JC\",\"9S\",\"10C\",\"KH\",\"AS\",\"AH\"],\"player1_hand\":[\"9D\",\"5D\",\"10D\",\"4S\",\"6H\",\"3D\"],\"player2_hand\":[\"10S\",\"4H\",\"6S\",\"4C\",\"8H\",\"AC\"],\"table_pile\":[\"6C\",\"2S\",\"JD\",\"2H\"],\"player1_collected\":[],\"player2_collected\":[],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":0,\"last_collector_id\":null,\"game_rounds_left\":6,\"player1_id\":98,\"player2_id\":null}', '2026-02-04 18:48:51'),
(11, 98, NULL, 'waiting', 98, '{\"deck\":[\"9D\",\"8H\",\"JS\",\"8S\",\"5S\",\"9H\",\"6H\",\"3S\",\"JD\",\"3C\",\"4D\",\"7S\",\"6C\",\"QC\",\"JC\",\"2C\",\"3H\",\"2D\",\"2H\",\"4C\",\"6S\",\"7D\",\"QS\",\"5C\",\"4S\",\"QH\",\"7C\",\"2S\",\"KD\",\"JH\",\"8D\",\"7H\",\"AC\",\"9C\",\"9S\",\"4H\"],\"player1_hand\":[\"QD\",\"KC\",\"10C\",\"8C\",\"5H\",\"5D\"],\"player2_hand\":[\"10H\",\"10S\",\"AS\",\"AH\",\"6D\",\"KS\"],\"table_pile\":[\"3D\",\"AD\",\"10D\",\"KH\"],\"player1_collected\":[],\"player2_collected\":[],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":0,\"last_collector_id\":null,\"game_rounds_left\":6,\"player1_id\":98,\"player2_id\":null}', '2026-02-04 18:49:13'),
(12, 100, NULL, 'waiting', 100, '{\"deck\":[\"QH\",\"KH\",\"7H\",\"10S\",\"7S\",\"JS\",\"6D\",\"6C\",\"2S\",\"3H\",\"3C\",\"8S\",\"4C\",\"JD\",\"3D\",\"6S\",\"7C\",\"2H\",\"10C\",\"QD\",\"8D\",\"QS\",\"10D\",\"KD\",\"AS\",\"5C\",\"9H\",\"2D\",\"5H\",\"4D\",\"3S\",\"5D\",\"AH\",\"9S\",\"10H\",\"6H\"],\"player1_hand\":[\"2C\",\"8H\",\"4S\",\"5S\",\"KC\",\"JC\"],\"player2_hand\":[\"AD\",\"JH\",\"QC\",\"AC\",\"9C\",\"9D\"],\"table_pile\":[\"4H\",\"8C\",\"7D\",\"KS\"],\"player1_collected\":[],\"player2_collected\":[],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":0,\"last_collector_id\":null,\"game_rounds_left\":6,\"player1_id\":100,\"player2_id\":null}', '2026-02-05 10:00:07'),
(13, 102, 103, 'active', 102, '{\"deck\":[\"2C\",\"JH\",\"4S\",\"3D\",\"KS\",\"7C\",\"8S\",\"5C\",\"QC\",\"9C\",\"6D\",\"8H\",\"JD\",\"8C\",\"5S\",\"QH\",\"10D\",\"9H\",\"5H\",\"QS\",\"4C\",\"KC\",\"10S\",\"2S\",\"9S\",\"AS\",\"KD\",\"3S\",\"2H\",\"QD\",\"4H\",\"10C\",\"3H\",\"8D\",\"AD\",\"7H\"],\"player1_hand\":[\"7S\",\"5D\",\"7D\",\"AH\",\"JC\",\"6H\"],\"player2_hand\":[\"10H\",\"4D\",\"6C\",\"AC\",\"3C\",\"JS\"],\"table_pile\":[\"KH\",\"6S\",\"9D\",\"2D\"],\"player1_collected\":[],\"player2_collected\":[],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":0,\"last_collector_id\":null,\"game_rounds_left\":6,\"player1_id\":102,\"player2_id\":103}', '2026-02-05 10:05:20'),
(14, 104, 105, 'active', 104, '{\"deck\":[\"QH\",\"10H\",\"6H\",\"9D\",\"QC\",\"9H\",\"2S\",\"5H\",\"AH\",\"7D\",\"6C\",\"KC\",\"JH\",\"8D\",\"6S\",\"KS\",\"3C\",\"9S\",\"8C\",\"4S\",\"4D\",\"4C\",\"KD\",\"JC\",\"QS\",\"QD\",\"3D\",\"4H\",\"9C\",\"2H\",\"JD\",\"3S\",\"3H\",\"5C\",\"10D\",\"8H\"],\"player1_hand\":[\"7S\",\"10C\",\"6D\",\"5D\",\"8S\",\"7H\"],\"player2_hand\":[\"AS\",\"7C\",\"2C\",\"AC\",\"10S\",\"KH\"],\"table_pile\":[\"JS\",\"2D\",\"5S\",\"AD\"],\"player1_collected\":[],\"player2_collected\":[],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":0,\"last_collector_id\":null,\"game_rounds_left\":6,\"player1_id\":104,\"player2_id\":105}', '2026-02-05 10:14:47'),
(15, 106, 107, 'active', 106, '{\"deck\":[\"2H\",\"9C\",\"QH\",\"4S\",\"7C\",\"3C\",\"6D\",\"8D\",\"JD\",\"9D\",\"10S\",\"6H\",\"AH\",\"AC\",\"7H\",\"7S\",\"QS\",\"6S\",\"9S\",\"8H\",\"8C\",\"KH\",\"2S\",\"5H\",\"AD\",\"10C\",\"5C\",\"KD\",\"4C\",\"10D\",\"3S\",\"8S\",\"JH\",\"3H\",\"JC\",\"4H\"],\"player1_hand\":[\"AS\",\"KC\",\"7D\",\"5D\",\"QC\",\"10H\"],\"player2_hand\":[\"4D\",\"9H\",\"QD\",\"JS\",\"2D\",\"5S\"],\"table_pile\":[\"KS\",\"2C\",\"3D\",\"6C\"],\"player1_collected\":[],\"player2_collected\":[],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":0,\"last_collector_id\":null,\"game_rounds_left\":6,\"player1_id\":106,\"player2_id\":107}', '2026-02-05 11:17:50'),
(16, 108, 109, 'active', 108, '{\"deck\":[\"2C\",\"AH\",\"KH\",\"3S\",\"10H\",\"2S\",\"7D\",\"5H\",\"7S\",\"3H\",\"9D\",\"5D\",\"AD\",\"10S\",\"6C\",\"6H\",\"KS\",\"4C\",\"QD\",\"2D\",\"AC\",\"8C\",\"3D\",\"9C\",\"QH\",\"KC\",\"4S\",\"9H\",\"8D\",\"KD\",\"7C\",\"5C\",\"3C\",\"8H\",\"JD\",\"4D\"],\"player1_hand\":[\"4H\",\"6S\",\"5S\",\"QC\",\"6D\",\"JS\"],\"player2_hand\":[\"10D\",\"JH\",\"7H\",\"AS\",\"QS\",\"10C\"],\"table_pile\":[\"2H\",\"JC\",\"8S\",\"9S\"],\"player1_collected\":[],\"player2_collected\":[],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":0,\"last_collector_id\":null,\"game_rounds_left\":6,\"player1_id\":108,\"player2_id\":109}', '2026-02-05 17:11:27'),
(17, 110, 111, 'active', 111, '{\"deck\":[\"KC\",\"3D\",\"3C\",\"QC\",\"7H\",\"QH\",\"JS\",\"8D\",\"JH\",\"5C\",\"KH\",\"7D\",\"JD\",\"5H\",\"2C\",\"5D\",\"QD\",\"10D\",\"5S\",\"3H\",\"9C\",\"6H\",\"9D\",\"2S\",\"7C\",\"3S\",\"10C\",\"4D\",\"AH\",\"6S\",\"7S\",\"10H\",\"9S\",\"AD\",\"4S\",\"6D\"],\"player1_hand\":[\"2D\",\"8S\",\"4H\",\"10S\",\"8C\"],\"player2_hand\":[\"KD\",\"AC\",\"2H\",\"QS\",\"6C\",\"KS\"],\"table_pile\":[\"JC\",\"4C\",\"9H\",\"8H\",\"AS\"],\"player1_collected\":[],\"player2_collected\":[],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":0,\"last_collector_id\":null,\"game_rounds_left\":6,\"player1_id\":110,\"player2_id\":111}', '2026-02-05 17:18:19'),
(18, 112, 113, 'active', 113, '{\"deck\":[],\"player1_hand\":[],\"player2_hand\":[\"KC\"],\"table_pile\":[\"9S\",\"5S\",\"QD\",\"4D\",\"QC\"],\"player1_collected\":[\"AH\",\"3C\",\"KH\",\"3H\",\"KD\",\"4C\",\"8C\",\"4H\",\"5D\",\"9C\",\"AD\",\"7D\",\"7S\",\"6C\",\"8S\",\"6H\"],\"player2_collected\":[\"JD\",\"9H\",\"JS\",\"3D\",\"QS\",\"3S\",\"6D\",\"4S\",\"7H\",\"9D\",\"6S\",\"JH\",\"10C\",\"8H\",\"2D\",\"QH\",\"10H\",\"AC\",\"10S\",\"5H\",\"2C\",\"KS\",\"10D\",\"7C\",\"AS\",\"JC\",\"8D\",\"2S\",\"5C\",\"2H\"],\"p1_xeri_count\":1,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":1,\"last_collector_id\":null,\"game_rounds_left\":3,\"player1_id\":112,\"player2_id\":113}', '2026-02-05 20:48:52'),
(19, 121, NULL, 'waiting', 121, '{\"deck\":[\"AS\",\"3C\",\"2D\",\"3H\",\"3D\",\"8S\",\"10D\",\"6S\",\"4D\",\"6C\",\"QS\",\"7D\",\"QH\",\"4C\",\"KC\",\"7H\",\"JD\",\"10C\",\"KH\",\"3S\",\"7C\",\"QC\",\"KD\",\"2C\",\"5S\",\"8C\",\"5D\",\"AC\",\"4S\",\"AH\",\"9D\",\"6H\",\"5H\",\"JH\",\"KS\",\"8H\"],\"player1_hand\":[\"2S\",\"10H\",\"7S\",\"6D\",\"8D\",\"9S\"],\"player2_hand\":[\"2H\",\"5C\",\"JC\",\"AD\",\"4H\",\"QD\"],\"table_pile\":[\"9C\",\"JS\",\"9H\",\"10S\"],\"player1_collected\":[],\"player2_collected\":[],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":0,\"last_collector_id\":null,\"game_rounds_left\":6,\"player1_id\":121,\"player2_id\":null}', '2026-02-06 11:20:27'),
(20, 123, 124, 'active', 124, '{\"deck\":[\"3C\",\"KS\",\"7S\",\"7C\",\"4C\",\"8S\",\"8H\",\"QH\",\"5C\",\"4H\",\"10D\",\"7H\",\"5S\",\"5H\",\"QD\",\"10C\",\"5D\",\"JD\",\"9H\",\"9D\",\"8C\",\"2D\",\"10H\",\"AC\",\"3H\",\"6S\",\"10S\",\"KC\",\"AD\",\"6D\",\"QC\",\"7D\",\"6H\",\"JC\",\"AH\",\"3S\"],\"player1_hand\":[\"2H\",\"2C\",\"9C\",\"6C\",\"2S\"],\"player2_hand\":[\"JS\",\"9S\",\"KD\",\"AS\",\"8D\",\"QS\"],\"table_pile\":[],\"player1_collected\":[\"JH\",\"3D\",\"4D\",\"4S\",\"KH\"],\"player2_collected\":[],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":0,\"last_collector_id\":123,\"game_rounds_left\":6,\"player1_id\":123,\"player2_id\":124}', '2026-02-06 12:46:41'),
(21, 125, 126, 'active', 126, '{\"deck\":[\"8S\",\"KD\",\"QS\",\"5C\",\"KS\",\"AD\",\"JD\",\"6C\",\"10H\",\"6S\",\"9C\",\"AS\",\"4D\",\"6H\",\"QH\",\"3D\",\"5D\",\"8D\",\"9H\",\"AH\",\"AC\",\"JH\",\"2C\",\"QC\",\"8H\",\"7H\",\"3C\",\"10D\",\"9D\",\"JC\",\"10C\",\"3H\",\"10S\",\"JS\",\"5S\",\"6D\"],\"player1_hand\":[\"4H\",\"4S\",\"3S\",\"4C\",\"KC\"],\"player2_hand\":[\"7C\",\"7S\",\"2D\",\"9S\",\"QD\",\"8C\"],\"table_pile\":[\"7D\",\"2H\",\"2S\",\"KH\",\"5H\"],\"player1_collected\":[],\"player2_collected\":[],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":0,\"last_collector_id\":null,\"game_rounds_left\":6,\"player1_id\":125,\"player2_id\":126}', '2026-02-06 12:50:56');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `players`
--

CREATE TABLE `players` (
  `player_id` int(11) NOT NULL,
  `token` varchar(32) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Άδειασμα δεδομένων του πίνακα `players`
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
(15, 'f863d1179331dcc67e68f035c1a9df50', '2025-12-25 19:41:28'),
(16, '77d8bf67bd3a9472d32e1716aed96654', '2026-02-01 17:14:21'),
(17, '0c3f06be700d23d80eca02d69cd8fa70', '2026-02-01 17:14:54'),
(18, 'ab1775bfef2cb7d933b8f5c120157e59', '2026-02-01 17:15:33'),
(19, '9279b3ecdf0da9e45508cf49518c22be', '2026-02-01 17:15:37'),
(20, 'fc9626100f420fbcb8e0ef4b0286d59a', '2026-02-01 17:20:46'),
(21, 'c3533db86bdc40ea42ad1d0418996d62', '2026-02-01 17:20:48'),
(22, '7706aa9fa467b1b2350224b9d74f817b', '2026-02-01 17:25:22'),
(23, '87d60cc6fc11edd17db99fd802242705', '2026-02-01 17:25:23'),
(24, 'a2dde6f3f2f8f4fa2fd2319fbdcc83a1', '2026-02-01 17:29:37'),
(25, 'f71568111a774066e894245393cdb25e', '2026-02-01 17:29:38'),
(26, 'ad8959f0f57efde1d3f54a53cea8db31', '2026-02-01 17:30:01'),
(27, 'd6fa12678656068f294c8ca0c95d7bee', '2026-02-01 17:30:02'),
(28, 'e9008b458f9a09a6e20cd94d30af8890', '2026-02-01 17:31:29'),
(29, 'ed047ae7267e51d243bc15ed17f3f576', '2026-02-01 17:31:30'),
(30, '4871c9968ecb50df61dedb1868f2000f', '2026-02-01 17:35:41'),
(31, '51f12db12d802e1b30bbac7b591c22bb', '2026-02-01 17:35:43'),
(32, '579a3765548a8b00d4674d701bdf9a43', '2026-02-01 17:36:51'),
(33, '9ea4224e7b0c34c06b2194b3d1be6ee0', '2026-02-01 17:37:00'),
(34, 'c91d198e07c99015f9c133f1738d5cd7', '2026-02-01 17:40:23'),
(35, 'aea6f2af80440f3b09323e18709fd2e8', '2026-02-01 17:40:24'),
(36, '8568e2ce33d65a3bedb7a0eeb0635ef9', '2026-02-01 17:43:55'),
(37, '53ba89b771c099226aa413f22473e1d2', '2026-02-01 17:43:56'),
(38, '84cac32f7cd9d8c4cb7c201ffbfa4c11', '2026-02-01 17:57:51'),
(39, '00f785a238198848b26ed14c3b560a48', '2026-02-01 17:57:54'),
(40, '2116d9b2e73db7fdb8a3686465a1c692', '2026-02-01 18:10:14'),
(41, 'e97b7014feecc2a3e5e057da095e9e3a', '2026-02-01 18:10:15'),
(42, '14084555c25bb45ea8b1682686c750d4', '2026-02-01 18:12:29'),
(43, 'f6229bedbd11835ec8152a7f16475f4f', '2026-02-01 18:12:30'),
(44, '56008a9f157ee122cbd33981c37efd47', '2026-02-01 18:18:33'),
(45, 'd492d8f2da3a5f58c49a2d67e9935393', '2026-02-01 18:18:34'),
(46, 'e421e62fad7e7c4915efa820bb574244', '2026-02-01 18:20:27'),
(47, '238729f218987e09dcb4b566ab985c79', '2026-02-01 18:20:28'),
(48, '5a8cb134456e756fda620e3c617a98c1', '2026-02-01 18:22:06'),
(49, '4fe32cf02f32e6246fb91408b39866c3', '2026-02-01 18:22:07'),
(50, '213fb1852ea61b5d26de481c7029dfcf', '2026-02-01 18:27:23'),
(51, '463e6828b2b64f6ad205292790e7085a', '2026-02-01 18:27:24'),
(52, '88cae689fa504b346256e458c70f1554', '2026-02-01 18:29:22'),
(53, 'ff22305d566c1154c157ba15131744df', '2026-02-01 18:29:24'),
(54, 'c0da8c6bb83ef09c39ee23aba158c3a5', '2026-02-01 18:31:00'),
(55, 'c5a909c54d392338e13898859b7bff5e', '2026-02-01 18:31:02'),
(56, '13a97231647ce93b501db91b9d3a6311', '2026-02-01 18:39:54'),
(57, 'ddfa2663ca7cd772b1ef281afb9bf9e8', '2026-02-01 18:39:55'),
(58, '8da2a1b3e5c6d280d58bece17b0f6b08', '2026-02-01 18:47:42'),
(59, '29c51269d41c6703f507c63da4345ef0', '2026-02-01 18:47:48'),
(60, 'b23b1a0329dbe72641905726e2956115', '2026-02-01 18:49:35'),
(61, '2c752528a7c9e0a97f8fc0ac4d71db90', '2026-02-01 18:49:36'),
(62, 'b82bb207828aa7eafc508b94c83fcabe', '2026-02-01 18:55:24'),
(63, '112656605f393141e4f5e029a2b9bf41', '2026-02-01 18:55:26'),
(64, 'a27c6eab6b3c58249f40c2e3bdac71a3', '2026-02-01 19:06:51'),
(65, 'e9a54cefa20eedebb299fb5015dc3dab', '2026-02-01 19:06:53'),
(66, 'a6aff8fbe2d15f3bbfda02a122db1260', '2026-02-01 19:11:07'),
(67, 'd16f0123e312e1d6a025cd98f9f7a656', '2026-02-01 19:11:08'),
(68, 'd138bbdaf6f3a04fe9c505728cffed78', '2026-02-01 19:14:26'),
(69, '38dbfd6396858b21b2d19225b21ead01', '2026-02-01 19:14:29'),
(70, '84d2d0aced3337ccfe8ca8bdb5e337a9', '2026-02-04 16:56:21'),
(71, 'fd0837273d734a8ff6bbc0b80f9e618e', '2026-02-04 16:56:25'),
(72, '20bcb9d42bcac72a6ce878763d4b60fb', '2026-02-04 17:03:48'),
(73, 'aef720de1bd0b3798f53451303f592cf', '2026-02-04 17:03:49'),
(74, '44d7d807f0a732276ae04758192dc9d0', '2026-02-04 17:09:28'),
(75, '1b375f0953ff73b28a204039da62a72f', '2026-02-04 17:09:29'),
(76, '875738a8b5b14ccd8db5cd865eb2ad35', '2026-02-04 17:24:48'),
(77, 'd2706478bf22e1524d95728b038fbe9c', '2026-02-04 17:24:49'),
(78, 'faf23f81aad5cdd0754df6683765b4b6', '2026-02-04 17:25:46'),
(79, '152bff573eb4a14293706de4f8c0afd4', '2026-02-04 17:25:47'),
(80, '32323a089559d9d3fb05986ecd2fb680', '2026-02-04 17:29:38'),
(81, '28deb02c73a4043b8ddee74820a82b3e', '2026-02-04 17:29:39'),
(82, '56f78983d95ec8500b58317f44d393b4', '2026-02-04 17:34:20'),
(83, '0fcb1f2bbcc20dccf921356bfe1d2b56', '2026-02-04 17:34:20'),
(84, '61a035a664492ac642d55f3b0d473130', '2026-02-04 17:34:22'),
(85, '37b20ff4f03d69b805ed2a74ec36d783', '2026-02-04 17:34:42'),
(86, '9e336fe57108dfa73a27dfc0e5e1b46e', '2026-02-04 17:34:45'),
(87, 'a5e34672ca8ce3f86e1e1685457748b2', '2026-02-04 17:47:43'),
(88, '0cb143d6545fec967247fbbb480549ef', '2026-02-04 18:01:35'),
(89, '22e002fb73bf7e20c2f0f5600c586586', '2026-02-04 18:01:36'),
(90, '44c4e37cb761481a9b261b3c46fce317', '2026-02-04 18:05:15'),
(91, 'c959c9d31e3c4d1e5637f9728bcd2bfd', '2026-02-04 18:05:16'),
(92, 'b1f0c71f03eb0451499cb4ad3c327ee4', '2026-02-04 18:15:20'),
(93, '6ec5c526eb42b9192c6240cb8d868314', '2026-02-04 18:15:21'),
(94, 'c0ca01a2a14747a497c29c95b23f7b67', '2026-02-04 18:23:25'),
(95, '1f8ab50aa97b7490d0e7adaeb1147d7c', '2026-02-04 18:23:26'),
(96, 'ad744a444b05cded5ae3d72214293bff', '2026-02-04 18:33:32'),
(97, '0625796adcf603f97d8b69d3f69b9fa3', '2026-02-04 18:33:35'),
(98, 'bb3140358cbd8b28ecf37d06de9c4897', '2026-02-04 18:48:47'),
(99, 'a33436e78bb92a51b09e4473d292b4f5', '2026-02-04 18:48:49'),
(100, '1e49a50eb9e032d46a715854658f1af6', '2026-02-05 10:00:02'),
(101, 'abddcfe9fed4136ed080c7d96bb42f6b', '2026-02-05 10:00:04'),
(102, 'a647a4ebddbc5c67154477c4eeafc59e', '2026-02-05 10:05:13'),
(103, '289b0d4b925b1f1ad63f68dc8e79be6a', '2026-02-05 10:05:16'),
(104, '4f57146bd67b5d1269018621dfc76785', '2026-02-05 10:14:44'),
(105, '87b6ef98e0399b9662458666972aeacb', '2026-02-05 10:14:45'),
(106, 'caf77fe85e9cffb3dc8224493826d796', '2026-02-05 11:17:47'),
(107, '37e1e9937a0be97c16a4897655b568d3', '2026-02-05 11:17:48'),
(108, 'cfa84429502ea361300d84088415e326', '2026-02-05 17:11:25'),
(109, 'c6d47c89f558815f3efc39049ad58c68', '2026-02-05 17:11:26'),
(110, '3bd87afc5f3df3e98275a0b638337ea6', '2026-02-05 17:18:13'),
(111, '228128e5fa854dbbe61d0cd4d552b48b', '2026-02-05 17:18:16'),
(112, '1a5ffa4b4b57f1d8605b4506ab543c1b', '2026-02-05 20:48:47'),
(113, 'f1b9447ef33c6f7462ad99e927e7edae', '2026-02-05 20:48:48'),
(114, 'f82a6aa02d29356cf25038ff2bef447b', '2026-02-06 11:08:00'),
(115, '6095306f2b51b1aae6267ec290895302', '2026-02-06 11:08:01'),
(116, 'c1ae799e766749a0796a5b10ecc695df', '2026-02-06 11:08:47'),
(117, 'fe7d762683ac6729fbd19f6cdf3a2c4e', '2026-02-06 11:08:48'),
(118, '2a2628db0f0287a9382efdce2b646c61', '2026-02-06 11:10:07'),
(119, '565abda689f1986994cde3629de3adc8', '2026-02-06 11:10:08'),
(120, '811b433377a08414229c3c0503bd71d1', '2026-02-06 11:15:42'),
(121, '21dfeed99262b858f8a7c197296f3ce8', '2026-02-06 11:20:26'),
(122, 'bc38d51a01c72051801d0c9ae46cc830', '2026-02-06 11:20:26'),
(123, '401a101b59dcd1787d308ef6245d1742', '2026-02-06 12:46:39'),
(124, 'ef2a9c7dd0a6076611a62b6e5b23dd5c', '2026-02-06 12:46:40'),
(125, 'ebd5e5d7715f1b708342772dcf471012', '2026-02-06 12:50:54'),
(126, 'a98db3bf057c35e5d3031001606fa9c5', '2026-02-06 12:50:54');

--
-- Ευρετήρια για άχρηστους πίνακες
--

--
-- Ευρετήρια για πίνακα `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`game_id`),
  ADD KEY `player1_id` (`player1_id`),
  ADD KEY `player2_id` (`player2_id`);

--
-- Ευρετήρια για πίνακα `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`player_id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- AUTO_INCREMENT για άχρηστους πίνακες
--

--
-- AUTO_INCREMENT για πίνακα `games`
--
ALTER TABLE `games`
  MODIFY `game_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT για πίνακα `players`
--
ALTER TABLE `players`
  MODIFY `player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- Περιορισμοί για άχρηστους πίνακες
--

--
-- Περιορισμοί για πίνακα `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `games_ibfk_1` FOREIGN KEY (`player1_id`) REFERENCES `players` (`player_id`),
  ADD CONSTRAINT `games_ibfk_2` FOREIGN KEY (`player2_id`) REFERENCES `players` (`player_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
