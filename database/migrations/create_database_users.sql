-- ============================================================================
-- Create Database Users for Microservices
-- ============================================================================
-- Pattern: Database Per Service
-- Each service has its own database user with limited permissions
-- 
-- Usage: mysql -u root -p < create_database_users.sql
-- ============================================================================

-- Drop users if exist (for clean setup)
DROP USER IF EXISTS 'catalog_user'@'localhost';
DROP USER IF EXISTS 'customer_user'@'localhost';
DROP USER IF EXISTS 'order_user'@'localhost';
DROP USER IF EXISTS 'content_user'@'localhost';

-- Create users
CREATE USER 'catalog_user'@'localhost' IDENTIFIED BY 'catalog_pass_2026';
CREATE USER 'customer_user'@'localhost' IDENTIFIED BY 'customer_pass_2026';
CREATE USER 'order_user'@'localhost' IDENTIFIED BY 'order_pass_2026';
CREATE USER 'content_user'@'localhost' IDENTIFIED BY 'content_pass_2026';

-- Grant permissions (only to their own database)
GRANT ALL PRIVILEGES ON catalog_db.* TO 'catalog_user'@'localhost';
GRANT ALL PRIVILEGES ON customer_db.* TO 'customer_user'@'localhost';
GRANT ALL PRIVILEGES ON order_db.* TO 'order_user'@'localhost';
GRANT ALL PRIVILEGES ON content_db.* TO 'content_user'@'localhost';

-- For Docker (allow from any host)
DROP USER IF EXISTS 'catalog_user'@'%';
DROP USER IF EXISTS 'customer_user'@'%';
DROP USER IF EXISTS 'order_user'@'%';
DROP USER IF EXISTS 'content_user'@'%';

CREATE USER 'catalog_user'@'%' IDENTIFIED BY 'catalog_pass_2026';
CREATE USER 'customer_user'@'%' IDENTIFIED BY 'customer_pass_2026';
CREATE USER 'order_user'@'%' IDENTIFIED BY 'order_pass_2026';
CREATE USER 'content_user'@'%' IDENTIFIED BY 'content_pass_2026';

GRANT ALL PRIVILEGES ON catalog_db.* TO 'catalog_user'@'%';
GRANT ALL PRIVILEGES ON customer_db.* TO 'customer_user'@'%';
GRANT ALL PRIVILEGES ON order_db.* TO 'order_user'@'%';
GRANT ALL PRIVILEGES ON content_db.* TO 'content_user'@'%';

-- Flush privileges
FLUSH PRIVILEGES;

-- Show created users
SELECT User, Host FROM mysql.user WHERE User LIKE '%_user';

-- ============================================================================
-- SECURITY NOTES:
-- ============================================================================
-- 1. Change passwords in production!
-- 2. Use environment variables for passwords
-- 3. Grant only necessary permissions
-- 4. Consider using SSL/TLS for connections
-- 5. Rotate passwords regularly
-- ============================================================================
