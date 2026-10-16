-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: kopi
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
-- Table structure for table `app_settings`
--

DROP TABLE IF EXISTS `app_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'string',
  `group` varchar(255) NOT NULL DEFAULT 'system',
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_settings`
--

LOCK TABLES `app_settings` WRITE;
/*!40000 ALTER TABLE `app_settings` DISABLE KEYS */;
INSERT INTO `app_settings` VALUES (1,'developer_mode','0','boolean','system','Mode Pengembang: Jika aktif (ON), seluruh produk tidak dapat dibeli dan transaksi pembelian dinonaktifkan sementara.','2026-10-15 23:20:30','2026-10-15 23:20:30');
/*!40000 ALTER TABLE `app_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Kopi Bubuk Campur','kopi-bubuk-campur','Kopi campur tradisional perpaduan biji kopi pilihan dan serelia dengan aroma khas dan gurih mantap.',1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(2,'Kopi Bubuk Robusta','kopi-bubuk-robusta','100% Kopi bubuk Robusta murni dari dataran tinggi Sulawesi Tengah dengan cita rasa nikmat dan aroma harum.',1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(3,'Kopi Gula Aren','kopi-gula-aren','Paduan kopi Robusta dan manis legit alami gula aren asli Sulawesi dalam kemasan sachet praktis.',1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(6,'Kopi Bubuk Arabika','kopi-bubuk-arabika','100% Kopi bubuk Arabika pilihan pegunungan Sulawesi dengan aroma floral, keasaman segar elegan, dan body halus.',1,'2026-10-15 23:50:36','2026-10-15 23:50:36');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consignment_audit_items`
--

DROP TABLE IF EXISTS `consignment_audit_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consignment_audit_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `consignment_audit_id` bigint(20) unsigned NOT NULL,
  `consignment_item_id` bigint(20) unsigned NOT NULL,
  `product_variant_id` bigint(20) unsigned NOT NULL,
  `stock_before` int(11) NOT NULL DEFAULT 0,
  `physical_remaining` int(11) NOT NULL DEFAULT 0,
  `quantity_sold` int(11) NOT NULL DEFAULT 0,
  `quantity_returned` int(11) NOT NULL DEFAULT 0,
  `restock_quantity` int(11) NOT NULL DEFAULT 0,
  `stock_after` int(11) NOT NULL DEFAULT 0,
  `price_per_unit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consignment_audit_items_consignment_audit_id_foreign` (`consignment_audit_id`),
  KEY `consignment_audit_items_consignment_item_id_foreign` (`consignment_item_id`),
  KEY `consignment_audit_items_product_variant_id_foreign` (`product_variant_id`),
  CONSTRAINT `consignment_audit_items_consignment_audit_id_foreign` FOREIGN KEY (`consignment_audit_id`) REFERENCES `consignment_audits` (`id`) ON DELETE CASCADE,
  CONSTRAINT `consignment_audit_items_consignment_item_id_foreign` FOREIGN KEY (`consignment_item_id`) REFERENCES `consignment_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `consignment_audit_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consignment_audit_items`
--

LOCK TABLES `consignment_audit_items` WRITE;
/*!40000 ALTER TABLE `consignment_audit_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `consignment_audit_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consignment_audits`
--

DROP TABLE IF EXISTS `consignment_audits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consignment_audits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `audit_number` varchar(50) NOT NULL,
  `consignment_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `audit_date` date NOT NULL,
  `total_sold_qty` int(11) NOT NULL DEFAULT 0,
  `total_sold_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_restock_qty` int(11) NOT NULL DEFAULT 0,
  `payment_status` varchar(30) NOT NULL DEFAULT 'paid',
  `payment_method` varchar(30) NOT NULL DEFAULT 'cash',
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `consignment_audits_audit_number_unique` (`audit_number`),
  KEY `consignment_audits_consignment_id_foreign` (`consignment_id`),
  KEY `consignment_audits_customer_id_foreign` (`customer_id`),
  KEY `consignment_audits_user_id_foreign` (`user_id`),
  KEY `consignment_audits_order_id_foreign` (`order_id`),
  KEY `consignment_audits_audit_date_index` (`audit_date`),
  KEY `consignment_audits_payment_status_index` (`payment_status`),
  KEY `consignment_audits_payment_method_index` (`payment_method`),
  CONSTRAINT `consignment_audits_consignment_id_foreign` FOREIGN KEY (`consignment_id`) REFERENCES `consignments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `consignment_audits_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `consignment_audits_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `consignment_audits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consignment_audits`
--

LOCK TABLES `consignment_audits` WRITE;
/*!40000 ALTER TABLE `consignment_audits` DISABLE KEYS */;
/*!40000 ALTER TABLE `consignment_audits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consignment_items`
--

DROP TABLE IF EXISTS `consignment_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consignment_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `consignment_id` bigint(20) unsigned NOT NULL,
  `product_variant_id` bigint(20) unsigned NOT NULL,
  `initial_quantity` int(11) NOT NULL DEFAULT 0,
  `current_quantity` int(11) NOT NULL DEFAULT 0,
  `sold_quantity` int(11) NOT NULL DEFAULT 0,
  `returned_quantity` int(11) NOT NULL DEFAULT 0,
  `selling_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consignment_items_consignment_id_foreign` (`consignment_id`),
  KEY `consignment_items_product_variant_id_foreign` (`product_variant_id`),
  CONSTRAINT `consignment_items_consignment_id_foreign` FOREIGN KEY (`consignment_id`) REFERENCES `consignments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `consignment_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consignment_items`
--

LOCK TABLES `consignment_items` WRITE;
/*!40000 ALTER TABLE `consignment_items` DISABLE KEYS */;
INSERT INTO `consignment_items` VALUES (1,1,1,15,10,5,0,7500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(2,1,2,20,13,7,0,37500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(3,1,3,25,17,8,0,1200.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(4,1,4,30,20,10,0,12000.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(5,1,5,35,23,12,0,1500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(6,1,6,15,10,5,0,15000.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(7,1,7,20,13,7,0,2500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(8,1,8,25,17,8,0,20000.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(9,2,1,20,13,7,0,7500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(10,2,2,25,17,8,0,37500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(11,2,3,30,20,10,0,1200.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(12,2,4,35,23,12,0,12000.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(13,2,5,15,10,5,0,1500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(14,2,6,20,13,7,0,15000.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(15,2,7,25,17,8,0,2500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(16,2,8,30,20,10,0,20000.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(17,3,1,25,17,8,0,7500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(18,3,2,30,20,10,0,37500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(19,3,3,35,23,12,0,1200.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(20,3,4,15,10,5,0,12000.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(21,3,5,20,13,7,0,1500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(22,3,6,25,17,8,0,15000.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(23,3,7,30,20,10,0,2500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(24,3,8,35,23,12,0,20000.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(25,4,1,30,20,10,0,7500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(26,4,2,35,23,12,0,37500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(27,4,3,15,10,5,0,1200.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(28,4,4,20,13,7,0,12000.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(29,4,5,25,17,8,0,1500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(30,4,6,30,20,10,0,15000.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(31,4,7,35,23,12,0,2500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(32,4,8,15,10,5,0,20000.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(33,5,1,35,23,12,0,7500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(34,5,2,15,10,5,0,37500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(35,5,3,20,13,7,0,1200.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(36,5,4,25,17,8,0,12000.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(37,5,5,30,20,10,0,1500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(38,5,6,35,23,12,0,15000.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(39,5,7,15,10,5,0,2500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(40,5,8,20,13,7,0,20000.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(41,6,1,15,10,5,0,7500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(42,6,2,20,13,7,0,37500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(43,6,3,25,17,8,0,1200.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(44,6,4,30,20,10,0,12000.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(45,6,5,35,23,12,0,1500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(46,6,6,15,10,5,0,15000.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(47,6,7,20,13,7,0,2500.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30'),(48,6,8,25,17,8,0,20000.00,'Display Rak Utama','2026-10-15 23:20:30','2026-10-15 23:20:30');
/*!40000 ALTER TABLE `consignment_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consignments`
--

DROP TABLE IF EXISTS `consignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `consignment_number` varchar(50) NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `sales_user_id` bigint(20) unsigned DEFAULT NULL,
  `created_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `delivery_date` date NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'active',
  `total_items_count` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `consignments_consignment_number_unique` (`consignment_number`),
  KEY `consignments_customer_id_foreign` (`customer_id`),
  KEY `consignments_sales_user_id_foreign` (`sales_user_id`),
  KEY `consignments_created_by_user_id_foreign` (`created_by_user_id`),
  KEY `consignments_delivery_date_index` (`delivery_date`),
  KEY `consignments_status_index` (`status`),
  CONSTRAINT `consignments_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `consignments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `consignments_sales_user_id_foreign` FOREIGN KEY (`sales_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consignments`
--

LOCK TABLES `consignments` WRITE;
/*!40000 ALTER TABLE `consignments` DISABLE KEYS */;
INSERT INTO `consignments` VALUES (1,'KS-INIT-0001',1,3,3,'2026-10-06','active',123,'Penempatan rak display awal kopi kemasan Alfamart Hayam Wuruk Denpasar','2026-10-15 23:20:30','2026-10-15 23:20:30'),(2,'KS-INIT-0002',2,3,3,'2026-10-06','active',133,'Penempatan rak display awal kopi kemasan Alfamart Teuku Umar Barat','2026-10-15 23:20:30','2026-10-15 23:20:30'),(3,'KS-INIT-0003',3,3,3,'2026-10-06','active',143,'Penempatan rak display awal kopi kemasan Indomaret Sudirman Central','2026-10-15 23:20:30','2026-10-15 23:20:30'),(4,'KS-INIT-0004',4,3,3,'2026-10-06','active',136,'Penempatan rak display awal kopi kemasan Indomaret Gatot Subroto Timur','2026-10-15 23:20:30','2026-10-15 23:20:30'),(5,'KS-INIT-0005',5,3,3,'2026-10-06','active',129,'Penempatan rak display awal kopi kemasan Toko Kelontong Berkah Jaya','2026-10-15 23:20:30','2026-10-15 23:20:30'),(6,'KS-INIT-0006',6,3,3,'2026-10-06','active',123,'Penempatan rak display awal kopi kemasan Kafe Kopi Senja Sunset','2026-10-15 23:20:30','2026-10-15 23:20:30');
/*!40000 ALTER TABLE `consignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `customer_type` varchar(30) NOT NULL DEFAULT 'kafe',
  `store_chain` varchar(50) DEFAULT NULL,
  `business_name` varchar(255) NOT NULL,
  `pic_name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `qr_token` varchar(64) DEFAULT NULL,
  `sales_user_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `credit_limit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_terms_days` int(11) NOT NULL DEFAULT 0,
  `min_stock_alert` int(11) NOT NULL DEFAULT 5,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_code_unique` (`code`),
  UNIQUE KEY `customers_qr_token_unique` (`qr_token`),
  KEY `customers_sales_user_id_foreign` (`sales_user_id`),
  KEY `customers_user_id_foreign` (`user_id`),
  KEY `customers_customer_type_index` (`customer_type`),
  KEY `customers_is_active_index` (`is_active`),
  KEY `customers_store_chain_index` (`store_chain`),
  CONSTRAINT `customers_sales_user_id_foreign` FOREIGN KEY (`sales_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `customers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'CUST-ALFA-001','konsinyasi','alfamart','Alfamart Hayam Wuruk Denpasar','Made Hendra (Kepala Toko)','081234111222','alfa.hayamwuruk@retail.id','Jl. Hayam Wuruk No. 88, Denpasar Timur','Jl. Hayam Wuruk No. 88, Denpasar Timur, Bali',-8.6534000,115.2285000,'QR-ALFA-HW-001',3,NULL,5000000.00,14,10,NULL,1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(2,'CUST-ALFA-002','konsinyasi','alfamart','Alfamart Teuku Umar Barat','Ketut Sukarma (Store Manager)','081234333444','alfa.teukuumar@retail.id','Jl. Teuku Umar Barat No. 120, Denpasar Barat','Jl. Teuku Umar Barat No. 120, Denpasar Barat, Bali',-8.6745000,115.2078000,'QR-ALFA-TU-002',3,NULL,6000000.00,14,8,NULL,1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(3,'CUST-INDO-001','konsinyasi','indomaret','Indomaret Sudirman Central','Agus Pratama (Kepala Toko)','081234555666','indo.sudirman@retail.id','Jl. PB Sudirman No. 45, Denpasar','Jl. PB Sudirman No. 45, Denpasar, Bali',-8.6621000,115.2198000,'QR-INDO-SDR-001',3,NULL,8000000.00,14,12,NULL,1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(4,'CUST-INDO-002','konsinyasi','indomaret','Indomaret Gatot Subroto Timur','Wayan Juniarta (Supervisor)','081234777888','indo.gatsu@retail.id','Jl. Gatot Subroto Timur No. 210, Denpasar','Jl. Gatot Subroto Timur No. 210, Denpasar, Bali',-8.6389000,115.2154000,'QR-INDO-GTS-002',3,NULL,5000000.00,14,6,NULL,1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(5,'CUST-TOKO-001','toko','kelontong','Toko Kelontong Berkah Jaya','Ibu Rahayu','081234999000','toko.berkahjaya@gmail.com','Jl. Tukad Yeh Aya No. 34, Panjer, Denpasar','Jl. Tukad Yeh Aya No. 34, Panjer, Denpasar',-8.6792000,115.2341000,'QR-TOKO-BRK-001',3,NULL,3000000.00,7,4,NULL,1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(6,'CUST-KAFE-001','kafe','kafe','Kafe Kopi Senja Sunset','Bli Arya (Owner Kafe)','081234222333','kopisenja.bali@gmail.com','Jl. Sunset Road No. 89, Kuta, Badung','Jl. Sunset Road No. 89, Kuta, Badung, Bali',-8.7052000,115.1789000,'QR-KAFE-SNJ-001',3,NULL,7000000.00,30,5,NULL,1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(63,'CUST-MIDI-001','konsinyasi','alfamidi','Alfamidi Super Sam Ratulangi Palu','Bambang Irawan (Store Manager)','081242334455','midi.samratulangi@alfamidiku.com','Jl. Sam Ratulangi No. 88, Palu, Sulawesi Tengah','Jl. Sam Ratulangi No. 88, Palu, Sulawesi Tengah',-0.8924000,119.8732000,'QR-MIDI-SR-001',3,NULL,15000000.00,30,10,NULL,1,'2026-10-15 23:50:36','2026-10-15 23:50:36'),(64,'CUST-MIDI-002','konsinyasi','alfamidi','Alfamidi Diponegoro Palu','Dewi Sartika (Kepala Toko)','081242334456','midi.diponegoro@alfamidiku.com','Jl. Diponegoro No. 45, Palu, Sulawesi Tengah','Jl. Diponegoro No. 45, Palu, Sulawesi Tengah',-0.8978000,119.8654000,'QR-MIDI-DP-002',3,NULL,12000000.00,30,10,NULL,1,'2026-10-15 23:50:36','2026-10-15 23:50:36');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_order_items`
--

DROP TABLE IF EXISTS `delivery_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_order_id` bigint(20) unsigned NOT NULL,
  `order_item_id` bigint(20) unsigned DEFAULT NULL,
  `product_variant_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `package_info` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_order_items_delivery_order_id_foreign` (`delivery_order_id`),
  KEY `delivery_order_items_order_item_id_foreign` (`order_item_id`),
  KEY `delivery_order_items_product_variant_id_foreign` (`product_variant_id`),
  CONSTRAINT `delivery_order_items_delivery_order_id_foreign` FOREIGN KEY (`delivery_order_id`) REFERENCES `delivery_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `delivery_order_items_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `delivery_order_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_order_items`
--

LOCK TABLES `delivery_order_items` WRITE;
/*!40000 ALTER TABLE `delivery_order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_orders`
--

DROP TABLE IF EXISTS `delivery_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_number` varchar(50) NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `delivery_date` date NOT NULL,
  `courier_type` varchar(50) NOT NULL DEFAULT 'internal',
  `courier_name` varchar(100) DEFAULT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `driver_phone` varchar(50) DEFAULT NULL,
  `vehicle_plate_number` varchar(30) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `shipping_address` text NOT NULL,
  `recipient_name` varchar(255) NOT NULL,
  `recipient_phone` varchar(255) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'processing',
  `received_by` varchar(100) DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_orders_delivery_number_unique` (`delivery_number`),
  KEY `delivery_orders_order_id_foreign` (`order_id`),
  KEY `delivery_orders_customer_id_foreign` (`customer_id`),
  KEY `delivery_orders_user_id_foreign` (`user_id`),
  KEY `delivery_orders_delivery_date_index` (`delivery_date`),
  KEY `delivery_orders_courier_type_index` (`courier_type`),
  KEY `delivery_orders_tracking_number_index` (`tracking_number`),
  KEY `delivery_orders_status_index` (`status`),
  CONSTRAINT `delivery_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `delivery_orders_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `delivery_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_orders`
--

LOCK TABLES `delivery_orders` WRITE;
/*!40000 ALTER TABLE `delivery_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_orders` ENABLE KEYS */;
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
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `remaining_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` varchar(30) NOT NULL DEFAULT 'unpaid',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  KEY `invoices_order_id_foreign` (`order_id`),
  KEY `invoices_customer_id_foreign` (`customer_id`),
  KEY `invoices_user_id_foreign` (`user_id`),
  KEY `invoices_invoice_date_index` (`invoice_date`),
  KEY `invoices_due_date_index` (`due_date`),
  KEY `invoices_status_index` (`status`),
  CONSTRAINT `invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (1,'INV-OVERDUE-001',1,6,3,'2026-09-11','2026-10-04',1850000.00,0.00,1850000.00,'unpaid','Menunggak 12 hari. Perlu follow up segera oleh sales.','2026-10-15 23:20:31','2026-10-15 23:20:31');
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2026_10_11_000001_create_categories_table',1),(6,'2026_10_11_000002_create_products_table',1),(7,'2026_10_11_000003_create_product_variants_table',1),(8,'2026_10_11_000004_create_suppliers_table',1),(9,'2026_10_11_000005_create_customers_table',1),(10,'2026_10_11_000006_create_purchases_table',1),(11,'2026_10_11_000007_create_purchase_items_table',1),(12,'2026_10_11_000008_create_stock_movements_table',1),(13,'2026_10_11_000009_create_orders_table',1),(14,'2026_10_11_000010_create_order_items_table',1),(15,'2026_10_11_000011_create_invoices_table',1),(16,'2026_10_11_000012_create_payments_table',1),(17,'2026_10_11_000013_create_delivery_orders_table',1),(18,'2026_10_12_000001_create_consignments_tables',1),(19,'2026_10_12_000002_create_app_settings_table',1),(20,'2026_10_12_000003_add_tier_pricing_to_product_variants_table',1),(21,'2026_10_16_000001_add_geo_and_qr_to_customers_table',1),(22,'2026_10_16_000002_create_store_visits_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `product_variant_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `cost_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_variant_id_foreign` (`product_variant_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL,
  `order_type` varchar(20) NOT NULL DEFAULT 'b2b',
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `sales_user_id` bigint(20) unsigned DEFAULT NULL,
  `order_date` date NOT NULL,
  `payment_type` varchar(30) NOT NULL DEFAULT 'cash',
  `due_date` date DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `payment_status` varchar(30) NOT NULL DEFAULT 'unpaid',
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `shipping_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `shipping_address` text DEFAULT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `recipient_phone` varchar(255) DEFAULT NULL,
  `stock_reduced` tinyint(1) NOT NULL DEFAULT 0,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `confirmed_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_customer_id_foreign` (`customer_id`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_sales_user_id_foreign` (`sales_user_id`),
  KEY `orders_confirmed_by_user_id_foreign` (`confirmed_by_user_id`),
  KEY `orders_order_type_index` (`order_type`),
  KEY `orders_order_date_index` (`order_date`),
  KEY `orders_payment_type_index` (`payment_type`),
  KEY `orders_due_date_index` (`due_date`),
  KEY `orders_status_index` (`status`),
  KEY `orders_payment_status_index` (`payment_status`),
  KEY `orders_stock_reduced_index` (`stock_reduced`),
  CONSTRAINT `orders_confirmed_by_user_id_foreign` FOREIGN KEY (`confirmed_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_sales_user_id_foreign` FOREIGN KEY (`sales_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'ORD-TEMPO-OVERDUE-01','b2b',6,3,NULL,'2026-09-11','credit',NULL,'confirmed','unpaid',1850000.00,0.00,0.00,1850000.00,0.00,'Jl. Sunset Road No. 89, Kuta, Badung, Bali',NULL,NULL,0,NULL,NULL,'Pesanan B2B Grosir Kopi Kafe - Tempo 14 Hari','2026-10-15 23:20:31','2026-10-15 23:20:31');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_number` varchar(50) NOT NULL,
  `invoice_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(30) NOT NULL DEFAULT 'transfer',
  `amount` decimal(15,2) NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_payment_number_unique` (`payment_number`),
  KEY `payments_invoice_id_foreign` (`invoice_id`),
  KEY `payments_order_id_foreign` (`order_id`),
  KEY `payments_customer_id_foreign` (`customer_id`),
  KEY `payments_user_id_foreign` (`user_id`),
  KEY `payments_payment_date_index` (`payment_date`),
  KEY `payments_payment_method_index` (`payment_method`),
  CONSTRAINT `payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_variants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `sku` varchar(255) NOT NULL,
  `variant_name` varchar(255) NOT NULL,
  `package_weight_grams` int(11) NOT NULL DEFAULT 250,
  `grind_form` varchar(255) NOT NULL DEFAULT 'Biji Kopi',
  `barcode` varchar(255) DEFAULT NULL,
  `cost_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(15,2) NOT NULL,
  `price_toko` decimal(15,2) DEFAULT NULL,
  `price_kafe` decimal(15,2) DEFAULT NULL,
  `tier_pricing` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tier_pricing`)),
  `current_stock` int(11) NOT NULL DEFAULT 0,
  `minimum_stock` int(11) NOT NULL DEFAULT 5,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_variants_sku_unique` (`sku`),
  KEY `product_variants_product_id_foreign` (`product_id`),
  KEY `product_variants_barcode_index` (`barcode`),
  KEY `product_variants_is_active_index` (`is_active`),
  CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variants`
--

LOCK TABLES `product_variants` WRITE;
/*!40000 ALTER TABLE `product_variants` DISABLE KEYS */;
INSERT INTO `product_variants` VALUES (1,1,'SKU-AM-135G','Kopi ATAPMERAH 135g (Bungkus)',135,'Bubuk Halus',NULL,5000.00,7500.00,7300.00,7000.00,'{\"tier_10_59\":7300,\"tier_60_99\":7250,\"tier_100_up\":7000}',150,20,1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(2,1,'SKU-AM-135G-PAK5','Kopi ATAPMERAH 135g (Pak Isi 5 Bungkus)',675,'Bubuk Halus',NULL,24000.00,37500.00,36500.00,35000.00,'{\"tier_10_59\":36500,\"tier_60_99\":36250,\"tier_100_up\":35000}',60,10,1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(3,2,'SKU-BH-10G-SCT','Robusta Bintang Harapan 10g (Sachet)',10,'Bubuk Halus',NULL,600.00,1200.00,900.00,850.00,NULL,300,50,1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(4,2,'SKU-BH-10G-RCG','Robusta Bintang Harapan 10g (Renceng 10 Sachet)',100,'Bubuk Halus',NULL,6000.00,12000.00,9000.00,8500.00,NULL,120,20,1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(5,3,'SKU-BS-20G-SCT','Kopi Gula Aren 20g (Sachet)',20,'Bubuk Kopi Aren',NULL,700.00,1500.00,1100.00,1000.00,NULL,250,40,1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(6,3,'SKU-BS-20G-RCG','Kopi Gula Aren 20g (Pak Isi 15 Sachet)',300,'Bubuk Kopi Aren',NULL,10000.00,15000.00,14625.00,13500.00,'{\"tier_10_59\":14625,\"tier_60_99\":14250,\"tier_100_up\":13500}',90,15,1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(7,4,'SKU-AM-35G-BKS','Kopi ATAPMERAH 35g (Bungkus)',35,'Bubuk Halus',NULL,1200.00,2500.00,2000.00,1900.00,NULL,200,30,1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(8,4,'SKU-AM-35G-PAK10','Kopi ATAPMERAH 35g (Pak Isi 10 Bungkus)',350,'Bubuk Halus',NULL,13000.00,20000.00,19500.00,19000.00,'{\"tier_10_59\":19500,\"tier_60_99\":19250,\"tier_100_up\":19000}',80,15,1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(77,9,'SKU-BH-ROB-50G','Kopi Robusta Bintang Harapan 50g (Bungkus)',50,'Bubuk Halus','8994418101065',3800.00,7000.00,5675.68,5500.00,'{\"packaging_dims\":\"8,5 x 16 cm\",\"carton_volume\":\"25 Bks \\/ Pak Karton\",\"carton_dims\":\"30 x 15,5 cm\",\"price_toko_exclude_ppn\":5675.68,\"price_toko_disc_5pct\":5391.9,\"price_bal_carton\":141891.89}',200,25,1,'2026-10-15 23:50:36','2026-10-15 23:50:36'),(78,9,'SKU-BH-ROB-50G-BAL','Kopi Robusta Bintang Harapan 50g (Pak/Bal Isi 25 Bks)',1250,'Bubuk Halus','8994418101065-BAL',95000.00,175000.00,141891.89,137500.00,'{\"isi_per_bal\":25,\"carton_dims\":\"30 x 15,5 cm\",\"price_toko_exclude_ppn\":141891.89,\"price_toko_disc_5pct\":134797.3}',50,5,1,'2026-10-15 23:50:36','2026-10-15 23:50:36'),(79,9,'SKU-BH-ROB-125G','Kopi Robusta Bintang Harapan 125g (Bungkus)',125,'Bubuk Halus','8994418101713',9500.00,17500.00,14189.00,13500.00,'{\"packaging_dims\":\"20 x 15 cm\",\"carton_volume\":\"50 Bks \\/ Pak Karton\",\"carton_dims\":\"48 x 40 cm\",\"price_toko_exclude_ppn\":14189,\"price_toko_disc_5pct\":13479.55,\"price_bal_carton\":709450}',200,25,1,'2026-10-15 23:50:36','2026-10-15 23:50:36'),(80,9,'SKU-BH-ROB-125G-BAL','Kopi Robusta Bintang Harapan 125g (Pak/Bal Isi 50 Bks)',6250,'Bubuk Halus','8994418101713-BAL',475000.00,875000.00,709450.00,675000.00,'{\"isi_per_bal\":50,\"carton_dims\":\"48 x 40 cm\",\"price_toko_exclude_ppn\":709450,\"price_toko_disc_5pct\":673977.5}',40,5,1,'2026-10-15 23:50:36','2026-10-15 23:50:36'),(81,9,'SKU-BH-ROB-250G','Kopi Robusta Bintang Harapan 250g (Bungkus)',250,'Bubuk Halus','8994418101102',19000.00,35000.00,28378.00,27000.00,'{\"packaging_dims\":\"22 x 14 cm\",\"carton_volume\":\"50 Bks \\/ Pak Karton\",\"carton_dims\":\"58 x 40 cm\",\"price_toko_exclude_ppn\":28378,\"price_toko_disc_5pct\":26959.1,\"price_bal_carton\":1418900}',150,20,1,'2026-10-15 23:50:36','2026-10-15 23:50:36'),(82,9,'SKU-BH-ROB-250G-BAL','Kopi Robusta Bintang Harapan 250g (Pak/Bal Isi 50 Bks)',12500,'Bubuk Halus','8994418101102-BAL',950000.00,1750000.00,1418900.00,1350000.00,'{\"isi_per_bal\":50,\"carton_dims\":\"58 x 40 cm\",\"price_toko_exclude_ppn\":1418900,\"price_toko_disc_5pct\":1347955}',30,5,1,'2026-10-15 23:50:36','2026-10-15 23:50:36'),(83,9,'SKU-BH-ROB-400G','Kopi Robusta Bintang Harapan 400g (Bungkus)',400,'Bubuk Halus','8994418101119',29000.00,53000.00,43243.00,41000.00,'{\"packaging_dims\":\"28 x 14 cm\",\"carton_volume\":\"30 Bks \\/ Pak Karton\",\"carton_dims\":\"40 x 42 cm\",\"price_toko_exclude_ppn\":43243,\"price_toko_disc_5pct\":41080.85,\"price_bal_carton\":1297290}',120,15,1,'2026-10-15 23:50:36','2026-10-15 23:50:36'),(84,9,'SKU-BH-ROB-400G-BAL','Kopi Robusta Bintang Harapan 400g (Pak/Bal Isi 30 Bks)',12000,'Bubuk Halus','8994418101119-BAL',870000.00,1590000.00,1297290.00,1230000.00,'{\"isi_per_bal\":30,\"carton_dims\":\"40 x 42 cm\",\"price_toko_exclude_ppn\":1297290,\"price_toko_disc_5pct\":1232425.5}',25,5,1,'2026-10-15 23:50:36','2026-10-15 23:50:36'),(85,10,'SKU-BH-ARA-125G','Kopi Arabika Bintang Harapan 125g (Bungkus)',125,'Bubuk Halus','8994418101720',11000.00,21000.00,16554.00,15500.00,'{\"packaging_dims\":\"20 x 15 cm\",\"carton_volume\":\"50 Bks \\/ Pak Karton\",\"carton_dims\":\"48 x 40 cm\",\"price_toko_exclude_ppn\":16554,\"price_toko_disc_5pct\":15726.3,\"price_bal_carton\":827700}',150,20,1,'2026-10-15 23:50:36','2026-10-15 23:50:36'),(86,10,'SKU-BH-ARA-125G-BAL','Kopi Arabika Bintang Harapan 125g (Pak/Bal Isi 50 Bks)',6250,'Bubuk Halus','8994418101720-BAL',550000.00,1050000.00,827700.00,775000.00,'{\"isi_per_bal\":50,\"carton_dims\":\"48 x 40 cm\",\"price_toko_exclude_ppn\":827700,\"price_toko_disc_5pct\":786315}',30,5,1,'2026-10-15 23:50:36','2026-10-15 23:50:36'),(87,10,'SKU-BH-ARA-250G','Kopi Arabika Bintang Harapan 250g (Bungkus)',250,'Bubuk Halus','8994418101348',22000.00,42000.00,33108.00,31000.00,'{\"packaging_dims\":\"22 x 14 cm\",\"carton_volume\":\"50 Bks \\/ Pak Karton\",\"carton_dims\":\"58 x 40 cm\",\"price_toko_exclude_ppn\":33108,\"price_toko_disc_5pct\":31452.6,\"price_bal_carton\":1655400}',120,15,1,'2026-10-15 23:50:36','2026-10-15 23:50:36'),(88,10,'SKU-BH-ARA-250G-BAL','Kopi Arabika Bintang Harapan 250g (Pak/Bal Isi 50 Bks)',12500,'Bubuk Halus','8994418101348-BAL',1100000.00,2100000.00,1655400.00,1550000.00,'{\"isi_per_bal\":50,\"carton_dims\":\"58 x 40 cm\",\"price_toko_exclude_ppn\":1655400,\"price_toko_disc_5pct\":1572630}',25,5,1,'2026-10-15 23:50:36','2026-10-15 23:50:36'),(89,10,'SKU-BH-ARA-400G','Kopi Arabika Bintang Harapan 400g (Bungkus)',400,'Bubuk Halus','8994418101355',34000.00,63000.00,50450.00,47000.00,'{\"packaging_dims\":\"28 x 14 cm\",\"carton_volume\":\"30 Bks \\/ Pak Karton\",\"carton_dims\":\"40 x 42 cm\",\"price_toko_exclude_ppn\":50450,\"price_toko_disc_5pct\":47927.5,\"price_bal_carton\":1513500}',100,10,1,'2026-10-15 23:50:36','2026-10-15 23:50:36'),(90,10,'SKU-BH-ARA-400G-BAL','Kopi Arabika Bintang Harapan 400g (Pak/Bal Isi 30 Bks)',12000,'Bubuk Halus','8994418101355-BAL',1020000.00,1890000.00,1513500.00,1410000.00,'{\"isi_per_bal\":30,\"carton_dims\":\"40 x 42 cm\",\"price_toko_exclude_ppn\":1513500,\"price_toko_disc_5pct\":1437825}',20,5,1,'2026-10-15 23:50:36','2026-10-15 23:50:36');
/*!40000 ALTER TABLE `product_variants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `brand` varchar(255) NOT NULL DEFAULT 'Kopi Distro',
  `coffee_type` varchar(255) NOT NULL DEFAULT 'Arabika',
  `form` varchar(255) NOT NULL DEFAULT 'ground',
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery`)),
  `net_weight` varchar(255) DEFAULT NULL,
  `producer` varchar(255) DEFAULT NULL,
  `origin` varchar(255) DEFAULT NULL,
  `composition` text DEFAULT NULL,
  `serving_suggestion` text DEFAULT NULL,
  `storage_instruction` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_is_active_index` (`is_active`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,'Kopi ATAPMERAH 135g — Kopi Bubuk Campur','kopi-atapmerah-135g','ATAPMERAH','Blend','ground','Nikmati kopi ATAPMERAH, kopi campur produksi PT Kopi Bintang Harapan, Palu, Sulawesi Tengah. Dikemas dalam kemasan 135 gram untuk menemani waktu santai maupun aktivitas sehari-hari.','images/products/01_atapmerah_135g_depan_edit.png','[\"images\\/products\\/02_atapmerah_135g_belakang_edit.png\",\"images\\/products\\/03_atapmerah_135g_belakang_detail_edit.png\",\"images\\/products\\/08_atapmerah_135g_belakang_instruksi_edit.png\"]','135 gram','PT Kopi Bintang Harapan','Palu, Sulawesi Tengah, Indonesia','60% kopi bubuk dan serelia','Masukkan kopi ke dalam cangkir, tambahkan gula atau krimer sesuai selera, tuangkan 200 ml air mendidih, lalu aduk hingga rata.','Simpan di tempat yang kering dan sejuk.',1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(2,2,'Kopi Bubuk Robusta Bintang Harapan 10g','kopi-bubuk-robusta-bintang-harapan-10g','Bintang Harapan','Robusta','ground','Kopi Bubuk Robusta Bintang Harapan hadir dalam sachet praktis 10 gram. Pada kemasan, produk ini diperkenalkan sebagai kopi dari dataran tinggi Sulawesi Tengah dengan aroma khas dan cita rasa gurih serta nikmat.','images/products/04_bintang_harapan_robusta_10g_depan_edit.png','[\"images\\/products\\/05_bintang_harapan_robusta_belakang_edit.png\"]','10 gram','Bintang Harapan, Palu, Indonesia','Palu, Sulawesi Tengah, Indonesia','100% kopi bubuk Robusta','Masukkan satu sachet ke dalam cangkir, tuangkan air panas sesuai petunjuk pada kemasan, tambahkan gula sesuai selera bila diinginkan, lalu aduk.','Simpan di tempat yang kering dan sejuk.',1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(3,3,'Kopi Gula Aren Bintang Sulawesi 20g','kopi-gula-aren-bintang-sulawesi-20g','Bintang Sulawesi','Blend','ground','Kopi Gula Aren Bintang Sulawesi memadukan kopi Robusta dan gula aren dalam satu sachet praktis. Pilihan untuk menikmati sajian kopi dengan sentuhan manis gula aren di rumah, kantor, atau saat bepergian.','images/products/09_bintang_sulawesi_gula_aren_20g_depan_foto_asli.jpg','[\"images\\/products\\/06_bintang_sulawesi_gula_aren_20g_belakang_edit.png\"]','20 gram','Bintang Sulawesi, Palu, Sulawesi Tengah, Indonesia','Palu, Sulawesi Tengah, Indonesia','Gula aren dan kopi bubuk murni','Tuangkan satu sachet ke dalam cangkir, tambahkan 140 ml air mendidih, lalu aduk hingga larut dan tercampur rata.','Simpan di tempat yang kering dan sejuk.',1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(4,1,'Kopi Bubuk ATAPMERAH 35g — Kopi Campur','kopi-bubuk-atapmerah-35g','ATAPMERAH','Blend','ground','Kopi ATAPMERAH hadir dalam kemasan 35 gram yang praktis dan ekonomis untuk dinikmati sehari-hari. Produk kopi campur ini diproduksi oleh PT Kopi Bintang Harapan di Palu, Sulawesi Tengah, Indonesia.','images/products/07_atapmerah_35g_depan_edit.png','[]','35 gram','PT Kopi Bintang Harapan, Palu, Sulawesi Tengah, Indonesia','Palu, Sulawesi Tengah, Indonesia','60% kopi bubuk dan serelia pilihan','Masukkan kopi ke dalam cangkir, tambahkan gula atau krimer sesuai selera, tuangkan 200 ml air mendidih, lalu aduk hingga rata.','Simpan di tempat yang kering dan sejuk.',1,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(9,2,'Kopi Bubuk Robusta Bintang Harapan','kopi-bubuk-robusta-bintang-harapan','Bintang Harapan','Robusta','ground','Kopi Bubuk Robusta murni produksi PT Kopi Bintang Harapan, Palu, Sulawesi Tengah. Dibuat dari biji kopi Robusta pilihan terbaik dataran tinggi Sulawesi dengan sangrai presisi untuk menghasilkan aroma wangi yang kuat, rasa gurih mantap, dan crema tebal. Terdaftar resmi di sistem ritel modern PT Midi Utama Indonesia Tbk (Alfamidi).','images/products/04_bintang_harapan_robusta_10g_depan_edit.png','[\"images\\/products\\/05_bintang_harapan_robusta_belakang_edit.png\"]','50g, 125g, 250g, 400g','PT Kopi Bintang Harapan, Palu, Sulawesi Tengah','Palu, Sulawesi Tengah, Indonesia','100% Kopi Bubuk Robusta Murni Pilihan','Masukkan 1-2 sendok makan kopi bubuk Robusta ke dalam cangkir, tambahkan gula sesuai selera, seduh dengan 150-200 ml air mendidih (95°C), aduk rata dan nikmati aroma khasnya.','Simpan dalam wadah kedap udara di tempat yang sejuk dan kering, terhindar dari sinar matahari langsung.',1,'2026-10-15 23:50:36','2026-10-15 23:50:36'),(10,6,'Kopi Bubuk Arabika Bintang Harapan','kopi-bubuk-arabika-bintang-harapan','Bintang Harapan','Arabika','ground','Kopi Bubuk Arabika murni produksi PT Kopi Bintang Harapan, Palu, Sulawesi Tengah. Dipetik dari perkebunan dataran tinggi Sulawesi Tengah, diolah dengan proses washed basah menghasilkan profil rasa floral, fruity, keasaman lembut yang segar, dan body bersih berkelas. Terdaftar resmi di sistem ritel modern PT Midi Utama Indonesia Tbk (Alfamidi).','images/products/01_atapmerah_135g_depan_edit.png','[\"images\\/products\\/02_atapmerah_135g_belakang_edit.png\"]','125g, 250g, 400g','PT Kopi Bintang Harapan, Palu, Sulawesi Tengah','Palu, Sulawesi Tengah, Indonesia','100% Kopi Bubuk Arabika Murni Pilihan','Seduh 10-12 gram kopi bubuk Arabika dengan air mendidih bersuhu 90-93°C sebanyak 150 ml. Nikmati aroma bunga dan buah segar serta aftertaste manis bersih tanpa ampas pahit.','Simpan di tempat yang sejuk, kering, dan terhindar dari panas berlebih.',1,'2026-10-15 23:50:36','2026-10-15 23:50:36');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_items`
--

DROP TABLE IF EXISTS `purchase_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint(20) unsigned NOT NULL,
  `product_variant_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `cost_price` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_items_purchase_id_foreign` (`purchase_id`),
  KEY `purchase_items_product_variant_id_foreign` (`product_variant_id`),
  CONSTRAINT `purchase_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`),
  CONSTRAINT `purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_items`
--

LOCK TABLES `purchase_items` WRITE;
/*!40000 ALTER TABLE `purchase_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_number` varchar(255) NOT NULL,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `purchase_date` date NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `shipping_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `received_at` timestamp NULL DEFAULT NULL,
  `received_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchases_purchase_number_unique` (`purchase_number`),
  KEY `purchases_supplier_id_foreign` (`supplier_id`),
  KEY `purchases_user_id_foreign` (`user_id`),
  KEY `purchases_received_by_user_id_foreign` (`received_by_user_id`),
  KEY `purchases_status_index` (`status`),
  CONSTRAINT `purchases_received_by_user_id_foreign` FOREIGN KEY (`received_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `purchases_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchases`
--

LOCK TABLES `purchases` WRITE;
/*!40000 ALTER TABLE `purchases` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_movements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_variant_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `reference_type` varchar(50) NOT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `stock_before` int(11) NOT NULL,
  `stock_after` int(11) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_movements_product_variant_id_foreign` (`product_variant_id`),
  KEY `stock_movements_user_id_foreign` (`user_id`),
  KEY `stock_movements_type_index` (`type`),
  KEY `stock_movements_reference_type_index` (`reference_type`),
  KEY `stock_movements_reference_id_index` (`reference_id`),
  CONSTRAINT `stock_movements_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
INSERT INTO `stock_movements` VALUES (1,1,4,'in','initial_stock',NULL,150,0,150,'Saldo Awal Stok Fisik Gudang — SKU SKU-AM-135G (Kopi ATAPMERAH 135g (Bungkus))','2026-10-15 23:20:30','2026-10-15 23:20:30'),(2,2,4,'in','initial_stock',NULL,60,0,60,'Saldo Awal Stok Fisik Gudang — SKU SKU-AM-135G-PAK5 (Kopi ATAPMERAH 135g (Pak Isi 5 Bungkus))','2026-10-15 23:20:30','2026-10-15 23:20:30'),(3,3,4,'in','initial_stock',NULL,300,0,300,'Saldo Awal Stok Fisik Gudang — SKU SKU-BH-10G-SCT (Robusta Bintang Harapan 10g (Sachet))','2026-10-15 23:20:30','2026-10-15 23:20:30'),(4,4,4,'in','initial_stock',NULL,120,0,120,'Saldo Awal Stok Fisik Gudang — SKU SKU-BH-10G-RCG (Robusta Bintang Harapan 10g (Renceng 10 Sachet))','2026-10-15 23:20:30','2026-10-15 23:20:30'),(5,5,4,'in','initial_stock',NULL,250,0,250,'Saldo Awal Stok Fisik Gudang — SKU SKU-BS-20G-SCT (Kopi Gula Aren 20g (Sachet))','2026-10-15 23:20:30','2026-10-15 23:20:30'),(6,6,4,'in','initial_stock',NULL,90,0,90,'Saldo Awal Stok Fisik Gudang — SKU SKU-BS-20G-RCG (Kopi Gula Aren 20g (Pak Isi 15 Sachet))','2026-10-15 23:20:30','2026-10-15 23:20:30'),(7,7,4,'in','initial_stock',NULL,200,0,200,'Saldo Awal Stok Fisik Gudang — SKU SKU-AM-35G-BKS (Kopi ATAPMERAH 35g (Bungkus))','2026-10-15 23:20:30','2026-10-15 23:20:30'),(8,8,4,'in','initial_stock',NULL,80,0,80,'Saldo Awal Stok Fisik Gudang — SKU SKU-AM-35G-PAK10 (Kopi ATAPMERAH 35g (Pak Isi 10 Bungkus))','2026-10-15 23:20:30','2026-10-15 23:20:30'),(19,77,4,'in','initial_stock',NULL,200,0,200,'Stok Awal Resmi PT Kopi Bintang Harapan — Kopi Robusta Bintang Harapan 50g (Bungkus)','2026-10-15 23:50:36','2026-10-15 23:50:36'),(20,78,4,'in','initial_stock',NULL,50,0,50,'Stok Awal Resmi PT Kopi Bintang Harapan — Kopi Robusta Bintang Harapan 50g (Pak/Bal Isi 25 Bks)','2026-10-15 23:50:36','2026-10-15 23:50:36'),(21,79,4,'in','initial_stock',NULL,200,0,200,'Stok Awal Resmi PT Kopi Bintang Harapan — Kopi Robusta Bintang Harapan 125g (Bungkus)','2026-10-15 23:50:36','2026-10-15 23:50:36'),(22,80,4,'in','initial_stock',NULL,40,0,40,'Stok Awal Resmi PT Kopi Bintang Harapan — Kopi Robusta Bintang Harapan 125g (Pak/Bal Isi 50 Bks)','2026-10-15 23:50:36','2026-10-15 23:50:36'),(23,81,4,'in','initial_stock',NULL,150,0,150,'Stok Awal Resmi PT Kopi Bintang Harapan — Kopi Robusta Bintang Harapan 250g (Bungkus)','2026-10-15 23:50:36','2026-10-15 23:50:36'),(24,82,4,'in','initial_stock',NULL,30,0,30,'Stok Awal Resmi PT Kopi Bintang Harapan — Kopi Robusta Bintang Harapan 250g (Pak/Bal Isi 50 Bks)','2026-10-15 23:50:36','2026-10-15 23:50:36'),(25,83,4,'in','initial_stock',NULL,120,0,120,'Stok Awal Resmi PT Kopi Bintang Harapan — Kopi Robusta Bintang Harapan 400g (Bungkus)','2026-10-15 23:50:36','2026-10-15 23:50:36'),(26,84,4,'in','initial_stock',NULL,25,0,25,'Stok Awal Resmi PT Kopi Bintang Harapan — Kopi Robusta Bintang Harapan 400g (Pak/Bal Isi 30 Bks)','2026-10-15 23:50:36','2026-10-15 23:50:36'),(27,85,4,'in','initial_stock',NULL,150,0,150,'Stok Awal Resmi PT Kopi Bintang Harapan — Kopi Arabika Bintang Harapan 125g (Bungkus)','2026-10-15 23:50:36','2026-10-15 23:50:36'),(28,86,4,'in','initial_stock',NULL,30,0,30,'Stok Awal Resmi PT Kopi Bintang Harapan — Kopi Arabika Bintang Harapan 125g (Pak/Bal Isi 50 Bks)','2026-10-15 23:50:36','2026-10-15 23:50:36'),(29,87,4,'in','initial_stock',NULL,120,0,120,'Stok Awal Resmi PT Kopi Bintang Harapan — Kopi Arabika Bintang Harapan 250g (Bungkus)','2026-10-15 23:50:36','2026-10-15 23:50:36'),(30,88,4,'in','initial_stock',NULL,25,0,25,'Stok Awal Resmi PT Kopi Bintang Harapan — Kopi Arabika Bintang Harapan 250g (Pak/Bal Isi 50 Bks)','2026-10-15 23:50:36','2026-10-15 23:50:36'),(31,89,4,'in','initial_stock',NULL,100,0,100,'Stok Awal Resmi PT Kopi Bintang Harapan — Kopi Arabika Bintang Harapan 400g (Bungkus)','2026-10-15 23:50:36','2026-10-15 23:50:36'),(32,90,4,'in','initial_stock',NULL,20,0,20,'Stok Awal Resmi PT Kopi Bintang Harapan — Kopi Arabika Bintang Harapan 400g (Pak/Bal Isi 30 Bks)','2026-10-15 23:50:36','2026-10-15 23:50:36');
/*!40000 ALTER TABLE `stock_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_visits`
--

DROP TABLE IF EXISTS `store_visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `store_visits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `visit_number` varchar(50) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `visit_date` date NOT NULL,
  `check_in_at` datetime NOT NULL,
  `check_out_at` datetime DEFAULT NULL,
  `check_in_lat` decimal(10,7) DEFAULT NULL,
  `check_in_lng` decimal(10,7) DEFAULT NULL,
  `distance_meters` int(11) DEFAULT NULL,
  `is_geofence_valid` tinyint(1) NOT NULL DEFAULT 1,
  `visit_purpose` varchar(50) NOT NULL DEFAULT 'audit_delivery',
  `status` varchar(30) NOT NULL DEFAULT 'in_progress',
  `photo_proof` varchar(255) DEFAULT NULL,
  `recipient_name` varchar(100) DEFAULT NULL,
  `recipient_phone` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `summary_sold_qty` int(11) NOT NULL DEFAULT 0,
  `summary_sold_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `summary_restock_qty` int(11) NOT NULL DEFAULT 0,
  `consignment_audit_id` bigint(20) unsigned DEFAULT NULL,
  `delivery_order_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `store_visits_visit_number_unique` (`visit_number`),
  KEY `store_visits_user_id_foreign` (`user_id`),
  KEY `store_visits_customer_id_foreign` (`customer_id`),
  KEY `store_visits_consignment_audit_id_foreign` (`consignment_audit_id`),
  KEY `store_visits_delivery_order_id_foreign` (`delivery_order_id`),
  KEY `store_visits_visit_date_index` (`visit_date`),
  KEY `store_visits_check_in_at_index` (`check_in_at`),
  KEY `store_visits_visit_purpose_index` (`visit_purpose`),
  KEY `store_visits_status_index` (`status`),
  CONSTRAINT `store_visits_consignment_audit_id_foreign` FOREIGN KEY (`consignment_audit_id`) REFERENCES `consignment_audits` (`id`) ON DELETE SET NULL,
  CONSTRAINT `store_visits_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `store_visits_delivery_order_id_foreign` FOREIGN KEY (`delivery_order_id`) REFERENCES `delivery_orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `store_visits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_visits`
--

LOCK TABLES `store_visits` WRITE;
/*!40000 ALTER TABLE `store_visits` DISABLE KEYS */;
INSERT INTO `store_visits` VALUES (1,'SV-20261016-0001',6,1,'2026-10-16','2026-10-16 09:15:00','2026-10-16 09:45:00',-8.6534120,115.2285180,18,1,'audit_delivery','completed',NULL,'Made Hendra','081234111222','Display rak tertata rapi di dekat kasir. 8 pack laku terjual. Tambah restock 10 pack baru.',8,85000.00,10,NULL,NULL,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(2,'SV-20261016-0002',6,3,'2026-10-16','2026-10-16 10:30:00','2026-10-16 11:00:00',-8.6621300,115.2198400,22,1,'audit_delivery','completed',NULL,'Agus Pratama','081234555666','Kopi Atapmerah sangat laku. Laku 12 pack. Restock 15 pack.',12,142000.00,15,NULL,NULL,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(3,'SV-20261016-0003',6,2,'2026-10-16','2026-10-16 07:05:30',NULL,-8.6745200,115.2078150,12,1,'audit','in_progress',NULL,'Ketut Sukarma','081234333444','Sopir sedang menghitung stok fisik di rak kopi.',0,0.00,0,NULL,NULL,'2026-10-15 23:20:30','2026-10-15 23:20:30');
/*!40000 ALTER TABLE `store_visits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `pic_name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_code_unique` (`code`),
  KEY `suppliers_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'SUP-KBH-01','PT Kopi Bintang Harapan','Hartono Limadhy (Pimpinan)','0451-422739','produksi@bintangharapan.co.id','Jl. R.E. Martadinata / Trans Sulawesi KM.5, Palu - Sulteng',1,'2026-10-15 23:20:30','2026-10-15 23:50:36'),(2,'SUP-BS-02','Bintang Sulawesi','Sentra Kopi Sulawesi','081398765432','distribusi@bintangsulawesi.co.id','Palu, Sulawesi Tengah, Indonesia',1,'2026-10-15 23:20:30','2026-10-15 23:20:30');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `role` varchar(30) NOT NULL DEFAULT 'customer',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=497 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Pemilik Bisnis (Owner)','owner@kopi.com','081234567890','owner',1,NULL,'$2y$10$J51nXcDlfwLesBIaUdIxB.sSg8mxTWjEQZ2cvJe5E8uRAUjU5lSeq',NULL,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(2,'Administrator Toko','admin@kopi.com','081234567891','admin',1,NULL,'$2y$10$J51nXcDlfwLesBIaUdIxB.sSg8mxTWjEQZ2cvJe5E8uRAUjU5lSeq',NULL,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(3,'Sales Representative','sales@kopi.com','081234567892','sales',1,NULL,'$2y$10$J51nXcDlfwLesBIaUdIxB.sSg8mxTWjEQZ2cvJe5E8uRAUjU5lSeq',NULL,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(4,'Staf Gudang (Warehouse)','warehouse@kopi.com','081234567893','warehouse',1,NULL,'$2y$10$J51nXcDlfwLesBIaUdIxB.sSg8mxTWjEQZ2cvJe5E8uRAUjU5lSeq',NULL,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(5,'Staf Keuangan (Finance)','finance@kopi.com','081234567894','finance',1,NULL,'$2y$10$J51nXcDlfwLesBIaUdIxB.sSg8mxTWjEQZ2cvJe5E8uRAUjU5lSeq',NULL,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(6,'Budi Santoso (Driver Armada 1)','driver@kopi.com','081234567895','driver',1,NULL,'$2y$10$J51nXcDlfwLesBIaUdIxB.sSg8mxTWjEQZ2cvJe5E8uRAUjU5lSeq',NULL,'2026-10-15 23:20:30','2026-10-15 23:20:30'),(7,'Joko Supriyadi (Driver Armada 2)','driver2@kopi.com','081234567896','driver',1,NULL,'$2y$10$J51nXcDlfwLesBIaUdIxB.sSg8mxTWjEQZ2cvJe5E8uRAUjU5lSeq',NULL,'2026-10-15 23:20:30','2026-10-15 23:20:30');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'kopi'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-16 15:51:15
