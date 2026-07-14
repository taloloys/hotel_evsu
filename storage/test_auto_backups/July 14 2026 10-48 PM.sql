-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: hotel_don_felipe
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
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activitylogs`
--

LOCK TABLES `activitylogs` WRITE;
/*!40000 ALTER TABLE `activitylogs` DISABLE KEYS */;
INSERT INTO `activitylogs` VALUES (1,4,'LOGIN','User logged in successfully.','2026-06-26 20:00:05'),(2,4,'ADD_CHARGE','POS cash sale of ₱220.00 recorded via Order POS-20260627-0001.','2026-06-26 20:00:25'),(3,4,'ADD_CHARGE','POS order POS-20260627-0001 closed (cash) for ade, total ₱220.00.','2026-06-26 20:00:25'),(4,1,'LOGIN','User logged in successfully.','2026-06-26 20:00:46'),(5,1,'LOGIN','User logged out.','2026-06-26 20:02:13'),(6,2,'LOGIN','User logged in successfully.','2026-06-26 20:02:20'),(7,2,'ADD_CHARGE','Automatically posted 1 nights of room charges totaling ₱200.00 on Folio #REG-2026001 (Booking #1).','2026-06-26 20:03:30'),(8,4,'ADD_CHARGE','POS room charge of ₱440.00 posted on Folio via Order POS-20260627-0002.','2026-06-26 20:03:56'),(9,4,'ADD_CHARGE','POS order POS-20260627-0002 closed (room_charge) for Room 107 - Kade Ortiz, total ₱440.00.','2026-06-26 20:03:56'),(10,4,'LOGIN','User logged in successfully.','2026-06-27 00:29:39'),(11,4,'LOGIN','User logged out.','2026-06-27 00:30:36'),(12,2,'LOGIN','User logged in successfully.','2026-06-27 00:30:41'),(13,2,'CHECK_IN','Opened Shift #2 for Front Desk User.','2026-06-27 00:31:08'),(14,2,'LOGIN','User logged out.','2026-06-27 00:42:19'),(15,4,'LOGIN','User logged in successfully.','2026-06-28 06:12:45'),(16,4,'LOGIN','User logged in successfully.','2026-06-28 09:34:28'),(17,4,'ADD_CHARGE','POS cash sale of ₱1,600.00 recorded via Order POS-20260628-0001.','2026-06-28 09:43:31'),(18,4,'ADD_CHARGE','POS order POS-20260628-0001 closed (cash) for klent, total ₱1,600.00.','2026-06-28 09:43:31'),(19,4,'ADD_CHARGE','POS cash sale of ₱220.00 recorded via Order POS-20260628-0002.','2026-06-28 09:44:20'),(20,4,'ADD_CHARGE','POS order POS-20260628-0002 closed (cash) for ade, total ₱220.00.','2026-06-28 09:44:20'),(21,4,'LOGIN','User logged out.','2026-06-28 09:52:13'),(22,2,'LOGIN','User logged in successfully.','2026-06-28 09:52:19'),(23,2,'LOGIN','User logged in successfully.','2026-06-28 10:06:49'),(24,2,'ADD_CHARGE','Automatically posted 1 nights of room charges totaling ₱10,000.00 on Folio #REG-2026002 (Booking #2).','2026-06-28 10:07:31'),(25,2,'ROOM_MODIFIED','Marked Folio #REG-2026002 as Paid (₱10,000.00) and closed.','2026-06-28 10:08:06'),(26,2,'ROOM_MODIFIED','Marked Folio #REG-2026001 as Paid (₱640.00) and closed.','2026-06-28 10:11:17'),(27,2,'LOGIN','User logged in successfully.','2026-06-30 02:24:58'),(28,2,'LOGIN','User logged out.','2026-06-30 02:26:32'),(29,4,'LOGIN','User logged in successfully.','2026-06-30 02:26:40'),(30,4,'LOGIN','User logged in successfully.','2026-07-01 09:57:34'),(31,4,'LOGIN','User logged in successfully.','2026-07-01 09:59:32'),(32,4,'ADD_PRODUCT','Created new coffeeshop product: qeq (₱2312.00)','2026-07-01 10:04:03'),(33,4,'ADD_PRODUCT','Created new coffeeshop product: 1221 (₱123.00)','2026-07-01 10:04:34'),(34,4,'LOGOUT','User logged out.','2026-07-01 10:12:07'),(35,2,'LOGIN','User logged in successfully.','2026-07-01 10:12:15'),(36,2,'LOGOUT','User logged out.','2026-07-01 10:16:42'),(37,3,'LOGIN','User logged in successfully.','2026-07-01 10:16:49'),(38,3,'LOGOUT','User logged out.','2026-07-01 10:17:06'),(39,1,'LOGIN','User logged in successfully.','2026-07-01 10:17:14'),(40,1,'LOGOUT','User logged out.','2026-07-01 10:18:42'),(41,4,'LOGIN','User logged in successfully.','2026-07-01 10:19:06'),(42,4,'LOGOUT','User logged out.','2026-07-01 11:31:46'),(43,2,'LOGIN','User logged in successfully.','2026-07-01 11:31:55'),(44,2,'LOGOUT','User logged out.','2026-07-01 11:41:07'),(45,2,'LOGIN','User logged in successfully.','2026-07-01 11:41:45'),(46,2,'LOGOUT','User logged out.','2026-07-01 11:42:17'),(47,1,'LOGIN','User logged in successfully.','2026-07-01 11:42:30'),(48,1,'LOGOUT','User logged out.','2026-07-01 11:42:38'),(49,4,'LOGIN','User logged in successfully.','2026-07-01 14:22:59'),(50,4,'LOGOUT','User logged out.','2026-07-01 14:23:13'),(51,2,'LOGIN','User logged in successfully.','2026-07-01 14:23:20'),(52,2,'LOGOUT','User logged out.','2026-07-01 14:25:27'),(53,4,'LOGIN','User logged in successfully.','2026-07-01 14:25:33'),(54,2,'LOGIN','User logged in successfully.','2026-07-03 14:07:25'),(55,2,'LOGIN','User logged in successfully.','2026-07-03 14:08:04'),(56,2,'FOLIO_REOPENED','Reopened Folio #REG-2026002.','2026-07-03 14:09:06'),(57,2,'LOGOUT','User logged out.','2026-07-03 14:09:26'),(58,4,'LOGIN','User logged in successfully.','2026-07-03 14:09:47'),(59,4,'POS_TAB_TRANSFER','Transferred Tab #5 billing target from walk_in to room','2026-07-03 14:10:51'),(60,4,'POS_TAB_TRANSFER','Transferred Tab #5 billing target from room to room','2026-07-03 14:11:30'),(61,4,'POS_TAB_TRANSFER','Transferred Tab #5 billing target from room to room','2026-07-03 14:11:48'),(62,4,'POS_SALE','POS room charge of ₱2,532.00 posted on Folio via Order POS-20260703-0001.','2026-07-03 14:12:34'),(63,4,'POS_SALE','POS order POS-20260703-0001 closed (room_charge) for ade, total ₱2,532.00.','2026-07-03 14:12:34'),(64,4,'LOGOUT','User logged out.','2026-07-03 14:12:51'),(65,2,'LOGIN','User logged in successfully.','2026-07-03 14:12:59'),(66,2,'LOGIN','User logged in successfully.','2026-07-03 14:13:07'),(67,2,'LOGOUT','User logged out.','2026-07-03 14:13:46'),(68,4,'LOGIN','User logged in successfully.','2026-07-03 14:13:54'),(69,4,'POS_TAB_TRANSFER','Transferred Tab #6 billing target from walk_in to room','2026-07-03 14:14:25'),(70,4,'POS_TAB_TRANSFER','Transferred Tab #6 billing target from room to room','2026-07-03 14:15:00'),(71,2,'LOGIN','User logged in successfully.','2026-07-03 14:15:30'),(72,2,'FOLIO_REOPENED','Reopened Folio #REG-2026001.','2026-07-03 14:16:13'),(73,4,'POS_SALE','POS room charge of ₱3,192.00 posted on Folio via Order POS-20260703-0002.','2026-07-03 14:18:34'),(74,4,'POS_SALE','POS order POS-20260703-0002 closed (room_charge) for ade, total ₱3,192.00.','2026-07-03 14:18:34'),(75,4,'POS_TAB_TRANSFER','Transferred Tab #7 billing target from walk_in to room','2026-07-03 14:21:23'),(76,4,'LOGIN','User logged in successfully.','2026-07-04 07:16:40'),(77,4,'LOGIN','User logged in successfully.','2026-07-04 07:16:58'),(78,4,'POS_TAB_TRANSFER','Transferred Tab #7 billing target from room to room','2026-07-04 07:17:33'),(79,4,'POS_TAB_TRANSFER','Transferred Tab #7 billing target from room to walk_in','2026-07-04 07:18:22'),(80,4,'POS_TAB_TRANSFER','Transferred Tab #7 billing target from walk_in to room','2026-07-04 07:19:38'),(81,4,'POS_TAB_TRANSFER','Transferred Tab #8 billing target from walk_in to room','2026-07-04 07:34:01'),(82,4,'POS_SALE','POS room charge of ₱220.00 posted on Folio via Order POS-20260704-0001.','2026-07-04 07:34:17'),(83,4,'POS_SALE','POS order POS-20260704-0001 closed (room_charge) for 855, total ₱220.00.','2026-07-04 07:34:17'),(84,4,'POS_SALE','POS room charge of ₱220.00 posted on Folio via Order POS-20260704-0002.','2026-07-04 07:34:33'),(85,4,'POS_SALE','POS order POS-20260704-0002 closed (room_charge) for ade, total ₱220.00.','2026-07-04 07:34:33'),(86,4,'POS_TAB_TRANSFER','Transferred Tab #9 billing target from walk_in to room','2026-07-04 07:36:37'),(87,4,'POS_SALE','POS room charge of ₱220.00 posted on Folio via Order POS-20260704-0003.','2026-07-04 07:36:40'),(88,4,'POS_SALE','POS order POS-20260704-0003 closed (room_charge) for ade, total ₱220.00.','2026-07-04 07:36:40'),(89,1,'LOGIN','User logged in successfully.','2026-07-04 07:39:23'),(90,1,'CREDIT_ACCOUNT_CREATED','Created new credit account: ade with limit of ₱10,000.00','2026-07-04 07:39:39'),(91,4,'POS_TAB_TRANSFER','Transferred Tab #10 billing target from walk_in to account','2026-07-04 07:40:16'),(92,4,'ACCOUNT_CHARGED','Charged ₱550.00 to Account (ade). Reference: POS Order POS-20260704-0004: French Fries x5','2026-07-04 07:40:21'),(93,4,'POS_SALE','POS order POS-20260704-0004 closed (account_charge) for adede2145, total ₱550.00.','2026-07-04 07:40:21'),(94,4,'LOGOUT','User logged out.','2026-07-04 07:40:41'),(95,2,'LOGIN','User logged in successfully.','2026-07-04 07:40:47'),(96,2,'ACCOUNT_CHARGED','Charged ₱2,752.00 to Account (ade). Reference: ade','2026-07-04 07:41:10'),(97,2,'FOLIO_PAID','Posted Account Charge (ade) of ₱2,752.00 to Folio #REG-2026002 as partial payment.','2026-07-04 07:41:10'),(98,2,'LOGIN','User logged in successfully.','2026-07-09 21:48:14'),(99,2,'ROOM_TRANSFER','Room Transfer: Transferred Kade Ortiz from Room 107 to Room 118.','2026-07-09 22:05:43'),(100,2,'LOGOUT','User logged out.','2026-07-09 22:06:19'),(101,4,'LOGIN','User logged in successfully.','2026-07-09 22:06:29'),(102,3,'LOGIN','User logged in successfully.','2026-07-10 11:46:45'),(103,3,'LOGIN','User logged in successfully.','2026-07-10 11:47:01'),(104,3,'LOGIN','User logged in successfully.','2026-07-11 05:35:18'),(105,3,'LOGOUT','User logged out.','2026-07-11 05:35:28'),(106,2,'LOGIN','User logged in successfully.','2026-07-11 05:35:36'),(107,2,'LOGOUT','User logged out.','2026-07-11 05:35:58'),(108,2,'LOGIN','User logged in successfully.','2026-07-11 05:40:40'),(109,2,'LOGIN','User logged in successfully.','2026-07-11 05:42:06'),(110,2,'CHECK_OUT','Checked out guest Kade Ortiz from Room 110 (Booking #2) via Folio.','2026-07-11 05:43:02'),(111,2,'ROOM_MODIFIED','Room 110 status updated to AVAILABLE (Housekeeping Cleaned).','2026-07-11 05:43:28'),(112,2,'ROOM_MODIFIED','Room 107 status updated to AVAILABLE (Housekeeping Cleaned).','2026-07-11 05:43:34'),(113,2,'FOLIO_PAID','Recorded payment of ₱3,632.00 to Folio #REG-2026001 as partial payment.','2026-07-11 05:44:03'),(114,2,'CHECK_OUT','Checked out guest Kade Ortiz from Room 118 (Booking #1) via Folio.','2026-07-11 05:44:07'),(115,2,'ROOM_MODIFIED','Room 118 status updated to AVAILABLE (Housekeeping Cleaned).','2026-07-11 05:44:21'),(116,2,'FOLIO_REOPENED','Reopened Folio #REG-2026001.','2026-07-11 06:16:56'),(117,2,'LOGIN','User logged in successfully.','2026-07-11 06:35:52'),(118,2,'LOGOUT','User logged out.','2026-07-11 07:00:47'),(119,4,'LOGIN','User logged in successfully.','2026-07-11 07:00:58'),(120,2,'LOGIN','User logged in successfully.','2026-07-11 07:19:55'),(121,2,'CLOSE_SHIFT','Closed Shift #2. Total sales cash out: ₱17,024.00.','2026-07-11 07:22:05'),(122,2,'LOGOUT','User logged out.','2026-07-11 07:51:55'),(123,2,'LOGIN','User logged in successfully.','2026-07-11 07:52:05'),(124,1,'LOGIN','User logged in successfully.','2026-07-12 11:06:40'),(125,1,'ADD_CHARGE','Automatically posted 2 nights of room charges totaling ₱10,000.00 on Folio #REG-2026003 (Booking #3).','2026-07-12 11:40:16'),(126,1,'LOGIN','User logged in successfully.','2026-07-14 14:05:26');
/*!40000 ALTER TABLE `activitylogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archived_activitylogs`
--

DROP TABLE IF EXISTS `archived_activitylogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `archived_activitylogs` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `action_type` enum('LOGIN','RESERVATION_CREATE','CHECK_IN','ADD_CHARGE','PRINT_FOLIO','CLOSE_SHIFT','ROOM_MODIFIED') NOT NULL,
  `description` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `archived_activitylogs_user_id_index` (`user_id`),
  CONSTRAINT `archived_activitylogs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archived_activitylogs`
--

