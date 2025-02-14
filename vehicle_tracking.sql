-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2024 at 03:59 PM
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
-- Database: `vehicle_tracking`
--
CREATE DATABASE IF NOT EXISTS `vehicle_tracking` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `vehicle_tracking`;

-- --------------------------------------------------------

--
-- Table structure for table `fueling`
--

DROP TABLE IF EXISTS `fueling`;
CREATE TABLE `fueling` (
  `id` int(11) NOT NULL,
  `date_time` datetime DEFAULT NULL,
  `amount_rm` decimal(5,2) DEFAULT NULL,
  `liter` decimal(5,3) DEFAULT NULL,
  `range_b4_km` int(11) DEFAULT NULL,
  `range_after_km` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mileagerecord`
--

DROP TABLE IF EXISTS `mileagerecord`;
CREATE TABLE `mileagerecord` (
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
-- Triggers `mileagerecord`
--
DROP TRIGGER IF EXISTS `calculate_changes`;
DELIMITER $$
CREATE TRIGGER `calculate_changes` BEFORE INSERT ON `mileagerecord` FOR EACH ROW BEGIN
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
-- Table structure for table `resettrip`
--

DROP TABLE IF EXISTS `resettrip`;
CREATE TABLE `resettrip` (
  `id` int(11) NOT NULL,
  `date_time` datetime DEFAULT NULL,
  `mileage_km` decimal(7,1) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `fueling`
--
ALTER TABLE `fueling`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mileagerecord`
--
ALTER TABLE `mileagerecord`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resettrip`
--
ALTER TABLE `resettrip`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fueling`
--
ALTER TABLE `fueling`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `mileagerecord`
--
ALTER TABLE `mileagerecord`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `resettrip`
--
ALTER TABLE `resettrip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
