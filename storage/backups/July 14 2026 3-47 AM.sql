-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: don_felipe_hotel
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activitylogs`
--

DROP TABLE IF EXISTS `activitylogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activitylogs` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `activitylogs_user_id_index` (`user_id`),
  KEY `idx_activitylogs_timestamp` (`timestamp`),
  CONSTRAINT `activitylogs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activitylogs`
--

LOCK TABLES `activitylogs` WRITE;
/*!40000 ALTER TABLE `activitylogs` DISABLE KEYS */;
INSERT INTO `activitylogs` VALUES (1,8,'LOGIN','User logged in successfully.','2026-07-12 05:44:13'),(2,8,'ADD_CHARGE','Automatically posted 1 nights of room charges totaling ₱200.00 on Folio #REG-2026001 (Booking #1).','2026-07-12 05:44:37'),(4,7,'ADD_CHARGE','Automatically posted 4 nights of room charges totaling ₱400.00 on Folio #REG-20267367 (Booking #3).','2026-07-12 05:48:59'),(5,8,'ADD_CHARGE','Automatically posted 4 nights of room charges totaling ₱400.00 on Folio #REG-20261382 (Booking #2).','2026-07-12 05:54:05'),(6,7,'LOGIN','User logged in successfully.','2026-07-12 05:55:09'),(7,7,'EDIT_PRODUCT','Updated coffeeshop product: Coffee Beans. Changes: type changed from \'Non-stockable\' to \'Non-stockable\', stock_quantity changed from \'0\' to \'0\', low_stock_threshold changed from \'\' to \'\'.','2026-07-12 05:55:24'),(8,7,'POS_SALE','POS cash sale of ₱660.00 recorded via Order POS-20260712-0001.','2026-07-12 05:55:59'),(9,7,'POS_SALE','POS order POS-20260712-0001 closed (cash) for idol for tacrking the id, total ₱660.00.','2026-07-12 05:55:59'),(10,8,'ADD_CHARGE','Automatically posted 1 nights of room charges totaling ₱150.00 on Folio #REG-2026002 (Booking #4).','2026-07-12 05:59:17'),(11,8,'ADD_CHARGE','Automatically posted 1 nights of room charges totaling ₱200.00 on Folio #REG-20260712-0001 (Booking #5).','2026-07-12 06:00:35'),(12,7,'ADD_CHARGE','Automatically posted 2 nights of room charges totaling ₱100.00 on Folio #REG-20260712-9999 (Booking #6).','2026-07-12 06:04:42'),(13,7,'ADD_CHARGE','Automatically posted 1 nights of room charges totaling ₱250.00 on Folio #REG-20260712-9999 (Booking #7).','2026-07-12 06:04:42'),(14,7,'ADD_CHARGE','Automatically posted 2 nights of room charges totaling ₱100.00 on Folio #REG-20260712-9999 (Booking #8).','2026-07-12 06:05:17'),(15,8,'ADD_CHARGE','Automatically posted 1 nights of room charges totaling ₱60.00 on Folio #REG-20260712-9999 (Booking #9).','2026-07-12 06:06:50'),(16,8,'ROOM_TRANSFER','Room Transfer (Multi-Day): Transferred Transfer Test from Room 105 to Room 119. New booking #9 created for remainder of stay.','2026-07-12 06:06:50'),(17,7,'ADD_CHARGE','Automatically posted 2 nights of room charges totaling ₱120.00 on Folio #REG-20260712-9999 (Booking #8).','2026-07-12 16:00:00'),(18,7,'ADD_CHARGE','Automatically posted 2 nights of room charges totaling ₱100.00 on Folio #REG-20260712-9999 (Booking #10).','2026-07-12 06:14:12'),(19,7,'ADD_CHARGE','Automatically posted 1 nights of room charges totaling ₱50.00 on Folio #REG-SIM-001 (Booking #11).','2026-07-12 06:14:36'),(20,7,'ADD_CHARGE','Automatically posted 1 nights of room charges totaling ₱50.00 on Folio #REG-SIM-001 (Booking #11).','2026-07-12 16:00:00'),(21,7,'ADD_CHARGE','Automatically posted 1 nights of room charges totaling ₱250.00 on Folio #REG-SIM-001 (Booking #12).','2026-07-12 16:00:00'),(22,8,'LOGOUT','User logged out.','2026-07-12 06:19:52'),(23,10,'LOGIN','User logged in successfully.','2026-07-12 06:19:59'),(24,10,'RESTOCK_PRODUCT','Restocked product \"Cookies\": quantity adjusted by 14 units (Stock: 5 -> 19).','2026-07-12 06:20:08'),(25,10,'RESTOCK_PRODUCT','Restocked product \"Fresh Milk\": quantity adjusted by 10 units (Stock: 8 -> 18).','2026-07-12 06:20:14'),(26,10,'EDIT_PRODUCT','Updated coffeeshop product: Americano. Changes: image_path changed from \'pos/products/9cd83b47-70f6-4329-9008-3b731d3f4e0f_1783837227.webp\' to \'pos/products/9cd83b47-70f6-4329-9008-3b731d3f4e0f_1783837227.webp\'.','2026-07-12 06:20:28'),(27,10,'ADD_PRODUCT','Created new coffeeshop product: image pradak (₱450.00)','2026-07-12 06:24:48'),(28,10,'LOGOUT','User logged out.','2026-07-12 06:29:26'),(29,10,'LOGIN','User logged in successfully.','2026-07-12 06:29:30'),(30,10,'POS_SALE','POS cash sale of ₱480.00 recorded via Order POS-20260712-0002.','2026-07-12 06:30:24'),(31,10,'POS_SALE','POS order POS-20260712-0002 closed (cash) for testing lodipapas, total ₱480.00.','2026-07-12 06:30:24'),(32,10,'POS_SALE','POS cash sale of ₱455.00 recorded via Order POS-20260712-0003.','2026-07-12 06:30:43'),(33,10,'POS_SALE','POS order POS-20260712-0003 closed (cash) for sukarap hereee, total ₱455.00.','2026-07-12 06:30:43'),(34,7,'LOGIN','User logged in successfully.','2026-07-13 19:04:38'),(35,7,'DATABASE_RESTORE','Database restored from uploaded file: July 14 2026 3-05 AM.sql','2026-07-13 19:25:52'),(36,7,'LOGIN','User logged in successfully.','2026-07-13 19:25:57'),(37,7,'ADD_CHARGE','Automatically posted 2 nights of room charges totaling ₱400.00 on Folio #REG-2026001 (Booking #1).','2026-07-13 19:26:17'),(38,7,'ADD_CHARGE','Automatically posted 2 nights of room charges totaling ₱300.00 on Folio #REG-2026002 (Booking #4).','2026-07-13 19:26:17'),(39,7,'ADD_CHARGE','Automatically posted 2 nights of room charges totaling ₱400.00 on Folio #REG-20260712-0001 (Booking #5).','2026-07-13 19:26:17'),(40,7,'ADD_CHARGE','Automatically posted 2 nights of room charges totaling ₱100.00 on Folio #REG-20260712-9999 (Booking #10).','2026-07-13 19:26:17'),(41,7,'ADD_CHARGE','Automatically posted 1 nights of room charges totaling ₱250.00 on Folio #REG-SIM-001 (Booking #12).','2026-07-13 19:26:17'),(42,8,'LOGIN','User logged in successfully.','2026-07-13 19:30:45'),(43,8,'STAY_EXTENDED','Extended stay for Transfer Test to 2026-07-15 (Booking #10).','2026-07-13 19:31:25'),(44,8,'ADD_CHARGE','Automatically posted 1 nights of room charges totaling ₱110.00 on Folio #REG-2026002 (Booking #13).','2026-07-13 19:32:10'),(45,8,'ROOM_TRANSFER','Room Transfer (Multi-Day): Transferred Marvin Abbott from Room 146 to Room 121. New booking #13 created for remainder of stay.','2026-07-13 19:32:10'),(46,8,'ADD_CHARGE','Automatically posted 1 nights of room charges totaling ₱100.00 on Folio #REG-20260712-0001 (Booking #14).','2026-07-13 19:33:09'),(47,8,'ROOM_TRANSFER','Room Transfer (Multi-Day): Transferred new folio number from Room 106 to Room 103. New booking #14 created for remainder of stay.','2026-07-13 19:33:09'),(48,7,'DATABASE_BACKUP','Database backup created and saved: July 14 2026 3-43 AM.sql','2026-07-13 19:43:17'),(49,7,'DATABASE_BACKUP','Database backup created and saved: July 14 2026 3-46 AM.sql','2026-07-13 19:46:03'),(50,7,'DATABASE_BACKUP','Database backup created and saved: July 14 2026 3-46 AM.sql','2026-07-13 19:46:29');
/*!40000 ALTER TABLE `activitylogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookings` (
  `booking_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `folio_id` int(10) unsigned NOT NULL,
  `room_id` int(10) unsigned NOT NULL,
  `arrival_date` date NOT NULL,
  `arrival_time` time DEFAULT NULL,
  `departure_date` date DEFAULT NULL,
  `departure_time` time DEFAULT NULL,
  `actual_check_in` datetime DEFAULT NULL,
  `actual_check_out` datetime DEFAULT NULL,
  `status` enum('RESERVED','CHECKED_IN','CHECKED_OUT','CANCELLED') NOT NULL DEFAULT 'RESERVED',
  `checked_in_by` int(10) unsigned DEFAULT NULL COMMENT 'User ID of the employee who performed the check-in',
  PRIMARY KEY (`booking_id`),
  KEY `bookings_folio_id_index` (`folio_id`),
  KEY `bookings_room_id_index` (`room_id`),
  KEY `bookings_checked_in_by_foreign` (`checked_in_by`),
  KEY `idx_bookings_arrival_departure` (`arrival_date`,`departure_date`),
  CONSTRAINT `bookings_checked_in_by_foreign` FOREIGN KEY (`checked_in_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_folio_id_foreign` FOREIGN KEY (`folio_id`) REFERENCES `folios` (`folio_id`),
  CONSTRAINT `bookings_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (1,4,1,'2026-07-12','12:00:00','2026-07-14','12:00:00','2026-07-12 13:44:37',NULL,'CHECKED_IN',NULL),(4,7,46,'2026-07-12','12:00:00','2026-07-14','03:32:00','2026-07-12 13:59:17','2026-07-14 03:32:10','CHECKED_OUT',NULL),(5,8,6,'2026-07-12','12:00:00','2026-07-14','03:33:00','2026-07-12 14:00:35','2026-07-14 03:33:09','CHECKED_OUT',NULL),(10,12,5,'2026-07-11','14:00:00','2026-07-15','12:00:00','2026-07-11 00:00:00',NULL,'CHECKED_IN',NULL),(11,13,8,'2026-07-12','14:00:00','2026-07-13','00:00:00','2026-07-12 14:14:36','2026-07-13 00:00:00','CHECKED_OUT',NULL),(12,13,10,'2026-07-13','00:00:00','2026-07-14','12:00:00','2026-07-13 00:00:00',NULL,'CHECKED_IN',NULL),(13,7,21,'2026-07-14','03:32:00',NULL,NULL,'2026-07-14 03:32:10',NULL,'CHECKED_IN',NULL),(14,8,3,'2026-07-14','03:33:00',NULL,NULL,'2026-07-14 03:33:09',NULL,'CHECKED_IN',NULL);
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chargecodes`
--

DROP TABLE IF EXISTS `chargecodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chargecodes` (
  `charge_code` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL,
  `category` enum('HOTEL','RESTAURANT','TAX_SERVICE','PAYMENT') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`charge_code`)
) ENGINE=InnoDB AUTO_INCREMENT=405 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chargecodes`
--

LOCK TABLES `chargecodes` WRITE;
/*!40000 ALTER TABLE `chargecodes` DISABLE KEYS */;
INSERT INTO `chargecodes` VALUES (100,'ROOM CHARGE','HOTEL',1),(101,'GOVERNMENT TAX','TAX_SERVICE',1),(102,'SERVICE CHARGE PAYABLE','TAX_SERVICE',1),(103,'EXTRA PAX','HOTEL',1),(104,'LAUNDRY SERVICE AND PRES','HOTEL',1),(105,'PRESSING','HOTEL',1),(106,'INCOMING FAX','HOTEL',1),(107,'OUTGOING FAX','HOTEL',1),(108,'FUNCTION ROOM','HOTEL',1),(109,'LONG DISTANCE-IDD','HOTEL',1),(110,'LONG DISTANCE-NDD','HOTEL',1),(114,'TRANSFER BALANCE','HOTEL',1),(115,'OTHER CHARGES','HOTEL',1),(200,'FOOD & BEVERAGE','RESTAURANT',1),(201,'COMPLIMENTARY','HOTEL',1),(401,'MASTERCARD','PAYMENT',1),(402,'VISA','PAYMENT',1),(403,'CASH','PAYMENT',1),(404,'ACCOUNT CHARGE','PAYMENT',1);
/*!40000 ALTER TABLE `chargecodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credit_account_ledgers`
--

DROP TABLE IF EXISTS `credit_account_ledgers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `credit_account_ledgers` (
  `ledger_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `type` enum('charge','payment') NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(10) unsigned DEFAULT NULL,
  `processed_by` int(10) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ledger_id`),
  KEY `credit_account_ledgers_account_id_index` (`account_id`),
  KEY `credit_account_ledgers_type_index` (`type`),
  KEY `credit_account_ledgers_processed_by_index` (`processed_by`),
  CONSTRAINT `credit_account_ledgers_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `credit_accounts` (`account_id`) ON DELETE CASCADE,
  CONSTRAINT `credit_account_ledgers_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_account_ledgers`
--

LOCK TABLES `credit_account_ledgers` WRITE;
/*!40000 ALTER TABLE `credit_account_ledgers` DISABLE KEYS */;
/*!40000 ALTER TABLE `credit_account_ledgers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credit_accounts`
--

DROP TABLE IF EXISTS `credit_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `credit_accounts` (
  `account_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_name` varchar(150) NOT NULL,
  `contact_name` varchar(150) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `credit_limit` decimal(12,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_accounts`
--

LOCK TABLES `credit_accounts` WRITE;
/*!40000 ALTER TABLE `credit_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `credit_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expenses` (
  `expense_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `expense_date` date NOT NULL,
  `department` enum('Front Office','Housekeeping','Maintenance','Purchasing','Food & Beverage') NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `status` enum('PENDING','APPROVED','REJECTED') NOT NULL DEFAULT 'APPROVED',
  `amount` decimal(10,2) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `funding_source` enum('FRONT DESK','CAFETERIA') NOT NULL DEFAULT 'FRONT DESK',
  `requested_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`expense_id`),
  KEY `expenses_user_id_index` (`user_id`),
  KEY `idx_expenses_expense_date` (`expense_date`),
  KEY `expenses_expense_date_index` (`expense_date`),
  KEY `expenses_department_index` (`department`),
  CONSTRAINT `expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `folios`
--

DROP TABLE IF EXISTS `folios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `folios` (
  `folio_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `folio_number` varchar(20) NOT NULL,
  `registration_number` varchar(20) DEFAULT NULL,
  `account_number` varchar(20) DEFAULT NULL,
  `guest_id` int(10) unsigned NOT NULL,
  `credit_account_id` int(10) unsigned DEFAULT NULL,
  `market_segment` varchar(50) NOT NULL DEFAULT 'NONE',
  `billing_arrangements` text DEFAULT NULL,
  `special_arrangements` text DEFAULT NULL,
  `num_pax` int(11) NOT NULL DEFAULT 1,
  `has_joiner` tinyint(1) NOT NULL DEFAULT 0,
  `num_free_breakfasts` int(11) NOT NULL DEFAULT 0,
  `breakfast_code` varchar(20) DEFAULT NULL,
  `symbol` varchar(10) NOT NULL DEFAULT 'CBO',
  `folio_type` varchar(20) NOT NULL DEFAULT 'GUEST',
  `status` enum('OPEN','CLOSED') NOT NULL DEFAULT 'OPEN',
  `payment_method` varchar(30) NOT NULL DEFAULT 'Cash',
  `net_rate` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`folio_id`),
  UNIQUE KEY `folios_folio_number_unique` (`folio_number`),
  UNIQUE KEY `folios_registration_number_unique` (`registration_number`),
  KEY `folios_guest_id_index` (`guest_id`),
  KEY `folios_credit_account_id_foreign` (`credit_account_id`),
  KEY `idx_folios_status` (`status`),
  CONSTRAINT `folios_credit_account_id_foreign` FOREIGN KEY (`credit_account_id`) REFERENCES `credit_accounts` (`account_id`) ON DELETE SET NULL,
  CONSTRAINT `folios_guest_id_foreign` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `folios`
--

LOCK TABLES `folios` WRITE;
/*!40000 ALTER TABLE `folios` DISABLE KEYS */;
INSERT INTO `folios` VALUES (3,'POS-WALKIN',NULL,NULL,163,NULL,'NONE',NULL,NULL,1,0,0,NULL,'POS','SYSTEM','OPEN','Cash',NULL),(4,'REG-2026001',NULL,NULL,26,NULL,'Walk-in',NULL,NULL,1,0,0,NULL,'CBO','GUEST','OPEN','Cash',200.00),(7,'REG-2026002',NULL,NULL,68,NULL,'Walk-in',NULL,NULL,1,0,0,NULL,'CBO','GUEST','OPEN','Cash',110.00),(8,'REG-20260712-0001',NULL,NULL,166,NULL,'Walk-in',NULL,NULL,1,0,0,NULL,'CBO','GUEST','OPEN','Cash',100.00),(12,'REG-20260712-9999',NULL,NULL,170,NULL,'Walk-in',NULL,NULL,1,0,0,NULL,'CBO','GUEST','OPEN','Cash',50.00),(13,'REG-SIM-001',NULL,NULL,171,NULL,'Walk-in',NULL,NULL,1,0,0,NULL,'CBO','GUEST','OPEN','Cash',250.00);
/*!40000 ALTER TABLE `folios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guests`
--

DROP TABLE IF EXISTS `guests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guests` (
  `guest_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `address_line1` varchar(100) DEFAULT NULL,
  `address_line2` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `guest_type` varchar(20) NOT NULL DEFAULT 'GUEST',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`guest_id`),
  KEY `idx_guests_search_name` (`last_name`,`first_name`)
) ENGINE=InnoDB AUTO_INCREMENT=172 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guests`
--

LOCK TABLES `guests` WRITE;
/*!40000 ALTER TABLE `guests` DISABLE KEYS */;
INSERT INTO `guests` VALUES (3,'Doe','John','123 Main St','Apt 4','+1234567890','GUEST','2026-07-12 05:41:37'),(4,'Smith','Jane','456 Oak Ave',NULL,'+1987654321','GUEST','2026-07-12 05:41:37'),(5,'Johnson','Robert','789 Pine Rd','Suite 100','+1555123456','GUEST','2026-07-12 05:41:37'),(6,'Williams','Emily','321 Elm St',NULL,'+1666789012','GUEST','2026-07-12 05:41:37'),(7,'Brown','Michael','654 Maple Dr','Unit B','+1777456789','GUEST','2026-07-12 05:41:37'),(8,'Davis','Sarah','987 Cedar Ln',NULL,'+1888012345','GUEST','2026-07-12 05:41:37'),(9,'Miller','David','147 Birch St','Floor 2','+1999345678','GUEST','2026-07-12 05:41:37'),(10,'Wilson','Lisa','258 Spruce Ave',NULL,'+1444678901','GUEST','2026-07-12 05:41:37'),(11,'Moore','James','369 Willow St','Apt 1','+1333901234','GUEST','2026-07-12 05:41:37'),(12,'Taylor','Jennifer','741 Ash Rd',NULL,'+1222234567','GUEST','2026-07-12 05:41:37'),(13,'Feest','Jovanny','53647 Lyric Run Apt. 085',NULL,'484-363-2954','GUEST','2026-07-12 05:41:37'),(14,'Durgan','Rubye','586 Kasey Loop',NULL,'218.335.6437','GUEST','2026-07-12 05:41:37'),(15,'Feil','Maritza','1633 Sawayn Plains','Suite 110','1-520-265-0726','GUEST','2026-07-12 05:41:37'),(16,'Weimann','Lonie','671 Prosacco Walks Apt. 862',NULL,'(701) 353-3710','GUEST','2026-07-12 05:41:37'),(17,'Walter','Frederick','714 Oswald Lodge Suite 583',NULL,'442.440.7314','GUEST','2026-07-12 05:41:37'),(18,'Morar','Lucie','9000 Funk Pine','Suite 889','862.699.9997','GUEST','2026-07-12 05:41:37'),(19,'Becker','Demetrius','4853 Lela Keys Suite 731',NULL,'+1-352-552-5667','GUEST','2026-07-12 05:41:37'),(20,'Fay','Bernadette','99385 Elza Dale Apt. 214',NULL,'1-913-556-0249','GUEST','2026-07-12 05:41:37'),(21,'Mayert','Darrick','2051 Hirthe Radial Suite 366',NULL,'+1.651.440.7815','GUEST','2026-07-12 05:41:37'),(22,'Douglas','Dexter','379 Percival Ways',NULL,'+13464506504','GUEST','2026-07-12 05:41:37'),(23,'Reichel','Elenor','9433 Maida Forks',NULL,'424.987.9041','GUEST','2026-07-12 05:41:37'),(24,'Streich','Eusebio','997 Denesik Court Suite 010',NULL,'(469) 722-6084','GUEST','2026-07-12 05:41:37'),(25,'Leffler','Pietro','30030 Tina Gardens',NULL,'+18163103076','GUEST','2026-07-12 05:41:37'),(26,'Farrell','Adriel','958 Eldon Meadow','Apt. 830','(757) 563-0492','GUEST','2026-07-12 05:41:37'),(27,'Lebsack','Joyce','8622 Von Skyway Suite 434','Apt. 287','1-628-433-7192','GUEST','2026-07-12 05:41:37'),(28,'Hyatt','Retta','516 Bode Passage Apt. 803',NULL,'+1.754.293.1028','GUEST','2026-07-12 05:41:37'),(29,'Kemmer','Zelma','43576 Alfonso Locks Apt. 876',NULL,'+1-615-785-4282','GUEST','2026-07-12 05:41:37'),(30,'Legros','Mallory','811 O\'Hara Inlet','Apt. 903','+1 (904) 653-6781','GUEST','2026-07-12 05:41:37'),(31,'Hegmann','Kara','36031 Berenice Locks Apt. 871',NULL,'+1 (281) 324-8765','GUEST','2026-07-12 05:41:37'),(32,'Kuhn','Alexys','62045 Cassie Prairie',NULL,'+18026082997','GUEST','2026-07-12 05:41:37'),(33,'Rice','Odell','7146 Johnson Prairie',NULL,'(325) 741-2087','GUEST','2026-07-12 05:41:37'),(34,'Johnston','Nedra','7197 McClure Mall Apt. 015','Suite 833','1-628-707-6685','GUEST','2026-07-12 05:41:37'),(35,'Blanda','Elmer','4033 Cruickshank Circle Suite 233','Suite 293','339.889.2943','GUEST','2026-07-12 05:41:37'),(36,'Tillman','Myrtis','31513 Mosciski Green',NULL,'1-513-325-3980','GUEST','2026-07-12 05:41:37'),(37,'Windler','Alfonzo','61920 Kirlin Bypass',NULL,'+1.551.805.9247','GUEST','2026-07-12 05:41:37'),(38,'Cassin','Anissa','59737 Amber Creek Apt. 892','Suite 592','+1-872-561-9161','GUEST','2026-07-12 05:41:37'),(39,'Feil','Jadyn','39037 Kozey Land Suite 807',NULL,'1-562-983-8175','GUEST','2026-07-12 05:41:37'),(40,'Paucek','Cassandre','9577 Yasmin Inlet Apt. 111',NULL,'(669) 841-4833','GUEST','2026-07-12 05:41:37'),(41,'Goyette','Vivien','3276 Luettgen Neck Apt. 143',NULL,'845.278.6944','GUEST','2026-07-12 05:41:37'),(42,'Wolff','Guiseppe','2741 Ryan Vista',NULL,'+1-361-698-3785','GUEST','2026-07-12 05:41:37'),(43,'Sawayn','Keira','200 Williamson Glen',NULL,'+18578959942','GUEST','2026-07-12 05:41:37'),(44,'Medhurst','Judge','1757 Zakary Square',NULL,'(930) 753-1669','GUEST','2026-07-12 05:41:37'),(45,'Rice','Ramona','5353 Alberta Plains Apt. 618',NULL,'+1-660-741-5510','GUEST','2026-07-12 05:41:37'),(46,'Weissnat','Lora','85938 Gislason Inlet Suite 494','Apt. 409','1-630-316-7698','GUEST','2026-07-12 05:41:37'),(47,'Stamm','Alejandra','41783 Kiana Estate Suite 245','Suite 924','386.579.4703','GUEST','2026-07-12 05:41:37'),(48,'Connelly','Conor','8558 Moshe Shoals',NULL,'+1-858-906-5480','GUEST','2026-07-12 05:41:37'),(49,'Macejkovic','Gina','6831 Romaguera Cape Apt. 861',NULL,'+1-212-318-8498','GUEST','2026-07-12 05:41:37'),(50,'Bashirian','Blaze','74168 Keebler Canyon',NULL,'+1.860.865.7350','GUEST','2026-07-12 05:41:37'),(51,'Hagenes','Christelle','791 Douglas Mountains',NULL,'1-551-773-0960','GUEST','2026-07-12 05:41:37'),(52,'Konopelski','Daisha','947 Miguel Cliff',NULL,'+1-847-594-1884','GUEST','2026-07-12 05:41:37'),(53,'Murazik','Ellen','943 Leola Forge',NULL,'1-434-370-5698','GUEST','2026-07-12 05:41:37'),(54,'McClure','Maynard','254 Collier Streets','Apt. 722','270-691-0425','GUEST','2026-07-12 05:41:37'),(55,'Cruickshank','Dessie','41415 Howe Rapids Suite 455',NULL,'332.629.5256','GUEST','2026-07-12 05:41:37'),(56,'Ledner','Lynn','58731 Hirthe Bypass Suite 338','Apt. 453','1-802-878-2087','GUEST','2026-07-12 05:41:37'),(57,'Russel','Ricardo','56405 Hilpert Orchard Suite 236',NULL,'1-424-810-4806','GUEST','2026-07-12 05:41:37'),(58,'Goodwin','Minnie','35530 Reyes Knoll Apt. 091','Apt. 309','(530) 814-8808','GUEST','2026-07-12 05:41:37'),(59,'McCullough','Kareem','732 Gilbert Junctions Apt. 320','Apt. 499','757-345-7325','GUEST','2026-07-12 05:41:37'),(60,'Rutherford','Gisselle','9920 Friesen Rue Suite 187',NULL,'857-317-1953','GUEST','2026-07-12 05:41:37'),(61,'Champlin','Burley','728 Chloe Harbors Apt. 099',NULL,'281.527.8793','GUEST','2026-07-12 05:41:37'),(62,'Dickinson','Terence','40324 Gisselle Parkways Apt. 354',NULL,'1-215-416-2454','GUEST','2026-07-12 05:41:37'),(63,'Johnston','Zackary','9500 Adela Path','Apt. 175','+1-845-964-1342','GUEST','2026-07-12 05:41:37'),(64,'Schamberger','Anne','9221 Rosalyn Streets','Suite 994','+1 (770) 444-9229','GUEST','2026-07-12 05:41:37'),(65,'Veum','Joanne','7633 Toby Mission','Suite 392','1-562-380-9195','GUEST','2026-07-12 05:41:37'),(66,'Metz','Alec','83714 Lockman Branch','Apt. 126','+1-814-577-7691','GUEST','2026-07-12 05:41:37'),(67,'Barton','Bridgette','2198 Smith Plains Apt. 330','Apt. 630','720.360.6130','GUEST','2026-07-12 05:41:37'),(68,'Abbott','Marvin','56068 Kaela Prairie Suite 778','Apt. 168','321-783-4428','GUEST','2026-07-12 05:41:37'),(69,'Schmitt','Ryder','9993 Goodwin Heights Suite 068',NULL,'938-912-1822','GUEST','2026-07-12 05:41:37'),(70,'Homenick','Harley','3186 Nienow Vista Suite 863',NULL,'+1 (959) 809-6583','GUEST','2026-07-12 05:41:37'),(71,'Bode','Brendon','5963 Kohler Pass Suite 195',NULL,'651-473-2791','GUEST','2026-07-12 05:41:37'),(72,'Wolf','Brennon','9979 Royal Forks Suite 986',NULL,'(308) 516-3612','GUEST','2026-07-12 05:41:37'),(73,'Luettgen','Oceane','497 Caesar Roads Apt. 811',NULL,'(225) 673-1349','GUEST','2026-07-12 05:41:37'),(74,'Rutherford','Nannie','8896 Gerald Fort Apt. 153',NULL,'(850) 774-3909','GUEST','2026-07-12 05:41:37'),(75,'Swift','Elliott','5089 Lilian Place Apt. 576','Apt. 842','240.782.3128','GUEST','2026-07-12 05:41:37'),(76,'Gleichner','Bennie','80967 Rodriguez Crossing',NULL,'386.562.8421','GUEST','2026-07-12 05:41:37'),(77,'Collins','Ettie','75985 Adaline Spring Apt. 794',NULL,'1-463-984-1189','GUEST','2026-07-12 05:41:37'),(78,'Harber','Denis','22139 Zieme Inlet Apt. 747',NULL,'1-484-972-7632','GUEST','2026-07-12 05:41:37'),(79,'Willms','Zachariah','7587 Kassulke Views',NULL,'1-352-717-5425','GUEST','2026-07-12 05:41:37'),(80,'Halvorson','Dax','6557 Roberts Ranch Apt. 730',NULL,'(980) 797-2733','GUEST','2026-07-12 05:41:37'),(81,'Glover','Sid','95339 Valerie Hills Apt. 426',NULL,'(985) 398-5294','GUEST','2026-07-12 05:41:37'),(82,'Nienow','Nils','9258 Hoppe Junctions',NULL,'1-878-917-1927','GUEST','2026-07-12 05:41:37'),(83,'Sauer','Deja','50460 Romaine Wall',NULL,'+1-386-208-5228','GUEST','2026-07-12 05:41:37'),(84,'Pfannerstill','Carter','721 Dan Turnpike',NULL,'1-606-380-2127','GUEST','2026-07-12 05:41:37'),(85,'Herzog','Oliver','62704 Connelly Mission',NULL,'765-688-0047','GUEST','2026-07-12 05:41:37'),(86,'Runolfsson','Corine','7595 Marks Dam',NULL,'+1.848.923.3166','GUEST','2026-07-12 05:41:37'),(87,'Welch','Wilber','704 O\'Hara Meadow Suite 768',NULL,'+12483391011','GUEST','2026-07-12 05:41:37'),(88,'Davis','Jennifer','278 Stark Groves','Apt. 079','650.943.3410','GUEST','2026-07-12 05:41:37'),(89,'Koss','Freeda','16019 Ratke Unions Apt. 336','Suite 328','1-573-766-8462','GUEST','2026-07-12 05:41:37'),(90,'Glover','Erwin','563 Rylan Island Apt. 354',NULL,'+1-307-235-2997','GUEST','2026-07-12 05:41:37'),(91,'Cassin','Maddison','260 Bruen Station Suite 110',NULL,'+1-347-293-7486','GUEST','2026-07-12 05:41:37'),(92,'Lueilwitz','Kaylie','792 Hill Green',NULL,'1-225-741-5914','GUEST','2026-07-12 05:41:37'),(93,'Bergstrom','Austen','716 Frieda Estate',NULL,'(618) 719-2022','GUEST','2026-07-12 05:41:37'),(94,'Funk','Priscilla','4247 Minnie Branch Suite 046',NULL,'(347) 471-9918','GUEST','2026-07-12 05:41:37'),(95,'Klocko','Marge','49322 Kian Road Suite 926','Suite 144','351.481.8765','GUEST','2026-07-12 05:41:37'),(96,'King','Forrest','94926 Sim Shores Suite 887',NULL,'+1-847-902-7939','GUEST','2026-07-12 05:41:37'),(97,'Sanford','Kristin','7030 Wintheiser Springs','Suite 288','309.444.1515','GUEST','2026-07-12 05:41:37'),(98,'Walter','Casimir','602 Cormier Parks Suite 715','Apt. 856','+1 (681) 810-3362','GUEST','2026-07-12 05:41:37'),(99,'Hane','Jerad','695 Fredy Knolls',NULL,'678-958-3760','GUEST','2026-07-12 05:41:37'),(100,'Huels','Emmalee','29382 Kuvalis Mountains Apt. 252',NULL,'443.306.9309','GUEST','2026-07-12 05:41:37'),(101,'Marquardt','Lucie','8225 Bianka Ports','Apt. 156','(360) 814-0416','GUEST','2026-07-12 05:41:37'),(102,'Jacobi','Lloyd','9688 Pamela Manors Apt. 333',NULL,'+1-872-413-7723','GUEST','2026-07-12 05:41:37'),(103,'O\'Reilly','Marquis','74299 Brown Isle',NULL,'+1 (559) 933-9967','GUEST','2026-07-12 05:41:37'),(104,'Stamm','Emmet','50341 Zoey Terrace',NULL,'+1-838-942-5745','GUEST','2026-07-12 05:41:37'),(105,'Reichel','Carson','75779 Madelyn Key',NULL,'1-864-647-1275','GUEST','2026-07-12 05:41:37'),(106,'Rosenbaum','Adelle','7319 Mann Islands Suite 421','Suite 699','(323) 436-8253','GUEST','2026-07-12 05:41:37'),(107,'Mitchell','Athena','644 Misty Grove',NULL,'415.681.3941','GUEST','2026-07-12 05:41:37'),(108,'Blanda','Emerald','857 Effertz Ramp','Apt. 100','+1.458.860.9082','GUEST','2026-07-12 05:41:37'),(109,'Hickle','Harvey','160 Gislason Crossroad Suite 919','Apt. 088','+1-423-270-5387','GUEST','2026-07-12 05:41:37'),(110,'Hills','Gwen','3612 Friesen Squares Apt. 413','Suite 951','310.217.8113','GUEST','2026-07-12 05:41:37'),(111,'Fahey','Garnet','62659 Bode Key',NULL,'(657) 786-1996','GUEST','2026-07-12 05:41:37'),(112,'Leannon','Trevor','89680 Schmeler Mountain Suite 700',NULL,'+1-317-485-8475','GUEST','2026-07-12 05:41:37'),(113,'Koss','Raymond','43325 Britney Skyway Suite 897',NULL,'606.816.4345','GUEST','2026-07-12 05:41:37'),(114,'Corwin','Abraham','58357 Pierce Extension Apt. 916',NULL,'(830) 589-4442','GUEST','2026-07-12 05:41:37'),(115,'Bailey','Janiya','10057 Duncan Plains Apt. 691',NULL,'1-541-904-1211','GUEST','2026-07-12 05:41:37'),(116,'Harris','Pasquale','71511 Fannie Shoal',NULL,'+1.385.919.1575','GUEST','2026-07-12 05:41:37'),(117,'Braun','Benedict','7985 Smith Inlet Suite 543',NULL,'+1-248-335-9635','GUEST','2026-07-12 05:41:37'),(118,'Will','Jamar','4243 Glover Plains',NULL,'+1 (323) 834-6543','GUEST','2026-07-12 05:41:37'),(119,'Oberbrunner','Felix','4563 Matt Ridges',NULL,'+1.757.218.4583','GUEST','2026-07-12 05:41:37'),(120,'Weissnat','Lesley','88475 Hessel Estate Suite 757',NULL,'+19563937169','GUEST','2026-07-12 05:41:37'),(121,'Kautzer','Roosevelt','10854 Nedra Ways',NULL,'1-917-912-5914','GUEST','2026-07-12 05:41:37'),(122,'Hills','Kenton','242 Huels Isle Apt. 985',NULL,'+1-470-649-8907','GUEST','2026-07-12 05:41:37'),(123,'Klein','Jacynthe','25904 Lorenza Freeway Suite 400','Suite 936','(586) 433-9906','GUEST','2026-07-12 05:41:37'),(124,'Ortiz','Fleta','6192 Cornell Pine',NULL,'669-538-0899','GUEST','2026-07-12 05:41:37'),(125,'Hintz','Willy','57542 Johnson Circle Apt. 823','Apt. 690','1-865-234-3534','GUEST','2026-07-12 05:41:37'),(126,'Simonis','Uriel','73683 Damaris Streets','Apt. 425','(334) 367-8916','GUEST','2026-07-12 05:41:37'),(127,'Dare','Miguel','806 Kuhn Streets','Apt. 892','+16816654281','GUEST','2026-07-12 05:41:37'),(128,'O\'Conner','Gretchen','435 Hammes Curve Suite 160',NULL,'+1-563-339-8432','GUEST','2026-07-12 05:41:37'),(129,'Bernhard','Hazle','630 Arvilla Points','Suite 072','+1-786-824-0047','GUEST','2026-07-12 05:41:37'),(130,'Rodriguez','Frank','8935 Hirthe Trace',NULL,'+1.270.568.7094','GUEST','2026-07-12 05:41:37'),(131,'Kutch','Rose','294 Horacio Canyon','Suite 248','+15867244817','GUEST','2026-07-12 05:41:37'),(132,'Skiles','Elbert','718 Milton Inlet','Suite 842','513.981.3230','GUEST','2026-07-12 05:41:37'),(133,'Price','Ulises','44184 Jerrod Freeway Suite 828','Apt. 397','734-640-9531','GUEST','2026-07-12 05:41:37'),(134,'Stiedemann','Garret','605 Gunner Garden Apt. 788',NULL,'+1.941.845.2890','GUEST','2026-07-12 05:41:37'),(135,'Lakin','Kolby','37808 Lulu Crossroad Apt. 835','Apt. 653','(463) 665-1628','GUEST','2026-07-12 05:41:37'),(136,'Hintz','Devyn','5465 Luettgen Stravenue',NULL,'813-693-4016','GUEST','2026-07-12 05:41:37'),(137,'Parisian','Philip','6270 Willms Club','Suite 359','+1 (701) 627-0008','GUEST','2026-07-12 05:41:37'),(138,'Leffler','Darrell','4451 Kunze Squares',NULL,'484.820.4989','GUEST','2026-07-12 05:41:37'),(139,'Runolfsson','Cecile','3536 Kaia Light',NULL,'+1-920-782-6700','GUEST','2026-07-12 05:41:37'),(140,'Fisher','Florence','743 Josiane Cape Apt. 950',NULL,'954-398-2024','GUEST','2026-07-12 05:41:37'),(141,'Koepp','Roel','81041 Kris Mill','Apt. 231','1-727-348-8374','GUEST','2026-07-12 05:41:37'),(142,'Bogan','Jackie','49278 Walsh Rapids',NULL,'1-304-496-5935','GUEST','2026-07-12 05:41:37'),(143,'Brekke','Evelyn','228 Presley Circle Apt. 356',NULL,'+15396372007','GUEST','2026-07-12 05:41:37'),(144,'Renner','Dayne','6576 Dasia Crossroad','Apt. 105','1-432-880-0364','GUEST','2026-07-12 05:41:37'),(145,'Kuhic','Hailey','29420 Jenkins Vista',NULL,'+1-337-787-2326','GUEST','2026-07-12 05:41:37'),(146,'Ledner','Warren','7079 Bernie Light',NULL,'+1 (559) 226-7249','GUEST','2026-07-12 05:41:37'),(147,'Bayer','Jasper','5975 Eric River','Apt. 854','(364) 909-3237','GUEST','2026-07-12 05:41:37'),(148,'Littel','Anne','785 Kessler Center','Apt. 898','785.565.7874','GUEST','2026-07-12 05:41:37'),(149,'Stamm','Reginald','3161 Nitzsche Skyway',NULL,'(253) 509-7541','GUEST','2026-07-12 05:41:37'),(150,'Corkery','Danielle','275 Casimer Views Apt. 088',NULL,'+1-458-378-4578','GUEST','2026-07-12 05:41:37'),(151,'McKenzie','Zula','7603 Deven Mill','Suite 261','1-234-896-9602','GUEST','2026-07-12 05:41:37'),(152,'Senger','Deshaun','616 Ophelia Key Apt. 299',NULL,'+1-364-285-5233','GUEST','2026-07-12 05:41:37'),(153,'Hilpert','Theodore','78426 Kuhn Meadows',NULL,'(434) 484-9945','GUEST','2026-07-12 05:41:37'),(154,'Bergnaum','Esther','39983 Nolan Path',NULL,'725.431.9862','GUEST','2026-07-12 05:41:37'),(155,'Adams','Adam','5093 Rex Harbor Suite 216',NULL,'351.286.2431','GUEST','2026-07-12 05:41:37'),(156,'Marvin','Zoe','47295 Nader Field Apt. 409','Apt. 635','+1-223-812-5429','GUEST','2026-07-12 05:41:37'),(157,'Schoen','Jamie','47491 Dickinson Stream',NULL,'(803) 393-1275','GUEST','2026-07-12 05:41:37'),(158,'Luettgen','Solon','5849 Cathrine Ferry',NULL,'+1 (765) 899-2958','GUEST','2026-07-12 05:41:37'),(159,'Ullrich','Sabrina','8853 Nakia Turnpike Suite 800',NULL,'+16288942954','GUEST','2026-07-12 05:41:37'),(160,'Yundt','Robbie','24841 Weimann Neck Apt. 357',NULL,'+1.640.680.4670','GUEST','2026-07-12 05:41:37'),(161,'Schaefer','Enos','53680 Hill Meadow',NULL,'+18646590483','GUEST','2026-07-12 05:41:37'),(162,'Schuppe','Pattie','75377 Stamm Viaduct Apt. 024',NULL,'903.924.5332','GUEST','2026-07-12 05:41:37'),(163,'WALK-IN','POS',NULL,NULL,'N/A','SYSTEM','2026-07-12 05:41:37'),(166,'number','new folio','barngay street city letsgo',NULL,'917310902','GUEST','2026-07-12 06:00:35'),(170,'Test','Transfer',NULL,NULL,NULL,'GUEST','2026-07-12 06:14:12'),(171,'Guest','Simulation',NULL,NULL,NULL,'GUEST','2026-07-12 06:14:36');
/*!40000 ALTER TABLE `guests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_06_17_000001_create_guests_table',1),(5,'2026_06_17_000002_create_chargecodes_table',1),(6,'2026_06_17_000003_create_rooms_table',1),(7,'2026_06_17_000004_create_folios_table',1),(8,'2026_06_17_000005_create_activitylogs_table',1),(9,'2026_06_17_000006_create_shifts_table',1),(10,'2026_06_17_000007_create_bookings_table',1),(11,'2026_06_17_000008_create_transactions_table',1),(12,'2026_06_17_032931_create_sessions_table',1),(13,'2026_06_18_000001_create_permissions_table',1),(14,'2026_06_18_000002_create_rolepermissions_table',1),(15,'2026_06_18_135845_add_is_active_to_users_table',1),(16,'2026_06_18_144749_add_is_active_to_roles_table',1),(17,'2026_06_18_153955_add_is_active_and_module_to_permissions_table',1),(18,'2026_06_19_041518_add_cleaning_status_to_rooms_table',1),(19,'2026_06_19_071926_add_is_active_to_rooms_table',1),(20,'2026_06_19_074955_add_is_active_to_chargecodes_table',1),(21,'2026_06_19_091219_create_shift_schedules_table',1),(22,'2026_06_19_091244_add_schedule_id_to_shifts_table',1),(23,'2026_06_20_085638_create_expenses_table',1),(24,'2026_06_21_062245_add_payment_method_to_folios_table',1),(25,'2026_06_21_063834_add_net_rate_to_folios_table',1),(26,'2026_06_23_133155_add_processed_by_to_bookings_table',1),(27,'2026_06_24_145200_add_is_system_admin_to_roles_table',1),(28,'2026_06_24_145208_create_userpermissions_table',1),(29,'2026_06_25_100000_create_pos_tables',1),(30,'2026_06_26_150836_add_explicit_types_to_guests_and_folios_tables',1),(31,'2026_06_26_153338_create_pos_approval_requests_table',1),(32,'2026_06_26_153350_alter_payment_method_on_pos_tables',1),(33,'2026_06_30_195340_create_credit_accounts_table',1),(34,'2026_06_30_195344_create_credit_account_ledgers_table',1),(35,'2026_06_30_195349_add_credit_account_id_and_discounts_to_pos_and_folios',1),(36,'2026_06_30_222329_add_system_setting_to_activitylogs_enum',1),(37,'2026_06_30_222941_alter_activitylogs_action_type_to_string',1),(38,'2026_07_02_203530_alter_tab_type_on_pos_tabs_table',1),(39,'2026_07_02_212602_alter_payment_method_on_transactions_and_folios_tables',1),(40,'2026_07_02_212846_insert_account_charge_to_chargecodes',1),(41,'2026_07_03_000001_fix_pos_tabs_tab_type_column',1),(42,'2026_07_03_224149_update_payment_method_enum_in_transactions_table',1),(43,'2026_07_10_222008_add_scalability_indexes_to_tables',1),(44,'2026_07_10_222756_add_is_stockable_to_pos_products_table',1),(45,'2026_07_11_014556_add_department_to_transactions_table',1),(46,'2026_07_11_024351_add_funding_source_and_requested_by_to_expenses_table',1),(47,'2026_07_11_030234_add_accounting_dashboard_indexes',1),(48,'2026_07_11_134703_make_departure_date_nullable_on_bookings_table',1),(49,'2026_07_12_025054_alter_shift_schedules_for_recurring_shifts',1),(50,'2026_07_12_131508_add_indexes_to_expenses_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `permission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_key` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL DEFAULT 'System',
  `description` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `permissions_permission_key_unique` (`permission_key`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (5,'manage-users','System','Manage system users and roles',1),(6,'manage-shifts','System','Manage shift schedules and view sales reports',1),(7,'manage-reservations','Front Desk','Manage reservations and guest registrations',1),(8,'process-checkout','Front Desk','Process guest checkout and record payments',1),(9,'view-guest-list','Front Desk','View guest list details',1),(10,'view-guest-folio','Front Desk','View guest folio details',1),(11,'manage-guest-folio','Front Desk','Open, close, reopen folios and post charges',1),(12,'view-shift-sales','Front Desk','View individual or shared shift sales',1),(13,'view-accounting-dashboard','Accounting','Access financial overview charts and statistics',1),(14,'manage-accounting-billing','Accounting','Access billing details and view billing lists',1),(15,'manage-accounting-payments','Accounting','Register payments and view payment history',1),(16,'manage-accounting-receivables','Accounting','View receivables ledger and accounts',1),(17,'manage-accounting-expenses','Accounting','Track, create, and approve expenses',1),(18,'view-accounting-reports','Accounting','Generate system financial reports',1),(19,'view-accounting-audit','Accounting','Access log changes and trace operations',1),(20,'manage-inventory','Inventory','Manage coffeeshop inventory and sales orders',1);
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_approval_requests`
--

DROP TABLE IF EXISTS `pos_approval_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_approval_requests` (
  `request_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned DEFAULT NULL,
  `tab_id` int(10) unsigned DEFAULT NULL,
  `request_type` enum('refund','cancel_tab','cancel_order') NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `requested_by` int(10) unsigned NOT NULL,
  `resolved_by` int(10) unsigned DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`request_id`),
  KEY `pos_approval_requests_order_id_index` (`order_id`),
  KEY `pos_approval_requests_tab_id_index` (`tab_id`),
  KEY `pos_approval_requests_request_type_index` (`request_type`),
  KEY `pos_approval_requests_status_index` (`status`),
  KEY `pos_approval_requests_requested_by_index` (`requested_by`),
  KEY `pos_approval_requests_resolved_by_index` (`resolved_by`),
  CONSTRAINT `pos_approval_requests_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `pos_orders` (`order_id`) ON DELETE SET NULL,
  CONSTRAINT `pos_approval_requests_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `pos_approval_requests_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `pos_approval_requests_tab_id_foreign` FOREIGN KEY (`tab_id`) REFERENCES `pos_tabs` (`tab_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_approval_requests`
--

LOCK TABLES `pos_approval_requests` WRITE;
/*!40000 ALTER TABLE `pos_approval_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `pos_approval_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_categories`
--

DROP TABLE IF EXISTS `pos_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_categories` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `pos_categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_categories`
--

LOCK TABLES `pos_categories` WRITE;
/*!40000 ALTER TABLE `pos_categories` DISABLE KEYS */;
INSERT INTO `pos_categories` VALUES (1,'Coffee',1,1,'2026-07-12 05:41:37','2026-07-12 05:41:37'),(2,'Tea',2,1,'2026-07-12 05:41:37','2026-07-12 05:41:37'),(3,'Beer',3,1,'2026-07-12 05:41:37','2026-07-12 05:41:37'),(4,'Food',4,1,'2026-07-12 05:41:37','2026-07-12 05:41:37'),(5,'Dessert',5,1,'2026-07-12 05:41:37','2026-07-12 05:41:37');
/*!40000 ALTER TABLE `pos_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_inventory_logs`
--

DROP TABLE IF EXISTS `pos_inventory_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_inventory_logs` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `change_qty` int(11) NOT NULL,
  `reason` enum('sale','restock','adjustment','refund','cancel') NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `pos_inventory_logs_product_id_index` (`product_id`),
  KEY `pos_inventory_logs_user_id_index` (`user_id`),
  CONSTRAINT `pos_inventory_logs_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `pos_products` (`product_id`),
  CONSTRAINT `pos_inventory_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_inventory_logs`
--

LOCK TABLES `pos_inventory_logs` WRITE;
/*!40000 ALTER TABLE `pos_inventory_logs` DISABLE KEYS */;
INSERT INTO `pos_inventory_logs` VALUES (1,6,-1,'sale','pos_order',1,7,'Added to tab idol for tacrking the id','2026-07-12 05:55:46'),(2,6,-1,'sale','pos_order',1,7,'Added to tab idol for tacrking the id','2026-07-12 05:55:46'),(3,6,-1,'sale','pos_order',1,7,'Added to tab idol for tacrking the id','2026-07-12 05:55:47'),(4,8,14,'restock','manual',NULL,10,NULL,'2026-07-12 06:20:08'),(5,10,10,'restock','manual',NULL,10,NULL,'2026-07-12 06:20:14'),(6,6,-1,'sale','pos_order',2,10,'Added to tab testing lodipapas','2026-07-12 06:30:15'),(7,3,-1,'sale','pos_order',2,10,'Added to tab testing lodipapas','2026-07-12 06:30:16'),(8,1,-1,'sale','pos_order',2,10,'Added to tab testing lodipapas','2026-07-12 06:30:17'),(9,6,-1,'sale','pos_order',3,10,'Added to tab sukarap hereee','2026-07-12 06:30:33'),(10,3,-1,'sale','pos_order',3,10,'Added to tab sukarap hereee','2026-07-12 06:30:33'),(11,5,-1,'sale','pos_order',3,10,'Added to tab sukarap hereee','2026-07-12 06:30:34');
/*!40000 ALTER TABLE `pos_inventory_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_order_items`
--

DROP TABLE IF EXISTS `pos_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_order_items` (
  `order_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `product_description` varchar(255) DEFAULT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `line_total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `pos_order_items_order_id_index` (`order_id`),
  KEY `pos_order_items_product_id_index` (`product_id`),
  CONSTRAINT `pos_order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `pos_orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `pos_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `pos_products` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_order_items`
--

LOCK TABLES `pos_order_items` WRITE;
/*!40000 ALTER TABLE `pos_order_items` DISABLE KEYS */;
INSERT INTO `pos_order_items` VALUES (1,1,6,'Club Sandwich','Triple-decker club sandwich',3,220.00,660.00),(2,2,6,'Club Sandwich','Triple-decker club sandwich',1,220.00,220.00),(3,2,3,'Cappuccino','Espresso with foamed milk',1,140.00,140.00),(4,2,1,'Americano','Classic black coffee',1,120.00,120.00),(5,3,6,'Club Sandwich','Triple-decker club sandwich',1,220.00,220.00),(6,3,3,'Cappuccino','Espresso with foamed milk',1,140.00,140.00),(7,3,5,'Beer','San Miguel Pale Pilsen',1,95.00,95.00);
/*!40000 ALTER TABLE `pos_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_orders`
--

DROP TABLE IF EXISTS `pos_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_orders` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(30) NOT NULL,
  `tab_id` int(10) unsigned DEFAULT NULL,
  `folio_id` int(10) unsigned DEFAULT NULL,
  `credit_account_id` int(10) unsigned DEFAULT NULL,
  `transaction_id` int(10) unsigned DEFAULT NULL,
  `customer_name` varchar(150) NOT NULL,
  `room_number` varchar(20) DEFAULT NULL,
  `status` enum('open','active','closed','cancelled','refunded') NOT NULL DEFAULT 'closed',
  `discount_type` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_discount_percentage` tinyint(1) NOT NULL DEFAULT 0,
  `payment_method` varchar(30) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `user_id` int(10) unsigned NOT NULL,
  `shift_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `closed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `pos_orders_order_number_unique` (`order_number`),
  KEY `pos_orders_tab_id_index` (`tab_id`),
  KEY `pos_orders_folio_id_index` (`folio_id`),
  KEY `pos_orders_transaction_id_index` (`transaction_id`),
  KEY `pos_orders_status_index` (`status`),
  KEY `pos_orders_user_id_index` (`user_id`),
  KEY `pos_orders_shift_id_index` (`shift_id`),
  KEY `pos_orders_credit_account_id_foreign` (`credit_account_id`),
  CONSTRAINT `pos_orders_credit_account_id_foreign` FOREIGN KEY (`credit_account_id`) REFERENCES `credit_accounts` (`account_id`) ON DELETE SET NULL,
  CONSTRAINT `pos_orders_folio_id_foreign` FOREIGN KEY (`folio_id`) REFERENCES `folios` (`folio_id`),
  CONSTRAINT `pos_orders_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`shift_id`),
  CONSTRAINT `pos_orders_tab_id_foreign` FOREIGN KEY (`tab_id`) REFERENCES `pos_tabs` (`tab_id`) ON DELETE SET NULL,
  CONSTRAINT `pos_orders_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`),
  CONSTRAINT `pos_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_orders`
--

LOCK TABLES `pos_orders` WRITE;
/*!40000 ALTER TABLE `pos_orders` DISABLE KEYS */;
INSERT INTO `pos_orders` VALUES (1,'POS-20260712-0001',1,NULL,NULL,NULL,'idol for tacrking the id',NULL,'closed',NULL,0.00,0,'cash',660.00,660.00,7,5,'2026-07-12 05:55:59','2026-07-12 05:55:59'),(2,'POS-20260712-0002',2,NULL,NULL,NULL,'testing lodipapas',NULL,'closed',NULL,0.00,0,'cash',480.00,480.00,10,9,'2026-07-12 06:30:24','2026-07-12 06:30:24'),(3,'POS-20260712-0003',3,NULL,NULL,NULL,'sukarap hereee',NULL,'closed',NULL,0.00,0,'cash',455.00,455.00,10,9,'2026-07-12 06:30:43','2026-07-12 06:30:43');
/*!40000 ALTER TABLE `pos_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_products`
--

DROP TABLE IF EXISTS `pos_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_products` (
  `product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_stockable` tinyint(1) NOT NULL DEFAULT 1,
  `image_path` varchar(255) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `low_stock_threshold` int(10) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  KEY `pos_products_name_is_active_index` (`name`,`is_active`),
  KEY `pos_products_category_id_index` (`category_id`),
  CONSTRAINT `pos_products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `pos_categories` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_products`
--

LOCK TABLES `pos_products` WRITE;
/*!40000 ALTER TABLE `pos_products` DISABLE KEYS */;
INSERT INTO `pos_products` VALUES (1,1,'Americano','Classic black coffee',120.00,1,'pos/products/9cd83b47-70f6-4329-9008-3b731d3f4e0f_1783837227.webp',99,20,1,'2026-07-12 05:41:37','2026-07-12 06:30:17'),(2,1,'Latte','Espresso with steamed milk',150.00,1,NULL,80,15,1,'2026-07-12 05:41:37','2026-07-12 05:41:37'),(3,1,'Cappuccino','Espresso with foamed milk',140.00,1,NULL,73,15,1,'2026-07-12 05:41:37','2026-07-12 06:30:33'),(4,2,'Green Tea','Hot green tea',90.00,1,NULL,60,10,1,'2026-07-12 05:41:37','2026-07-12 05:41:37'),(5,3,'Beer','San Miguel Pale Pilsen',95.00,1,NULL,47,12,1,'2026-07-12 05:41:37','2026-07-12 06:30:34'),(6,4,'Club Sandwich','Triple-decker club sandwich',220.00,1,NULL,25,8,1,'2026-07-12 05:41:37','2026-07-12 06:30:33'),(7,4,'French Fries','Crispy golden fries',110.00,1,NULL,40,10,1,'2026-07-12 05:41:37','2026-07-12 05:41:37'),(8,5,'Cookies','Fresh baked cookies',90.00,1,NULL,19,10,1,'2026-07-12 05:41:37','2026-07-12 06:20:08'),(9,1,'Coffee Beans','Arabica beans 1kg pack',450.00,0,NULL,0,NULL,1,'2026-07-12 05:41:37','2026-07-12 05:55:24'),(10,1,'Fresh Milk','Fresh milk 1 liter',85.00,1,NULL,18,10,1,'2026-07-12 05:41:37','2026-07-12 06:20:14'),(11,4,'image pradak','kani ang pinakalami nga taw sa tibuok kalibutan',450.00,0,'pos/products/b4c593df-9a12-412b-bad3-364147c4006f_1783837488.webp',0,NULL,1,'2026-07-12 06:24:48','2026-07-12 06:24:48');
/*!40000 ALTER TABLE `pos_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_settings`
--

DROP TABLE IF EXISTS `pos_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_settings`
--

LOCK TABLES `pos_settings` WRITE;
/*!40000 ALTER TABLE `pos_settings` DISABLE KEYS */;
INSERT INTO `pos_settings` VALUES ('default_low_stock_threshold','10','2026-07-12 05:41:37'),('walk_in_folio_id','3','2026-07-12 05:41:37');
/*!40000 ALTER TABLE `pos_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_tab_items`
--

DROP TABLE IF EXISTS `pos_tab_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_tab_items` (
  `tab_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tab_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `line_total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`tab_item_id`),
  KEY `pos_tab_items_tab_id_index` (`tab_id`),
  KEY `pos_tab_items_product_id_index` (`product_id`),
  CONSTRAINT `pos_tab_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `pos_products` (`product_id`),
  CONSTRAINT `pos_tab_items_tab_id_foreign` FOREIGN KEY (`tab_id`) REFERENCES `pos_tabs` (`tab_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_tab_items`
--

LOCK TABLES `pos_tab_items` WRITE;
/*!40000 ALTER TABLE `pos_tab_items` DISABLE KEYS */;
INSERT INTO `pos_tab_items` VALUES (1,1,6,3,220.00,660.00),(2,2,6,1,220.00,220.00),(3,2,3,1,140.00,140.00),(4,2,1,1,120.00,120.00),(5,3,6,1,220.00,220.00),(6,3,3,1,140.00,140.00),(7,3,5,1,95.00,95.00);
/*!40000 ALTER TABLE `pos_tab_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_tabs`
--

DROP TABLE IF EXISTS `pos_tabs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_tabs` (
  `tab_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tab_name` varchar(150) NOT NULL,
  `tab_type` varchar(50) NOT NULL DEFAULT 'walk_in',
  `guest_id` int(10) unsigned DEFAULT NULL,
  `folio_id` int(10) unsigned DEFAULT NULL,
  `credit_account_id` int(10) unsigned DEFAULT NULL,
  `booking_id` int(10) unsigned DEFAULT NULL,
  `room_id` int(10) unsigned DEFAULT NULL,
  `status` enum('open','closed','cancelled') NOT NULL DEFAULT 'open',
  `discount_type` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_discount_percentage` tinyint(1) NOT NULL DEFAULT 0,
  `payment_method` varchar(30) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `opened_by` int(10) unsigned NOT NULL,
  `closed_by` int(10) unsigned DEFAULT NULL,
  `opened_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `closed_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`tab_id`),
  KEY `pos_tabs_closed_by_foreign` (`closed_by`),
  KEY `pos_tabs_guest_id_index` (`guest_id`),
  KEY `pos_tabs_folio_id_index` (`folio_id`),
  KEY `pos_tabs_booking_id_index` (`booking_id`),
  KEY `pos_tabs_room_id_index` (`room_id`),
  KEY `pos_tabs_status_index` (`status`),
  KEY `pos_tabs_opened_by_index` (`opened_by`),
  KEY `pos_tabs_credit_account_id_foreign` (`credit_account_id`),
  CONSTRAINT `pos_tabs_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`),
  CONSTRAINT `pos_tabs_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `pos_tabs_credit_account_id_foreign` FOREIGN KEY (`credit_account_id`) REFERENCES `credit_accounts` (`account_id`) ON DELETE SET NULL,
  CONSTRAINT `pos_tabs_folio_id_foreign` FOREIGN KEY (`folio_id`) REFERENCES `folios` (`folio_id`),
  CONSTRAINT `pos_tabs_guest_id_foreign` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`guest_id`),
  CONSTRAINT `pos_tabs_opened_by_foreign` FOREIGN KEY (`opened_by`) REFERENCES `users` (`user_id`),
  CONSTRAINT `pos_tabs_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_tabs`
--

LOCK TABLES `pos_tabs` WRITE;
/*!40000 ALTER TABLE `pos_tabs` DISABLE KEYS */;
INSERT INTO `pos_tabs` VALUES (1,'idol for tacrking the id','walk_in',NULL,NULL,NULL,NULL,NULL,'closed',NULL,0.00,0,'cash',660.00,660.00,7,7,'2026-07-12 05:55:44','2026-07-12 05:55:59',NULL),(2,'testing lodipapas','walk_in',NULL,NULL,NULL,NULL,NULL,'closed',NULL,0.00,0,'cash',480.00,480.00,10,10,'2026-07-12 06:30:14','2026-07-12 06:30:24',NULL),(3,'sukarap hereee','walk_in',NULL,NULL,NULL,NULL,NULL,'closed',NULL,0.00,0,'cash',455.00,455.00,10,10,'2026-07-12 06:30:32','2026-07-12 06:30:43',NULL);
/*!40000 ALTER TABLE `pos_tabs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rolepermissions`
--

DROP TABLE IF EXISTS `rolepermissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rolepermissions` (
  `role_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `rolepermissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `rolepermissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE,
  CONSTRAINT `rolepermissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rolepermissions`
--

LOCK TABLES `rolepermissions` WRITE;
/*!40000 ALTER TABLE `rolepermissions` DISABLE KEYS */;
INSERT INTO `rolepermissions` VALUES (5,5),(5,6),(5,7),(5,8),(5,9),(5,10),(5,11),(5,12),(5,13),(5,14),(5,15),(5,16),(5,17),(5,18),(5,19),(5,20),(6,7),(6,8),(6,9),(6,10),(6,11),(6,12),(7,13),(7,14),(7,15),(7,16),(7,17),(7,18),(7,19),(8,20);
/*!40000 ALTER TABLE `rolepermissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_system_admin` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `roles_role_name_unique` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (5,'ADMIN','Full system administrator with all access privileges',1,1),(6,'FRONT_DESK','Front desk receptionist handling bookings, check-ins, and folios',1,0),(7,'ACCOUNTING','Finance staff auditing invoices, payments, and sales reports',1,0),(8,'CAFETERIA','Cafeteria cashier managing orders, POS, and inventory',1,0);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rooms` (
  `room_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_number` varchar(10) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `base_rate` decimal(10,2) NOT NULL,
  `status` enum('AVAILABLE','OCCUPIED','RESERVED','CLEANING','MAINTENANCE') NOT NULL DEFAULT 'AVAILABLE',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`room_id`),
  UNIQUE KEY `rooms_room_number_unique` (`room_number`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (1,'101','Connecting Room',200.00,'OCCUPIED',1),(2,'102','Deluxe Room',100.00,'AVAILABLE',1),(3,'103','Deluxe Room',100.00,'OCCUPIED',1),(4,'104','Suite',150.00,'AVAILABLE',1),(5,'105','Single Room',50.00,'OCCUPIED',1),(6,'106','Connecting Room',200.00,'CLEANING',1),(7,'107','Connecting Room',200.00,'AVAILABLE',1),(8,'108','Single Room',50.00,'CLEANING',1),(9,'109','Deluxe Room',100.00,'AVAILABLE',1),(10,'110','President Suite',250.00,'OCCUPIED',1),(11,'111','Deluxe Room',100.00,'AVAILABLE',1),(12,'112','Studio Room',75.00,'AVAILABLE',1),(13,'113','Studio Room',75.00,'AVAILABLE',1),(14,'114','Connecting Room',200.00,'AVAILABLE',1),(15,'115','Deluxe Room',100.00,'AVAILABLE',1),(16,'116','Connecting Room',200.00,'AVAILABLE',1),(17,'117','Suite',150.00,'AVAILABLE',1),(18,'118','Deluxe Room',100.00,'AVAILABLE',1),(19,'119','Twin Room',60.00,'AVAILABLE',1),(20,'120','Suite',150.00,'AVAILABLE',1),(21,'121','Single Room',50.00,'OCCUPIED',1),(22,'122','Deluxe Room',100.00,'AVAILABLE',1),(23,'123','Deluxe Room',100.00,'AVAILABLE',1),(24,'124','Single Room',50.00,'AVAILABLE',1),(25,'125','Suite',150.00,'AVAILABLE',1),(26,'126','Deluxe Room',100.00,'AVAILABLE',1),(27,'127','Twin Room',60.00,'AVAILABLE',1),(28,'128','Single Room',50.00,'AVAILABLE',1),(29,'129','Connecting Room',200.00,'AVAILABLE',1),(30,'130','Twin Room',60.00,'AVAILABLE',1),(31,'131','President Suite',250.00,'AVAILABLE',1),(32,'132','Twin Room',60.00,'AVAILABLE',1),(33,'133','Studio Room',75.00,'AVAILABLE',1),(34,'134','Deluxe Room',100.00,'AVAILABLE',1),(35,'135','President Suite',250.00,'AVAILABLE',1),(36,'136','Connecting Room',200.00,'AVAILABLE',1),(37,'137','Studio Room',75.00,'AVAILABLE',1),(38,'138','Single Room',50.00,'AVAILABLE',1),(39,'139','President Suite',250.00,'AVAILABLE',1),(40,'140','Suite',150.00,'AVAILABLE',1),(41,'141','Suite',150.00,'AVAILABLE',1),(42,'142','Studio Room',75.00,'AVAILABLE',1),(43,'143','Single Room',50.00,'AVAILABLE',1),(44,'144','President Suite',250.00,'AVAILABLE',1),(45,'145','Studio Room',75.00,'AVAILABLE',1),(46,'146','Suite',150.00,'CLEANING',1),(47,'147','Connecting Room',200.00,'AVAILABLE',1),(48,'148','President Suite',250.00,'AVAILABLE',1),(49,'149','Deluxe Room',100.00,'AVAILABLE',1),(50,'150','Twin Room',60.00,'AVAILABLE',1),(51,'151','Connecting Room',200.00,'AVAILABLE',1),(52,'152','Twin Room',60.00,'AVAILABLE',1),(53,'153','Deluxe Room',100.00,'AVAILABLE',1),(54,'154','President Suite',250.00,'AVAILABLE',1),(55,'155','Twin Room',60.00,'AVAILABLE',1),(56,'156','Twin Room',60.00,'AVAILABLE',1),(57,'157','Connecting Room',200.00,'AVAILABLE',1),(58,'158','Twin Room',60.00,'AVAILABLE',1),(59,'159','Twin Room',60.00,'AVAILABLE',1),(60,'160','Deluxe Room',100.00,'AVAILABLE',1);
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('fBsD6Rx6GYo5U4xLp1m4uNMNoqb2K4VipKOk3FmZ',7,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZFVpNUxOSlJOSGFldExmNk1oYTh1QmNHRHFDNndzUzZBUVlLNkNOMCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wb3MtYXBwcm92YWxzIjtzOjU6InJvdXRlIjtzOjE5OiJhZG1pbi5wb3MtYXBwcm92YWxzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Nzt9',1783969513),('KtlTgSJEXV8wn4oXPR9JOYujYfrg28QhLjCXrHFN',8,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWno0M2d1dHpCZFpyUmhqWU00UG5RV25tQWhacGZSVHNnMUJlUFBkRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9mcm9udGRlc2svZ3Vlc3QtZm9saW8iO3M6NToicm91dGUiO3M6MjE6ImZyb250ZGVzay5ndWVzdC1mb2xpbyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjg7fQ==',1783971990),('yWYQgH1CFZSH19pgbqyLIAQRxcewWuv1E3LEhgsL',7,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoic2hobE84SU5aWkx5dEdlRTBDWnlDSVFpVk1QQ25RRzVqaVFia25hWSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQyOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vYmFja3VwLXJlc3RvcmUiO3M6NToicm91dGUiO3M6MjA6ImFkbWluLmJhY2t1cC1yZXN0b3JlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Nzt9',1783972038);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shift_schedules`
--

DROP TABLE IF EXISTS `shift_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shift_schedules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `shift_name` varchar(100) NOT NULL,
  `is_monday` tinyint(1) NOT NULL DEFAULT 0,
  `is_tuesday` tinyint(1) NOT NULL DEFAULT 0,
  `is_wednesday` tinyint(1) NOT NULL DEFAULT 0,
  `is_thursday` tinyint(1) NOT NULL DEFAULT 0,
  `is_friday` tinyint(1) NOT NULL DEFAULT 0,
  `is_saturday` tinyint(1) NOT NULL DEFAULT 0,
  `is_sunday` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `scheduled_start_time` time NOT NULL,
  `scheduled_end_time` time NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shift_schedules_user_id_index` (`user_id`),
  CONSTRAINT `shift_schedules_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shift_schedules`
--

LOCK TABLES `shift_schedules` WRITE;
/*!40000 ALTER TABLE `shift_schedules` DISABLE KEYS */;
/*!40000 ALTER TABLE `shift_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shifts`
--

DROP TABLE IF EXISTS `shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shifts` (
  `shift_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `schedule_id` int(10) unsigned DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  PRIMARY KEY (`shift_id`),
  KEY `shifts_user_id_index` (`user_id`),
  KEY `shifts_schedule_id_index` (`schedule_id`),
  CONSTRAINT `shifts_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `shift_schedules` (`id`) ON DELETE SET NULL,
  CONSTRAINT `shifts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shifts`
--

LOCK TABLES `shifts` WRITE;
/*!40000 ALTER TABLE `shifts` DISABLE KEYS */;
INSERT INTO `shifts` VALUES (4,8,NULL,'2026-07-12 13:44:13','2026-07-12 13:44:13'),(5,8,NULL,'2026-07-12 13:44:13','2026-07-12 14:19:52'),(6,10,NULL,'2026-07-12 14:19:59','2026-07-12 14:19:59'),(7,10,NULL,'2026-07-12 14:19:59','2026-07-12 14:29:26'),(8,10,NULL,'2026-07-12 14:29:30','2026-07-12 14:29:30'),(9,10,NULL,'2026-07-12 14:29:30',NULL),(10,8,NULL,'2026-07-14 03:30:45','2026-07-14 03:30:45'),(11,8,NULL,'2026-07-14 03:30:45',NULL);
/*!40000 ALTER TABLE `shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `folio_id` int(10) unsigned NOT NULL,
  `charge_code` int(10) unsigned NOT NULL,
  `shift_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `transaction_date` date NOT NULL,
  `charge_number` varchar(30) DEFAULT NULL,
  `payment_method` enum('CASH','CREDIT_CARD','CHECK','NONE','ACCOUNT_CHARGE') DEFAULT 'NONE',
  `reference_notes` varchar(255) DEFAULT NULL,
  `charge_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `credit_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `department` enum('FRONT_DESK','COFFEE_SHOP') NOT NULL DEFAULT 'FRONT_DESK',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`transaction_id`),
  KEY `transactions_folio_id_index` (`folio_id`),
  KEY `transactions_charge_code_index` (`charge_code`),
  KEY `transactions_shift_id_index` (`shift_id`),
  KEY `transactions_user_id_index` (`user_id`),
  KEY `idx_transactions_reporting_date` (`transaction_date`,`timestamp`),
  KEY `idx_transactions_payment_method` (`payment_method`),
  CONSTRAINT `transactions_charge_code_foreign` FOREIGN KEY (`charge_code`) REFERENCES `chargecodes` (`charge_code`),
  CONSTRAINT `transactions_folio_id_foreign` FOREIGN KEY (`folio_id`) REFERENCES `folios` (`folio_id`),
  CONSTRAINT `transactions_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`shift_id`),
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES (4,4,100,5,8,'2026-07-12','RM-1-1','NONE','Room charge for Night 1 (Date: 2026-07-12)',200.00,0.00,'FRONT_DESK','2026-07-12 05:44:37'),(17,3,200,5,7,'2026-07-12','POS-CASH-1','NONE','POS CASH Order POS-20260712-0001: Club Sandwich x3',660.00,0.00,'COFFEE_SHOP','2026-07-12 05:55:59'),(18,3,403,5,7,'2026-07-12','POS-CASH-1-PAY','CASH','CASH payment for POS-20260712-0001',0.00,660.00,'COFFEE_SHOP','2026-07-12 05:55:59'),(19,7,100,5,8,'2026-07-12','RM-4-1','NONE','Room charge for Night 1 (Date: 2026-07-12)',150.00,0.00,'FRONT_DESK','2026-07-12 05:59:17'),(20,8,100,5,8,'2026-07-12','RM-5-1','NONE','Room charge for Night 1 (Date: 2026-07-12)',200.00,0.00,'FRONT_DESK','2026-07-12 06:00:35'),(29,12,100,5,7,'2026-07-11','RM-10-1','NONE','Room charge for Night 1 (Date: 2026-07-11)',50.00,0.00,'FRONT_DESK','2026-07-12 06:14:12'),(30,12,100,5,7,'2026-07-12','RM-10-2','NONE','Room charge for Night 2 (Date: 2026-07-12)',50.00,0.00,'FRONT_DESK','2026-07-12 06:14:12'),(31,13,100,5,7,'2026-07-12','RM-11-1','NONE','Room charge for Night 1 (Date: 2026-07-12)',50.00,0.00,'FRONT_DESK','2026-07-12 06:14:36'),(33,13,100,5,7,'2026-07-13','RM-12-1','NONE','Room charge for Night 1 (Date: 2026-07-13)',250.00,0.00,'FRONT_DESK','2026-07-12 16:00:00'),(34,3,200,9,10,'2026-07-12','POS-CASH-2','NONE','POS CASH Order POS-20260712-0002: Club Sandwich x1, Cappuccino x1, Americano x1',480.00,0.00,'COFFEE_SHOP','2026-07-12 06:30:24'),(35,3,403,9,10,'2026-07-12','POS-CASH-2-PAY','CASH','CASH payment for POS-20260712-0002',0.00,480.00,'COFFEE_SHOP','2026-07-12 06:30:24'),(36,3,200,9,10,'2026-07-12','POS-CASH-3','NONE','POS CASH Order POS-20260712-0003: Club Sandwich x1, Cappuccino x1, Beer x1',455.00,0.00,'COFFEE_SHOP','2026-07-12 06:30:43'),(37,3,403,9,10,'2026-07-12','POS-CASH-3-PAY','CASH','CASH payment for POS-20260712-0003',0.00,455.00,'COFFEE_SHOP','2026-07-12 06:30:43'),(38,4,100,9,7,'2026-07-13','RM-1-2','NONE','Room charge for Night 2 (Date: 2026-07-13)',200.00,0.00,'FRONT_DESK','2026-07-13 19:26:17'),(39,4,100,9,7,'2026-07-14','RM-1-3','NONE','Room charge for Night 3 (Date: 2026-07-14)',200.00,0.00,'FRONT_DESK','2026-07-13 19:26:17'),(40,7,100,9,7,'2026-07-13','RM-4-2','NONE','Room charge for Night 2 (Date: 2026-07-13)',150.00,0.00,'FRONT_DESK','2026-07-13 19:26:17'),(42,8,100,9,7,'2026-07-13','RM-5-2','NONE','Room charge for Night 2 (Date: 2026-07-13)',200.00,0.00,'FRONT_DESK','2026-07-13 19:26:17'),(44,12,100,9,7,'2026-07-13','RM-10-3','NONE','Room charge for Night 3 (Date: 2026-07-13)',50.00,0.00,'FRONT_DESK','2026-07-13 19:26:17'),(45,12,100,9,7,'2026-07-14','RM-10-4','NONE','Room charge for Night 4 (Date: 2026-07-14)',50.00,0.00,'FRONT_DESK','2026-07-13 19:26:17'),(46,13,100,9,7,'2026-07-14','RM-12-2','NONE','Room charge for Night 2 (Date: 2026-07-14)',250.00,0.00,'FRONT_DESK','2026-07-13 19:26:17'),(47,7,100,11,8,'2026-07-14','RM-13-1','NONE','Room charge for Night 1 (Date: 2026-07-14)',110.00,0.00,'FRONT_DESK','2026-07-13 19:32:10'),(48,8,100,11,8,'2026-07-14','RM-14-1','NONE','Room charge for Night 1 (Date: 2026-07-14)',100.00,0.00,'FRONT_DESK','2026-07-13 19:33:09');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userpermissions`
--

DROP TABLE IF EXISTS `userpermissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userpermissions` (
  `user_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`permission_id`),
  KEY `userpermissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `userpermissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE,
  CONSTRAINT `userpermissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userpermissions`
--

LOCK TABLES `userpermissions` WRITE;
/*!40000 ALTER TABLE `userpermissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `userpermissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `users_username_unique` (`username`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (7,'admin','$2y$12$xgmLztW7SLL.VwM91y/qjOX1J/I.dicR8inqtsw6Whd8CLqAJv34q','SoftwareAdmin',5,1),(8,'frontdesk','$2y$12$FNSu1nv4m/l6QRa5BY5hf.KD/VjSIykRx.1iWWw05rPAgDp.qq6QK','Front Desk User',6,1),(9,'accounting','$2y$12$mGpaOKc3o72XOfd.pd2RGu717gobkS8oel7X9MLg0wK/rjV1njKD2','Cashier',7,1),(10,'cafeteria','$2y$12$WC1F25sZtZ2xgdyvSiy3KOk/csR5vk4dH2yo8/XudxCHPzTG7lYam','Cafeteria User',8,1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'don_felipe_hotel'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-14  3:47:23
