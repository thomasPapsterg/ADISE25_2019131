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
-- Table structure for table `games`
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
-- Dumping data for table `games`
--

INSERT INTO `games` (`game_id`, `player1_id`, `player2_id`, `status`, `current_turn`, `board_state`, `last_move`) VALUES
(1, 1, 2, 'active', NULL, '{\"deck\":[\"10D\",\"8S\",\"3H\",\"JS\",\"8C\",\"6S\",\"QS\",\"5H\",\"QC\",\"2C\",\"3S\",\"9S\",\"5C\",\"4S\",\"5S\",\"2H\",\"JH\",\"10C\",\"AC\",\"6D\",\"3C\",\"9H\",\"10H\",\"AH\",\"KD\",\"2D\",\"4D\",\"QH\",\"8D\",\"7C\",\"KC\",\"6C\",\"QD\",\"2S\",\"8H\",\"AS\"],\"player1_hand\":[\"3D\",\"7S\",\"7D\",\"9D\",\"KH\"],\"player2_hand\":[\"JD\",\"10S\",\"9C\",\"4H\",\"4C\",\"6H\"],\"table_pile\":[],\"player1_collected\":[\"JC\",\"5D\",\"7H\",\"KS\",\"AD\"],\"player2_collected\":[],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":0,\"last_collector_id\":1,\"game_rounds_left\":6,\"player1_id\":1,\"player2_id\":2}', '2025-12-03 19:08:30'),
(2, 1, 2, 'active', 2, '{\"deck\":[\"9D\",\"9C\",\"KC\",\"2H\",\"10C\",\"8D\",\"JH\",\"8C\",\"8S\",\"10H\",\"3H\",\"QS\",\"9S\",\"AC\",\"5D\",\"JD\",\"7S\",\"AD\",\"4C\",\"8H\",\"7C\",\"9H\",\"7H\",\"AH\"],\"player1_hand\":[\"2D\",\"QC\",\"10S\",\"QD\",\"KS\"],\"player2_hand\":[\"3D\",\"2C\",\"KD\",\"7D\",\"4D\",\"5H\"],\"table_pile\":[],\"player1_collected\":[\"JS\",\"6C\",\"5C\",\"6D\",\"QH\"],\"player2_collected\":[\"JC\",\"4H\",\"10D\",\"2S\",\"5S\",\"AS\",\"6H\",\"KH\",\"4S\",\"6S\",\"3C\",\"3S\"],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":1,\"p2_xeri_jack_count\":0,\"last_collector_id\":1,\"game_rounds_left\":5,\"player1_id\":1,\"player2_id\":2}', '2025-12-03 19:25:20'),
(3, 5, 7, 'active', 5, '{\"deck\":[\"9C\",\"JD\",\"8H\",\"JH\",\"5C\",\"QC\",\"AC\",\"6H\",\"2H\",\"7S\",\"QS\",\"8S\",\"KS\",\"6C\",\"QD\",\"5H\",\"3C\",\"7H\",\"7D\",\"10D\",\"8D\",\"10C\",\"JS\",\"3D\",\"3H\",\"AS\",\"9D\",\"AD\",\"QH\",\"8C\",\"KC\",\"JC\",\"KH\",\"5D\",\"2S\",\"5S\"],\"player1_hand\":[\"9H\",\"3S\",\"4D\",\"4S\",\"4C\",\"2D\"],\"player2_hand\":[\"7C\",\"6D\",\"AH\",\"KD\",\"2C\",\"9S\"],\"table_pile\":[\"10H\",\"4H\",\"10S\",\"6S\"],\"player1_collected\":[],\"player2_collected\":[],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":0,\"last_collector_id\":null,\"game_rounds_left\":6}', '2025-12-24 16:11:49'),
(4, 8, 9, 'active', 8, '{\"deck\":[\"6H\",\"4S\",\"JH\",\"4D\",\"4C\",\"8H\",\"5H\",\"KC\",\"2C\",\"AH\",\"8C\",\"JC\",\"QC\",\"KD\",\"AS\",\"8D\",\"QD\",\"4H\",\"2H\",\"AD\",\"8S\",\"3S\",\"7H\",\"10D\"],\"player1_hand\":[\"AC\",\"5C\",\"3D\",\"KH\"],\"player2_hand\":[\"9D\",\"3C\",\"QH\",\"6C\"],\"table_pile\":[],\"player1_collected\":[],\"player2_collected\":[\"9S\",\"QS\",\"2S\",\"2D\",\"JS\",\"10H\",\"JD\",\"9C\",\"6D\",\"5S\",\"7S\",\"5D\",\"6S\",\"3H\",\"KS\",\"10S\",\"7D\",\"7C\",\"10C\",\"9H\"],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":0,\"last_collector_id\":9,\"game_rounds_left\":5,\"player1_id\":8,\"player2_id\":9}', '2025-12-24 16:52:15'),
(5, 10, 11, 'active', 11, '{\"deck\":[\"6H\",\"7D\",\"7C\",\"10C\",\"AS\",\"3S\",\"AC\",\"3D\",\"QD\",\"8H\",\"5H\",\"JD\",\"4S\",\"QS\",\"5D\",\"QH\",\"10S\",\"10H\",\"3C\",\"6S\",\"5C\",\"8C\",\"9H\",\"10D\"],\"player1_hand\":[\"5S\",\"4D\",\"KH\",\"3H\",\"AD\"],\"player2_hand\":[\"2S\",\"2H\",\"KC\",\"6D\",\"JH\",\"7H\"],\"table_pile\":[\"2C\",\"9D\",\"2D\",\"KS\",\"QC\",\"9S\",\"7S\",\"8S\",\"4H\",\"8D\",\"AH\",\"KD\"],\"player1_collected\":[\"JS\",\"6C\",\"4C\",\"9C\",\"JC\"],\"player2_collected\":[],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":0,\"last_collector_id\":null,\"game_rounds_left\":5,\"player1_id\":10,\"player2_id\":11}', '2025-12-25 19:35:30'),
(6, 12, 13, 'active', 13, '{\"deck\":[\"3H\",\"7C\",\"6H\",\"6S\",\"QH\",\"3S\",\"8S\",\"8D\",\"10S\",\"QC\",\"JD\",\"7H\",\"3C\",\"4S\",\"4C\",\"5H\",\"5D\",\"2S\",\"JS\",\"AC\",\"AD\",\"6D\",\"AS\",\"KD\",\"10C\",\"AH\",\"QD\",\"5C\",\"KC\",\"KS\",\"2H\",\"7D\",\"JC\",\"JH\",\"3D\",\"KH\"],\"player1_hand\":[\"9D\",\"5S\",\"10H\",\"2C\",\"10D\"],\"player2_hand\":[\"9S\",\"9C\",\"7S\",\"8H\",\"9H\",\"4D\"],\"table_pile\":[\"2D\",\"QS\",\"8C\",\"6C\",\"4H\"],\"player1_collected\":[],\"player2_collected\":[],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":0,\"last_collector_id\":null,\"game_rounds_left\":6,\"player1_id\":12,\"player2_id\":13}', '2025-12-25 19:39:40'),
(7, 14, 15, 'active', 14, '{\"deck\":[\"10S\",\"6C\",\"9C\",\"5D\",\"4D\",\"JC\",\"KH\",\"9D\",\"5H\",\"QS\",\"AD\",\"7S\",\"JS\",\"2D\",\"JD\",\"JH\",\"QC\",\"6D\",\"8D\",\"KC\",\"2C\",\"6H\",\"3C\",\"4C\",\"10D\",\"3D\",\"8S\",\"7H\",\"KD\",\"AH\",\"KS\",\"3H\",\"10C\",\"4S\",\"9S\",\"5S\"],\"player1_hand\":[\"QH\",\"8H\",\"AC\",\"8C\"],\"player2_hand\":[\"6S\",\"10H\",\"QD\",\"3S\"],\"table_pile\":[\"2S\",\"5C\"],\"player1_collected\":[],\"player2_collected\":[\"7C\",\"9H\",\"AS\",\"2H\",\"4H\",\"7D\"],\"p1_xeri_count\":0,\"p1_xeri_jack_count\":0,\"p2_xeri_count\":0,\"p2_xeri_jack_count\":0,\"last_collector_id\":null,\"game_rounds_left\":6,\"player1_id\":14,\"player2_id\":15}', '2025-12-25 19:43:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`game_id`),
  ADD KEY `player1_id` (`player1_id`),
  ADD KEY `player2_id` (`player2_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `game_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `games_ibfk_1` FOREIGN KEY (`player1_id`) REFERENCES `players` (`player_id`),
  ADD CONSTRAINT `games_ibfk_2` FOREIGN KEY (`player2_id`) REFERENCES `players` (`player_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
