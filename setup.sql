-- ============================================================
-- Colegio Vallemar · Blog · Database Setup (WITH LANG SUPPORT)
-- ============================================================

CREATE DATABASE IF NOT EXISTS vallemar_blog
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE vallemar_blog;

-- =========================
-- POSTS (С ЯЗЫКОМ)
-- =========================
CREATE TABLE IF NOT EXISTS posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  image VARCHAR(255),
  category_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  lang VARCHAR(5) DEFAULT 'ru'  -- 🔥 ВОТ ГЛАВНОЕ
);

-- =========================
-- CATEGORIES
-- =========================
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  color VARCHAR(20) DEFAULT '#000'
);

-- =========================
-- COMMENTS
-- =========================
CREATE TABLE IF NOT EXISTS comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT,
  author VARCHAR(100),
  content TEXT,
  approved TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- ПРИМЕРЫ СТАТЕЙ
-- =========================
INSERT INTO posts (title, content, lang) VALUES
('Пример статья RU', 'Это пример русской статьи', 'ru'),
('Example EN post', 'This is an English article', 'en');
