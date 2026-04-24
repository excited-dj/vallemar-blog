-- ============================================================
-- Colegio Vallemar · Blog · Database Setup
-- Run this in phpMyAdmin or MySQL CLI BEFORE using the blog
-- ============================================================

CREATE DATABASE IF NOT EXISTS vallemar_blog
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE vallemar_blog;

-- Tables are created automatically by PHP on first visit (initDB())
-- But you can also run the SQL from includes/db.php manually here.

-- Optional: create a dedicated MySQL user
-- CREATE USER 'vallemar'@'localhost' IDENTIFIED BY 'your_password';
-- GRANT ALL PRIVILEGES ON vallemar_blog.* TO 'vallemar'@'localhost';
-- FLUSH PRIVILEGES;
