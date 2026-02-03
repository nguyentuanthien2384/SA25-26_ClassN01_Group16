-- ============================================================================
-- Database Per Service - Creation Script
-- ============================================================================
-- Execute this script manually when ready to separate databases
-- DO NOT run via Laravel migration (use raw SQL execution)
-- ============================================================================

-- 1. CATALOG DATABASE
-- ============================================================================
CREATE DATABASE IF NOT EXISTS catalog_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'catalog_user'@'localhost' IDENTIFIED BY 'catalog_password_change_me';
GRANT SELECT, INSERT, UPDATE, DELETE ON catalog_db.* TO 'catalog_user'@'localhost';
FLUSH PRIVILEGES;

-- 2. CUSTOMER DATABASE
-- ============================================================================
CREATE DATABASE IF NOT EXISTS customer_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'customer_user'@'localhost' IDENTIFIED BY 'customer_password_change_me';
GRANT SELECT, INSERT, UPDATE, DELETE ON customer_db.* TO 'customer_user'@'localhost';
FLUSH PRIVILEGES;

-- 3. ORDER DATABASE
-- ============================================================================
CREATE DATABASE IF NOT EXISTS order_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'order_user'@'localhost' IDENTIFIED BY 'order_password_change_me';
GRANT SELECT, INSERT, UPDATE, DELETE ON order_db.* TO 'order_user'@'localhost';
FLUSH PRIVILEGES;

-- 4. CONTENT DATABASE
-- ============================================================================
CREATE DATABASE IF NOT EXISTS content_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'content_user'@'localhost' IDENTIFIED BY 'content_password_change_me';
GRANT SELECT, INSERT, UPDATE, DELETE ON content_db.* TO 'content_user'@'localhost';
FLUSH PRIVILEGES;

-- ============================================================================
-- NOTES:
-- ============================================================================
-- 1. Change passwords before running in production
-- 2. Cart data will move to Redis (in-memory)
-- 3. Review & Support can share databases initially or use separate ones
-- 4. After creating databases, migrate tables:
--    - Copy table structure: CREATE TABLE catalog_db.products LIKE main_db.products;
--    - Copy data: INSERT INTO catalog_db.products SELECT * FROM main_db.products;
-- 5. Update Laravel config/database.php with new connections
-- 6. Update Models to specify connection
-- ============================================================================

-- Migration Script (Run after creating DBs)
-- ============================================================================
-- USE catalog_db;
-- CREATE TABLE products LIKE csdl.products;
-- INSERT INTO products SELECT * FROM csdl.products;
-- 
-- CREATE TABLE categories LIKE csdl.categories;
-- INSERT INTO categories SELECT * FROM csdl.categories;
-- 
-- CREATE TABLE pro_images LIKE csdl.pro_images;
-- INSERT INTO pro_images SELECT * FROM csdl.pro_images;
-- ============================================================================
