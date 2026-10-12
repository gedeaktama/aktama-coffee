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
INSERT INTO `app_settings` VALUES (1,'developer_mode','0','boolean','system','Mode Pengembang: Jika aktif (ON), seluruh produk tidak dapat dibeli dan transaksi pembelian dinonaktifkan sementara.','2026-10-11 17:03:47','2026-10-11 19:07:56');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Kopi Bubuk Campur','kopi-bubuk-campur','Kopi campur tradisional perpaduan biji kopi pilihan dan serelia dengan aroma khas dan gurih mantap.',1,'2026-10-11 16:55:19','2026-10-11 16:55:19'),(2,'Kopi Bubuk Robusta','kopi-bubuk-robusta','100% Kopi bubuk Robusta murni dari dataran tinggi Sulawesi Tengah dengan cita rasa nikmat dan aroma harum.',1,'2026-10-11 16:55:19','2026-10-11 16:55:19'),(3,'Kopi Gula Aren','kopi-gula-aren','Paduan kopi Robusta dan manis legit alami gula aren asli Sulawesi dalam kemasan sachet praktis.',1,'2026-10-11 16:55:19','2026-10-11 16:55:19');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consignment_items`
--

LOCK TABLES `consignment_items` WRITE;
/*!40000 ALTER TABLE `consignment_items` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consignments`
--

LOCK TABLES `consignments` WRITE;
/*!40000 ALTER TABLE `consignments` DISABLE KEYS */;
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
  `business_name` varchar(255) NOT NULL,
  `pic_name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `sales_user_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `credit_limit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_terms_days` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_code_unique` (`code`),
  KEY `customers_sales_user_id_foreign` (`sales_user_id`),
  KEY `customers_user_id_foreign` (`user_id`),
  KEY `customers_customer_type_index` (`customer_type`),
  KEY `customers_is_active_index` (`is_active`),
  CONSTRAINT `customers_sales_user_id_foreign` FOREIGN KEY (`sales_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `customers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2026_10_11_000001_create_categories_table',1),(6,'2026_10_11_000002_create_products_table',1),(7,'2026_10_11_000003_create_product_variants_table',1),(8,'2026_10_11_000004_create_suppliers_table',1),(9,'2026_10_11_000005_create_customers_table',1),(10,'2026_10_11_000006_create_purchases_table',1),(11,'2026_10_11_000007_create_purchase_items_table',1),(12,'2026_10_11_000008_create_stock_movements_table',1),(13,'2026_10_11_000009_create_orders_table',1),(14,'2026_10_11_000010_create_order_items_table',1),(15,'2026_10_11_000011_create_invoices_table',1),(16,'2026_10_11_000012_create_payments_table',1),(17,'2026_10_11_000013_create_delivery_orders_table',1),(18,'2026_10_12_000001_create_consignments_tables',1),(19,'2026_10_12_000002_create_app_settings_table',2),(20,'2026_10_12_000003_add_tier_pricing_to_product_variants_table',3);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variants`
--

