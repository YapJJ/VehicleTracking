-- MariaDB dump 10.19-11.8.6-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: vehicle_tracking
-- ------------------------------------------------------
-- Server version	11.8.6-MariaDB-5ubuntu0.1 from Ubuntu

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

-- Database: `vehicle_tracking`
--
CREATE DATABASE IF NOT EXISTS `vehicle_tracking` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `vehicle_tracking`;

-- --------------------------------------------------------

--
-- Table structure for table `Fueling`
--

DROP TABLE IF EXISTS `Fueling`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Fueling` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_time` datetime DEFAULT NULL,
  `amount_rm` decimal(5,2) DEFAULT NULL,
  `liter` decimal(5,3) DEFAULT NULL,
  `range_b4_km` int(11) DEFAULT NULL,
  `range_after_km` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Fueling`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `Fueling` WRITE;
/*!40000 ALTER TABLE `Fueling` DISABLE KEYS */;
INSERT INTO `Fueling` VALUES
(1,'2024-07-24 12:40:00',55.00,26.829,NULL,487,'Petronas Grand Saga 2'),
(2,'2024-08-22 03:10:00',50.00,25.125,125,467,'Petronas Seremban R&R (Southbound)');
/*!40000 ALTER TABLE `Fueling` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `MileageRecord`
--

DROP TABLE IF EXISTS `MileageRecord`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `MileageRecord` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `odo_km` int(11) DEFAULT NULL,
  `trip_km` decimal(6,1) DEFAULT NULL,
  `avg_km_l` decimal(3,1) DEFAULT NULL,
  `range_km` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `odo_change` int(11) DEFAULT NULL,
  `trip_change` decimal(6,1) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=333 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MileageRecord`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `MileageRecord` WRITE;
/*!40000 ALTER TABLE `MileageRecord` DISABLE KEYS */;
INSERT INTO `MileageRecord` VALUES
(1,NULL,0.0,13.6,487,'Petronas Grand Saga 2',NULL,NULL,'2024-07-24 12:40:00'),
(2,40700,23.6,13.6,486,'Petronas Seremban R&R (Southbound)',NULL,23.6,'2024-08-22 03:10:00');
/*!40000 ALTER TABLE `MileageRecord` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_uca1400_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`admin`@`localhost`*/ /*!50003 TRIGGER `calculate_changes` BEFORE INSERT ON `MileageRecord` FOR EACH ROW BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `ResetTrip`
--

DROP TABLE IF EXISTS `ResetTrip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ResetTrip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_time` datetime DEFAULT NULL,
  `mileage_km` decimal(7,1) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ResetTrip`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `ResetTrip` WRITE;
/*!40000 ALTER TABLE `ResetTrip` DISABLE KEYS */;
INSERT INTO `ResetTrip` VALUES
(1,'2024-07-24 12:40:00',1103.7,'Petronas Grand Saga 2'),
(2,'2024-08-22 03:10:00',376.2,'Petronas Seremban R&R (Southbound)');
/*!40000 ALTER TABLE `ResetTrip` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-08-23 14:45:26
