-- Employee Tracking System - Complete Database Setup
-- Run this SQL file in phpMyAdmin to initialize the entire database
-- Created: December 15, 2025

-- Note: Replace 'employee_tracking' with your actual database name if different

USE employee_tracking;

-- ==================================================
-- 1. CREATE EMPLOYEES TABLE
-- ==================================================
CREATE TABLE IF NOT EXISTS `employees` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `is_active` BOOLEAN DEFAULT TRUE,
    `device_id` VARCHAR(255) UNIQUE,
    `last_seen_at` TIMESTAMP NULL,
    `remember_token` VARCHAR(100),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_device_id` (`device_id`),
    INDEX `idx_last_seen` (`last_seen_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==================================================
-- 2. CREATE DEVICES TABLE (NEW)
-- ==================================================
CREATE TABLE IF NOT EXISTS `devices` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `employee_id` BIGINT UNSIGNED DEFAULT NULL,
    `device_id` VARCHAR(255) NOT NULL UNIQUE,
    `device_name` VARCHAR(255) DEFAULT NULL,
    `device_model` VARCHAR(255) DEFAULT NULL,
    `device_manufacturer` VARCHAR(255) DEFAULT NULL,
    `os_name` VARCHAR(255) DEFAULT NULL,
    `os_version` VARCHAR(255) DEFAULT NULL,
    `app_version` VARCHAR(255) DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `last_seen_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_device_id` (`device_id`),
    INDEX `idx_employee_id` (`employee_id`),
    CONSTRAINT `devices_employee_id_foreign` 
        FOREIGN KEY (`employee_id`) 
        REFERENCES `employees` (`id`) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==================================================
-- 3. CREATE EMPLOYEE_LOCATIONS TABLE
-- ==================================================
CREATE TABLE IF NOT EXISTS `employee_locations` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `employee_id` BIGINT UNSIGNED NOT NULL,
    `lat` DECIMAL(10, 7) NOT NULL,
    `lng` DECIMAL(10, 7) NOT NULL,
    `accuracy` DECIMAL(10, 2),
    `speed` DECIMAL(10, 2),
    `heading` DECIMAL(10, 2),
    `battery` INT,
    `device_os` VARCHAR(50),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_employee_id` (`employee_id`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_employee_time` (`employee_id`, `created_at`),
    CONSTRAINT `employee_locations_employee_id_foreign` 
        FOREIGN KEY (`employee_id`) 
        REFERENCES `employees` (`id`) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==================================================
-- 4. CREATE TRACKING_SESSIONS TABLE
-- ==================================================
CREATE TABLE IF NOT EXISTS `tracking_sessions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `employee_id` BIGINT UNSIGNED NOT NULL,
    `started_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `ended_at` TIMESTAMP NULL,
    `total_locations` INT DEFAULT 0,
    `distance_km` DECIMAL(10, 2) DEFAULT 0,
    `device_os` VARCHAR(50),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_employee_id` (`employee_id`),
    INDEX `idx_started_at` (`started_at`),
    CONSTRAINT `tracking_sessions_employee_id_foreign` 
        FOREIGN KEY (`employee_id`) 
        REFERENCES `employees` (`id`) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==================================================
-- 5. VERIFY TABLES
-- ==================================================
-- Show all tables to verify creation
SHOW TABLES;

-- Show structure of each table
DESCRIBE employees;
DESCRIBE devices;
DESCRIBE employee_locations;
DESCRIBE tracking_sessions;

-- ==================================================
-- SETUP COMPLETE!
-- ==================================================
-- Next steps:
-- 1. Upload your Laravel backend to the server
-- 2. Configure .env file with database credentials
-- 3. Test API endpoints with curl or Postman
-- 4. Deploy mobile app APK
-- ==================================================