LOCK TABLES `product_variants` WRITE;
/*!40000 ALTER TABLE `product_variants` DISABLE KEYS */;
INSERT INTO `product_variants` VALUES (1,1,'SKU-AM-135G','Kopi ATAPMERAH 135g (Bungkus)',135,'Bubuk Halus',NULL,5000.00,7500.00,7300.00,7000.00,'{\"tier_10_59\":7300,\"tier_60_99\":7250,\"tier_100_up\":7000}',150,20,1,'2026-10-11 16:55:19','2026-10-11 19:11:19'),(2,1,'SKU-AM-135G-PAK5','Kopi ATAPMERAH 135g (Pak Isi 5 Bungkus)',675,'Bubuk Halus',NULL,24000.00,37500.00,36500.00,35000.00,'{\"tier_10_59\":36500,\"tier_60_99\":36250,\"tier_100_up\":35000}',60,10,1,'2026-10-11 16:55:19','2026-10-11 19:11:19'),(3,2,'SKU-BH-10G-SCT','Robusta Bintang Harapan 10g (Sachet)',10,'Bubuk Halus',NULL,600.00,1200.00,900.00,850.00,NULL,300,50,1,'2026-10-11 16:55:19','2026-10-11 19:11:19'),(4,2,'SKU-BH-10G-RCG','Robusta Bintang Harapan 10g (Renceng 10 Sachet)',100,'Bubuk Halus',NULL,6000.00,12000.00,9000.00,8500.00,NULL,120,20,1,'2026-10-11 16:55:19','2026-10-11 19:11:19'),(5,3,'SKU-BS-20G-SCT','Kopi Gula Aren 20g (Sachet)',20,'Bubuk Kopi Aren',NULL,700.00,1500.00,1100.00,1000.00,NULL,250,40,1,'2026-10-11 16:55:19','2026-10-11 19:11:19'),(6,3,'SKU-BS-20G-RCG','Kopi Gula Aren 20g (Pak Isi 15 Sachet)',300,'Bubuk Kopi Aren',NULL,10000.00,15000.00,14625.00,13500.00,'{\"tier_10_59\":14625,\"tier_60_99\":14250,\"tier_100_up\":13500}',90,15,1,'2026-10-11 16:55:19','2026-10-11 19:11:19'),(7,4,'SKU-AM-35G-BKS','Kopi ATAPMERAH 35g (Bungkus)',35,'Bubuk Halus',NULL,1200.00,2500.00,2000.00,1900.00,NULL,200,30,1,'2026-10-11 16:55:19','2026-10-11 19:11:19'),(8,4,'SKU-AM-35G-PAK10','Kopi ATAPMERAH 35g (Pak Isi 10 Bungkus)',350,'Bubuk Halus',NULL,13000.00,20000.00,19500.00,19000.00,'{\"tier_10_59\":19500,\"tier_60_99\":19250,\"tier_100_up\":19000}',80,15,1,'2026-10-11 16:55:19','2026-10-11 19:11:19');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,'Kopi ATAPMERAH 135g — Kopi Bubuk Campur','kopi-atapmerah-135g','ATAPMERAH','Blend','ground','Nikmati kopi ATAPMERAH, kopi campur produksi PT Kopi Bintang Harapan, Palu, Sulawesi Tengah. Dikemas dalam kemasan 135 gram untuk menemani waktu santai maupun aktivitas sehari-hari.','images/products/01_atapmerah_135g_depan_edit.png','[\"images\\/products\\/02_atapmerah_135g_belakang_edit.png\",\"images\\/products\\/03_atapmerah_135g_belakang_detail_edit.png\",\"images\\/products\\/08_atapmerah_135g_belakang_instruksi_edit.png\"]','135 gram','PT Kopi Bintang Harapan','Palu, Sulawesi Tengah, Indonesia','60% kopi bubuk dan serelia','Masukkan kopi ke dalam cangkir, tambahkan gula atau krimer sesuai selera, tuangkan 200 ml air mendidih, lalu aduk hingga rata.','Simpan di tempat yang kering dan sejuk.',1,'2026-10-11 16:55:19','2026-10-11 16:55:19'),(2,2,'Kopi Bubuk Robusta Bintang Harapan 10g','kopi-bubuk-robusta-bintang-harapan-10g','Bintang Harapan','Robusta','ground','Kopi Bubuk Robusta Bintang Harapan hadir dalam sachet praktis 10 gram. Pada kemasan, produk ini diperkenalkan sebagai kopi dari dataran tinggi Sulawesi Tengah dengan aroma khas dan cita rasa gurih serta nikmat.','images/products/04_bintang_harapan_robusta_10g_depan_edit.png','[\"images\\/products\\/05_bintang_harapan_robusta_belakang_edit.png\"]','10 gram','Bintang Harapan, Palu, Indonesia','Palu, Sulawesi Tengah, Indonesia','100% kopi bubuk Robusta','Masukkan satu sachet ke dalam cangkir, tuangkan air panas sesuai petunjuk pada kemasan, tambahkan gula sesuai selera bila diinginkan, lalu aduk.','Simpan di tempat yang kering dan sejuk.',1,'2026-10-11 16:55:19','2026-10-11 16:55:19'),(3,3,'Kopi Gula Aren Bintang Sulawesi 20g','kopi-gula-aren-bintang-sulawesi-20g','Bintang Sulawesi','Blend','ground','Kopi Gula Aren Bintang Sulawesi memadukan kopi Robusta dan gula aren dalam satu sachet praktis. Pilihan untuk menikmati sajian kopi dengan sentuhan manis gula aren di rumah, kantor, atau saat bepergian.','images/products/09_bintang_sulawesi_gula_aren_20g_depan_foto_asli.jpg','[\"images\\/products\\/06_bintang_sulawesi_gula_aren_20g_belakang_edit.png\"]','20 gram','Bintang Sulawesi, Palu, Sulawesi Tengah, Indonesia','Palu, Sulawesi Tengah, Indonesia','Gula aren dan kopi bubuk murni','Tuangkan satu sachet ke dalam cangkir, tambahkan 140 ml air mendidih, lalu aduk hingga larut dan tercampur rata.','Simpan di tempat yang kering dan sejuk.',1,'2026-10-11 16:55:19','2026-10-11 16:55:19'),(4,1,'Kopi Bubuk ATAPMERAH 35g — Kopi Campur','kopi-bubuk-atapmerah-35g','ATAPMERAH','Blend','ground','Kopi ATAPMERAH hadir dalam kemasan 35 gram yang praktis dan ekonomis untuk dinikmati sehari-hari. Produk kopi campur ini diproduksi oleh PT Kopi Bintang Harapan di Palu, Sulawesi Tengah, Indonesia.','images/products/07_atapmerah_35g_depan_edit.png','[]','35 gram','PT Kopi Bintang Harapan, Palu, Sulawesi Tengah, Indonesia','Palu, Sulawesi Tengah, Indonesia','60% kopi bubuk dan serelia pilihan','Masukkan kopi ke dalam cangkir, tambahkan gula atau krimer sesuai selera, tuangkan 200 ml air mendidih, lalu aduk hingga rata.','Simpan di tempat yang kering dan sejuk.',1,'2026-10-11 16:55:19','2026-10-11 16:55:19');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
INSERT INTO `stock_movements` VALUES (1,1,4,'in','initial_stock',NULL,150,0,150,'Saldo Awal Stok Fisik Gudang — SKU SKU-AM-135G (Kopi ATAPMERAH 135g (Bungkus))','2026-10-11 16:55:19','2026-10-11 16:55:19'),(2,2,4,'in','initial_stock',NULL,60,0,60,'Saldo Awal Stok Fisik Gudang — SKU SKU-AM-135G-PAK5 (Kopi ATAPMERAH 135g (Pak Isi 5 Bungkus))','2026-10-11 16:55:19','2026-10-11 16:55:19'),(3,3,4,'in','initial_stock',NULL,300,0,300,'Saldo Awal Stok Fisik Gudang — SKU SKU-BH-10G-SCT (Robusta Bintang Harapan 10g (Sachet))','2026-10-11 16:55:19','2026-10-11 16:55:19'),(4,4,4,'in','initial_stock',NULL,120,0,120,'Saldo Awal Stok Fisik Gudang — SKU SKU-BH-10G-RCG (Robusta Bintang Harapan 10g (Renceng 10 Sachet))','2026-10-11 16:55:19','2026-10-11 16:55:19'),(5,5,4,'in','initial_stock',NULL,250,0,250,'Saldo Awal Stok Fisik Gudang — SKU SKU-BS-20G-SCT (Kopi Gula Aren 20g (Sachet))','2026-10-11 16:55:19','2026-10-11 16:55:19'),(6,6,4,'in','initial_stock',NULL,90,0,90,'Saldo Awal Stok Fisik Gudang — SKU SKU-BS-20G-RCG (Kopi Gula Aren 20g (Renceng 10 Sachet))','2026-10-11 16:55:19','2026-10-11 16:55:19'),(7,7,4,'in','initial_stock',NULL,200,0,200,'Saldo Awal Stok Fisik Gudang — SKU SKU-AM-35G-BKS (Kopi ATAPMERAH 35g (Bungkus))','2026-10-11 16:55:19','2026-10-11 16:55:19'),(8,8,4,'in','initial_stock',NULL,80,0,80,'Saldo Awal Stok Fisik Gudang — SKU SKU-AM-35G-PAK10 (Kopi ATAPMERAH 35g (Pak Isi 10 Bungkus))','2026-10-11 16:55:19','2026-10-11 16:55:19');
/*!40000 ALTER TABLE `stock_movements` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'SUP-KBH-01','PT Kopi Bintang Harapan','Bintang Harapan Palu','081245678901','produksi@bintangharapan.co.id','Palu, Sulawesi Tengah, Indonesia',1,'2026-10-11 16:55:19','2026-10-11 16:55:19'),(2,'SUP-BS-02','Bintang Sulawesi','Sentra Kopi Sulawesi','081398765432','distribusi@bintangsulawesi.co.id','Palu, Sulawesi Tengah, Indonesia',1,'2026-10-11 16:55:19','2026-10-11 16:55:19');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Pemilik Bisnis (Owner)','owner@kopi.com','081234567890','owner',1,NULL,'$2y$10$BMiKQnGBvPXh1hDdGZEJZujfmEMvrhNxg.UiaBOgNNzebrgXVnovi',NULL,'2026-10-11 16:55:19','2026-10-11 19:56:55'),(2,'Administrator Toko','admin@kopi.com','081234567891','admin',1,NULL,'$2y$10$BMiKQnGBvPXh1hDdGZEJZujfmEMvrhNxg.UiaBOgNNzebrgXVnovi',NULL,'2026-10-11 16:55:19','2026-10-11 19:56:55'),(3,'Sales Representative','sales@kopi.com','081234567892','sales',1,NULL,'$2y$10$BMiKQnGBvPXh1hDdGZEJZujfmEMvrhNxg.UiaBOgNNzebrgXVnovi',NULL,'2026-10-11 16:55:19','2026-10-11 19:56:55'),(4,'Staf Gudang (Warehouse)','warehouse@kopi.com','081234567893','warehouse',1,NULL,'$2y$10$BMiKQnGBvPXh1hDdGZEJZujfmEMvrhNxg.UiaBOgNNzebrgXVnovi',NULL,'2026-10-11 16:55:19','2026-10-11 19:56:55'),(5,'Staf Keuangan (Finance)','finance@kopi.com','081234567894','finance',1,NULL,'$2y$10$BMiKQnGBvPXh1hDdGZEJZujfmEMvrhNxg.UiaBOgNNzebrgXVnovi',NULL,'2026-10-11 16:55:19','2026-10-11 19:56:55');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-12 11:57:28
