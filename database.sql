-- ============================================================
-- Campus Gebeta — Full Database Schema (v2 with Role System)
-- ============================================================
CREATE DATABASE IF NOT EXISTS campus_gebeta;
USE campus_gebeta;

-- ── Users ────────────────────────────────────────────────────
-- Roles: student | seller | lecturer | admin
-- Status: active | suspended
CREATE TABLE IF NOT EXISTS users (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    name           VARCHAR(100)  NOT NULL,
    email          VARCHAR(100)  NOT NULL UNIQUE,
    password       VARCHAR(255)  NOT NULL,
    role           ENUM('student','seller','lecturer','admin') DEFAULT 'student',
    status         ENUM('active','suspended') DEFAULT 'active',
    wallet_balance DECIMAL(10,2) DEFAULT 0.00,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin user (password: 1212)
INSERT INTO users (name, email, password, role, status)
VALUES ('Admin', 'admin@campusgebeta.com',
        '$2y$10$5boPe7xQylcplR0re3LLoOPgumIc8s8FJIk7LPUYuG.F/WV42EZo.', 'admin', 'active')
ON DUPLICATE KEY UPDATE email = email;

-- ── Menu Items ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS menu_items (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(100) NOT NULL,
    description  TEXT,
    price        DECIMAL(10,2) NOT NULL,
    image_url    VARCHAR(255),
    category     VARCHAR(50),
    is_available BOOLEAN DEFAULT TRUE,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Orders ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS orders (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status       ENUM('Pending','Preparing','Ready','Served','Cancelled') DEFAULT 'Pending',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ── Order Items ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS order_items (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    order_id     INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity     INT NOT NULL,
    price        DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id)     REFERENCES orders(id)     ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
);

-- ── Marketplace Items (Gebeta Market) ────────────────────────
-- Posted by sellers or admins only (enforced at app level)
CREATE TABLE IF NOT EXISTS marketplace_items (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    title       VARCHAR(150) NOT NULL,
    description TEXT,
    price       DECIMAL(10,2) NOT NULL,
    category    VARCHAR(50),
    image_url   VARCHAR(255),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ── Notices (Noticeboard) ────────────────────────────────────
-- Announcements & Events: admins + lecturers only
-- Lost & Found: all logged-in users
CREATE TABLE IF NOT EXISTS notices (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    type       ENUM('event','lost_found','announcement') DEFAULT 'announcement',
    title      VARCHAR(150) NOT NULL,
    content    TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ── User Favorites ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS user_favorites (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT NOT NULL,
    menu_item_id INT NOT NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)      REFERENCES users(id)      ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, menu_item_id)
);

-- ── Wallet Transactions ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS wallet_transactions (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    amount      DECIMAL(10,2) NOT NULL,
    type        ENUM('credit','debit') NOT NULL,
    description VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ── Meal Plans ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS meal_plans (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    description   TEXT,
    price         DECIMAL(10,2) NOT NULL,
    meals_per_week INT NOT NULL,
    duration_days  INT DEFAULT 30,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── User Meal Plans ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS user_meal_plans (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NOT NULL,
    plan_id         INT NOT NULL,
    meals_remaining INT NOT NULL,
    start_date      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_date        DATETIME NOT NULL,
    status          ENUM('active','expired') DEFAULT 'active',
    FOREIGN KEY (user_id)  REFERENCES users(id)       ON DELETE CASCADE,
    FOREIGN KEY (plan_id)  REFERENCES meal_plans(id)  ON DELETE CASCADE
);

-- ============================================================
-- MIGRATION: Run this block if upgrading an EXISTING database
-- ============================================================
-- ALTER TABLE users MODIFY COLUMN role ENUM('student','seller','lecturer','admin') DEFAULT 'student';
-- ALTER TABLE users ADD COLUMN status ENUM('active','suspended') DEFAULT 'active' AFTER role;