LOCK TABLES `archived_activitylogs` WRITE;
/*!40000 ALTER TABLE `archived_activitylogs` DISABLE KEYS */;
/*!40000 ALTER TABLE `archived_activitylogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archived_expenses`
--

DROP TABLE IF EXISTS `archived_expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `archived_expenses` (
  `expense_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `expense_date` date NOT NULL,
  `department` varchar(100) NOT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `status` enum('PENDING','APPROVED','REJECTED') NOT NULL DEFAULT 'APPROVED',
  `amount` decimal(10,2) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `funding_source` varchar(100) DEFAULT NULL,
  `requested_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `archived_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`expense_id`),
  KEY `archived_expenses_user_id_index` (`user_id`),
  CONSTRAINT `archived_expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archived_expenses`
--

LOCK TABLES `archived_expenses` WRITE;
/*!40000 ALTER TABLE `archived_expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `archived_expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archived_pos_order_items`
--

DROP TABLE IF EXISTS `archived_pos_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `archived_pos_order_items` (
  `order_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `product_description` varchar(255) DEFAULT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `line_total` decimal(10,2) NOT NULL,
  `archived_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `archived_pos_order_items_order_id_index` (`order_id`),
  KEY `archived_pos_order_items_product_id_index` (`product_id`),
  CONSTRAINT `archived_pos_order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `pos_orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `archived_pos_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `pos_products` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archived_pos_order_items`
--

LOCK TABLES `archived_pos_order_items` WRITE;
/*!40000 ALTER TABLE `archived_pos_order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `archived_pos_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archived_pos_orders`
--

DROP TABLE IF EXISTS `archived_pos_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `archived_pos_orders` (
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
  `payment_method` enum('cash','room_charge','credit_account') DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `user_id` int(10) unsigned NOT NULL,
  `shift_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `closed_at` timestamp NULL DEFAULT NULL,
  `archived_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `archived_pos_orders_order_number_unique` (`order_number`),
  KEY `archived_pos_orders_tab_id_index` (`tab_id`),
  KEY `archived_pos_orders_folio_id_index` (`folio_id`),
  KEY `archived_pos_orders_credit_account_id_index` (`credit_account_id`),
  KEY `archived_pos_orders_transaction_id_index` (`transaction_id`),
  KEY `archived_pos_orders_status_index` (`status`),
  KEY `archived_pos_orders_user_id_index` (`user_id`),
  KEY `archived_pos_orders_shift_id_index` (`shift_id`),
  CONSTRAINT `archived_pos_orders_folio_id_foreign` FOREIGN KEY (`folio_id`) REFERENCES `folios` (`folio_id`),
  CONSTRAINT `archived_pos_orders_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`shift_id`),
  CONSTRAINT `archived_pos_orders_tab_id_foreign` FOREIGN KEY (`tab_id`) REFERENCES `pos_tabs` (`tab_id`) ON DELETE SET NULL,
  CONSTRAINT `archived_pos_orders_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`),
  CONSTRAINT `archived_pos_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archived_pos_orders`
--

LOCK TABLES `archived_pos_orders` WRITE;
/*!40000 ALTER TABLE `archived_pos_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `archived_pos_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archived_transactions`
--

DROP TABLE IF EXISTS `archived_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `archived_transactions` (
  `transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `folio_id` int(10) unsigned NOT NULL,
  `charge_code` int(10) unsigned NOT NULL,
  `shift_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `transaction_date` date NOT NULL,
  `charge_number` varchar(30) DEFAULT NULL,
  `payment_method` enum('CASH','CREDIT_CARD','CHECK','NONE') NOT NULL DEFAULT 'NONE',
  `reference_notes` varchar(255) DEFAULT NULL,
  `charge_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `credit_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `department` varchar(50) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`transaction_id`),
  KEY `archived_transactions_folio_id_index` (`folio_id`),
  KEY `archived_transactions_charge_code_index` (`charge_code`),
  KEY `archived_transactions_shift_id_index` (`shift_id`),
  KEY `archived_transactions_user_id_index` (`user_id`),
  CONSTRAINT `archived_transactions_charge_code_foreign` FOREIGN KEY (`charge_code`) REFERENCES `chargecodes` (`charge_code`),
  CONSTRAINT `archived_transactions_folio_id_foreign` FOREIGN KEY (`folio_id`) REFERENCES `folios` (`folio_id`),
  CONSTRAINT `archived_transactions_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`shift_id`),
  CONSTRAINT `archived_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archived_transactions`
--

LOCK TABLES `archived_transactions` WRITE;
/*!40000 ALTER TABLE `archived_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `archived_transactions` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (1,2,18,'2026-06-27','12:00:00','2026-06-28','12:00:00','2026-06-27 04:03:30','2026-07-11 13:44:00','CHECKED_OUT',NULL),(2,3,10,'2026-06-28','12:00:00','2026-06-29','12:00:00','2026-06-28 18:07:31','2026-07-11 13:42:00','CHECKED_OUT',NULL),(3,4,6,'2026-07-11','12:00:00',NULL,NULL,'2026-07-11 14:17:55',NULL,'CHECKED_IN',NULL);
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
INSERT INTO `cache` VALUES ('laravel-cache-last_archive_run','O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2026-07-14 22:05:26.544180\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:11:\"Asia/Manila\";}',1784041526),('laravel-cache-layout_data_user_1','a:2:{s:13:\"notifications\";a:4:{i:0;a:6:{s:2:\"id\";s:18:\"booking-checkout-1\";s:4:\"type\";s:8:\"checkout\";s:4:\"icon\";s:25:\"fa-door-open text-primary\";s:7:\"message\";s:54:\"Guest Kade Ortiz (Room 107) is due for checkout today.\";s:4:\"link\";s:43:\"http://127.0.0.1:8000/frontdesk/guest-folio\";s:4:\"time\";s:12:\"Jun 28, 2026\";}i:1;a:6:{s:2:\"id\";s:18:\"booking-checkout-2\";s:4:\"type\";s:8:\"checkout\";s:4:\"icon\";s:25:\"fa-door-open text-primary\";s:7:\"message\";s:54:\"Guest Kade Ortiz (Room 110) is due for checkout today.\";s:4:\"link\";s:43:\"http://127.0.0.1:8000/frontdesk/guest-folio\";s:4:\"time\";s:12:\"Jun 29, 2026\";}i:2;a:6:{s:2:\"id\";s:15:\"inventory-low-3\";s:4:\"type\";s:9:\"inventory\";s:4:\"icon\";s:23:\"fa-box-open text-danger\";s:7:\"message\";s:45:\"Low Stock: \'tubig\' is below 20 (Current: 20).\";s:4:\"link\";s:59:\"http://127.0.0.1:8000/coffeeshop/inventory?filter=low_stock\";s:4:\"time\";s:9:\"Low Stock\";}i:3;a:6:{s:2:\"id\";s:15:\"shift-none-open\";s:4:\"type\";s:5:\"shift\";s:4:\"icon\";s:33:\"fa-clock-rotate-left text-warning\";s:7:\"message\";s:43:\"No active shift open. Please start a shift.\";s:4:\"link\";s:41:\"http://127.0.0.1:8000/frontdesk/dashboard\";s:4:\"time\";s:9:\"Attention\";}}s:13:\"lowStockCount\";i:1;}',1782906181),('laravel-cache-layout_data_user_2','a:2:{s:13:\"notifications\";a:2:{i:0;a:6:{s:2:\"id\";s:18:\"booking-checkout-1\";s:4:\"type\";s:8:\"checkout\";s:4:\"icon\";s:25:\"fa-door-open text-primary\";s:7:\"message\";s:54:\"Guest Kade Ortiz (Room 107) is due for checkout today.\";s:4:\"link\";s:43:\"http://127.0.0.1:8000/frontdesk/guest-folio\";s:4:\"time\";s:12:\"Jun 28, 2026\";}i:1;a:6:{s:2:\"id\";s:18:\"booking-checkout-2\";s:4:\"type\";s:8:\"checkout\";s:4:\"icon\";s:25:\"fa-door-open text-primary\";s:7:\"message\";s:54:\"Guest Kade Ortiz (Room 110) is due for checkout today.\";s:4:\"link\";s:43:\"http://127.0.0.1:8000/frontdesk/guest-folio\";s:4:\"time\";s:12:\"Jun 29, 2026\";}}s:13:\"lowStockCount\";i:1;}',1782915831),('laravel-cache-layout_data_user_3','a:2:{s:13:\"notifications\";a:0:{}s:13:\"lowStockCount\";i:1;}',1782901040),('laravel-cache-layout_data_user_4','a:2:{s:13:\"notifications\";a:1:{i:0;a:6:{s:2:\"id\";s:15:\"inventory-low-3\";s:4:\"type\";s:9:\"inventory\";s:4:\"icon\";s:23:\"fa-box-open text-danger\";s:7:\"message\";s:45:\"Low Stock: \'tubig\' is below 20 (Current: 20).\";s:4:\"link\";s:59:\"http://127.0.0.1:8000/coffeeshop/inventory?filter=low_stock\";s:4:\"time\";s:9:\"Low Stock\";}}s:13:\"lowStockCount\";i:1;}',1782915964);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_account_ledgers`
--

LOCK TABLES `credit_account_ledgers` WRITE;
/*!40000 ALTER TABLE `credit_account_ledgers` DISABLE KEYS */;
INSERT INTO `credit_account_ledgers` VALUES (1,1,'charge',550.00,'pos_order',10,4,'POS Order POS-20260704-0004: French Fries x5','2026-07-04 07:40:21','2026-07-04 07:40:21'),(2,1,'charge',2752.00,'folio',3,2,'ade','2026-07-04 07:41:10','2026-07-04 07:41:10');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_accounts`
--

LOCK TABLES `credit_accounts` WRITE;
/*!40000 ALTER TABLE `credit_accounts` DISABLE KEYS */;
INSERT INTO `credit_accounts` VALUES (1,'ade','21312321312312','213232112',10000.00,1,'2026-07-04 07:39:39','2026-07-04 07:39:39');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `folios`
--

LOCK TABLES `folios` WRITE;
/*!40000 ALTER TABLE `folios` DISABLE KEYS */;
INSERT INTO `folios` VALUES (1,'POS-WALKIN',NULL,NULL,161,NULL,'NONE',NULL,NULL,1,0,0,NULL,'POS','SYSTEM','OPEN','Cash',NULL),(2,'REG-2026001',NULL,NULL,85,NULL,'Walk-in',NULL,NULL,1,0,0,NULL,'CBO','GUEST','OPEN','Cash',200.00),(3,'REG-2026002',NULL,NULL,85,NULL,'Walk-in',NULL,NULL,1,0,0,NULL,'CBO','GUEST','CLOSED','Cash',10000.00),(4,'REG-2026003',NULL,NULL,49,NULL,'Walk-in',NULL,NULL,1,0,0,NULL,'CBO','GUEST','OPEN','Cash',5000.00);
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
) ENGINE=InnoDB AUTO_INCREMENT=482 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guests`
--

