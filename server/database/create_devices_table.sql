-- Create devices table for employee device registration
-- This table stores information about devices used by employees

CREATE TABLE IF NOT EXISTS `devices` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `device_id` varchar(255) NOT NULL,
  `device_name` varchar(255) DEFAULT NULL,
  `device_model` varchar(255) DEFAULT NULL,
  `device_manufacturer` varchar(255) DEFAULT NULL,
  `os_name` varchar(255) DEFAULT NULL,
  `os_version` varchar(255) DEFAULT NULL,
  `app_version` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `devices_device_id_unique` (`device_id`),
  KEY `devices_device_id_index` (`device_id`),
  KEY `devices_employee_id_index` (`employee_id`),
  CONSTRAINT `devices_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
