-- Clear all test data from database
-- Run this in phpMyAdmin to reset the database

-- Delete all location tracking data
DELETE FROM employee_locations;

-- Delete all tracking sessions
DELETE FROM tracking_sessions;

-- Delete all devices
DELETE FROM devices;

-- Delete all auto-registered employees (keep only manual ones if needed)
-- This keeps employees 1-6 (demo data) and deletes 7+ (auto-registered devices)
DELETE FROM employees WHERE id >= 7;

-- Reset auto-increment counters
ALTER TABLE employee_locations AUTO_INCREMENT = 1;
ALTER TABLE tracking_sessions AUTO_INCREMENT = 1;
ALTER TABLE devices AUTO_INCREMENT = 1;
ALTER TABLE employees AUTO_INCREMENT = 7;

-- Verify deletion
SELECT 'Employees remaining:' as info, COUNT(*) as count FROM employees
UNION ALL
SELECT 'Devices remaining:', COUNT(*) FROM devices
UNION ALL
SELECT 'Locations remaining:', COUNT(*) FROM employee_locations
UNION ALL
SELECT 'Sessions remaining:', COUNT(*) FROM tracking_sessions;