LOCK TABLES `guests` WRITE;
/*!40000 ALTER TABLE `guests` DISABLE KEYS */;
INSERT INTO `guests` VALUES (1,'Doe','John','123 Main St','Apt 4','+1234567890','GUEST','2026-06-26 19:59:42'),(2,'Smith','Jane','456 Oak Ave',NULL,'+1987654321','GUEST','2026-06-26 19:59:42'),(3,'Johnson','Robert','789 Pine Rd','Suite 100','+1555123456','GUEST','2026-06-26 19:59:42'),(4,'Williams','Emily','321 Elm St',NULL,'+1666789012','GUEST','2026-06-26 19:59:42'),(5,'Brown','Michael','654 Maple Dr','Unit B','+1777456789','GUEST','2026-06-26 19:59:42'),(6,'Davis','Sarah','987 Cedar Ln',NULL,'+1888012345','GUEST','2026-06-26 19:59:42'),(7,'Miller','David','147 Birch St','Floor 2','+1999345678','GUEST','2026-06-26 19:59:42'),(8,'Wilson','Lisa','258 Spruce Ave',NULL,'+1444678901','GUEST','2026-06-26 19:59:42'),(9,'Moore','James','369 Willow St','Apt 1','+1333901234','GUEST','2026-06-26 19:59:42'),(10,'Taylor','Jennifer','741 Ash Rd',NULL,'+1222234567','GUEST','2026-06-26 19:59:42'),(11,'Murazik','Marcel','737 Scot Islands Apt. 024','Apt. 644','765.276.0632','GUEST','2026-06-26 19:59:42'),(12,'Ritchie','Orlando','463 Ava Islands',NULL,'+1-559-532-1303','GUEST','2026-06-26 19:59:42'),(13,'Cartwright','Arnaldo','937 Grant Bypass',NULL,'406-461-1544','GUEST','2026-06-26 19:59:42'),(14,'Goodwin','Barney','28272 Lyda Branch Suite 907',NULL,'1-320-722-6394','GUEST','2026-06-26 19:59:42'),(15,'Daugherty','Clemmie','6719 Casimer Shores',NULL,'(909) 558-6044','GUEST','2026-06-26 19:59:42'),(16,'Quitzon','Garry','4834 Bogan Cliffs Suite 944',NULL,'+1 (432) 343-5744','GUEST','2026-06-26 19:59:42'),(17,'White','Dameon','5748 Grimes Rue Apt. 623','Suite 044','1-614-721-9584','GUEST','2026-06-26 19:59:42'),(18,'Towne','Jennings','140 Lottie Drive Suite 199','Apt. 484','+1-531-263-6379','GUEST','2026-06-26 19:59:42'),(19,'McCullough','Teagan','304 Robert Crossing',NULL,'785.635.5731','GUEST','2026-06-26 19:59:42'),(20,'Schamberger','Deontae','388 Boyle Landing',NULL,'+1-838-502-3791','GUEST','2026-06-26 19:59:42'),(21,'Dicki','Antonietta','196 Lind Manor',NULL,'(559) 501-5590','GUEST','2026-06-26 19:59:42'),(22,'Ward','Itzel','523 Lonny Canyon','Suite 070','1-563-644-2739','GUEST','2026-06-26 19:59:42'),(23,'DuBuque','Noemy','6143 Shanahan Fords Suite 478',NULL,'+1.283.969.0819','GUEST','2026-06-26 19:59:42'),(24,'Goldner','Ashtyn','1796 Kreiger Green Suite 395',NULL,'(337) 663-5469','GUEST','2026-06-26 19:59:42'),(25,'Davis','Mustafa','715 Hessel Course',NULL,'+13854072996','GUEST','2026-06-26 19:59:42'),(26,'Beatty','Sarah','4002 Mathilde Street Suite 742',NULL,'(386) 323-8449','GUEST','2026-06-26 19:59:42'),(27,'Carroll','Pansy','288 Mraz Route Suite 165',NULL,'+1-870-488-6055','GUEST','2026-06-26 19:59:42'),(28,'Runolfsdottir','Liam','350 Yost Trail',NULL,'+1-858-602-5393','GUEST','2026-06-26 19:59:42'),(29,'Wisozk','Sean','8257 Kiehn Plaza',NULL,'916-287-4937','GUEST','2026-06-26 19:59:42'),(30,'Durgan','Domenick','7718 Blick Road Apt. 990','Suite 635','702.455.2736','GUEST','2026-06-26 19:59:42'),(31,'Hegmann','Stone','52607 Howe Bridge Suite 023','Suite 809','+1-262-344-7905','GUEST','2026-06-26 19:59:42'),(32,'Gerlach','Kelsi','59924 Hipolito Fords',NULL,'336.276.5819','GUEST','2026-06-26 19:59:42'),(33,'Jakubowski','Elliot','78110 Clare Brooks Apt. 507',NULL,'1-314-555-0946','GUEST','2026-06-26 19:59:42'),(34,'Towne','Yessenia','179 Bernardo Prairie',NULL,'283-561-8964','GUEST','2026-06-26 19:59:42'),(35,'Leuschke','Sylvia','2603 Alphonso Bridge',NULL,'680-476-3618','GUEST','2026-06-26 19:59:42'),(36,'Rath','Reid','435 Mayert Manors Apt. 993',NULL,'754.653.0915','GUEST','2026-06-26 19:59:42'),(37,'Runte','Carmen','49224 Schinner Mountains',NULL,'1-769-221-7172','GUEST','2026-06-26 19:59:42'),(38,'Bruen','Leon','326 Patsy Well Apt. 389','Apt. 120','1-541-516-8668','GUEST','2026-06-26 19:59:42'),(39,'Kiehn','Zane','59842 Lynch Streets',NULL,'517-255-8512','GUEST','2026-06-26 19:59:42'),(40,'Kuphal','Pablo','350 Kutch Square Apt. 464',NULL,'(559) 350-0818','GUEST','2026-06-26 19:59:42'),(41,'Wyman','Melvina','14835 Sawayn Meadow Suite 216',NULL,'(732) 664-7251','GUEST','2026-06-26 19:59:42'),(42,'Abbott','Thomas','49472 Davon Dale Apt. 042','Suite 856','+1-989-404-0312','GUEST','2026-06-26 19:59:42'),(43,'Bailey','Brycen','65025 Ullrich Villages',NULL,'(716) 883-7023','GUEST','2026-06-26 19:59:42'),(44,'Anderson','Cory','7304 Reichert Ford Apt. 984',NULL,'(520) 584-5793','GUEST','2026-06-26 19:59:42'),(45,'Tromp','Keely','41338 Bartoletti Coves Suite 070',NULL,'1-432-835-2103','GUEST','2026-06-26 19:59:42'),(46,'Crona','Lincoln','87429 Amya Mountains Suite 341','Apt. 978','934.304.5098','GUEST','2026-06-26 19:59:42'),(47,'Turcotte','Frankie','2302 Botsford Place','Suite 243','857.298.6793','GUEST','2026-06-26 19:59:42'),(48,'Hermiston','Alanis','531 Shields Corner Apt. 372',NULL,'253.856.7270','GUEST','2026-06-26 19:59:42'),(49,'Schaden','Betty','638 Ruthie Meadows Suite 688','Suite 303','857.677.0086','GUEST','2026-06-26 19:59:42'),(50,'Hane','Viviane','498 Herzog Cove Suite 065','Suite 640','364-204-4149','GUEST','2026-06-26 19:59:42'),(51,'Armstrong','Quentin','6707 Bednar Station','Suite 644','+1-321-542-2853','GUEST','2026-06-26 19:59:42'),(52,'Treutel','Mavis','25821 King Freeway',NULL,'(608) 797-8377','GUEST','2026-06-26 19:59:42'),(53,'Frami','Lura','36089 McKenzie Underpass',NULL,'(680) 922-9097','GUEST','2026-06-26 19:59:42'),(54,'Fritsch','Trace','509 Madeline Track Apt. 634',NULL,'+1 (316) 832-9115','GUEST','2026-06-26 19:59:42'),(55,'Wiza','George','49468 Cole Gardens Suite 207',NULL,'+1-989-502-7272','GUEST','2026-06-26 19:59:42'),(56,'Corkery','Enrique','3588 Cremin Bridge Apt. 492',NULL,'+15208173614','GUEST','2026-06-26 19:59:42'),(57,'Harris','Tamia','91888 Ilene Track',NULL,'562-971-5384','GUEST','2026-06-26 19:59:42'),(58,'Collier','Ashleigh','2078 Ernser Street',NULL,'(423) 729-0463','GUEST','2026-06-26 19:59:42'),(59,'Wiza','Alda','773 Stokes Lake','Suite 402','1-531-704-4238','GUEST','2026-06-26 19:59:42'),(60,'Spencer','Alexa','6357 Muriel Springs',NULL,'(651) 389-8560','GUEST','2026-06-26 19:59:42'),(61,'Gleason','Dianna','518 Kling Square Suite 633',NULL,'(610) 952-6856','GUEST','2026-06-26 19:59:42'),(62,'Olson','Alverta','2832 Lebsack Vista',NULL,'254.693.2913','GUEST','2026-06-26 19:59:42'),(63,'Reynolds','Adella','4778 Bernardo Park Apt. 825',NULL,'458.373.1915','GUEST','2026-06-26 19:59:42'),(64,'Haag','Candace','133 Effertz Roads Apt. 639','Apt. 710','+1 (720) 213-3915','GUEST','2026-06-26 19:59:42'),(65,'Okuneva','Trent','274 Vicky Courts',NULL,'217-353-1168','GUEST','2026-06-26 19:59:42'),(66,'Hoppe','Newell','844 Norberto Ways Suite 686','Suite 237','380-942-0404','GUEST','2026-06-26 19:59:42'),(67,'Halvorson','Shyann','28104 Turner Centers Suite 721',NULL,'+1.360.768.6980','GUEST','2026-06-26 19:59:42'),(68,'Turner','Jaclyn','33207 Monahan Via',NULL,'(248) 580-2930','GUEST','2026-06-26 19:59:42'),(69,'Tillman','Rafael','16944 Kirlin Station Suite 438','Apt. 305','612-689-9506','GUEST','2026-06-26 19:59:42'),(70,'Schmitt','Viviane','145 Dicki Light Apt. 758',NULL,'+13412936209','GUEST','2026-06-26 19:59:42'),(71,'Christiansen','Kurtis','9501 Lueilwitz Summit',NULL,'(256) 529-8782','GUEST','2026-06-26 19:59:42'),(72,'Dickinson','Leonie','65036 Collier Locks',NULL,'+1.531.892.7885','GUEST','2026-06-26 19:59:42'),(73,'O\'Conner','Miles','96936 Stiedemann Views Suite 331',NULL,'+18562487713','GUEST','2026-06-26 19:59:42'),(74,'Wisozk','Ozella','6428 Mueller Village Suite 812',NULL,'+13302786137','GUEST','2026-06-26 19:59:42'),(75,'Stiedemann','Tomasa','40751 Daron Burgs','Apt. 414','(680) 292-6042','GUEST','2026-06-26 19:59:42'),(76,'Beahan','Kendall','5191 Kohler Drives Apt. 935','Apt. 657','(423) 950-3291','GUEST','2026-06-26 19:59:42'),(77,'Keeling','Kristofer','6517 Tina Divide',NULL,'347.201.6366','GUEST','2026-06-26 19:59:42'),(78,'Kihn','Lafayette','9204 Stokes Groves',NULL,'1-309-465-4213','GUEST','2026-06-26 19:59:42'),(79,'Emard','Hayden','854 Bret Crossroad Suite 507',NULL,'+1.956.643.8985','GUEST','2026-06-26 19:59:42'),(80,'Lebsack','Mellie','2723 Ratke Groves Suite 032',NULL,'629-968-4920','GUEST','2026-06-26 19:59:42'),(81,'Koepp','Noel','3844 Kaylie Center Apt. 817','Apt. 223','(941) 734-9793','GUEST','2026-06-26 19:59:42'),(82,'Walker','Leonard','6220 Schuster Port','Apt. 885','937.236.4037','GUEST','2026-06-26 19:59:42'),(83,'Bergstrom','Cordie','35323 Erika Forest Suite 141','Suite 023','+1 (351) 295-7807','GUEST','2026-06-26 19:59:42'),(84,'Jenkins','Camden','218 Rutherford Ford','Apt. 883','+1-689-639-3410','GUEST','2026-06-26 19:59:42'),(85,'Ortiz','Kade','5636 Boehm Mountains Suite 417',NULL,'562-366-8415','GUEST','2026-06-26 19:59:42'),(86,'White','Joan','34127 Koepp Stravenue',NULL,'+18034784088','GUEST','2026-06-26 19:59:42'),(87,'Schmeler','Estrella','8039 Erwin Ways','Apt. 207','+18135693642','GUEST','2026-06-26 19:59:42'),(88,'Bradtke','Alva','36191 Milan Curve','Suite 611','571-424-6702','GUEST','2026-06-26 19:59:42'),(89,'Grady','Clarissa','578 Kunze Estates',NULL,'+18625319863','GUEST','2026-06-26 19:59:42'),(90,'Skiles','Carolanne','2225 Johnnie Roads Apt. 894',NULL,'1-872-298-1146','GUEST','2026-06-26 19:59:42'),(91,'Lynch','Reba','86181 Swift Forest',NULL,'1-408-763-4042','GUEST','2026-06-26 19:59:42'),(92,'Parisian','Domenico','62480 Robyn Dam Apt. 283',NULL,'989.567.5166','GUEST','2026-06-26 19:59:42'),(93,'Schiller','Maida','73845 Dolly Course','Suite 281','(706) 434-4621','GUEST','2026-06-26 19:59:42'),(94,'Pfeffer','Elmore','6582 Mueller Circle Suite 163',NULL,'1-484-592-6859','GUEST','2026-06-26 19:59:42'),(95,'Goldner','Raymundo','671 Homenick Lodge Apt. 308','Suite 400','+1-414-668-2552','GUEST','2026-06-26 19:59:42'),(96,'McClure','Angus','329 Carley Common Apt. 919',NULL,'1-657-354-3949','GUEST','2026-06-26 19:59:42'),(97,'Pagac','Verlie','93457 Gussie Points Suite 450','Apt. 214','1-364-238-5374','GUEST','2026-06-26 19:59:42'),(98,'Jaskolski','Jermain','17394 Corkery Fall Apt. 418',NULL,'(854) 233-7382','GUEST','2026-06-26 19:59:42'),(99,'Weimann','Norma','30567 Michaela Skyway Apt. 064',NULL,'385.897.0062','GUEST','2026-06-26 19:59:42'),(100,'Ledner','Elisha','586 Myrtie Estate',NULL,'1-702-225-3241','GUEST','2026-06-26 19:59:42'),(101,'Herman','Skye','253 Harber Forest',NULL,'1-323-758-4773','GUEST','2026-06-26 19:59:42'),(102,'Walsh','Roslyn','874 Verdie Burgs Suite 746','Suite 984','828-709-0795','GUEST','2026-06-26 19:59:42'),(103,'Boyer','Doug','3598 Konopelski Hill Apt. 860','Suite 884','319-541-1462','GUEST','2026-06-26 19:59:42'),(104,'Kreiger','Harrison','3158 Jacobs Garden',NULL,'+1 (239) 562-2407','GUEST','2026-06-26 19:59:42'),(105,'Hill','Stacey','5148 Runolfsson Cliff Suite 721','Suite 002','+15737821205','GUEST','2026-06-26 19:59:42'),(106,'Bartell','Johnson','68663 Graham Viaduct',NULL,'+1 (231) 523-9652','GUEST','2026-06-26 19:59:42'),(107,'Brakus','Mathias','824 Grover Pine Apt. 172','Suite 014','661.935.1569','GUEST','2026-06-26 19:59:42'),(108,'Walter','Heidi','36510 Daron Knoll',NULL,'(270) 932-1767','GUEST','2026-06-26 19:59:42'),(109,'Krajcik','Mekhi','34446 Jaquelin Alley','Suite 222','218.552.8565','GUEST','2026-06-26 19:59:42'),(110,'Anderson','Belle','174 Bogan Gateway Apt. 760',NULL,'+1.480.344.4539','GUEST','2026-06-26 19:59:42'),(111,'Wunsch','Anibal','101 Buster Oval','Apt. 129','+14436489863','GUEST','2026-06-26 19:59:42'),(112,'Franecki','Marta','3820 Trantow Ford Apt. 839',NULL,'620.493.0521','GUEST','2026-06-26 19:59:42'),(113,'Bauch','Rosa','7572 Amparo Islands Apt. 347',NULL,'681.654.8045','GUEST','2026-06-26 19:59:42'),(114,'Baumbach','Pearlie','7593 Gabriel Divide Suite 744','Apt. 449','801.988.3277','GUEST','2026-06-26 19:59:42'),(115,'Thompson','Kaleigh','520 Claud Summit Suite 474',NULL,'812.285.3155','GUEST','2026-06-26 19:59:42'),(116,'Ernser','Karolann','271 Raina Center Suite 316','Apt. 519','805-701-8594','GUEST','2026-06-26 19:59:42'),(117,'McCullough','Pedro','812 Levi Parkway Suite 857','Suite 240','1-805-445-6963','GUEST','2026-06-26 19:59:42'),(118,'Nikolaus','Lowell','86968 Linnie Radial','Suite 559','380-558-4328','GUEST','2026-06-26 19:59:42'),(119,'D\'Amore','Arnaldo','809 Murazik Gardens Apt. 588',NULL,'619-783-9617','GUEST','2026-06-26 19:59:42'),(120,'Daugherty','Deangelo','848 Koelpin Coves',NULL,'+1 (479) 830-5713','GUEST','2026-06-26 19:59:42'),(121,'Hammes','Antone','4285 Ruecker Pass',NULL,'818-593-8452','GUEST','2026-06-26 19:59:42'),(122,'Kirlin','Jazmyn','369 Feeney Keys',NULL,'+1.518.336.4760','GUEST','2026-06-26 19:59:42'),(123,'Littel','Shanie','7338 Boyer Lock','Suite 210','+1.743.315.5419','GUEST','2026-06-26 19:59:42'),(124,'Funk','Ron','3168 Dovie Shores Apt. 241',NULL,'+1-346-808-9897','GUEST','2026-06-26 19:59:42'),(125,'Bosco','Rupert','74870 Nienow Canyon',NULL,'262-347-8333','GUEST','2026-06-26 19:59:42'),(126,'Harvey','Korbin','8932 Spinka Neck Apt. 810',NULL,'325-914-6099','GUEST','2026-06-26 19:59:42'),(127,'Larkin','Winifred','326 Isabelle Stravenue Suite 074',NULL,'+1-707-497-6385','GUEST','2026-06-26 19:59:42'),(128,'Murazik','Ara','1131 Ullrich Pass Suite 752','Apt. 038','1-651-564-1032','GUEST','2026-06-26 19:59:42'),(129,'Toy','Heloise','596 Joesph Row Apt. 798',NULL,'1-620-347-2571','GUEST','2026-06-26 19:59:42'),(130,'Abshire','Leonor','8244 Friesen Estate','Suite 164','+1-979-458-2200','GUEST','2026-06-26 19:59:42'),(131,'Ward','Tianna','18147 Vance Gardens Suite 158','Apt. 667','+1-385-995-9427','GUEST','2026-06-26 19:59:42'),(132,'Hoeger','Albin','9159 Kemmer Parkway Suite 694',NULL,'770-965-0755','GUEST','2026-06-26 19:59:42'),(133,'Lebsack','Omer','991 Marks Port Suite 263','Suite 447','+14697928729','GUEST','2026-06-26 19:59:42'),(134,'Kovacek','Deron','382 Cronin Village',NULL,'1-719-891-0612','GUEST','2026-06-26 19:59:42'),(135,'Lueilwitz','Glenda','774 Prudence Valleys',NULL,'+1-859-698-7063','GUEST','2026-06-26 19:59:42'),(136,'Kling','Mckenna','379 Stehr Groves Suite 446',NULL,'1-225-386-0379','GUEST','2026-06-26 19:59:42'),(137,'Bosco','Vernice','858 Abbott Station Apt. 556','Suite 407','562-618-7164','GUEST','2026-06-26 19:59:42'),(138,'Yundt','Mina','8139 Lang Hills',NULL,'626-701-2720','GUEST','2026-06-26 19:59:42'),(139,'Harber','Cyril','3314 Waylon Trail Apt. 826',NULL,'+18708630602','GUEST','2026-06-26 19:59:42'),(140,'Dickinson','Beulah','8820 Marks Crossing Suite 531','Suite 473','+13862664691','GUEST','2026-06-26 19:59:42'),(141,'Schimmel','Keira','35871 Donnell Plaza',NULL,'1-864-328-2591','GUEST','2026-06-26 19:59:42'),(142,'Welch','Kobe','8859 Walsh Ways Apt. 314',NULL,'812.972.2952','GUEST','2026-06-26 19:59:42'),(143,'Mayer','Enos','139 Hand Unions',NULL,'341.396.6194','GUEST','2026-06-26 19:59:42'),(144,'Blanda','Molly','21311 Kshlerin Oval Apt. 364',NULL,'520-655-1516','GUEST','2026-06-26 19:59:42'),(145,'Durgan','Lilliana','2927 Stark Freeway',NULL,'931-748-1181','GUEST','2026-06-26 19:59:42'),(146,'Hills','Victor','12953 Harmony Highway','Apt. 983','872.295.1778','GUEST','2026-06-26 19:59:42'),(147,'O\'Kon','Leonardo','612 Ruth Way',NULL,'+1-650-879-4520','GUEST','2026-06-26 19:59:42'),(148,'Harvey','Else','1286 Kobe Tunnel','Apt. 755','(915) 875-9061','GUEST','2026-06-26 19:59:42'),(149,'Hermann','Celestino','1059 Rory Well','Suite 757','+1 (956) 897-3868','GUEST','2026-06-26 19:59:42'),(150,'O\'Keefe','Belle','466 Dickens Corners',NULL,'+1 (820) 808-0023','GUEST','2026-06-26 19:59:42'),(151,'Balistreri','Ressie','334 Leannon Road Apt. 728',NULL,'(331) 323-5039','GUEST','2026-06-26 19:59:42'),(152,'Harvey','Tremayne','7373 Tomasa Manors Suite 511',NULL,'830.544.7255','GUEST','2026-06-26 19:59:42'),(153,'D\'Amore','Morton','21644 Leland Pines Apt. 631',NULL,'669-583-5312','GUEST','2026-06-26 19:59:42'),(154,'Bergstrom','Edd','7715 Jerrold Meadow Apt. 409',NULL,'1-770-533-0157','GUEST','2026-06-26 19:59:42'),(155,'Harber','Esta','138 Elenora Flat Suite 499',NULL,'341.279.4252','GUEST','2026-06-26 19:59:42'),(156,'Hickle','Anderson','9496 Gottlieb Roads','Suite 363','+12838898041','GUEST','2026-06-26 19:59:42'),(157,'Price','Lesly','802 Jocelyn Hollow',NULL,'(740) 804-0490','GUEST','2026-06-26 19:59:42'),(158,'Mayer','Sincere','5779 Carroll Avenue','Apt. 956','1-270-390-7475','GUEST','2026-06-26 19:59:42'),(159,'Zulauf','Katheryn','2163 Zemlak Plains',NULL,'660.520.8627','GUEST','2026-06-26 19:59:42'),(160,'Witting','Ressie','509 Marc Vista',NULL,'907-366-2175','GUEST','2026-06-26 19:59:42'),(161,'WALK-IN','POS',NULL,NULL,'N/A','SYSTEM','2026-06-26 19:59:42'),(162,'Doe','John','123 Main St','Apt 4','+1234567890','GUEST','2026-07-01 09:58:23'),(163,'Smith','Jane','456 Oak Ave',NULL,'+1987654321','GUEST','2026-07-01 09:58:23'),(164,'Johnson','Robert','789 Pine Rd','Suite 100','+1555123456','GUEST','2026-07-01 09:58:23'),(165,'Williams','Emily','321 Elm St',NULL,'+1666789012','GUEST','2026-07-01 09:58:23'),(166,'Brown','Michael','654 Maple Dr','Unit B','+1777456789','GUEST','2026-07-01 09:58:23'),(167,'Davis','Sarah','987 Cedar Ln',NULL,'+1888012345','GUEST','2026-07-01 09:58:23'),(168,'Miller','David','147 Birch St','Floor 2','+1999345678','GUEST','2026-07-01 09:58:23'),(169,'Wilson','Lisa','258 Spruce Ave',NULL,'+1444678901','GUEST','2026-07-01 09:58:23'),(170,'Moore','James','369 Willow St','Apt 1','+1333901234','GUEST','2026-07-01 09:58:23'),(171,'Taylor','Jennifer','741 Ash Rd',NULL,'+1222234567','GUEST','2026-07-01 09:58:23'),(172,'Stoltenberg','Liam','2602 Kimberly Divide Apt. 545',NULL,'+15398868268','GUEST','2026-07-01 09:58:23'),(173,'Thiel','Rebecca','7060 Cartwright Shoals','Apt. 663','820.576.3115','GUEST','2026-07-01 09:58:23'),(174,'King','Eryn','8783 Dayna Inlet','Suite 169','1-517-706-0088','GUEST','2026-07-01 09:58:23'),(175,'Mayert','Petra','782 Jones Shoal',NULL,'1-513-296-8882','GUEST','2026-07-01 09:58:23'),(176,'Hamill','Meghan','105 McKenzie Park Apt. 888',NULL,'+1 (725) 804-8986','GUEST','2026-07-01 09:58:23'),(177,'Moen','Lisandro','33428 Stephen Club',NULL,'+14098890396','GUEST','2026-07-01 09:58:23'),(178,'Stracke','Weston','12659 Schroeder Port',NULL,'1-484-689-5500','GUEST','2026-07-01 09:58:23'),(179,'Hansen','Arlie','94682 Morris Parks Apt. 356',NULL,'404.361.2409','GUEST','2026-07-01 09:58:23'),(180,'Miller','Cecil','823 Vance Passage',NULL,'469-391-1979','GUEST','2026-07-01 09:58:23'),(181,'Murray','Ethan','12551 Agustin Courts',NULL,'(385) 288-5416','GUEST','2026-07-01 09:58:23'),(182,'Lockman','Gregorio','95313 Gottlieb Tunnel',NULL,'+1-413-585-9794','GUEST','2026-07-01 09:58:23'),(183,'Pouros','Name','15165 Shakira Bridge','Suite 057','929.361.9139','GUEST','2026-07-01 09:58:23'),(184,'Murazik','Samanta','1296 Kuhlman Way',NULL,'878.389.2444','GUEST','2026-07-01 09:58:23'),(185,'Corwin','Damaris','5922 Kreiger Rue',NULL,'+1 (380) 827-2716','GUEST','2026-07-01 09:58:23'),(186,'Torp','Axel','97141 Wintheiser Light Suite 206',NULL,'+19078932462','GUEST','2026-07-01 09:58:23'),(187,'Huels','Guy','6184 Wilkinson Roads',NULL,'+16085513953','GUEST','2026-07-01 09:58:23'),(188,'Harris','Sven','148 Lindgren Trail',NULL,'1-404-277-2071','GUEST','2026-07-01 09:58:23'),(189,'Maggio','Consuelo','305 Johnson Streets Apt. 474','Suite 103','+1-909-437-8891','GUEST','2026-07-01 09:58:23'),(190,'Murray','Hunter','33220 Cruickshank Wall Suite 913',NULL,'520-785-1773','GUEST','2026-07-01 09:58:23'),(191,'Cummings','Jessyca','1548 Toy Key',NULL,'339-293-1608','GUEST','2026-07-01 09:58:23'),(192,'Corkery','Blake','1672 Prosacco Pike Suite 507',NULL,'1-706-930-9890','GUEST','2026-07-01 09:58:23'),(193,'Weber','Jefferey','5445 Lang Drive',NULL,'+1.678.220.0551','GUEST','2026-07-01 09:58:23'),(194,'Quigley','Brennan','909 Fannie Grove Suite 282','Suite 319','+14192230769','GUEST','2026-07-01 09:58:23'),(195,'Franecki','Helen','8140 Von Terrace Suite 141','Suite 942','+1.872.277.8832','GUEST','2026-07-01 09:58:23'),(196,'Dare','Orlo','565 Thompson Meadow Apt. 642','Suite 216','1-804-475-4617','GUEST','2026-07-01 09:58:23'),(197,'Hills','Ardella','133 Senger Corner Apt. 848',NULL,'480.444.9889','GUEST','2026-07-01 09:58:23'),(198,'Haley','Dominic','666 Cole Camp Apt. 207','Apt. 190','+12485070807','GUEST','2026-07-01 09:58:23'),(199,'Leannon','Dahlia','294 Estel Camp',NULL,'+1.870.245.6686','GUEST','2026-07-01 09:58:23'),(200,'Barton','Stephany','214 Eula Flats','Apt. 715','+18022615015','GUEST','2026-07-01 09:58:23'),(201,'Schimmel','Lexus','833 Hyman Fall','Apt. 232','681.619.7133','GUEST','2026-07-01 09:58:23'),(202,'Ziemann','Laurianne','66879 Prohaska Islands Apt. 245',NULL,'+1.463.663.8937','GUEST','2026-07-01 09:58:23'),(203,'Marvin','Jerrold','1234 Ashlee Fields','Suite 241','+1-623-673-1131','GUEST','2026-07-01 09:58:23'),(204,'Kihn','Micheal','78715 King Rest',NULL,'1-667-609-2615','GUEST','2026-07-01 09:58:23'),(205,'Kuvalis','Wava','605 Virgil Curve Suite 403',NULL,'862.559.4637','GUEST','2026-07-01 09:58:23'),(206,'Walsh','Rae','28163 Wisozk Lodge','Apt. 148','(254) 317-9102','GUEST','2026-07-01 09:58:23'),(207,'Welch','Matilde','8978 Nader Drives',NULL,'1-331-293-5346','GUEST','2026-07-01 09:58:23'),(208,'Schiller','Asha','287 Ivory Valley',NULL,'1-830-823-4797','GUEST','2026-07-01 09:58:23'),(209,'Grimes','Mauricio','92728 Fahey Villages',NULL,'1-918-232-2316','GUEST','2026-07-01 09:58:23'),(210,'Abshire','Judah','73434 Schulist Gateway',NULL,'978.244.6995','GUEST','2026-07-01 09:58:23'),(211,'Denesik','Clara','3604 Ariane Ridges',NULL,'+1-424-705-4002','GUEST','2026-07-01 09:58:23'),(212,'Ledner','Ruby','35680 Jakubowski Pines',NULL,'507-990-6474','GUEST','2026-07-01 09:58:23'),(213,'Dickens','Crystel','938 Braden Throughway',NULL,'+1 (530) 207-4292','GUEST','2026-07-01 09:58:23'),(214,'Borer','Clara','78226 Konopelski Bypass',NULL,'681-450-2423','GUEST','2026-07-01 09:58:23'),(215,'Ortiz','Reggie','30663 Maximus Station Suite 970',NULL,'+1 (214) 662-6330','GUEST','2026-07-01 09:58:23'),(216,'Jenkins','Dylan','789 Lang Lake',NULL,'413.645.3891','GUEST','2026-07-01 09:58:23'),(217,'Sauer','Dangelo','4104 Jerome Burgs Suite 226',NULL,'+12517045082','GUEST','2026-07-01 09:58:23'),(218,'Denesik','Sibyl','19226 Hammes Burgs','Apt. 182','1-979-995-5507','GUEST','2026-07-01 09:58:23'),(219,'Willms','Aylin','3547 Ima Trafficway Apt. 437','Apt. 462','+1.239.804.3584','GUEST','2026-07-01 09:58:23'),(220,'Leuschke','Gloria','2087 Walton Pines Apt. 435','Apt. 460','+1-260-798-9074','GUEST','2026-07-01 09:58:23'),(221,'Kirlin','Lorena','196 Jakubowski Mission Apt. 377',NULL,'+1.701.521.5641','GUEST','2026-07-01 09:58:23'),(222,'Gottlieb','Naomie','9873 Raegan Falls Apt. 941',NULL,'(513) 974-2856','GUEST','2026-07-01 09:58:23'),(223,'Corwin','Berneice','7185 Rogahn Shoals',NULL,'(678) 397-2136','GUEST','2026-07-01 09:58:23'),(224,'Yundt','Shea','927 Ottis Lakes Suite 999','Apt. 282','347-775-8468','GUEST','2026-07-01 09:58:23'),(225,'Swift','Verna','364 Howe Station Suite 146',NULL,'+1.458.527.3333','GUEST','2026-07-01 09:58:23'),(226,'Hirthe','Esmeralda','97891 Sandra Port','Apt. 658','(941) 838-6028','GUEST','2026-07-01 09:58:23'),(227,'Aufderhar','Reinhold','3317 Griffin Fords',NULL,'1-540-279-9977','GUEST','2026-07-01 09:58:23'),(228,'Durgan','Daren','988 Baumbach Walks','Suite 372','203.712.1606','GUEST','2026-07-01 09:58:23'),(229,'D\'Amore','Amie','157 Camren Coves','Suite 317','785.541.8767','GUEST','2026-07-01 09:58:23'),(230,'Douglas','Ethyl','7013 Lowe Curve','Suite 889','(321) 642-9014','GUEST','2026-07-01 09:58:23'),(231,'Denesik','Dianna','9374 Hodkiewicz Key',NULL,'(832) 958-4985','GUEST','2026-07-01 09:58:23'),(232,'Kunde','Katharina','6985 Halvorson Drive',NULL,'989-919-5315','GUEST','2026-07-01 09:58:23'),(233,'Hudson','Albert','7341 Darion Via',NULL,'1-651-429-0475','GUEST','2026-07-01 09:58:23'),(234,'Gusikowski','Grant','6646 Kozey Divide Apt. 675',NULL,'+1-832-627-9684','GUEST','2026-07-01 09:58:23'),(235,'Mosciski','Misty','65049 Sarina Hills',NULL,'+1.920.814.9329','GUEST','2026-07-01 09:58:23'),(236,'Collier','Carmelo','42656 Homenick Extension Suite 393',NULL,'848-451-4674','GUEST','2026-07-01 09:58:23'),(237,'Balistreri','Erling','41821 Blick Crest Suite 525','Apt. 024','(718) 891-6124','GUEST','2026-07-01 09:58:23'),(238,'Jacobson','Raymond','70716 Khalil Villages','Apt. 167','702.450.2661','GUEST','2026-07-01 09:58:23'),(239,'Jones','Kyle','73223 Elvie Land Apt. 993',NULL,'+1.425.409.2835','GUEST','2026-07-01 09:58:23'),(240,'Erdman','Keegan','8028 Feest Mill Suite 216','Apt. 893','+1.970.957.2994','GUEST','2026-07-01 09:58:23'),(241,'Fisher','Emilia','1589 Fahey Union Apt. 632',NULL,'+18656073642','GUEST','2026-07-01 09:58:23'),(242,'Hill','Jasper','59704 Naomi Lodge',NULL,'+1-678-822-1635','GUEST','2026-07-01 09:58:23'),(243,'Hoeger','Bulah','5056 Bette Ports','Apt. 759','252-993-8123','GUEST','2026-07-01 09:58:23'),(244,'Roob','Assunta','6653 Jack Coves Suite 259',NULL,'838.858.2392','GUEST','2026-07-01 09:58:23'),(245,'Kuhn','Jamaal','861 Hane Way Apt. 669',NULL,'475-478-3843','GUEST','2026-07-01 09:58:23'),(246,'Hodkiewicz','Amy','86262 Feest Mountains Suite 731',NULL,'(347) 780-5251','GUEST','2026-07-01 09:58:23'),(247,'Boyer','Earlene','38524 Ronaldo Motorway',NULL,'+1-907-222-8681','GUEST','2026-07-01 09:58:23'),(248,'Watsica','Gay','7323 Corkery Ways Suite 588',NULL,'580-837-0340','GUEST','2026-07-01 09:58:23'),(249,'Gutkowski','Reagan','592 Pollich Springs',NULL,'1-830-366-7448','GUEST','2026-07-01 09:58:23'),(250,'Connelly','Thurman','9423 Isaiah Estates Apt. 531',NULL,'478.589.6088','GUEST','2026-07-01 09:58:23'),(251,'McGlynn','Geovanni','12781 Olson Fall Apt. 136',NULL,'781.348.1332','GUEST','2026-07-01 09:58:23'),(252,'Howell','Audrey','961 Cole Squares Suite 196','Apt. 594','+1-947-643-7287','GUEST','2026-07-01 09:58:23'),(253,'Kertzmann','Lazaro','378 Harley Forks',NULL,'508.802.3804','GUEST','2026-07-01 09:58:23'),(254,'Hills','Philip','43274 Marlon Gateway',NULL,'810.385.3318','GUEST','2026-07-01 09:58:23'),(255,'Runolfsdottir','Ervin','9166 Okuneva Track',NULL,'(413) 308-3607','GUEST','2026-07-01 09:58:23'),(256,'Ferry','Garret','6738 Bogisich Place Suite 633',NULL,'+1.225.216.2909','GUEST','2026-07-01 09:58:23'),(257,'Carter','Chaya','75511 Brown View','Suite 661','1-667-940-2346','GUEST','2026-07-01 09:58:23'),(258,'Nikolaus','Tatyana','98603 Hintz Fords',NULL,'+1.240.834.3915','GUEST','2026-07-01 09:58:23'),(259,'Fisher','Wilhelmine','390 Gusikowski Passage',NULL,'1-346-434-1234','GUEST','2026-07-01 09:58:23'),(260,'Lakin','Winston','919 Emmerich Lights Suite 396','Suite 985','337.458.8466','GUEST','2026-07-01 09:58:23'),(261,'Torphy','Adolphus','9029 Marilyne Inlet Suite 434','Suite 154','682-444-7863','GUEST','2026-07-01 09:58:23'),(262,'Cole','Katelynn','746 Leda Street Suite 555',NULL,'323.695.8406','GUEST','2026-07-01 09:58:23'),(263,'O\'Connell','Tyrell','4442 Beahan Cliffs',NULL,'+1-364-655-2808','GUEST','2026-07-01 09:58:23'),(264,'Terry','Boyd','86361 Ethan Grove',NULL,'248-600-3101','GUEST','2026-07-01 09:58:23'),(265,'Herman','Grace','704 Charley Tunnel',NULL,'(304) 530-6169','GUEST','2026-07-01 09:58:23'),(266,'Littel','Deion','27759 Kole Branch Apt. 821',NULL,'551-415-8840','GUEST','2026-07-01 09:58:23'),(267,'Kessler','Jarrod','56608 Parker Corner',NULL,'914-231-7170','GUEST','2026-07-01 09:58:23'),(268,'McLaughlin','Jazmyne','709 Reese Brooks',NULL,'(838) 376-6841','GUEST','2026-07-01 09:58:23'),(269,'Barrows','Delpha','8043 Jadon Port Apt. 535',NULL,'470.399.9205','GUEST','2026-07-01 09:58:23'),(270,'Olson','Sterling','57353 Ferry Glens',NULL,'+1-445-959-6590','GUEST','2026-07-01 09:58:23'),(271,'Boehm','Ali','5422 Selmer Cape',NULL,'(380) 593-2856','GUEST','2026-07-01 09:58:23'),(272,'Flatley','Delpha','9876 Stephania Shores Apt. 078',NULL,'+1-863-214-9995','GUEST','2026-07-01 09:58:23'),(273,'Schulist','Bert','7703 Tyra Spurs Suite 149','Suite 632','1-804-521-8007','GUEST','2026-07-01 09:58:23'),(274,'Marvin','Ryley','70283 Heathcote Burg Suite 154',NULL,'435.865.5278','GUEST','2026-07-01 09:58:23'),(275,'Kuphal','Emilia','379 Joannie Prairie','Suite 522','1-878-710-2387','GUEST','2026-07-01 09:58:23'),(276,'Denesik','Jayne','12058 Corkery Port Suite 028',NULL,'+1 (804) 715-2360','GUEST','2026-07-01 09:58:23'),(277,'Tillman','Rowan','39684 Mann Lodge Apt. 903',NULL,'(520) 763-7726','GUEST','2026-07-01 09:58:23'),(278,'Medhurst','Green','6467 Abernathy Way Suite 093',NULL,'(415) 541-8221','GUEST','2026-07-01 09:58:23'),(279,'Schmidt','Dina','38156 Smitham Plain',NULL,'+13603973826','GUEST','2026-07-01 09:58:23'),(280,'Abbott','Hal','1193 Betty Trail','Apt. 086','774.943.9183','GUEST','2026-07-01 09:58:23'),(281,'Stark','Ambrose','7749 Gerry Spur Apt. 349',NULL,'505-429-4834','GUEST','2026-07-01 09:58:23'),(282,'Emard','Pink','829 Vladimir Pass',NULL,'321.716.9824','GUEST','2026-07-01 09:58:23'),(283,'Jerde','Sunny','69858 Colby Via Suite 621',NULL,'+1-949-857-5955','GUEST','2026-07-01 09:58:23'),(284,'Larkin','Marisol','26247 Levi Summit Apt. 583',NULL,'920-912-5600','GUEST','2026-07-01 09:58:23'),(285,'Ankunding','Americo','35482 Paucek River','Apt. 386','+1.636.594.4443','GUEST','2026-07-01 09:58:23'),(286,'McCullough','Trace','93392 Kevon Trail','Apt. 618','+1 (469) 307-5301','GUEST','2026-07-01 09:58:23'),(287,'Kulas','Dahlia','888 Zetta Lakes','Apt. 960','1-317-743-8085','GUEST','2026-07-01 09:58:23'),(288,'Kris','Mittie','9703 Dicki Field Apt. 648',NULL,'+1-229-702-7641','GUEST','2026-07-01 09:58:23'),(289,'Schmidt','Kamren','248 Ziemann Course Suite 950',NULL,'+1.520.990.5997','GUEST','2026-07-01 09:58:23'),(290,'Christiansen','Cindy','87580 Christiansen Port Suite 259',NULL,'1-540-935-9614','GUEST','2026-07-01 09:58:23'),(291,'Haley','Monroe','54950 Jayme Ferry Apt. 641','Apt. 988','+1-279-383-0536','GUEST','2026-07-01 09:58:23'),(292,'Harvey','Alverta','387 Eichmann Islands Apt. 762','Suite 708','+1-346-836-5960','GUEST','2026-07-01 09:58:23'),(293,'Heller','Baron','23362 Hartmann Mill',NULL,'+1-909-448-8506','GUEST','2026-07-01 09:58:23'),(294,'Mayert','Darrell','778 Marisol Field',NULL,'1-925-742-8561','GUEST','2026-07-01 09:58:23'),(295,'Armstrong','Quentin','593 Gennaro Walks',NULL,'(239) 824-6495','GUEST','2026-07-01 09:58:23'),(296,'Klein','Vivianne','888 Kennith Park Apt. 576',NULL,'(443) 227-8225','GUEST','2026-07-01 09:58:23'),(297,'Koelpin','Angus','333 Rebekah Inlet',NULL,'+1 (947) 276-0001','GUEST','2026-07-01 09:58:23'),(298,'Mayer','Ayden','31098 Paucek Mews Apt. 331','Suite 261','1-781-898-5063','GUEST','2026-07-01 09:58:23'),(299,'Mann','Abelardo','8161 Torphy Club',NULL,'+1.680.510.0428','GUEST','2026-07-01 09:58:23'),(300,'Abbott','Ike','857 Howell Plaza Apt. 302',NULL,'425.469.1624','GUEST','2026-07-01 09:58:23'),(301,'Effertz','Celia','1366 Tatyana Locks',NULL,'1-561-563-5269','GUEST','2026-07-01 09:58:23'),(302,'Wintheiser','Junius','5011 Moen Points',NULL,'+1 (843) 738-7083','GUEST','2026-07-01 09:58:23'),(303,'Haag','Jermain','539 Maud Crossroad Suite 130','Apt. 764','603.478.7885','GUEST','2026-07-01 09:58:23'),(304,'Schinner','Sandra','94893 Kimberly Isle',NULL,'986-768-0898','GUEST','2026-07-01 09:58:23'),(305,'Konopelski','Annabel','3726 Rempel Bypass',NULL,'+1-657-939-2930','GUEST','2026-07-01 09:58:23'),(306,'O\'Conner','Alena','40759 Lilly Stravenue Apt. 332',NULL,'(914) 599-9862','GUEST','2026-07-01 09:58:23'),(307,'Mann','Sabryna','7653 Klocko Turnpike',NULL,'+1-757-256-1254','GUEST','2026-07-01 09:58:23'),(308,'Kulas','Clarissa','177 Savanna Valleys',NULL,'+1-631-308-0732','GUEST','2026-07-01 09:58:23'),(309,'Hackett','Alda','7051 Toy Wall Suite 663','Suite 769','+1-747-361-4316','GUEST','2026-07-01 09:58:23'),(310,'Braun','Rosina','9291 Wendy Island Apt. 717',NULL,'(279) 910-9601','GUEST','2026-07-01 09:58:23'),(311,'Keebler','Scarlett','398 Kacie Fords Apt. 317',NULL,'(216) 539-7216','GUEST','2026-07-01 09:58:23'),(312,'Renner','Kacie','300 Krajcik Islands',NULL,'1-539-357-8366','GUEST','2026-07-01 09:58:23'),(313,'Schowalter','Rylan','97431 Altenwerth Crossroad Apt. 350',NULL,'+1-862-881-3768','GUEST','2026-07-01 09:58:23'),(314,'Stiedemann','Ashleigh','2931 Jacobs Forge Apt. 059','Suite 614','+1-281-797-4277','GUEST','2026-07-01 09:58:23'),(315,'Koelpin','Evans','66499 Gusikowski Extensions Suite 806','Suite 582','+1-931-623-7586','GUEST','2026-07-01 09:58:23'),(316,'Gleason','Clementine','4122 Rohan Mount',NULL,'(719) 645-6030','GUEST','2026-07-01 09:58:23'),(317,'Homenick','Orlando','2136 Houston Turnpike Apt. 599',NULL,'(484) 543-0010','GUEST','2026-07-01 09:58:23'),(318,'Bahringer','Antone','84284 Sipes Ridges Suite 216','Suite 108','469-669-5108','GUEST','2026-07-01 09:58:23'),(319,'Jaskolski','Zoie','3172 Cole Overpass Suite 759','Suite 072','1-423-620-3504','GUEST','2026-07-01 09:58:23'),(320,'Huels','Magnus','511 Brooklyn Forge Suite 080','Suite 829','1-630-316-5263','GUEST','2026-07-01 09:58:23'),(321,'Hudson','Monserrat','489 Jaskolski Village Suite 001','Suite 071','951-681-7858','GUEST','2026-07-01 09:58:23'),(322,'Doe','John','123 Main St','Apt 4','+1234567890','GUEST','2026-07-03 14:05:53'),(323,'Smith','Jane','456 Oak Ave',NULL,'+1987654321','GUEST','2026-07-03 14:05:53'),(324,'Johnson','Robert','789 Pine Rd','Suite 100','+1555123456','GUEST','2026-07-03 14:05:53'),(325,'Williams','Emily','321 Elm St',NULL,'+1666789012','GUEST','2026-07-03 14:05:53'),(326,'Brown','Michael','654 Maple Dr','Unit B','+1777456789','GUEST','2026-07-03 14:05:53'),(327,'Davis','Sarah','987 Cedar Ln',NULL,'+1888012345','GUEST','2026-07-03 14:05:53'),(328,'Miller','David','147 Birch St','Floor 2','+1999345678','GUEST','2026-07-03 14:05:53'),(329,'Wilson','Lisa','258 Spruce Ave',NULL,'+1444678901','GUEST','2026-07-03 14:05:53'),(330,'Moore','James','369 Willow St','Apt 1','+1333901234','GUEST','2026-07-03 14:05:53'),(331,'Taylor','Jennifer','741 Ash Rd',NULL,'+1222234567','GUEST','2026-07-03 14:05:53'),(332,'Hermann','Keara','8874 Thompson Estate Suite 739','Apt. 298','848-657-1432','GUEST','2026-07-03 14:05:53'),(333,'Grady','Nannie','77659 Bayer Corners',NULL,'+17572175447','GUEST','2026-07-03 14:05:53'),(334,'Kessler','Garnet','647 Cecilia Walk','Apt. 529','626.250.5937','GUEST','2026-07-03 14:05:53'),(335,'Spencer','Leon','58981 Denesik Walk',NULL,'+1 (828) 928-2814','GUEST','2026-07-03 14:05:53'),(336,'Pagac','Pat','890 Grant Circles Apt. 823','Apt. 562','+15612531167','GUEST','2026-07-03 14:05:53'),(337,'Jerde','Stephania','56980 Koch Junctions Apt. 123','Suite 848','+19408984922','GUEST','2026-07-03 14:05:53'),(338,'Hahn','Ward','47460 Amanda Cove',NULL,'1-504-452-3586','GUEST','2026-07-03 14:05:53'),(339,'Rau','Friedrich','16282 Reta Springs Suite 541','Suite 764','262-600-6749','GUEST','2026-07-03 14:05:53'),(340,'Quigley','Maya','212 Silas Brook Apt. 167',NULL,'205.328.0411','GUEST','2026-07-03 14:05:53'),(341,'Stehr','Ethan','86739 Cartwright Drive','Apt. 558','(318) 691-0971','GUEST','2026-07-03 14:05:53'),(342,'Jakubowski','Arely','8731 Janiya Plaza','Apt. 407','1-505-254-4716','GUEST','2026-07-03 14:05:53'),(343,'Gutkowski','Jennifer','5384 Bins Groves',NULL,'1-408-801-2751','GUEST','2026-07-03 14:05:53'),(344,'Thiel','Raymundo','617 Horace Loaf Suite 895',NULL,'+1.743.324.9932','GUEST','2026-07-03 14:05:53'),(345,'Nader','Cathy','8245 Arthur Gardens Apt. 374',NULL,'+1-330-709-2985','GUEST','2026-07-03 14:05:53'),(346,'Luettgen','Jada','815 Mack Glens',NULL,'283.424.1210','GUEST','2026-07-03 14:05:53'),(347,'Wilkinson','Lyda','6587 Smith Summit Apt. 888',NULL,'+1.540.670.8024','GUEST','2026-07-03 14:05:53'),(348,'Hessel','Waino','751 Elinore Square',NULL,'1-425-543-1918','GUEST','2026-07-03 14:05:53'),(349,'Bernier','Mae','74064 Myah Falls Apt. 530',NULL,'816-979-1629','GUEST','2026-07-03 14:05:53'),(350,'Douglas','Gabriel','8910 Sawayn Flat',NULL,'+1.917.898.9626','GUEST','2026-07-03 14:05:53'),(351,'Hackett','Beatrice','96785 Tillman Parkway',NULL,'(870) 382-7317','GUEST','2026-07-03 14:05:53'),(352,'Blick','Althea','61165 Emory Field',NULL,'+1-724-644-0396','GUEST','2026-07-03 14:05:53'),(353,'Gusikowski','Guido','3946 Mertz Lodge Suite 534',NULL,'(781) 584-7422','GUEST','2026-07-03 14:05:53'),(354,'Goyette','Greg','7778 Hudson Roads','Apt. 354','1-617-326-7951','GUEST','2026-07-03 14:05:53'),(355,'Schoen','Mona','78605 Pagac Brook',NULL,'+1 (760) 834-2256','GUEST','2026-07-03 14:05:53'),(356,'Bergnaum','Electa','376 Labadie Mills Suite 033','Apt. 209','534.570.8188','GUEST','2026-07-03 14:05:53'),(357,'Kulas','Gerard','892 Collier Burg','Apt. 887','1-774-504-2533','GUEST','2026-07-03 14:05:53'),(358,'Jacobson','Camryn','76468 Talia Estate Apt. 480','Suite 708','+1-551-790-4252','GUEST','2026-07-03 14:05:53'),(359,'Boyle','Ardith','117 Dayne Vista Apt. 849',NULL,'267-609-2388','GUEST','2026-07-03 14:05:53'),(360,'Frami','Theresa','894 McCullough Lane','Apt. 198','510.534.6627','GUEST','2026-07-03 14:05:53'),(361,'Jaskolski','Rick','78069 Bednar Tunnel',NULL,'843.368.8050','GUEST','2026-07-03 14:05:53'),(362,'Runolfsson','Arno','49006 Liza Junctions Suite 164','Suite 278','+15349832810','GUEST','2026-07-03 14:05:53'),(363,'Pfeffer','Marilou','34956 Eve Forks Apt. 914','Suite 550','(769) 372-9164','GUEST','2026-07-03 14:05:53'),(364,'Oberbrunner','Eileen','9577 Barrows Junctions','Apt. 399','(260) 599-4982','GUEST','2026-07-03 14:05:53'),(365,'Towne','Reva','460 Reynolds Forges Apt. 532',NULL,'1-202-628-2435','GUEST','2026-07-03 14:05:53'),(366,'Anderson','Wilber','458 Barrows Lights Suite 798','Apt. 590','+1 (463) 703-1951','GUEST','2026-07-03 14:05:53'),(367,'Paucek','Concepcion','255 Braxton Centers',NULL,'1-351-522-2108','GUEST','2026-07-03 14:05:53'),(368,'Cummerata','Vesta','1604 Bechtelar Highway Apt. 352',NULL,'636-252-7795','GUEST','2026-07-03 14:05:53'),(369,'Miller','Cristian','83326 Klocko Isle Apt. 882',NULL,'1-475-487-5176','GUEST','2026-07-03 14:05:53'),(370,'Beatty','Eric','790 Rolfson Key Apt. 455',NULL,'1-917-918-8144','GUEST','2026-07-03 14:05:53'),(371,'Trantow','Laurence','95821 Wunsch Plains Apt. 078',NULL,'+15712910661','GUEST','2026-07-03 14:05:53'),(372,'Nikolaus','Ruthie','62245 Kutch Islands Suite 310',NULL,'563-419-6596','GUEST','2026-07-03 14:05:53'),(373,'Kris','Jamar','1470 Thompson Road',NULL,'1-586-718-5771','GUEST','2026-07-03 14:05:53'),(374,'Torphy','Sanford','7536 Aubree Inlet',NULL,'817-755-8160','GUEST','2026-07-03 14:05:53'),(375,'Toy','Duane','843 Walker Mountain',NULL,'+1.678.875.6680','GUEST','2026-07-03 14:05:53'),(376,'Von','Carroll','73559 Legros Well Suite 854',NULL,'862.785.6076','GUEST','2026-07-03 14:05:53'),(377,'Wehner','Harmon','6104 Devon Station',NULL,'1-571-655-7249','GUEST','2026-07-03 14:05:53'),(378,'Huels','Andy','1264 Kihn Common',NULL,'+1 (954) 685-2513','GUEST','2026-07-03 14:05:53'),(379,'Hermiston','Danika','871 Jazmyn Tunnel Apt. 981',NULL,'1-719-280-0644','GUEST','2026-07-03 14:05:53'),(380,'Ward','Saul','12236 Judy Alley Suite 145',NULL,'(920) 983-7558','GUEST','2026-07-03 14:05:53'),(381,'Schneider','Eryn','1222 Jameson Lakes Apt. 389',NULL,'+1-931-754-2366','GUEST','2026-07-03 14:05:53'),(382,'Tremblay','Vinnie','342 Jacinto Village Apt. 825',NULL,'564-381-3985','GUEST','2026-07-03 14:05:53'),(383,'Keeling','Misael','8731 Kub Mountains',NULL,'704.978.3681','GUEST','2026-07-03 14:05:53'),(384,'Heaney','Helena','9809 Rosenbaum Center Suite 354','Suite 058','+1 (732) 694-6758','GUEST','2026-07-03 14:05:53'),(385,'Cormier','Ole','6637 Olen Pine',NULL,'+1-650-304-1499','GUEST','2026-07-03 14:05:53'),(386,'Vandervort','Reymundo','258 Pouros Mountains',NULL,'843.312.0879','GUEST','2026-07-03 14:05:53'),(387,'Fadel','Hazel','889 Avery Estates Apt. 245',NULL,'+1-409-451-5162','GUEST','2026-07-03 14:05:53'),(388,'Kshlerin','Melody','254 Susan Creek Suite 865',NULL,'+1-630-947-0697','GUEST','2026-07-03 14:05:53'),(389,'Douglas','Lincoln','7020 Herman Lodge Suite 630','Suite 815','+1.731.785.6867','GUEST','2026-07-03 14:05:53'),(390,'Ullrich','Gerard','1978 Scot Roads Apt. 388',NULL,'279-400-0741','GUEST','2026-07-03 14:05:53'),(391,'Borer','Lester','3738 Predovic Rue Suite 049',NULL,'754.204.6661','GUEST','2026-07-03 14:05:53'),(392,'Kerluke','Donato','964 Daugherty Drives Suite 727','Suite 005','702-556-4710','GUEST','2026-07-03 14:05:53'),(393,'Schoen','Ava','400 Vernice Mission','Suite 706','938.634.3914','GUEST','2026-07-03 14:05:53'),(394,'Mills','Mitchell','432 Ledner Crescent Suite 569',NULL,'321.393.6343','GUEST','2026-07-03 14:05:53'),(395,'Grady','Shawn','12105 Osinski Turnpike',NULL,'1-731-442-6994','GUEST','2026-07-03 14:05:53'),(396,'Harvey','Martine','820 Walsh Ports',NULL,'+1.930.506.9142','GUEST','2026-07-03 14:05:53'),(397,'Ledner','Tessie','7216 Pacocha Shores',NULL,'351-398-3377','GUEST','2026-07-03 14:05:53'),(398,'Denesik','Ozella','94035 Runte Circle Apt. 639',NULL,'435.430.8098','GUEST','2026-07-03 14:05:53'),(399,'Batz','Maryjane','97635 Mayert Drive',NULL,'(609) 607-5216','GUEST','2026-07-03 14:05:53'),(400,'O\'Hara','Abdul','76748 Ortiz Burgs',NULL,'+1-978-304-0474','GUEST','2026-07-03 14:05:53'),(401,'VonRueden','Jodie','7714 Gulgowski Mountain Apt. 821',NULL,'283.451.7604','GUEST','2026-07-03 14:05:53'),(402,'Kunde','Alan','6313 Reagan Island Suite 906',NULL,'1-458-555-4818','GUEST','2026-07-03 14:05:53'),(403,'Daugherty','Clare','3320 Victor Pine Suite 604','Suite 963','+1.940.802.2760','GUEST','2026-07-03 14:05:53'),(404,'Kulas','Pascale','35884 Dee Cape Apt. 278',NULL,'+1-339-841-2590','GUEST','2026-07-03 14:05:53'),(405,'Rosenbaum','Olaf','7311 Powlowski Plains Apt. 576','Suite 746','1-914-779-1320','GUEST','2026-07-03 14:05:53'),(406,'O\'Connell','Dell','43803 Walker Prairie Apt. 093',NULL,'1-831-859-1051','GUEST','2026-07-03 14:05:53'),(407,'O\'Keefe','Brennon','97673 McDermott Stream','Apt. 833','(228) 550-1250','GUEST','2026-07-03 14:05:53'),(408,'Mraz','Declan','2029 Durgan Path Apt. 517',NULL,'1-520-431-7915','GUEST','2026-07-03 14:05:53'),(409,'Moen','Mariana','476 Alfonso Underpass Suite 394',NULL,'434.413.6283','GUEST','2026-07-03 14:05:53'),(410,'Kessler','Meta','118 Stan Ridges','Suite 913','(812) 309-9228','GUEST','2026-07-03 14:05:53'),(411,'Sipes','Iva','93380 Volkman Ville Suite 804',NULL,'(478) 421-4812','GUEST','2026-07-03 14:05:53'),(412,'Medhurst','Ashleigh','29271 Terry Plain',NULL,'(216) 900-2736','GUEST','2026-07-03 14:05:53'),(413,'Goyette','Lora','362 Jesse Harbor',NULL,'1-337-820-6893','GUEST','2026-07-03 14:05:53'),(414,'Heaney','Maximilian','16451 Liam Passage Suite 815','Suite 332','+1-541-560-9815','GUEST','2026-07-03 14:05:53'),(415,'Schroeder','Brenna','212 Bailey Field Suite 119',NULL,'936-867-2574','GUEST','2026-07-03 14:05:53'),(416,'Armstrong','Juvenal','8492 Koss Mountain','Suite 112','+1 (225) 689-5158','GUEST','2026-07-03 14:05:53'),(417,'Feeney','Lucy','90918 Liliana Rue Suite 613',NULL,'803.530.0232','GUEST','2026-07-03 14:05:53'),(418,'Abbott','Keshawn','900 Julie Shore Suite 239',NULL,'+1-251-584-0351','GUEST','2026-07-03 14:05:53'),(419,'Labadie','Jordane','99378 Camron Ferry',NULL,'+1-228-372-0819','GUEST','2026-07-03 14:05:53'),(420,'Orn','Maggie','96701 Wyman Shore Suite 588','Apt. 525','678-891-1136','GUEST','2026-07-03 14:05:53'),(421,'Kunze','Elnora','24366 Dietrich Alley Suite 721','Apt. 627','(985) 309-5838','GUEST','2026-07-03 14:05:53'),(422,'Wyman','Aron','6933 Weissnat Square','Apt. 131','559.646.9064','GUEST','2026-07-03 14:05:53'),(423,'Ruecker','Sylvia','862 Jast Rue','Suite 828','1-720-873-0809','GUEST','2026-07-03 14:05:53'),(424,'Yost','Joanne','62594 Baby Radial','Suite 902','(573) 601-6136','GUEST','2026-07-03 14:05:53'),(425,'Schinner','Urban','7955 Reichert Squares Suite 908',NULL,'+1-571-940-6535','GUEST','2026-07-03 14:05:53'),(426,'Russel','Raleigh','12411 Becker Ports',NULL,'386-947-3650','GUEST','2026-07-03 14:05:53'),(427,'Wolff','Monty','85154 Kraig Causeway Apt. 904',NULL,'+1-231-379-1494','GUEST','2026-07-03 14:05:53'),(428,'Little','Leora','79712 Santina Fall','Suite 871','+1-408-859-6986','GUEST','2026-07-03 14:05:53'),(429,'Willms','Bert','366 Junius Highway',NULL,'+12813757032','GUEST','2026-07-03 14:05:53'),(430,'Jacobson','Destiney','755 Daron Cove Apt. 292',NULL,'402-321-9464','GUEST','2026-07-03 14:05:53'),(431,'Ferry','Alfonso','6188 Schinner Locks Apt. 619',NULL,'949-603-5432','GUEST','2026-07-03 14:05:53'),(432,'Wolff','Jaiden','9752 Elena Track','Suite 070','+1-616-405-7958','GUEST','2026-07-03 14:05:53'),(433,'Lakin','Kelly','5785 Jacynthe Roads Suite 771','Suite 148','+1 (405) 392-3646','GUEST','2026-07-03 14:05:53'),(434,'Torphy','Caden','58957 Sheila Villages',NULL,'+1-248-568-3295','GUEST','2026-07-03 14:05:53'),(435,'Pfannerstill','Lucious','87726 Rutherford Light Apt. 474',NULL,'734.634.3401','GUEST','2026-07-03 14:05:53'),(436,'Nicolas','Llewellyn','866 Vicente Knolls Apt. 282','Suite 617','907.819.9160','GUEST','2026-07-03 14:05:53'),(437,'Hamill','Dillan','5124 Towne Falls',NULL,'1-251-385-1022','GUEST','2026-07-03 14:05:53'),(438,'Runte','Ada','35330 Willie Mount Apt. 040',NULL,'678.912.8647','GUEST','2026-07-03 14:05:53'),(439,'Goodwin','Nickolas','4419 Flatley Underpass Suite 943','Suite 783','(859) 381-4382','GUEST','2026-07-03 14:05:53'),(440,'Halvorson','Lacey','6824 Isom Roads',NULL,'1-351-917-8826','GUEST','2026-07-03 14:05:53'),(441,'Conn','Glen','96802 Kreiger Locks',NULL,'+1-346-401-3459','GUEST','2026-07-03 14:05:53'),(442,'Klein','Jeramy','407 Reilly Lodge Apt. 713',NULL,'+1-352-330-4088','GUEST','2026-07-03 14:05:53'),(443,'Osinski','Laurianne','77603 Hillary Orchard Apt. 826',NULL,'+1.860.248.4099','GUEST','2026-07-03 14:05:53'),(444,'Kozey','Kirk','689 Feest Harbor',NULL,'+1-541-419-8537','GUEST','2026-07-03 14:05:53'),(445,'Huel','Russell','19453 Ziemann Views Apt. 665','Suite 355','781-472-7056','GUEST','2026-07-03 14:05:53'),(446,'Swaniawski','Mertie','610 Mills Mall Apt. 708',NULL,'561.867.8323','GUEST','2026-07-03 14:05:53'),(447,'Bahringer','Alberta','71624 Koss Plains','Apt. 397','+1 (650) 501-6749','GUEST','2026-07-03 14:05:53'),(448,'Cole','Vaughn','29606 Breitenberg Common Suite 514','Apt. 196','(445) 213-3446','GUEST','2026-07-03 14:05:53'),(449,'Gorczany','Malachi','31976 Ana Garden Suite 666','Apt. 065','1-279-672-6112','GUEST','2026-07-03 14:05:53'),(450,'Flatley','Muriel','56429 Davis Cape',NULL,'(585) 347-5726','GUEST','2026-07-03 14:05:53'),(451,'Donnelly','Aida','567 Morissette Estate','Suite 086','281.245.4825','GUEST','2026-07-03 14:05:53'),(452,'Nienow','Florence','432 Cummings Valleys Suite 317','Apt. 227','+1 (240) 418-9723','GUEST','2026-07-03 14:05:53'),(453,'Bradtke','Presley','84901 Araceli Tunnel','Suite 084','+1.820.715.8170','GUEST','2026-07-03 14:05:53'),(454,'Parker','Howard','5985 Shayne Prairie Apt. 080',NULL,'717.352.0817','GUEST','2026-07-03 14:05:53'),(455,'Berge','Terry','11396 Romaguera Club Suite 090',NULL,'(985) 295-3474','GUEST','2026-07-03 14:05:53'),(456,'Langworth','Betty','543 Chaya Street','Apt. 266','+1-417-940-7256','GUEST','2026-07-03 14:05:53'),(457,'McDermott','Blanca','659 Federico Fort Apt. 755',NULL,'503.938.7623','GUEST','2026-07-03 14:05:53'),(458,'Boyle','Brenna','23060 Lockman Ford Suite 968',NULL,'1-916-545-6804','GUEST','2026-07-03 14:05:53'),(459,'Volkman','Jesus','49520 Shields Views Apt. 601','Suite 635','(838) 321-8688','GUEST','2026-07-03 14:05:53'),(460,'Gislason','Icie','610 Ansel Oval',NULL,'+1-830-286-6963','GUEST','2026-07-03 14:05:53'),(461,'Veum','Nick','52768 Myrtle Oval',NULL,'+1 (954) 814-7192','GUEST','2026-07-03 14:05:53'),(462,'Bahringer','Bernard','5467 Armando Canyon Apt. 603',NULL,'725-466-3861','GUEST','2026-07-03 14:05:53'),(463,'Mante','Miles','50349 Adams Centers Suite 687',NULL,'+18145071462','GUEST','2026-07-03 14:05:53'),(464,'Wolff','Bartholome','54882 Alexa Coves',NULL,'+1-808-528-2894','GUEST','2026-07-03 14:05:53'),(465,'Crooks','Ima','194 Collier Track Suite 234',NULL,'1-321-791-5387','GUEST','2026-07-03 14:05:53'),(466,'Orn','Jared','568 Sipes Pines',NULL,'+16787272868','GUEST','2026-07-03 14:05:53'),(467,'Kovacek','Kenyatta','707 Jean Knolls',NULL,'769-839-5938','GUEST','2026-07-03 14:05:53'),(468,'Spencer','Jakob','6903 Christy Place','Apt. 752','+1.325.730.8025','GUEST','2026-07-03 14:05:53'),(469,'Boyer','Genoveva','879 Dameon Branch',NULL,'(570) 816-9633','GUEST','2026-07-03 14:05:53'),(470,'Kassulke','Kavon','309 Steuber Inlet',NULL,'+1-979-318-0132','GUEST','2026-07-03 14:05:53'),(471,'Sawayn','Aida','7995 Rippin Streets Suite 933',NULL,'1-717-958-7493','GUEST','2026-07-03 14:05:53'),(472,'Sanford','Joe','70140 Ullrich Islands',NULL,'+1-770-661-0576','GUEST','2026-07-03 14:05:53'),(473,'Cassin','Ahmed','42106 Rudy Harbor Suite 675','Apt. 326','239.427.8467','GUEST','2026-07-03 14:05:53'),(474,'Spinka','Garland','4418 Donnie Pike Suite 036','Apt. 694','+1.409.673.9110','GUEST','2026-07-03 14:05:53'),(475,'Bechtelar','Unique','221 Kuhlman Mount Suite 339',NULL,'(315) 297-7378','GUEST','2026-07-03 14:05:53'),(476,'Hettinger','Nasir','7304 Domingo Ferry','Apt. 401','1-828-352-5480','GUEST','2026-07-03 14:05:53'),(477,'Pfannerstill','Neoma','254 Rogers View',NULL,'+1-860-230-4205','GUEST','2026-07-03 14:05:53'),(478,'Greenholt','Albina','87671 Kari Park',NULL,'520-761-1405','GUEST','2026-07-03 14:05:53'),(479,'Stroman','Terence','22538 Zemlak Haven Apt. 650',NULL,'+18724146921','GUEST','2026-07-03 14:05:53'),(480,'Hayes','Ali','1349 Huels Port Apt. 796',NULL,'986.261.5573','GUEST','2026-07-03 14:05:53'),(481,'Littel','Elvis','4406 Mazie Glens','Suite 827','+1 (305) 776-2089','GUEST','2026-07-03 14:05:53');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (1,'default','{\"uuid\":\"89483bf7-e484-4a56-a757-923fa0232a3d\",\"displayName\":\"app:archive-old-data\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Foundation\\\\Console\\\\QueuedCommand\",\"command\":\"O:43:\\\"Illuminate\\\\Foundation\\\\Console\\\\QueuedCommand\\\":12:{s:7:\\\"\\u0000*\\u0000data\\\";a:1:{i:0;s:20:\\\"app:archive-old-data\\\";}s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1784037926,\"delay\":null}',0,NULL,1784037926,1784037926);
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
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_06_17_000001_create_guests_table',1),(5,'2026_06_17_000002_create_chargecodes_table',1),(6,'2026_06_17_000003_create_rooms_table',1),(7,'2026_06_17_000004_create_folios_table',1),(8,'2026_06_17_000005_create_activitylogs_table',1),(9,'2026_06_17_000006_create_shifts_table',1),(10,'2026_06_17_000007_create_bookings_table',1),(11,'2026_06_17_000008_create_transactions_table',1),(12,'2026_06_17_032931_create_sessions_table',1),(13,'2026_06_18_000001_create_permissions_table',1),(14,'2026_06_18_000002_create_rolepermissions_table',1),(15,'2026_06_18_135845_add_is_active_to_users_table',1),(16,'2026_06_18_144749_add_is_active_to_roles_table',1),(17,'2026_06_18_153955_add_is_active_and_module_to_permissions_table',1),(18,'2026_06_19_041518_add_cleaning_status_to_rooms_table',1),(19,'2026_06_19_071926_add_is_active_to_rooms_table',1),(20,'2026_06_19_074955_add_is_active_to_chargecodes_table',1),(21,'2026_06_19_091219_create_shift_schedules_table',1),(22,'2026_06_19_091244_add_schedule_id_to_shifts_table',1),(23,'2026_06_20_085638_create_expenses_table',1),(24,'2026_06_21_062245_add_payment_method_to_folios_table',1),(25,'2026_06_21_063834_add_net_rate_to_folios_table',1),(26,'2026_06_23_133155_add_processed_by_to_bookings_table',1),(27,'2026_06_24_145200_add_is_system_admin_to_roles_table',1),(28,'2026_06_24_145208_create_userpermissions_table',1),(29,'2026_06_25_100000_create_pos_tables',1),(30,'2026_06_26_150836_add_explicit_types_to_guests_and_folios_tables',1),(31,'2026_06_26_153338_create_pos_approval_requests_table',1),(32,'2026_06_26_153350_alter_payment_method_on_pos_tables',1),(33,'2026_06_30_195340_create_credit_accounts_table',2),(34,'2026_06_30_195344_create_credit_account_ledgers_table',2),(35,'2026_06_30_195349_add_credit_account_id_and_discounts_to_pos_and_folios',2),(36,'2026_06_30_222329_add_system_setting_to_activitylogs_enum',2),(37,'2026_06_30_222941_alter_activitylogs_action_type_to_string',2),(38,'2026_07_02_203530_alter_tab_type_on_pos_tabs_table',3),(39,'2026_07_02_212602_alter_payment_method_on_transactions_and_folios_tables',3),(40,'2026_07_02_212846_insert_account_charge_to_chargecodes',3),(41,'2026_07_03_000001_fix_pos_tabs_tab_type_column',3),(42,'2026_07_03_224149_update_payment_method_enum_in_transactions_table',4),(43,'2026_07_10_222008_add_scalability_indexes_to_tables',5),(44,'2026_07_10_222756_add_is_stockable_to_pos_products_table',5),(45,'2026_07_11_014556_add_department_to_transactions_table',5),(46,'2026_07_11_024351_add_funding_source_and_requested_by_to_expenses_table',5),(47,'2026_07_11_030234_add_accounting_dashboard_indexes',5),(48,'2026_07_11_134703_make_departure_date_nullable_on_bookings_table',6),(49,'2026_07_12_025054_alter_shift_schedules_for_recurring_shifts',7),(50,'2026_07_12_131508_add_indexes_to_expenses_table',7),(51,'2026_07_14_000000_create_archive_tables',8);
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'manage-users','System','Manage system users and roles',1),(2,'manage-shifts','System','Manage shift schedules and view sales reports',1),(3,'manage-reservations','Front Desk','Manage reservations and guest registrations',1),(4,'process-checkout','Front Desk','Process guest checkout and record payments',1),(5,'view-guest-list','Front Desk','View guest list details',1),(6,'view-guest-folio','Front Desk','View guest folio details',1),(7,'manage-guest-folio','Front Desk','Open, close, reopen folios and post charges',1),(8,'view-shift-sales','Front Desk','View individual or shared shift sales',1),(9,'view-accounting-dashboard','Accounting','Access financial overview charts and statistics',1),(10,'manage-accounting-billing','Accounting','Access billing details and view billing lists',1),(11,'manage-accounting-payments','Accounting','Register payments and view payment history',1),(12,'manage-accounting-receivables','Accounting','View receivables ledger and accounts',1),(13,'manage-accounting-expenses','Accounting','Track, create, and approve expenses',1),(14,'view-accounting-reports','Accounting','Generate system financial reports',1),(15,'view-accounting-audit','Accounting','Access log changes and trace operations',1),(16,'manage-inventory','Inventory','Manage coffeeshop inventory and sales orders',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_categories`
--

LOCK TABLES `pos_categories` WRITE;
/*!40000 ALTER TABLE `pos_categories` DISABLE KEYS */;
INSERT INTO `pos_categories` VALUES (1,'Drinks',1,1,'2026-06-26 19:59:42','2026-06-26 19:59:42'),(2,'Food',2,1,'2026-06-26 19:59:42','2026-06-26 19:59:42'),(3,'Other',3,1,'2026-06-26 19:59:42','2026-06-26 19:59:42');
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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_inventory_logs`
--

LOCK TABLES `pos_inventory_logs` WRITE;
/*!40000 ALTER TABLE `pos_inventory_logs` DISABLE KEYS */;
INSERT INTO `pos_inventory_logs` VALUES (1,2,-1,'sale','pos_order',1,4,'Added to tab ade','2026-06-26 20:00:18'),(2,2,-1,'sale','pos_order',1,4,'Added to tab ade','2026-06-26 20:00:19'),(3,2,-1,'sale','pos_order',2,4,'Added to tab Room 107 - Kade Ortiz','2026-06-26 20:03:50'),(4,2,-1,'sale','pos_order',2,4,'Added to tab Room 107 - Kade Ortiz','2026-06-26 20:03:50'),(5,2,-1,'sale','pos_order',2,4,'Added to tab Room 107 - Kade Ortiz','2026-06-26 20:03:51'),(6,2,-1,'sale','pos_order',2,4,'Added to tab Room 107 - Kade Ortiz','2026-06-26 20:03:51'),(7,1,9,'adjustment','manual',NULL,4,NULL,'2026-06-28 09:41:48'),(8,2,100,'restock','manual',NULL,4,NULL,'2026-06-28 09:41:55'),(9,3,-1,'sale','pos_order',3,4,'Added to tab klent','2026-06-28 09:42:43'),(10,3,-1,'sale','pos_order',3,4,'Increased tab item quantity','2026-06-28 09:42:46'),(11,3,1,'cancel','pos_order',3,4,'Decreased tab item quantity','2026-06-28 09:42:46'),(12,3,-79,'sale','pos_order',3,4,'Increased tab item quantity','2026-06-28 09:43:06'),(13,2,-1,'sale','pos_order',4,4,'Added to tab ade','2026-06-28 09:44:00'),(14,2,-1,'sale','pos_order',4,4,'Increased tab item quantity','2026-06-28 09:44:03'),(15,4,-1,'sale','pos_order',5,4,'Added to tab ade','2026-07-03 14:10:39'),(16,2,-1,'sale','pos_order',5,4,'Added to tab ade','2026-07-03 14:10:41'),(17,2,-1,'sale','pos_order',5,4,'Added to tab ade','2026-07-03 14:10:43'),(18,2,-1,'sale','pos_order',6,4,'Added to tab ade','2026-07-03 14:14:30'),(19,2,-1,'sale','pos_order',6,4,'Added to tab ade','2026-07-03 14:14:30'),(20,2,-1,'sale','pos_order',6,4,'Added to tab ade','2026-07-03 14:14:31'),(21,2,-1,'sale','pos_order',6,4,'Added to tab ade','2026-07-03 14:14:31'),(22,2,-1,'sale','pos_order',6,4,'Added to tab ade','2026-07-03 14:14:32'),(23,2,-1,'sale','pos_order',6,4,'Added to tab ade','2026-07-03 14:14:32'),(24,2,-1,'sale','pos_order',6,4,'Added to tab ade','2026-07-03 14:14:32'),(25,2,-1,'sale','pos_order',6,4,'Added to tab ade','2026-07-03 14:14:33'),(26,4,-1,'sale','pos_order',6,4,'Added to tab ade','2026-07-03 14:18:30'),(27,2,-1,'sale','pos_order',8,4,'Added to tab ade','2026-07-03 14:20:59'),(28,2,-1,'sale','pos_order',8,4,'Added to tab ade','2026-07-03 14:21:00'),(29,2,-1,'sale','pos_order',7,4,'Added to tab 855','2026-07-04 07:34:14'),(30,2,-1,'sale','pos_order',7,4,'Added to tab 855','2026-07-04 07:34:15'),(31,2,-1,'sale','pos_order',9,4,'Added to tab ade','2026-07-04 07:36:30'),(32,2,-1,'sale','pos_order',9,4,'Added to tab ade','2026-07-04 07:36:31'),(33,2,-1,'sale','pos_order',10,4,'Added to tab adede2145','2026-07-04 07:39:55'),(34,2,-1,'sale','pos_order',10,4,'Added to tab adede2145','2026-07-04 07:39:56'),(35,2,-1,'sale','pos_order',10,4,'Added to tab adede2145','2026-07-04 07:39:56'),(36,2,-1,'sale','pos_order',10,4,'Added to tab adede2145','2026-07-04 07:39:57'),(37,2,-1,'sale','pos_order',10,4,'Added to tab adede2145','2026-07-04 07:39:57');
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_order_items`
--

LOCK TABLES `pos_order_items` WRITE;
/*!40000 ALTER TABLE `pos_order_items` DISABLE KEYS */;
INSERT INTO `pos_order_items` VALUES (1,1,2,'French Fries','Crispy golden fries',2,110.00,220.00),(2,2,2,'French Fries','Crispy golden fries',4,110.00,440.00),(3,3,3,'tubig','mineral',80,20.00,1600.00),(4,4,2,'French Fries','Crispy golden fries',2,110.00,220.00),(5,5,4,'qeq','dasd',1,2312.00,2312.00),(6,5,2,'French Fries','Crispy golden fries',2,110.00,220.00),(7,6,2,'French Fries','Crispy golden fries',8,110.00,880.00),(8,6,4,'qeq','dasd',1,2312.00,2312.00),(9,7,2,'French Fries','Crispy golden fries',2,110.00,220.00),(10,8,2,'French Fries','Crispy golden fries',2,110.00,220.00),(11,9,2,'French Fries','Crispy golden fries',2,110.00,220.00),(12,10,2,'French Fries','Crispy golden fries',5,110.00,550.00);
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_orders`
--

LOCK TABLES `pos_orders` WRITE;
/*!40000 ALTER TABLE `pos_orders` DISABLE KEYS */;
INSERT INTO `pos_orders` VALUES (1,'POS-20260627-0001',1,NULL,NULL,NULL,'ade',NULL,'closed',NULL,0.00,0,'cash',220.00,220.00,4,1,'2026-06-26 20:00:25','2026-06-26 20:00:25'),(2,'POS-20260627-0002',3,2,NULL,4,'Room 107 - Kade Ortiz','107','closed',NULL,0.00,0,'room_charge',440.00,440.00,4,1,'2026-06-26 20:03:56','2026-06-26 20:03:56'),(3,'POS-20260628-0001',2,NULL,NULL,NULL,'klent',NULL,'closed',NULL,0.00,0,'cash',1600.00,1600.00,4,1,'2026-06-28 09:43:31','2026-06-28 09:43:31'),(4,'POS-20260628-0002',4,NULL,NULL,NULL,'ade',NULL,'closed',NULL,0.00,0,'cash',220.00,220.00,4,1,'2026-06-28 09:44:20','2026-06-28 09:44:20'),(5,'POS-20260703-0001',5,3,NULL,12,'ade','110','closed',NULL,0.00,0,'room_charge',2532.00,2532.00,4,1,'2026-07-03 14:12:33','2026-07-03 14:12:33'),(6,'POS-20260703-0002',6,2,NULL,13,'ade','107','closed',NULL,0.00,0,'room_charge',3192.00,3192.00,4,1,'2026-07-03 14:18:34','2026-07-03 14:18:34'),(7,'POS-20260704-0001',8,2,NULL,14,'855','107','closed',NULL,0.00,0,'room_charge',220.00,220.00,4,1,'2026-07-04 07:34:17','2026-07-04 07:34:17'),(8,'POS-20260704-0002',7,3,NULL,15,'ade','110','closed',NULL,0.00,0,'room_charge',220.00,220.00,4,1,'2026-07-04 07:34:33','2026-07-04 07:34:33'),(9,'POS-20260704-0003',9,2,NULL,16,'ade','107','closed',NULL,0.00,0,'room_charge',220.00,220.00,4,1,'2026-07-04 07:36:40','2026-07-04 07:36:40'),(10,'POS-20260704-0004',10,NULL,1,NULL,'adede2145',NULL,'closed',NULL,0.00,0,'account_charge',550.00,550.00,4,1,'2026-07-04 07:40:21','2026-07-04 07:40:21');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_products`
--

LOCK TABLES `pos_products` WRITE;
/*!40000 ALTER TABLE `pos_products` DISABLE KEYS */;
INSERT INTO `pos_products` VALUES (1,2,'Club Sandwich','Triple-decker club sandwich',220.00,1,NULL,39,8,1,'2026-06-26 19:59:42','2026-06-28 09:41:48'),(2,2,'French Fries','Crispy golden fries',110.00,1,NULL,111,10,1,'2026-06-26 19:59:42','2026-07-04 07:39:57'),(3,1,'tubig','mineral',20.00,1,NULL,20,20,1,'2026-06-28 09:42:28','2026-06-28 09:43:06'),(4,1,'qeq','dasd',2312.00,1,NULL,0,1,1,'2026-07-01 10:04:03','2026-07-03 14:18:30'),(5,1,'1221','asdasd',123.00,1,NULL,3,1,1,'2026-07-01 10:04:34','2026-07-01 10:04:34');
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
INSERT INTO `pos_settings` VALUES ('default_low_stock_threshold','10','2026-06-26 19:59:42'),('walk_in_folio_id','1','2026-06-26 19:59:42');
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_tab_items`
--

LOCK TABLES `pos_tab_items` WRITE;
/*!40000 ALTER TABLE `pos_tab_items` DISABLE KEYS */;
INSERT INTO `pos_tab_items` VALUES (1,1,2,2,110.00,220.00),(2,3,2,4,110.00,440.00),(3,2,3,80,20.00,1600.00),(4,4,2,2,110.00,220.00),(5,5,4,1,2312.00,2312.00),(6,5,2,2,110.00,220.00),(7,6,2,8,110.00,880.00),(8,6,4,1,2312.00,2312.00),(9,7,2,2,110.00,220.00),(10,8,2,2,110.00,220.00),(11,9,2,2,110.00,220.00),(12,10,2,5,110.00,550.00);
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_tabs`
--

LOCK TABLES `pos_tabs` WRITE;
/*!40000 ALTER TABLE `pos_tabs` DISABLE KEYS */;
INSERT INTO `pos_tabs` VALUES (1,'ade','walk_in',NULL,NULL,NULL,NULL,NULL,'closed',NULL,0.00,0,'cash',220.00,220.00,4,4,'2026-06-26 20:00:16','2026-06-26 20:00:25',NULL),(2,'klent','walk_in',NULL,NULL,NULL,NULL,NULL,'closed',NULL,0.00,0,'cash',1600.00,1600.00,4,4,'2026-06-26 20:01:38','2026-06-28 09:43:31',NULL),(3,'Room 107 - Kade Ortiz','room',85,2,NULL,1,7,'closed',NULL,0.00,0,'room_charge',440.00,440.00,4,4,'2026-06-26 20:03:47','2026-06-26 20:03:56',NULL),(4,'ade','walk_in',NULL,NULL,NULL,NULL,NULL,'closed',NULL,0.00,0,'cash',220.00,220.00,4,4,'2026-06-28 09:43:53','2026-06-28 09:44:20',NULL),(5,'ade','room',NULL,3,NULL,2,10,'closed',NULL,0.00,0,'room_charge',2532.00,2532.00,4,4,'2026-07-03 14:10:23','2026-07-03 14:12:34',NULL),(6,'ade','room',NULL,2,NULL,1,7,'closed',NULL,0.00,0,'room_charge',3192.00,3192.00,4,4,'2026-07-03 14:14:08','2026-07-03 14:18:34',NULL),(7,'ade','room',NULL,3,NULL,2,10,'closed',NULL,0.00,0,'room_charge',220.00,220.00,4,4,'2026-07-03 14:20:53','2026-07-04 07:34:33',NULL),(8,'855','room',NULL,2,NULL,1,7,'closed',NULL,0.00,0,'room_charge',220.00,220.00,4,4,'2026-07-03 14:22:31','2026-07-04 07:34:17',NULL),(9,'ade','room',NULL,2,NULL,1,7,'closed',NULL,0.00,0,'room_charge',220.00,220.00,4,4,'2026-07-04 07:36:26','2026-07-04 07:36:40',NULL),(10,'adede2145','account',NULL,NULL,1,NULL,NULL,'closed',NULL,0.00,0,'account_charge',550.00,550.00,4,4,'2026-07-04 07:39:50','2026-07-04 07:40:21',NULL);
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
INSERT INTO `rolepermissions` VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,10),(1,11),(1,12),(1,13),(1,14),(1,15),(1,16),(2,3),(2,4),(2,5),(2,6),(2,7),(2,8),(3,9),(3,10),(3,11),(3,12),(3,13),(3,14),(3,15),(4,16);
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'ADMIN','Full system administrator with all access privileges',1,1),(2,'FRONT_DESK','Front desk receptionist handling bookings, check-ins, and folios',1,0),(3,'ACCOUNTING','Finance staff auditing invoices, payments, and sales reports',1,0),(4,'CAFETERIA','Cafeteria cashier managing orders, POS, and inventory',1,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (1,'101','President Suite',250.00,'AVAILABLE',1),(2,'102','President Suite',250.00,'AVAILABLE',1),(3,'103','Twin Room',60.00,'AVAILABLE',1),(4,'104','Suite',150.00,'AVAILABLE',1),(5,'105','Twin Room',60.00,'AVAILABLE',1),(6,'106','Deluxe Room',100.00,'OCCUPIED',1),(7,'107','Connecting Room',200.00,'AVAILABLE',1),(8,'108','Deluxe Room',100.00,'AVAILABLE',1),(9,'109','Suite',150.00,'AVAILABLE',1),(10,'110','Connecting Room',200.00,'AVAILABLE',1),(11,'111','Studio Room',75.00,'AVAILABLE',1),(12,'112','Deluxe Room',100.00,'AVAILABLE',1),(13,'113','Twin Room',60.00,'AVAILABLE',1),(14,'114','Connecting Room',200.00,'AVAILABLE',1),(15,'115','Connecting Room',200.00,'AVAILABLE',1),(16,'116','Suite',150.00,'AVAILABLE',1),(17,'117','Single Room',50.00,'AVAILABLE',1),(18,'118','Connecting Room',200.00,'AVAILABLE',1),(19,'119','Single Room',50.00,'AVAILABLE',1),(20,'120','Suite',150.00,'AVAILABLE',1),(21,'121','Studio Room',75.00,'AVAILABLE',1),(22,'122','Twin Room',60.00,'AVAILABLE',1),(23,'123','Suite',150.00,'AVAILABLE',1),(24,'124','Twin Room',60.00,'AVAILABLE',1),(25,'125','President Suite',250.00,'AVAILABLE',1),(26,'126','Suite',150.00,'AVAILABLE',1),(27,'127','Studio Room',75.00,'AVAILABLE',1),(28,'128','President Suite',250.00,'AVAILABLE',1),(29,'129','Suite',150.00,'AVAILABLE',1),(30,'130','Connecting Room',200.00,'AVAILABLE',1),(31,'131','Connecting Room',200.00,'AVAILABLE',1),(32,'132','Connecting Room',200.00,'AVAILABLE',1),(33,'133','Connecting Room',200.00,'AVAILABLE',1),(34,'134','President Suite',250.00,'AVAILABLE',1),(35,'135','Twin Room',60.00,'AVAILABLE',1),(36,'136','Single Room',50.00,'AVAILABLE',1),(37,'137','Connecting Room',200.00,'AVAILABLE',1),(38,'138','Studio Room',75.00,'AVAILABLE',1),(39,'139','Deluxe Room',100.00,'AVAILABLE',1),(40,'140','President Suite',250.00,'AVAILABLE',1),(41,'141','Connecting Room',200.00,'AVAILABLE',1),(42,'142','Studio Room',75.00,'AVAILABLE',1),(43,'143','Connecting Room',200.00,'AVAILABLE',1),(44,'144','President Suite',250.00,'AVAILABLE',1),(45,'145','Studio Room',75.00,'AVAILABLE',1),(46,'146','Studio Room',75.00,'AVAILABLE',1),(47,'147','Twin Room',60.00,'AVAILABLE',1),(48,'148','Deluxe Room',100.00,'AVAILABLE',1),(49,'149','Twin Room',60.00,'AVAILABLE',1),(50,'150','Connecting Room',200.00,'AVAILABLE',1),(51,'151','Single Room',50.00,'AVAILABLE',1),(52,'152','Suite',150.00,'AVAILABLE',1),(53,'153','Connecting Room',200.00,'AVAILABLE',1),(54,'154','Twin Room',60.00,'AVAILABLE',1),(55,'155','Twin Room',60.00,'AVAILABLE',1),(56,'156','Studio Room',75.00,'AVAILABLE',1),(57,'157','President Suite',250.00,'AVAILABLE',1),(58,'158','Single Room',50.00,'AVAILABLE',1),(59,'159','President Suite',250.00,'AVAILABLE',1),(60,'160','Suite',150.00,'AVAILABLE',1);
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
INSERT INTO `sessions` VALUES ('Z73iRQG0CLhg3zdt13xemowVeHdCgE3PgLBFzG1c',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/3.11.13 Chrome/144.0.7559.236 Electron/40.10.3 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYlJkU0FuUWgwRGZWZEZyN3BRZ1BkQWNERU14V1I5VXFrNHoyWHR0NCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL2JhY2t1cC1yZXN0b3JlIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1784039368),('ZkolMdBZbpT7uDuZ64Zosz6Vo18R9Cf8A0d2s8CB',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoialJwQmRIU2ZRSnRmY2pWV05NUU9DOWVUandod2VNZXZEWnJuQWNQRSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQyOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vYmFja3VwLXJlc3RvcmUiO3M6NToicm91dGUiO3M6MjA6ImFkbWluLmJhY2t1cC1yZXN0b3JlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1784040488);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shifts`
--

LOCK TABLES `shifts` WRITE;
/*!40000 ALTER TABLE `shifts` DISABLE KEYS */;
INSERT INTO `shifts` VALUES (1,4,NULL,'2026-06-27 04:00:25',NULL),(2,2,NULL,'2026-06-27 08:31:08','2026-07-11 15:22:05');
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES (1,1,200,1,4,'2026-06-27','POS-CASH-1','NONE','POS CASH Order POS-20260627-0001: French Fries x2',220.00,0.00,'FRONT_DESK','2026-06-26 20:00:25'),(2,1,403,1,4,'2026-06-27','POS-CASH-1-PAY','CASH','CASH payment for POS-20260627-0001',0.00,220.00,'FRONT_DESK','2026-06-26 20:00:25'),(3,2,100,1,2,'2026-06-27','RM-1-1','NONE','Room charge for Night 1 (Date: 2026-06-27)',200.00,0.00,'FRONT_DESK','2026-06-26 20:03:30'),(4,2,200,1,4,'2026-06-27','POS-2','NONE','POS Order POS-20260627-0002: French Fries x4',440.00,0.00,'FRONT_DESK','2026-06-26 20:03:56'),(5,1,200,1,4,'2026-06-28','POS-CASH-3','NONE','POS CASH Order POS-20260628-0001: tubig x80',1600.00,0.00,'FRONT_DESK','2026-06-28 09:43:31'),(6,1,403,1,4,'2026-06-28','POS-CASH-3-PAY','CASH','CASH payment for POS-20260628-0001',0.00,1600.00,'FRONT_DESK','2026-06-28 09:43:31'),(7,1,200,1,4,'2026-06-28','POS-CASH-4','NONE','POS CASH Order POS-20260628-0002: French Fries x2',220.00,0.00,'FRONT_DESK','2026-06-28 09:44:20'),(8,1,403,1,4,'2026-06-28','POS-CASH-4-PAY','CASH','CASH payment for POS-20260628-0002',0.00,220.00,'FRONT_DESK','2026-06-28 09:44:20'),(9,3,100,2,2,'2026-06-28','RM-2-1','NONE','Room charge for Night 1 (Date: 2026-06-28)',10000.00,0.00,'FRONT_DESK','2026-06-28 10:07:31'),(10,3,403,2,2,'2026-06-28','PAY-1782641286','CASH','Full payment',0.00,10000.00,'FRONT_DESK','2026-06-28 10:08:06'),(11,2,403,2,2,'2026-06-28','PAY-1782641477','CASH','Full payment',0.00,640.00,'FRONT_DESK','2026-06-28 10:11:17'),(12,3,200,1,4,'2026-07-03','POS-5','NONE','POS Order POS-20260703-0001: qeq x1, French Fries x2',2532.00,0.00,'FRONT_DESK','2026-07-03 14:12:34'),(13,2,200,1,4,'2026-07-03','POS-6','NONE','POS Order POS-20260703-0002: French Fries x8, qeq x1',3192.00,0.00,'FRONT_DESK','2026-07-03 14:18:34'),(14,2,200,1,4,'2026-07-04','POS-7','NONE','POS Order POS-20260704-0001: French Fries x2',220.00,0.00,'FRONT_DESK','2026-07-04 07:34:17'),(15,3,200,1,4,'2026-07-04','POS-8','NONE','POS Order POS-20260704-0002: French Fries x2',220.00,0.00,'FRONT_DESK','2026-07-04 07:34:33'),(16,2,200,1,4,'2026-07-04','POS-9','NONE','POS Order POS-20260704-0003: French Fries x2',220.00,0.00,'FRONT_DESK','2026-07-04 07:36:40'),(17,3,404,2,2,'2026-07-04','AR-1783150870','ACCOUNT_CHARGE','ade',0.00,2752.00,'FRONT_DESK','2026-07-04 07:41:10'),(18,2,403,2,2,'2026-07-11','PAY-1783748643','CASH','Folio payment',0.00,3632.00,'FRONT_DESK','2026-07-11 05:44:03'),(19,4,100,2,1,'2026-07-11','RM-3-1','NONE','Room charge for Night 1 (Date: 2026-07-11)',5000.00,0.00,'FRONT_DESK','2026-07-12 11:40:16'),(20,4,100,2,1,'2026-07-12','RM-3-2','NONE','Room charge for Night 2 (Date: 2026-07-12)',5000.00,0.00,'FRONT_DESK','2026-07-12 11:40:16');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$12$1N8LO9W6DydAalNLelyWR.SdtpQF944cVFjCGhKutD/R.ZuoZ6vTW','SoftwareAdmin',1,1),(2,'frontdesk','$2y$12$lQW9BTvPPjJLulBJKNW8L.6PZts8Q7DisHTRUvCFWKq6AgdFahRxO','Front Desk User',2,1),(3,'accounting','$2y$12$KSlHXL5GCfTb3lkO47QUSOpXtcw.vIFWlEcHvrIWdN972.ywf2dda','Cashier',3,1),(4,'cafeteria','$2y$12$au/gbvu5IEquvSzFphLGLelh8KJ2dU/B5S2Nj6RChKyNpEOpgB7c2','Cafeteria User',4,1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'hotel_don_felipe'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-14 22:48:13
