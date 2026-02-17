-- Bloombit - Migration (Update Existing Database)
-- Use this to update your server database with the latest schema changes.
-- Safe to run on existing databases - only adds missing columns/tables, does not delete data.
--
-- In phpMyAdmin: Select your database → Import → Choose this file → Execute
--
-- NOTE: This migration only includes RECENT changes. If your database is fresh, use database.sql instead.

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Add missing user columns (safe - checks if column exists first)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'two_factor_enabled');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE users ADD COLUMN two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER active', 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'admin_notes');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE users ADD COLUMN admin_notes TEXT NULL AFTER updated_at', 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'avatar_url');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE users ADD COLUMN avatar_url VARCHAR(500) NULL AFTER admin_notes', 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'phone_number');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE users ADD COLUMN phone_number VARCHAR(50) NULL AFTER avatar_url', 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'referral_code');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE users ADD COLUMN referral_code VARCHAR(100) NULL AFTER phone_number', 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create coins table if not exists
CREATE TABLE IF NOT EXISTS coins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  coin_key VARCHAR(50) NOT NULL UNIQUE,
  display_name VARCHAR(100) NOT NULL,
  symbol VARCHAR(20) NOT NULL,
  logo VARCHAR(500) DEFAULT NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_coins_enabled (enabled),
  INDEX idx_coins_sort (sort_order)
) ENGINE=InnoDB;

-- Create wallet_addresses table if not exists
CREATE TABLE IF NOT EXISTS wallet_addresses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  coin_id INT UNSIGNED NOT NULL,
  address VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_wallet_addresses_coin (coin_id),
  FOREIGN KEY (coin_id) REFERENCES coins(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Add missing plan columns
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'plans' AND COLUMN_NAME = 'description');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE plans ADD COLUMN description TEXT NULL AFTER slug', 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'plans' AND COLUMN_NAME = 'icon');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE plans ADD COLUMN icon VARCHAR(50) NULL AFTER description', 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'plans' AND COLUMN_NAME = 'min_duration_months');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE plans ADD COLUMN min_duration_months INT UNSIGNED NULL AFTER withdrawal_days', 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'plans' AND COLUMN_NAME = 'max_duration_months');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE plans ADD COLUMN max_duration_months INT UNSIGNED NULL AFTER min_duration_months', 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add min_duration_days, max_duration_days to plans (flexible user duration)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'plans' AND COLUMN_NAME = 'min_duration_days');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE plans ADD COLUMN min_duration_days INT UNSIGNED NULL AFTER max_duration_months', 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'plans' AND COLUMN_NAME = 'max_duration_days');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE plans ADD COLUMN max_duration_days INT UNSIGNED NULL AFTER min_duration_days', 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add duration_days to user_investments (user's chosen duration)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user_investments' AND COLUMN_NAME = 'duration_days');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE user_investments ADD COLUMN duration_days INT UNSIGNED NULL AFTER amount', 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add 'paused' status to user_investments (for pausing daily earnings)
SET @col = (SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user_investments' AND COLUMN_NAME = 'status');
SET @has_paused = IF(@col LIKE '%paused%', 1, 0);
SET @sql = IF(@has_paused = 0, 
    "ALTER TABLE user_investments MODIFY COLUMN status ENUM('active', 'paused', 'completed', 'cancelled') NOT NULL DEFAULT 'active'",
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ensure transactions table has 'rejected' status (if using older schema)
SET @enum_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'transactions' 
    AND COLUMN_NAME = 'status' 
    AND COLUMN_TYPE LIKE '%rejected%');
SET @sql = IF(@enum_exists = 0, 
    'ALTER TABLE transactions MODIFY COLUMN status ENUM(\'pending\', \'completed\', \'rejected\', \'cancelled\') NOT NULL DEFAULT \'pending\'', 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
