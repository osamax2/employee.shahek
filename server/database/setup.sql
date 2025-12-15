-- Employee Tracking System - Direct SQL Setup
-- Alternative to Laravel migrations for quick setup

-- Create database
CREATE DATABASE IF NOT EXISTS employee_tracking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE employee_tracking;

-- Drop tables if exist (be careful in production!)
DROP TABLE IF EXISTS employee_locations;
DROP TABLE IF EXISTS tracking_sessions;
DROP TABLE IF EXISTS employees;

-- Create employees table
CREATE TABLE employees (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    device_id VARCHAR(255) UNIQUE,
    last_seen_at TIMESTAMP NULL,
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_device_id (device_id),
    INDEX idx_last_seen (last_seen_at)
) ENGINE=InnoDB;

-- Create employee_locations table
CREATE TABLE employee_locations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id BIGINT UNSIGNED NOT NULL,
    lat DECIMAL(10, 7) NOT NULL,
    lng DECIMAL(10, 7) NOT NULL,
    accuracy DECIMAL(10, 2),
    speed DECIMAL(10, 2),
    heading DECIMAL(10, 2),
    battery INT,
    device_os VARCHAR(50),
    device_version VARCHAR(50),
    recorded_at TIMESTAMP NOT NULL,
    received_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    INDEX idx_employee_recorded (employee_id, recorded_at),
    INDEX idx_recorded_at (recorded_at)
) ENGINE=InnoDB;

-- Create tracking_sessions table
CREATE TABLE tracking_sessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id BIGINT UNSIGNED NOT NULL,
    started_at TIMESTAMP NOT NULL,
    ended_at TIMESTAMP NULL,
    location_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    INDEX idx_employee_started (employee_id, started_at)
) ENGINE=InnoDB;

-- Insert sample admin user
-- Password: admin123 (bcrypt hash)
INSERT INTO employees (name, email, password, is_active) VALUES 
('Administrator', 'admin@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE);

-- Insert test employees
INSERT INTO employees (name, email, password, device_id, is_active) VALUES 
('Device Test 1', 'device1@device.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'test-device-1', TRUE),
('Device Test 2', 'device2@device.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'test-device-2', TRUE);

-- Grant privileges (adjust username/password as needed)
-- GRANT ALL PRIVILEGES ON employee_tracking.* TO 'tracking_user'@'localhost' IDENTIFIED BY 'secure_password';
-- FLUSH PRIVILEGES;

-- Verify tables
SHOW TABLES;

-- Show structure
DESCRIBE employees;
DESCRIBE employee_locations;
DESCRIBE tracking_sessions;

-- Sample query: Get latest location per employee
SELECT 
    e.id,
    e.name,
    e.email,
    l.lat,
    l.lng,
    l.battery,
    l.recorded_at,
    CASE 
        WHEN e.last_seen_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) THEN 'Online'
        ELSE 'Offline'
    END AS status
FROM employees e
LEFT JOIN employee_locations l ON e.id = l.employee_id
WHERE l.id IN (
    SELECT MAX(id) 
    FROM employee_locations 
    GROUP BY employee_id
)
ORDER BY e.name;
