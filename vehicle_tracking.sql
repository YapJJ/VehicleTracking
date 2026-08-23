-- phpMyAdmin SQL Dump
-- version 5.2.3deb1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 23, 2026 at 09:01 AM
-- Server version: 11.8.6-MariaDB-5ubuntu0.1 from Ubuntu
-- PHP Version: 8.5.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vehicle_tracking`
--
CREATE DATABASE IF NOT EXISTS `vehicle_tracking` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `vehicle_tracking`;

-- --------------------------------------------------------

--
-- Table structure for table `Fueling`
--

DROP TABLE IF EXISTS `Fueling`;
CREATE TABLE `Fueling` (
  `id` int(11) NOT NULL,
  `date_time` datetime DEFAULT NULL,
  `amount_rm` decimal(5,2) DEFAULT NULL,
  `liter` decimal(5,3) DEFAULT NULL,
  `range_b4_km` int(11) DEFAULT NULL,
  `range_after_km` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Fueling`
--

INSERT INTO `Fueling` (`id`, `date_time`, `amount_rm`, `liter`, `range_b4_km`, `range_after_km`, `location`) VALUES
(1, '2024-07-24 12:40:00', 55.00, 26.829, NULL, 487, 'Petronas Grand Saga 2'),
(2, '2024-08-22 03:10:00', 50.00, 25.125, 125, 467, 'Petronas Seremban R&R (Southbound)');

-- --------------------------------------------------------

--
-- Table structure for table `MileageRecord`
--

DROP TABLE IF EXISTS `MileageRecord`;
CREATE TABLE `MileageRecord` (
  `id` int(11) NOT NULL,
  `odo_km` int(11) DEFAULT NULL,
  `trip_km` decimal(6,1) DEFAULT NULL,
  `avg_km_l` decimal(3,1) DEFAULT NULL,
  `range_km` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `odo_change` int(11) DEFAULT NULL,
  `trip_change` decimal(6,1) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `MileageRecord`
--

INSERT INTO `MileageRecord` (`id`, `odo_km`, `trip_km`, `avg_km_l`, `range_km`, `location`, `odo_change`, `trip_change`, `date_time`) VALUES
(1, NULL, 0.0, 13.6, 487, 'Petronas Grand Saga 2', NULL, NULL, '2024-07-24 12:40:00'),
(2, 40700, 23.6, 13.6, 486,'Petronas Seremban R&R (Southbound)', NULL, 23.6, '2024-07-25 08:30:00');

--
-- Triggers `MileageRecord`
--
DROP TRIGGER IF EXISTS `calculate_changes`;
DELIMITER $$
CREATE TRIGGER `calculate_changes` BEFORE INSERT ON `MileageRecord` FOR EACH ROW BEGIN
    DECLARE prev_odo INT;
    DECLARE prev_trip DECIMAL(6,1);
    
    SELECT odo_km, trip_km INTO prev_odo, prev_trip
    FROM MileageRecord
    ORDER BY id DESC
    LIMIT 1;
    
    IF prev_odo IS NOT NULL THEN
        SET NEW.odo_change = NEW.odo_km - prev_odo;
        SET NEW.trip_change = NEW.trip_km - prev_trip;
    ELSE
        SET NEW.odo_change = NULL;
        SET NEW.trip_change = NULL;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ResetTrip`
--

DROP TABLE IF EXISTS `ResetTrip`;
CREATE TABLE `ResetTrip` (
  `id` int(11) NOT NULL,
  `date_time` datetime DEFAULT NULL,
  `mileage_km` decimal(7,1) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ResetTrip`
--

INSERT INTO `ResetTrip` (`id`, `date_time`, `mileage_km`, `location`) VALUES
(1, '2024-07-24 12:40:00', 1103.7, 'Petronas Grand Saga 2');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Fueling`
--
ALTER TABLE `Fueling`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `MileageRecord`
--
ALTER TABLE `MileageRecord`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ResetTrip`
--
ALTER TABLE `ResetTrip`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Fueling`
--
ALTER TABLE `Fueling`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `MileageRecord`
--
ALTER TABLE `MileageRecord`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=333;

--
-- AUTO_INCREMENT for table `ResetTrip`
--
ALTER TABLE `ResetTrip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
