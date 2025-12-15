-- ============================================
-- Employee Tracking System - Complete Database Setup
-- For direct import into phpMyAdmin
-- 
-- Database: shahek_employee
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Drop tables if they exist (clean install)
--

DROP TABLE IF EXISTS `employee_locations`;
DROP TABLE IF EXISTS `tracking_sessions`;
DROP TABLE IF EXISTS `employees`;

-- ============================================
-- Table: employees
-- ============================================

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `device_id` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_email_unique` (`email`),
  UNIQUE KEY `employees_device_id_unique` (`device_id`),
  KEY `employees_is_active_index` (`is_active`),
  KEY `employees_last_seen_at_index` (`last_seen_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: employee_locations
-- ============================================

CREATE TABLE `employee_locations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `latitude` decimal(10, 8) NOT NULL,
  `longitude` decimal(11, 8) NOT NULL,
  `accuracy` decimal(8, 2) DEFAULT NULL,
  `altitude` decimal(8, 2) DEFAULT NULL,
  `speed` decimal(8, 2) DEFAULT NULL,
  `heading` decimal(8, 2) DEFAULT NULL,
  `battery_level` decimal(5, 2) DEFAULT NULL,
  `is_charging` tinyint(1) DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_locations_employee_id_foreign` (`employee_id`),
  KEY `employee_locations_recorded_at_index` (`recorded_at`),
  KEY `employee_locations_latitude_longitude_index` (`latitude`, `longitude`),
  CONSTRAINT `employee_locations_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: tracking_sessions
-- ============================================

CREATE TABLE `tracking_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ended_at` timestamp NULL DEFAULT NULL,
  `total_locations` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tracking_sessions_employee_id_foreign` (`employee_id`),
  KEY `tracking_sessions_started_at_index` (`started_at`),
  CONSTRAINT `tracking_sessions_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Insert Admin User
-- Email: admin@company.com
-- Password: admin123
-- ============================================

INSERT INTO `employees` (`id`, `name`, `email`, `password`, `device_id`, `is_active`, `last_seen_at`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin-device-001', 1, NULL, NOW(), NOW());

-- ============================================
-- Insert Test Employees (Optional)
-- All test employees use password: password123
-- ============================================

INSERT INTO `employees` (`id`, `name`, `email`, `password`, `device_id`, `is_active`, `last_seen_at`, `created_at`, `updated_at`) VALUES
(2, 'John Smith', 'employee1@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'device-employee-001', 1, NULL, NOW(), NOW()),
(3, 'Sarah Johnson', 'employee2@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'device-employee-002', 1, NULL, NOW(), NOW()),
(4, 'Michael Brown', 'employee3@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'device-employee-003', 1, NULL, NOW(), NOW());

-- ============================================
-- Insert Sample Location Data (Optional)
-- ============================================

INSERT INTO `employee_locations` (`employee_id`, `latitude`, `longitude`, `accuracy`, `altitude`, `speed`, `heading`, `battery_level`, `is_charging`, `recorded_at`, `created_at`, `updated_at`) VALUES
(2, 40.712776, -74.005974, 10.50, 15.00, 0.00, 0.00, 85.00, 0, DATE_SUB(NOW(), INTERVAL 5 MINUTE), NOW(), NOW()),
(3, 34.052235, -118.243683, 12.00, 20.00, 5.50, 45.00, 72.00, 1, DATE_SUB(NOW(), INTERVAL 3 MINUTE), NOW(), NOW()),
(4, 51.507351, -0.127758, 8.00, 10.00, 2.30, 90.00, 95.00, 0, DATE_SUB(NOW(), INTERVAL 1 MINUTE), NOW(), NOW());

-- ============================================
-- Reset AUTO_INCREMENT values
-- ============================================

ALTER TABLE `employees` AUTO_INCREMENT = 5;
ALTER TABLE `employee_locations` AUTO_INCREMENT = 4;
ALTER TABLE `tracking_sessions` AUTO_INCREMENT = 1;

-- ============================================
-- Setup Complete!
-- ============================================

-- ✅ Database tables created successfully
-- ✅ Admin user created: admin@company.com / admin123
-- ✅ 3 test employees created (optional)
-- ✅ Sample location data inserted (optional)
--
-- ⚠️ IMPORTANT: Change the admin password after first login!
--
-- Next steps:
-- 1. Test API: https://employee.shahek.org/api/auth/login
-- 2. Visit Dashboard: https://employee.shahek.org/admin/dashboard
-- 3. Login with: admin@company.com / admin123
-- 4. Change default passwords
-- 5. Deploy mobile app
