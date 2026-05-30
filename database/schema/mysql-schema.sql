/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `accessories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accessories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `qty` int NOT NULL DEFAULT '0',
  `requestable` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `location_id` int DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_cost` decimal(20,2) DEFAULT NULL,
  `order_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_id` int unsigned DEFAULT NULL,
  `min_amt` int DEFAULT NULL,
  `manufacturer_id` int DEFAULT NULL,
  `model_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `accessories_company_id_index` (`company_id`),
  KEY `accessories_deleted_at_category_id_index` (`deleted_at`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `accessories_checkout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accessories_checkout` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `created_by` int DEFAULT NULL,
  `accessory_id` int DEFAULT NULL,
  `assigned_to` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `note` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accessories_checkout_assigned_to_assigned_type_index` (`assigned_to`,`assigned_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `action_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `created_by` int DEFAULT NULL,
  `action_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_id` int DEFAULT NULL,
  `target_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_id` int DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `filename` text COLLATE utf8mb4_unicode_ci,
  `item_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` int NOT NULL,
  `expected_checkin` date DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `accepted_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `thread_id` int DEFAULT NULL,
  `company_id` int DEFAULT NULL,
  `accept_signature` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_meta` text COLLATE utf8mb4_unicode_ci,
  `action_date` datetime DEFAULT NULL,
  `stored_eula` text COLLATE utf8mb4_unicode_ci,
  `action_source` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remote_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `action_logs_thread_id_index` (`thread_id`),
  KEY `action_logs_created_at_index` (`created_at`),
  KEY `action_logs_item_type_item_id_action_type_index` (`item_type`,`item_id`,`action_type`),
  KEY `action_logs_target_type_target_id_action_type_index` (`target_type`,`target_id`,`action_type`),
  KEY `action_logs_target_type_target_id_index` (`target_type`,`target_id`),
  KEY `action_logs_company_id_index` (`company_id`),
  KEY `action_logs_action_type_index` (`action_type`),
  KEY `action_logs_remote_ip_index` (`remote_ip`),
  KEY `action_logs_action_date_index` (`action_date`),
  KEY `action_logs_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `asset_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asset_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `action_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `asset_id` int NOT NULL,
  `checkedout_to` int DEFAULT NULL,
  `location_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `asset_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `filename` text COLLATE utf8mb4_unicode_ci,
  `requested_at` datetime DEFAULT NULL,
  `accepted_at` datetime DEFAULT NULL,
  `accessory_id` int DEFAULT NULL,
  `accepted_id` int DEFAULT NULL,
  `consumable_id` int DEFAULT NULL,
  `expected_checkin` date DEFAULT NULL,
  `component_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `asset_uploads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asset_uploads` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `filename` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `asset_id` int NOT NULL,
  `filenotes` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asset_tag` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` int DEFAULT NULL,
  `serial` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `asset_eol_date` date DEFAULT NULL,
  `eol_explicit` tinyint(1) NOT NULL DEFAULT '0',
  `purchase_cost` decimal(20,2) DEFAULT NULL,
  `order_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_to` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `image` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `physical` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status_id` int DEFAULT NULL,
  `archived` tinyint(1) DEFAULT '0',
  `warranty_months` int DEFAULT NULL,
  `depreciate` tinyint(1) DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `requestable` tinyint NOT NULL DEFAULT '0',
  `rtd_location_id` int DEFAULT NULL,
  `accepted` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_checkout` datetime DEFAULT NULL,
  `last_checkin` datetime DEFAULT NULL,
  `expected_checkin` date DEFAULT NULL,
  `company_id` int unsigned DEFAULT NULL,
  `assigned_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_audit_date` datetime DEFAULT NULL,
  `next_audit_date` date DEFAULT NULL,
  `location_id` int DEFAULT NULL,
  `checkin_counter` int NOT NULL DEFAULT '0',
  `checkout_counter` int NOT NULL DEFAULT '0',
  `requests_counter` int NOT NULL DEFAULT '0',
  `byod` tinyint(1) DEFAULT '0',
  `_snipeit_imei_1` text COLLATE utf8mb4_unicode_ci,
  `_snipeit_phone_number_2` text COLLATE utf8mb4_unicode_ci,
  `_snipeit_ram_3` text COLLATE utf8mb4_unicode_ci,
  `_snipeit_cpu_4` text COLLATE utf8mb4_unicode_ci,
  `_snipeit_mac_address_5` text COLLATE utf8mb4_unicode_ci,
  `_snipeit_test_encrypted_6` text COLLATE utf8mb4_unicode_ci,
  `_snipeit_test_checkbox_7` text COLLATE utf8mb4_unicode_ci,
  `_snipeit_test_radio_8` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `assets_rtd_location_id_index` (`rtd_location_id`),
  KEY `assets_assigned_type_assigned_to_index` (`assigned_type`,`assigned_to`),
  KEY `assets_created_at_index` (`created_at`),
  KEY `assets_deleted_at_status_id_index` (`deleted_at`,`status_id`),
  KEY `assets_deleted_at_model_id_index` (`deleted_at`,`model_id`),
  KEY `assets_deleted_at_assigned_type_assigned_to_index` (`deleted_at`,`assigned_type`,`assigned_to`),
  KEY `assets_deleted_at_supplier_id_index` (`deleted_at`,`supplier_id`),
  KEY `assets_deleted_at_location_id_index` (`deleted_at`,`location_id`),
  KEY `assets_deleted_at_rtd_location_id_index` (`deleted_at`,`rtd_location_id`),
  KEY `assets_deleted_at_asset_tag_index` (`deleted_at`,`asset_tag`),
  KEY `assets_deleted_at_name_index` (`deleted_at`,`name`),
  KEY `assets_serial_index` (`serial`),
  KEY `assets_company_id_index` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `eula_text` longtext COLLATE utf8mb4_unicode_ci,
  `use_default_eula` tinyint(1) NOT NULL DEFAULT '0',
  `require_acceptance` tinyint(1) NOT NULL DEFAULT '0',
  `alert_on_response` tinyint(1) NOT NULL DEFAULT '0',
  `category_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'asset',
  `checkin_email` tinyint(1) NOT NULL DEFAULT '0',
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `categories_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `checkout_acceptances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `checkout_acceptances` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `checkoutable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `checkoutable_id` bigint unsigned NOT NULL,
  `assigned_to_id` int DEFAULT NULL,
  `qty` int unsigned DEFAULT NULL,
  `signature_filename` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `alert_on_response_id` bigint unsigned DEFAULT NULL,
  `accepted_at` timestamp NULL DEFAULT NULL,
  `declined_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `stored_eula` text COLLATE utf8mb4_unicode_ci,
  `stored_eula_file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `checkout_acceptances_checkoutable_type_checkoutable_id_index` (`checkoutable_type`,`checkoutable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `checkout_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `checkout_requests` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `requestable_id` int NOT NULL,
  `requestable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `canceled_at` datetime DEFAULT NULL,
  `fulfilled_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `checkout_requests_user_id_requestable_id_requestable_type` (`user_id`,`requestable_id`,`requestable_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `companies_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `components`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `components` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `location_id` int DEFAULT NULL,
  `company_id` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `order_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_cost` decimal(20,2) DEFAULT NULL,
  `model_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manufacturer_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `min_amt` int DEFAULT NULL,
  `serial` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `components_company_id_index` (`company_id`),
  KEY `components_deleted_at_category_id_index` (`deleted_at`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `components_assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `components_assets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `created_by` int DEFAULT NULL,
  `assigned_qty` int DEFAULT '1',
  `component_id` int DEFAULT NULL,
  `asset_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `consumables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consumables` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `location_id` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `qty` int NOT NULL DEFAULT '0',
  `requestable` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_cost` decimal(20,2) DEFAULT NULL,
  `order_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_id` int unsigned DEFAULT NULL,
  `min_amt` int DEFAULT NULL,
  `model_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manufacturer_id` int DEFAULT NULL,
  `item_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `consumables_company_id_index` (`company_id`),
  KEY `consumables_deleted_at_category_id_index` (`deleted_at`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `consumables_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consumables_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `created_by` int DEFAULT NULL,
  `consumable_id` int DEFAULT NULL,
  `assigned_to` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `custom_field_custom_fieldset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_field_custom_fieldset` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `custom_field_id` int NOT NULL,
  `custom_fieldset_id` int NOT NULL,
  `order` int NOT NULL,
  `required` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `custom_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_fields` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `format` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `element` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `field_values` text COLLATE utf8mb4_unicode_ci,
  `field_encrypted` tinyint(1) NOT NULL DEFAULT '0',
  `db_column` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `help_text` text COLLATE utf8mb4_unicode_ci,
  `show_in_email` tinyint(1) NOT NULL DEFAULT '0',
  `show_in_requestable_list` tinyint(1) DEFAULT '0',
  `is_unique` tinyint(1) DEFAULT '0',
  `display_in_user_view` tinyint(1) DEFAULT '0',
  `auto_add_to_fieldsets` tinyint(1) DEFAULT '0',
  `show_in_listview` tinyint(1) DEFAULT '0',
  `display_checkin` tinyint(1) NOT NULL DEFAULT '0',
  `display_checkout` tinyint(1) NOT NULL DEFAULT '0',
  `display_audit` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `custom_fieldsets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_fieldsets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int NOT NULL,
  `company_id` int DEFAULT NULL,
  `location_id` int DEFAULT NULL,
  `manager_id` int DEFAULT NULL,
  `notes` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departments_company_id_index` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `depreciations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `depreciations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `months` int NOT NULL,
  `depreciation_min` decimal(8,2) DEFAULT NULL,
  `depreciation_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'amount',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `imports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `imports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filesize` int NOT NULL,
  `import_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `header_row` text COLLATE utf8mb4_unicode_ci,
  `first_row` text COLLATE utf8mb4_unicode_ci,
  `field_map` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kits` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kits_accessories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kits_accessories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `kit_id` int DEFAULT NULL,
  `accessory_id` int DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kits_consumables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kits_consumables` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `kit_id` int DEFAULT NULL,
  `consumable_id` int DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kits_licenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kits_licenses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `kit_id` int DEFAULT NULL,
  `license_id` int DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kits_models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kits_models` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `kit_id` int DEFAULT NULL,
  `model_id` int DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `license_seats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `license_seats` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `license_id` int DEFAULT NULL,
  `assigned_to` int DEFAULT NULL,
  `unreassignable_seat` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `asset_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `license_seats_license_id_index` (`license_id`),
  KEY `license_seats_assigned_to_license_id_index` (`assigned_to`,`license_id`),
  KEY `license_seats_asset_id_license_id_index` (`asset_id`,`license_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `licenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `licenses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial` text COLLATE utf8mb4_unicode_ci,
  `purchase_date` date DEFAULT NULL,
  `purchase_cost` decimal(20,2) DEFAULT NULL,
  `order_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seats` int NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `depreciation_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `license_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `depreciate` tinyint(1) DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `purchase_order` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `termination_date` date DEFAULT NULL,
  `maintained` tinyint(1) DEFAULT NULL,
  `reassignable` tinyint(1) NOT NULL DEFAULT '1',
  `company_id` int unsigned DEFAULT NULL,
  `manufacturer_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `min_amt` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `licenses_company_id_index` (`company_id`),
  KEY `licenses_deleted_at_category_id_index` (`deleted_at`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `locations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tag_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_ou` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manager_id` int DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_id` int unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `locations_company_id_index` (`company_id`),
  KEY `locations_parent_id_index` (`parent_id`),
  KEY `locations_manager_id_deleted_at_index` (`manager_id`,`deleted_at`),
  KEY `locations_parent_id_deleted_at_index` (`parent_id`,`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_attempts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remote_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `successful` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `maintenances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `maintenances` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int unsigned NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `asset_maintenance_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` text COLLATE utf8mb4_unicode_ci,
  `is_warranty` tinyint(1) NOT NULL,
  `start_date` date NOT NULL,
  `completion_date` date DEFAULT NULL,
  `asset_maintenance_time` int DEFAULT NULL,
  `notes` longtext COLLATE utf8mb4_unicode_ci,
  `image` text COLLATE utf8mb4_unicode_ci,
  `cost` decimal(20,2) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `manufacturers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manufacturers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `support_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warranty_lookup_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `support_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `support_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `models` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_amt` int DEFAULT NULL,
  `manufacturer_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `require_serial` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `depreciation_id` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `eol` int DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deprecated_mac_address` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `fieldset_id` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `requestable` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `models_deleted_at_category_id_index` (`deleted_at`,`category_id`),
  KEY `models_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `models_custom_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `models_custom_fields` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `asset_model_id` int NOT NULL,
  `custom_field_id` int NOT NULL,
  `default_value` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_auth_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_personal_access_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL,
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permission_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permission_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `scim_externalid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `report_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_by` int DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_shared` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `report_templates_created_by_index` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `requested_assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `requested_assets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int NOT NULL,
  `user_id` int NOT NULL,
  `accepted_at` datetime DEFAULT NULL,
  `denied_at` datetime DEFAULT NULL,
  `notes` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `requests` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `request_code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `saml_nonces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `saml_nonces` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nonce` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `not_valid_after` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `saml_nonces_nonce_unique` (`nonce`),
  KEY `saml_nonces_not_valid_after_index` (`not_valid_after`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `per_page` int NOT NULL DEFAULT '20',
  `site_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Snipe IT Asset Management',
  `qr_code` int DEFAULT NULL,
  `qr_text` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_asset_name` int DEFAULT NULL,
  `display_checkout_date` int DEFAULT NULL,
  `display_eol` int DEFAULT NULL,
  `auto_increment_assets` int NOT NULL DEFAULT '0',
  `auto_increment_prefix` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `load_remote` tinyint(1) NOT NULL DEFAULT '1',
  `logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `header_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nav_link_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '#ffffff',
  `link_light_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_dark_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alert_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alerts_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `default_eula_text` longtext COLLATE utf8mb4_unicode_ci,
  `webhook_endpoint` text COLLATE utf8mb4_unicode_ci,
  `webhook_channel` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `webhook_botname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `webhook_selected` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'slack',
  `default_currency` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_css` text COLLATE utf8mb4_unicode_ci,
  `brand` tinyint NOT NULL DEFAULT '1',
  `ldap_enabled` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_server` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_uname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_pword` longtext COLLATE utf8mb4_unicode_ci,
  `ldap_basedn` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_default_group` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_filter` text COLLATE utf8mb4_unicode_ci,
  `ldap_username_field` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'samaccountname',
  `ldap_lname_field` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'sn',
  `ldap_fname_field` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'givenname',
  `ldap_display_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_auth_filter_query` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'uid=',
  `ldap_version` int DEFAULT '3',
  `ldap_active_flag` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_dept` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_emp_num` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_phone_field` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_mobile` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_jobtitle` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_manager` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_zip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_multiple_companies_support` tinyint(1) NOT NULL DEFAULT '0',
  `scope_locations_fmcs` tinyint(1) NOT NULL DEFAULT '0',
  `ldap_server_cert_ignore` tinyint(1) NOT NULL DEFAULT '0',
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'en-US',
  `week_start` tinyint DEFAULT '0',
  `labels_per_page` tinyint NOT NULL DEFAULT '30',
  `labels_width` decimal(6,5) NOT NULL DEFAULT '2.62500',
  `labels_height` decimal(6,5) NOT NULL DEFAULT '1.00000',
  `labels_pmargin_left` decimal(6,5) NOT NULL DEFAULT '0.21975',
  `labels_pmargin_right` decimal(6,5) NOT NULL DEFAULT '0.21975',
  `labels_pmargin_top` decimal(6,5) NOT NULL DEFAULT '0.50000',
  `labels_pmargin_bottom` decimal(6,5) NOT NULL DEFAULT '0.50000',
  `labels_display_bgutter` decimal(6,5) NOT NULL DEFAULT '0.07000',
  `labels_display_sgutter` decimal(6,5) NOT NULL DEFAULT '0.05000',
  `labels_fontsize` tinyint NOT NULL DEFAULT '9',
  `labels_pagewidth` decimal(7,5) NOT NULL DEFAULT '8.50000',
  `labels_pageheight` decimal(7,5) NOT NULL DEFAULT '11.00000',
  `labels_display_name` tinyint NOT NULL DEFAULT '0',
  `labels_display_serial` tinyint NOT NULL DEFAULT '1',
  `labels_display_tag` tinyint NOT NULL DEFAULT '1',
  `alt_barcode_enabled` tinyint(1) DEFAULT '1',
  `alert_interval` int DEFAULT '30',
  `alert_threshold` int DEFAULT '5',
  `name_display_format` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'first_last',
  `email_domain` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_format` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'filastname',
  `username_format` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'filastname',
  `is_ad` tinyint(1) NOT NULL DEFAULT '0',
  `ad_domain` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ldap_port` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '389',
  `ldap_tls` tinyint(1) NOT NULL DEFAULT '0',
  `zerofill_count` int NOT NULL DEFAULT '5',
  `ldap_pw_sync` tinyint(1) NOT NULL DEFAULT '1',
  `two_factor_enabled` tinyint DEFAULT NULL,
  `require_accept_signature` tinyint(1) NOT NULL DEFAULT '0',
  `date_display_format` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y-m-d',
  `time_display_format` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'h:i A',
  `next_auto_tag_base` bigint NOT NULL DEFAULT '1',
  `login_note` text COLLATE utf8mb4_unicode_ci,
  `thumbnail_max_h` int DEFAULT '50',
  `pwd_secure_uncommon` tinyint(1) NOT NULL DEFAULT '0',
  `pwd_secure_complexity` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pwd_secure_min` int NOT NULL DEFAULT '8',
  `audit_interval` int DEFAULT NULL,
  `audit_warning_days` int DEFAULT NULL,
  `show_url_in_emails` tinyint(1) NOT NULL DEFAULT '0',
  `custom_forgot_pass_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `show_alerts_in_menu` tinyint(1) NOT NULL DEFAULT '1',
  `labels_display_company_name` tinyint(1) NOT NULL DEFAULT '0',
  `show_archived_in_list` tinyint(1) NOT NULL DEFAULT '0',
  `dashboard_message` text COLLATE utf8mb4_unicode_ci,
  `support_footer` char(5) COLLATE utf8mb4_unicode_ci DEFAULT 'on',
  `footer_text` text COLLATE utf8mb4_unicode_ci,
  `modellist_displays` char(191) COLLATE utf8mb4_unicode_ci DEFAULT 'image,category,manufacturer,model_number',
  `login_remote_user_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `login_common_disabled` tinyint(1) NOT NULL DEFAULT '0',
  `login_remote_user_custom_logout_url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `skin` char(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `show_images_in_email` tinyint(1) NOT NULL DEFAULT '1',
  `admin_cc_email` char(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin_cc_always` tinyint(1) NOT NULL DEFAULT '1',
  `labels_display_model` tinyint(1) NOT NULL DEFAULT '0',
  `privacy_policy_link` char(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version_footer` char(5) COLLATE utf8mb4_unicode_ci DEFAULT 'on',
  `unique_serial` tinyint(1) NOT NULL DEFAULT '0',
  `logo_print_assets` tinyint(1) NOT NULL DEFAULT '0',
  `depreciation_method` char(10) COLLATE utf8mb4_unicode_ci DEFAULT 'default',
  `favicon` char(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'default.png',
  `email_logo` char(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `label_logo` char(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acceptance_pdf_logo` char(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allow_user_skin` tinyint(1) NOT NULL DEFAULT '0',
  `show_assigned_assets` tinyint(1) NOT NULL DEFAULT '0',
  `login_remote_user_header_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `ad_append_domain` tinyint(1) NOT NULL DEFAULT '0',
  `saml_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `saml_idp_metadata` mediumtext COLLATE utf8mb4_unicode_ci,
  `saml_attr_mapping_username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `saml_forcelogin` tinyint(1) NOT NULL DEFAULT '0',
  `saml_slo` tinyint(1) NOT NULL DEFAULT '0',
  `saml_sp_x509cert` text COLLATE utf8mb4_unicode_ci,
  `saml_sp_privatekey` text COLLATE utf8mb4_unicode_ci,
  `saml_custom_settings` text COLLATE utf8mb4_unicode_ci,
  `saml_sp_x509certNew` text COLLATE utf8mb4_unicode_ci,
  `digit_separator` char(191) COLLATE utf8mb4_unicode_ci DEFAULT '1,234.56',
  `ldap_client_tls_cert` text COLLATE utf8mb4_unicode_ci,
  `ldap_client_tls_key` text COLLATE utf8mb4_unicode_ci,
  `dash_chart_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'name',
  `label2_enable` tinyint(1) NOT NULL DEFAULT '0',
  `label2_template` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'DefaultLabel',
  `label2_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `label2_asset_logo` tinyint(1) NOT NULL DEFAULT '0',
  `label2_1d_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'C128',
  `label2_2d_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'QRCODE',
  `label2_2d_prefix` char(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `label2_2d_target` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hardware_id',
  `label2_fields` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'name=name;serial=serial;model=model.name;',
  `label2_empty_row_count` int unsigned NOT NULL DEFAULT '0',
  `google_login` tinyint(1) DEFAULT '0',
  `google_client_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_client_secret` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_edit` tinyint(1) DEFAULT '1',
  `require_checkinout_notes` tinyint(1) DEFAULT '0',
  `shortcuts_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `due_checkin_days` int DEFAULT NULL,
  `ldap_invert_active_flag` tinyint(1) NOT NULL DEFAULT '0',
  `manager_view_enabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Allow managers to view assets assigned to their subordinates',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `status_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `status_labels` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deployable` tinyint(1) NOT NULL DEFAULT '0',
  `pending` tinyint(1) NOT NULL DEFAULT '0',
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `show_in_nav` tinyint(1) DEFAULT '0',
  `default_label` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address2` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(35) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(35) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `zip` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_entries` (
  `sequence` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `family_hash` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `should_display_on_index` tinyint(1) NOT NULL DEFAULT '1',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`sequence`),
  UNIQUE KEY `telescope_entries_uuid_unique` (`uuid`),
  KEY `telescope_entries_batch_id_index` (`batch_id`),
  KEY `telescope_entries_family_hash_index` (`family_hash`),
  KEY `telescope_entries_created_at_index` (`created_at`),
  KEY `telescope_entries_type_should_display_on_index_index` (`type`,`should_display_on_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_entries_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_entries_tags` (
  `entry_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`entry_uuid`,`tag`),
  KEY `telescope_entries_tags_tag_index` (`tag`),
  CONSTRAINT `telescope_entries_tags_entry_uuid_foreign` FOREIGN KEY (`entry_uuid`) REFERENCES `telescope_entries` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `telescope_monitoring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telescope_monitoring` (
  `tag` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `throttle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `throttle` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `ip_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attempts` int NOT NULL DEFAULT '0',
  `suspended` tinyint(1) NOT NULL DEFAULT '0',
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  `last_attempt_at` timestamp NULL DEFAULT NULL,
  `suspended_at` timestamp NULL DEFAULT NULL,
  `banned_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `throttle_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8mb4_unicode_ci,
  `activated` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int DEFAULT NULL,
  `activation_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activated_at` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `persist_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_password_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gravatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_id` int DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` text COLLATE utf8mb4_unicode_ci,
  `jobtitle` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manager_id` int DEFAULT NULL,
  `employee_num` text COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `company_id` int unsigned DEFAULT NULL,
  `remember_token` text COLLATE utf8mb4_unicode_ci,
  `ldap_import` tinyint(1) NOT NULL DEFAULT '0',
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'en-US',
  `show_in_list` tinyint(1) NOT NULL DEFAULT '1',
  `two_factor_secret` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_enrolled` tinyint(1) NOT NULL DEFAULT '0',
  `two_factor_optin` tinyint(1) NOT NULL DEFAULT '0',
  `department_id` int DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skin` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nav_link_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '#ffffff',
  `link_light_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_dark_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remote` tinyint(1) DEFAULT '0',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `scim_externalid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `autoassign_licenses` tinyint(1) NOT NULL DEFAULT '1',
  `vip` tinyint(1) DEFAULT '0',
  `enable_sounds` tinyint(1) NOT NULL DEFAULT '0',
  `enable_confetti` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `users_activation_code_index` (`activation_code`),
  KEY `users_reset_password_code_index` (`reset_password_code`),
  KEY `users_company_id_index` (`company_id`),
  KEY `users_username_deleted_at_index` (`username`,`deleted_at`),
  KEY `users_manager_id_deleted_at_index` (`manager_id`,`deleted_at`),
  KEY `users_deleted_at_location_id_index` (`deleted_at`,`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_groups` (
  `user_id` int unsigned NOT NULL,
  `group_id` int unsigned NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`user_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2012_12_06_225921_migration_cartalyst_sentry_install_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2012_12_06_225929_migration_cartalyst_sentry_install_groups',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2012_12_06_225945_migration_cartalyst_sentry_install_users_groups_pivot',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2012_12_06_225988_migration_cartalyst_sentry_install_throttle',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2013_03_23_193214_update_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2013_11_13_075318_create_models_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2013_11_13_075335_create_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2013_11_13_075347_create_manufacturers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2013_11_15_015858_add_user_id_to_categories',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2013_11_15_112701_add_user_id_to_manufacturers',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2013_11_15_190327_create_assets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2013_11_15_190357_create_temp_licenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2013_11_15_201848_add_license_name_to_licenses',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2013_11_16_040323_create_depreciations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2013_11_16_042851_add_depreciation_id_to_models',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2013_11_16_084923_add_user_id_to_models',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2013_11_16_103258_create_locations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2013_11_16_103336_add_location_id_to_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2013_11_16_103407_add_checkedout_to_to_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2013_11_16_103425_create_history_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2013_11_17_054359_drop_licenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2013_11_17_054526_add_physical_to_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2013_11_17_055126_create_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2013_11_17_062634_add_license_to_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2013_11_18_134332_add_contacts_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2013_11_18_142847_add_info_to_locations',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2013_11_18_152942_remove_location_id_from_asset',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2013_11_18_164423_set_nullvalues_for_user',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2013_11_19_013337_create_asset_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2013_11_19_061409_edit_added_on_asset_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2013_11_19_062250_edit_location_id_asset_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2013_11_20_055822_add_soft_delete_on_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2013_11_20_121404_add_soft_delete_on_locations',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2013_11_20_123137_add_soft_delete_on_manufacturers',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2013_11_20_123725_add_soft_delete_on_categories',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2013_11_20_130248_create_status_labels',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2013_11_20_130830_add_status_id_on_assets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2013_11_20_131544_add_status_type_on_status_labels',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2013_11_20_134103_add_archived_to_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2013_11_21_002321_add_uploads_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2013_11_21_024531_remove_deployable_boolean_from_status_labels',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2013_11_22_075308_add_option_label_to_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2013_11_22_213400_edits_to_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2013_11_25_013244_recreate_licenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2013_11_25_031458_create_license_seats_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2013_11_25_032022_add_type_to_actionlog_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2013_11_25_033008_delete_bad_licenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2013_11_25_033131_create_new_licenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2013_11_25_033534_add_licensed_to_licenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2013_11_25_101308_add_warrantee_to_assets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2013_11_25_104343_alter_warranty_column_on_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2013_11_25_150450_drop_parent_from_categories',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2013_11_25_151920_add_depreciate_to_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2013_11_25_152903_add_depreciate_to_licenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2013_11_26_211820_drop_license_from_assets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2013_11_27_062510_add_note_to_asset_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2013_12_01_113426_add_filename_to_asset_log',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2013_12_06_094618_add_nullable_to_licenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2013_12_10_084038_add_eol_on_models_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2013_12_12_055218_add_manager_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2014_01_28_031200_add_qr_code_to_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2014_02_13_183016_add_qr_text_to_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2014_05_24_093839_alter_default_license_depreciation_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2014_05_27_231658_alter_default_values_licenses',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2014_06_19_191508_add_asset_name_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2014_06_20_004847_make_asset_log_checkedout_to_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2014_06_20_005050_make_asset_log_purchasedate_to_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2014_06_24_003011_add_suppliers',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2014_06_24_010742_add_supplier_id_to_asset',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2014_06_24_012839_add_zip_to_supplier',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2014_06_24_033908_add_url_to_supplier',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2014_07_08_054116_add_employee_id_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2014_07_09_134316_add_requestable_to_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2014_07_17_085822_add_asset_to_software',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2014_07_17_161625_make_asset_id_in_logs_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2014_08_12_053504_alpha_0_4_2_release',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2014_08_17_083523_make_location_id_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2014_10_16_200626_add_rtd_location_to_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2014_10_24_000417_alter_supplier_state_to_32',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2014_10_24_015641_add_display_checkout_date',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2014_10_28_222654_add_avatar_field_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2014_10_29_045924_add_image_field_to_models_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2014_11_01_214955_add_eol_display_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2014_11_04_231416_update_group_field_for_reporting',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2014_11_05_212408_add_fields_to_licenses',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2014_11_07_021042_add_image_to_supplier',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (87,'2014_11_20_203007_add_username_to_user',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (88,'2014_11_20_223947_add_auto_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2014_11_20_224421_add_prefix_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2014_11_21_104401_change_licence_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2014_12_09_082500_add_fields_maintained_term_to_licenses',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2015_02_04_155757_increase_user_field_lengths',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2015_02_07_013537_add_soft_deleted_to_log',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (94,'2015_02_10_040958_fix_bad_assigned_to_ids',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (95,'2015_02_10_053310_migrate_data_to_new_statuses',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (96,'2015_02_11_044104_migrate_make_license_assigned_null',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (97,'2015_02_11_104406_migrate_create_requests_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (98,'2015_02_12_001312_add_mac_address_to_asset',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (99,'2015_02_12_024100_change_license_notes_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (100,'2015_02_17_231020_add_localonly_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (101,'2015_02_19_222322_add_logo_and_colors_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (102,'2015_02_24_072043_add_alerts_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (103,'2015_02_25_022931_add_eula_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (104,'2015_02_25_204513_add_accessories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (105,'2015_02_26_091228_add_accessories_user_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (106,'2015_02_26_115128_add_deleted_at_models',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (107,'2015_02_26_233005_add_category_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (108,'2015_03_01_231912_update_accepted_at_to_acceptance_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (109,'2015_03_05_011929_add_qr_type_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (110,'2015_03_18_055327_add_note_to_user',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (111,'2015_04_29_234704_add_slack_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (112,'2015_05_04_085151_add_parent_id_to_locations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (113,'2015_05_22_124421_add_reassignable_to_licenses',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (114,'2015_06_10_003314_fix_default_for_user_notes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (115,'2015_06_10_003554_create_consumables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (116,'2015_06_15_183253_move_email_to_username',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (117,'2015_06_23_070346_make_email_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (118,'2015_06_26_213716_create_asset_maintenances_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (119,'2015_07_04_212443_create_custom_fields_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (120,'2015_07_09_014359_add_currency_to_settings_and_locations',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (121,'2015_07_21_122022_add_expected_checkin_date_to_asset_logs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (122,'2015_07_24_093845_add_checkin_email_to_category_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (123,'2015_07_25_055415_remove_email_unique_constraint',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (124,'2015_07_29_230054_add_thread_id_to_asset_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (125,'2015_07_31_015430_add_accepted_to_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (126,'2015_09_09_195301_add_custom_css_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (127,'2015_09_21_235926_create_custom_field_custom_fieldset',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (128,'2015_09_22_000104_create_custom_fieldsets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (129,'2015_09_22_003321_add_fieldset_id_to_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (130,'2015_09_22_003413_migrate_mac_address',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (131,'2015_09_28_003314_fix_default_purchase_order',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (132,'2015_10_01_024551_add_accessory_consumable_price_info',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (133,'2015_10_12_192706_add_brand_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (134,'2015_10_22_003314_fix_defaults_accessories',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (135,'2015_10_23_182625_add_checkout_time_and_expected_checkout_date_to_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (136,'2015_11_05_061015_create_companies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (137,'2015_11_05_061115_add_company_id_to_consumables_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (138,'2015_11_05_183749_add_image_to_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (139,'2015_11_06_092038_add_company_id_to_accessories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (140,'2015_11_06_100045_add_company_id_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (141,'2015_11_06_134742_add_company_id_to_licenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (142,'2015_11_08_035832_add_company_id_to_assets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (143,'2015_11_08_222305_add_ldap_fields_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (144,'2015_11_15_151803_add_full_multiple_companies_support_to_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (145,'2015_11_26_195528_import_ldap_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (146,'2015_11_30_191504_remove_fk_company_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (147,'2015_12_21_193006_add_ldap_server_cert_ignore_to_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (148,'2015_12_30_233509_add_timestamp_and_userId_to_custom_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (149,'2015_12_30_233658_add_timestamp_and_userId_to_custom_fieldsets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (150,'2016_01_28_041048_add_notes_to_models',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (151,'2016_02_19_070119_add_remember_token_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (152,'2016_02_19_073625_create_password_resets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (153,'2016_03_02_193043_add_ldap_flag_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (154,'2016_03_02_220517_update_ldap_filter_to_longer_field',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (155,'2016_03_08_225351_create_components_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (156,'2016_03_09_024038_add_min_stock_to_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (157,'2016_03_10_133849_add_locale_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (158,'2016_03_10_135519_add_locale_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (159,'2016_03_11_185621_add_label_settings_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (160,'2016_03_22_125911_fix_custom_fields_regexes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (161,'2016_04_28_141554_add_show_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (162,'2016_05_16_164733_add_model_mfg_to_consumable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (163,'2016_05_19_180351_add_alt_barcode_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (164,'2016_05_19_191146_add_alter_interval',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (165,'2016_05_19_192226_add_inventory_threshold',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (166,'2016_05_20_024859_remove_option_keys_from_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (167,'2016_05_20_143758_remove_option_value_from_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (168,'2016_06_01_000001_create_oauth_auth_codes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (169,'2016_06_01_000002_create_oauth_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (170,'2016_06_01_000003_create_oauth_refresh_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (171,'2016_06_01_000004_create_oauth_clients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (172,'2016_06_01_000005_create_oauth_personal_access_clients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (173,'2016_06_01_140218_add_email_domain_and_format_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (174,'2016_06_22_160725_add_user_id_to_maintenances',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (175,'2016_07_13_150015_add_is_ad_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (176,'2016_07_14_153609_add_ad_domain_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (177,'2016_07_22_003348_fix_custom_fields_regex_stuff',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (178,'2016_07_22_054850_one_more_mac_addr_fix',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (179,'2016_07_22_143045_add_port_to_ldap_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (180,'2016_07_22_153432_add_tls_to_ldap_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (181,'2016_07_27_211034_add_zerofill_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (182,'2016_08_02_124944_add_color_to_statuslabel',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (183,'2016_08_04_134500_add_disallow_ldap_pw_sync_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (184,'2016_08_09_002225_add_manufacturer_to_licenses',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (185,'2016_08_12_121613_add_manufacturer_to_accessories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (186,'2016_08_23_143353_add_new_fields_to_custom_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (187,'2016_08_23_145619_add_show_in_nav_to_status_labels',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (188,'2016_08_30_084634_make_purchase_cost_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (189,'2016_09_01_141051_add_requestable_to_asset_model',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (190,'2016_09_02_001448_create_checkout_requests_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (191,'2016_09_04_180400_create_actionlog_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (192,'2016_09_04_182149_migrate_asset_log_to_action_log',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (193,'2016_09_19_235935_fix_fieldtype_for_target_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (194,'2016_09_23_140722_fix_modelno_in_consumables_to_string',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (195,'2016_09_28_231359_add_company_to_logs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (196,'2016_10_14_130709_fix_order_number_to_varchar',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (197,'2016_10_16_015024_rename_modelno_to_model_number',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (198,'2016_10_16_015211_rename_consumable_modelno_to_model_number',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (199,'2016_10_16_143235_rename_model_note_to_notes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (200,'2016_10_16_165052_rename_component_total_qty_to_qty',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (201,'2016_10_19_145520_fix_order_number_in_components_to_string',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (202,'2016_10_27_151715_add_serial_to_components',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (203,'2016_10_27_213251_increase_serial_field_capacity',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (204,'2016_10_29_002724_enable_2fa_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (205,'2016_10_29_082408_add_signature_to_acceptance',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (206,'2016_11_01_030818_fix_forgotten_filename_in_action_logs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (207,'2016_11_13_020954_rename_component_serial_number_to_serial',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (208,'2016_11_16_172119_increase_purchase_cost_size',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (209,'2016_11_17_161317_longer_state_field_in_location',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (210,'2016_11_17_193706_add_model_number_to_accessories',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (211,'2016_11_24_160405_add_missing_target_type_to_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (212,'2016_12_07_173720_increase_size_of_state_in_suppliers',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (213,'2016_12_19_004212_adjust_locale_length_to_10',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (214,'2016_12_19_133936_extend_phone_lengths_in_supplier_and_elsewhere',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (215,'2016_12_27_212631_make_asset_assigned_to_polymorphic',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (216,'2017_01_09_040429_create_locations_ldap_query_field',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (217,'2017_01_14_002418_create_imports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (218,'2017_01_25_063357_fix_utf8_custom_field_column_names',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (219,'2017_03_03_154632_add_time_date_display_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (220,'2017_03_10_210807_add_fields_to_manufacturer',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (221,'2017_05_08_195520_increase_size_of_field_values_in_custom_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (222,'2017_05_22_204422_create_departments',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (223,'2017_05_22_233509_add_manager_to_locations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (224,'2017_06_14_122059_add_next_autoincrement_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (225,'2017_06_18_151753_add_header_and_first_row_to_importer_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (226,'2017_07_07_191533_add_login_text',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (227,'2017_07_25_130710_add_thumbsize_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (228,'2017_08_03_160105_set_asset_archived_to_zero_default',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (229,'2017_08_22_180636_add_secure_password_options',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (230,'2017_08_25_074822_add_auditing_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (231,'2017_08_25_101435_add_auditing_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (232,'2017_09_18_225619_fix_assigned_type_not_being_nulled',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (233,'2017_10_03_015503_drop_foreign_keys',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (234,'2017_10_10_123504_allow_nullable_depreciation_id_in_models',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (235,'2017_10_17_133709_add_display_url_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (236,'2017_10_19_120002_add_custom_forgot_password_url',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (237,'2017_10_19_130406_add_image_and_supplier_to_accessories',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (238,'2017_10_20_234129_add_location_indices_to_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (239,'2017_10_25_202930_add_images_uploads_to_locations_manufacturers_etc',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (240,'2017_10_27_180947_denorm_asset_locations',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (241,'2017_10_27_192423_migrate_denormed_asset_locations',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (242,'2017_10_30_182938_add_address_to_user',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (243,'2017_11_08_025918_add_alert_menu_setting',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (244,'2017_11_08_123942_labels_display_company_name',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (245,'2017_12_12_010457_normalize_asset_last_audit_date',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (246,'2017_12_12_033618_add_actionlog_meta',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (247,'2017_12_26_170856_re_normalize_last_audit',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (248,'2018_01_17_184354_add_archived_in_list_setting',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (249,'2018_01_19_203121_add_dashboard_message_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (250,'2018_01_24_062633_add_footer_settings_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (251,'2018_01_24_093426_add_modellist_preferenc',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (252,'2018_02_22_160436_add_remote_user_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (253,'2018_03_03_011032_add_theme_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (254,'2018_03_06_054937_add_default_flag_on_statuslabels',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (255,'2018_03_23_212048_add_display_in_email_to_custom_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (256,'2018_03_24_030738_add_show_images_in_email_setting',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (257,'2018_03_24_050108_add_cc_alerts',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (258,'2018_03_29_053618_add_canceled_at_and_fulfilled_at_in_requests',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (259,'2018_03_29_070121_add_drop_unique_requests',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (260,'2018_03_29_070511_add_new_index_requestable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (261,'2018_04_02_150700_labels_display_model_name',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (262,'2018_04_16_133902_create_custom_field_default_values_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (263,'2018_05_04_073223_add_category_to_licenses',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (264,'2018_05_04_075235_add_update_license_category',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (265,'2018_05_08_031515_add_gdpr_privacy_footer',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (266,'2018_05_14_215229_add_indexes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (267,'2018_05_14_223646_add_indexes_to_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (268,'2018_05_14_233638_denorm_counters_on_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (269,'2018_05_16_153409_add_first_counter_totals_to_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (270,'2018_06_21_134622_add_version_footer',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (271,'2018_07_05_215440_add_unique_serial_option_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (272,'2018_07_17_005911_create_login_attempts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (273,'2018_07_24_154348_add_logo_to_print_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (274,'2018_07_28_023826_create_checkout_acceptances_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (275,'2018_08_08_100000_create_telescope_entries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (276,'2018_08_20_204842_add_depreciation_option_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (277,'2018_09_10_082212_create_checkout_acceptances_for_unaccepted_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (278,'2018_10_18_191228_add_kits_licenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (279,'2018_10_19_153910_add_kits_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (280,'2018_10_19_154013_add_kits_models_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (281,'2018_12_05_211936_add_favicon_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (282,'2018_12_05_212119_add_email_logo_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (283,'2019_02_07_185953_add_kits_consumables_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (284,'2019_02_07_190030_add_kits_accessories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (285,'2019_02_12_182750_add_actiondate_to_actionlog',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (286,'2019_02_14_154310_change_auto_increment_prefix_to_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (287,'2019_02_16_143518_auto_increment_back_to_string',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (288,'2019_02_17_205048_add_label_logo_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (289,'2019_02_20_234421_make_serial_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (290,'2019_02_21_224703_make_fields_nullable_for_integrity',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (291,'2019_04_06_060145_add_user_skin_setting',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (292,'2019_04_06_205355_add_setting_allow_user_skin',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (293,'2019_06_12_184327_rename_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (294,'2019_07_23_140906_add_show_assigned_assets_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (295,'2019_08_20_084049_add_custom_remote_user_header',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (296,'2019_12_04_223111_passport_upgrade',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (297,'2020_02_04_172100_add_ad_append_domain_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (298,'2020_04_29_222305_add_saml_fields_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (299,'2020_08_11_200712_add_saml_key_rollover',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (300,'2020_10_22_233743_move_accessory_checkout_note_to_join_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (301,'2020_10_23_161736_fix_zero_values_for_locations',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (302,'2020_11_18_214827_widen_license_serial_field',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (303,'2020_12_14_233815_add_digit_separator_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (304,'2020_12_18_090026_swap_target_type_index_order',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (305,'2020_12_21_153235_update_min_password',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (306,'2020_12_21_210105_fix_bad_ldap_server_url_for_v5',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (307,'2021_02_05_172502_add_provider_to_oauth_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (308,'2021_03_18_184102_adds_several_ldap_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (309,'2021_04_07_001811_add_ldap_dept',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (310,'2021_04_14_180125_add_ids_to_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (311,'2021_06_07_155421_add_serial_number_indexes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (312,'2021_06_07_155436_add_company_id_indexes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (313,'2021_07_28_031345_add_client_side_l_d_a_p_cert_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (314,'2021_07_28_040554_add_client_side_l_d_a_p_key_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (315,'2021_08_11_005206_add_depreciation_minimum_value',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (316,'2021_08_24_124354_make_ldap_client_certs_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (317,'2021_09_20_183216_change_default_label_to_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (318,'2021_12_27_151849_change_supplier_address_length',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (319,'2022_01_10_182548_add_license_id_index_to_license_seats',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (320,'2022_02_03_214958_blank_out_ldap_active_flag',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (321,'2022_02_10_110210_add_company_id_to_locations',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (322,'2022_02_16_152431_add_unique_constraint_to_custom_field',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (323,'2022_03_03_225655_add_notes_to_accessories',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (324,'2022_03_03_225754_add_notes_to_components',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (325,'2022_03_03_225824_add_notes_to_consumables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (326,'2022_03_04_080836_add_remote_to_user',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (327,'2022_03_09_001334_add_eula_to_checkout_acceptance',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (328,'2022_03_10_175740_add_eula_to_action_logs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (329,'2022_03_21_162724_adds_ldap_manager',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (330,'2022_04_05_135340_add_primary_key_to_custom_fields_pivot',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (331,'2022_05_16_235350_remove_stored_eula_field',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (332,'2022_06_23_164407_add_user_id_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (333,'2022_06_28_234539_add_username_index_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (334,'2022_07_07_010406_add_indexes_to_license_seats',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (335,'2022_08_10_141328_add_notes_denorm_to_consumables_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (336,'2022_08_25_213308_adds_ldap_default_group_to_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (337,'2022_09_29_040231_add_chart_type_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (338,'2022_10_05_163044_add_start_termination_date_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (339,'2022_10_25_193823_add_externalid_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (340,'2022_10_25_215520_add_label2_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (341,'2022_11_07_134348_add_display_to_user_in_custom_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (342,'2022_11_15_232525_adds_should_autoassign_bool_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (343,'2022_12_20_171851_fix_nullable_migration_for_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (344,'2023_01_18_122534_add_byod_to_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (345,'2023_01_21_225350_add_eol_date_on_assets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (346,'2023_01_23_232933_add_vip_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (347,'2023_02_12_224353_fix_unescaped_customfields_format',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (348,'2023_02_27_092130_add_scope_locations_setting',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (349,'2023_02_28_173527_adds_webhook_option_to_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (350,'2023_03_21_215218_update_slack_setting',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (351,'2023_04_12_135822_add_supplier_to_components',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (352,'2023_04_25_085912_add_autoadd_to_customfields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (353,'2023_04_25_181817_adds_ldap_location_to_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (354,'2023_04_26_160235_add_warranty_url_to_manufacturers',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (355,'2023_05_08_132921_increase_state_to_more_than_3',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (356,'2023_05_10_001836_add_google_auth_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (357,'2023_07_05_092237_change_settings_table_increase_saml_idp_metadata_size',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (358,'2023_07_06_092507_add_phone_fax_to_locations',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (359,'2023_07_13_052204_denormalized_eol_and_add_column_for_explicit_date_to_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (360,'2023_07_14_004221_add_show_in_list_view_to_custom_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (361,'2023_08_01_174150_change_webhook_settings_variable_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (362,'2023_08_13_172600_add_email_to_companies',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (363,'2023_08_17_202638_add_last_checkin_to_assets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (364,'2023_08_21_064609_add_name_ordering_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (365,'2023_08_21_181742_add_min_amt_to_models_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (366,'2023_08_23_232739_create_report_templates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (367,'2023_09_13_200913_fix_asset_model_min_qty_nullability',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (368,'2023_10_25_064324_add_show_in_requestable_to_custom_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (369,'2023_12_14_032522_add_remote_ip_and_action_source_to_action_logs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (370,'2023_12_15_024643_add_indexes_to_new_activity_report_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (371,'2023_12_19_081112_fix_language_dirs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (372,'2024_01_24_145544_create_saml_nonce_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (373,'2024_02_28_080016_add_created_by_to_permission_groups',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (374,'2024_02_28_093807_add_min_qty_to_licenses',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (375,'2024_03_18_164714_add_note_to_checkout_acceptance_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (376,'2024_03_18_221612_update_legacy_locale',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (377,'2024_05_27_143554_add_parent_id_index_to_locations',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (378,'2024_06_24_130348_add_profile_edit_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (379,'2024_07_04_103729_add_default_avatar_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (380,'2024_07_16_184145_add_deprecitation_type_to_depreciations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (381,'2024_07_23_172032_change_no__n_o_to_nb__n_o',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (382,'2024_07_26_143301_add_checkout_for_all_types_to_accessories',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (383,'2024_08_01_201721_add_required_notes_setting',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (384,'2024_08_06_175114_add_shortcuts_enabled_to_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (385,'2024_08_07_204014_add_play_sounds_to_profile',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (386,'2024_08_15_111816_add_confetti_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (387,'2024_08_16_104137_add_due_checkin_days_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (388,'2024_09_17_204302_change_user_id_to_created_by',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (389,'2024_10_23_162301_add_manufacturer_id_model_number_to_consumables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (390,'2024_10_31_212512_update_new_and_drop_old_barcode_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (391,'2024_11_06_211457_add_manager_indexes_to_location_and_user',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (392,'2024_11_07_113631_improve_manager_indexes_on_users_and_locations',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (393,'2025_01_06_210534_change_report_templates_options_to_column_text_field',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (394,'2025_01_07_172419_fix_label_types_on_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (395,'2025_01_15_190348_adds_unavailable_to_license_seats_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (396,'2025_02_10_230155_add_notes_to_locations_companies_categories_manufacturers_groups',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (397,'2025_02_22_144518_add_checkin_checkout_to_customfields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (398,'2025_02_26_153413_add_ldap_invert_active_flag_to_setting_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (399,'2025_03_04_231256_purge_action_logs_of_report_template_activity',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (400,'2025_03_06_152922_copy_created_at_to_action_date_in_action_logs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (401,'2025_03_12_184851_purge_action_logs_table_of_consumable_checkin_entries',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (402,'2025_04_02_113438_add_acceptance_pdf_logo_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (403,'2025_04_07_145418_add_custom_fields_to_audit',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (404,'2025_04_22_170731_add_empty_row_count_to_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (405,'2025_05_12_183803_add_req_serial_bool_to_models_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (406,'2025_05_20_190317_repopulate_webhook_selected_setting',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (407,'2025_06_02_233556_add_admin_cc_always_to_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (408,'2025_06_03_000000_add_manager_view_enabled_to_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (409,'2025_06_03_053438_fix_assigned_type_without_assigned_to',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (410,'2025_06_04_101736_add_deleted_at_index_to_action_logs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (411,'2025_06_05_200518_add_alert_on_response_id_to_checkout_acceptances_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (412,'2025_06_05_204139_add_alert_on_response_to_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (413,'2025_06_06_155058_make_supplier_id_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (414,'2025_07_16_142036_clean_checkout_acceptances',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (415,'2025_08_06_192954_add_image_to_maintenances',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (416,'2025_08_10_111553_rename_title_to_name_on_asset_maintenances',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (417,'2025_08_10_113444_rename_asset_maintenances_to_maintenances',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (418,'2025_08_10_114135_change_asset_maintenance_in_logs_to_maintenance',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (419,'2025_08_11_181519_add_mobile_number_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (420,'2025_08_12_225214_add_qty_to_checkout_acceptances_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (421,'2025_08_19_114742_add_display_name_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (422,'2025_08_19_122533_add_category_indexes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (423,'2025_08_19_174823_add_display_name_to_ldap_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (424,'2025_08_20_190617_add_created_at_index_to_models',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (425,'2025_09_16_104604_create_users_deleted_at_location_id_index',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (426,'2025_09_25_124321_add_external_id_to_groups',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (427,'2025_10_07_113331_add_url_to_maintenances',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (428,'2025_10_13_102956_move_assetmodels_files',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (429,'2025_10_22_144927_migrate_incorrect_action_types',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (430,'2025_11_04_173713_add_2d_label_prefix',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (431,'2025_11_10_205136_change_suppliers_notes_to_text',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (432,'2025_11_10_211219_change_suppliers_notes_to_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (433,'2025_11_13_160816_add_day_of_week_setting',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (434,'2025_11_14_150613_add_color_to_companies',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (435,'2025_11_18_193933_add_share_report_template_option',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (436,'2025_11_28_175733_add_link_colors_to_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (437,'2025_12_10_211855_add_quantity_to_action_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (438,'2026_03_26_150316_add_deleted_at_to_companies',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (439,'2026_03_27_000000_clean_checkout_acceptances',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (440,'2026_04_07_141412_add_locations_parent_id_index',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (441,'2026_04_07_141930_accessories_checkout_assigned_to_index',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (442,'2026_04_20_200000_backfill_action_logs_company_id_from_item',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (443,'2026_05_05_125206_add_unique_index_to_nonces',1);
