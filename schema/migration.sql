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

-- Cache user last-known USD balance (for stable admin display without live CoinGecko)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'last_balance_usd');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE users ADD COLUMN last_balance_usd DECIMAL(18,2) NOT NULL DEFAULT 0 AFTER referral_code',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'last_balance_usd_updated_at');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE users ADD COLUMN last_balance_usd_updated_at DATETIME NULL AFTER last_balance_usd',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- One-time backfill: initialize cached USD balance from stable currencies only (no external pricing)
UPDATE users u
SET
  u.last_balance_usd = (
    SELECT COALESCE(SUM(wb.amount), 0)
    FROM wallet_balances wb
    WHERE wb.user_id = u.id AND UPPER(wb.currency) IN ('USD','USDT','USDC','BUSD','DAI')
  ),
  u.last_balance_usd_updated_at = COALESCE(u.last_balance_usd_updated_at, NOW())
WHERE u.role = 'user' AND (u.last_balance_usd_updated_at IS NULL);

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

-- Ensure transactions amount supports fractional USDT credits (for 5-min interval)
SET @col = (SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transactions' AND COLUMN_NAME = 'amount');
SET @has_precision = IF(LOWER(@col) LIKE 'decimal(36,18)%', 1, 0);
SET @sql = IF(@has_precision = 0,
    'ALTER TABLE transactions MODIFY COLUMN amount DECIMAL(36,18) NOT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add kyc_status and country to users
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'kyc_status');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE users ADD COLUMN kyc_status ENUM(\'none\', \'pending\', \'verified\', \'rejected\') NOT NULL DEFAULT \'none\' AFTER referral_code', 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'country');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE users ADD COLUMN country VARCHAR(100) NULL AFTER phone_number', 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add last_earnings_at to user_investments (for earnings distribution intervals)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user_investments' AND COLUMN_NAME = 'last_earnings_at');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE user_investments ADD COLUMN last_earnings_at DATETIME NULL AFTER status', 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- AI Bot Config / Earnings site settings
INSERT INTO site_settings (`key`, value) VALUES
  ('min_withdrawal_limit', '10'),
  ('max_withdrawal_limit', '50000'),
  ('earnings_paused', '0'),
  ('distribution_interval', 'daily'),
  ('distribution_start_time', '09:00:00')
ON DUPLICATE KEY UPDATE value = value;

-- Ensure transactions.amount supports fractional USDT payouts (DECIMAL 36,18)
SET @tx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transactions' AND COLUMN_NAME = 'amount');
SET @tx_scale = (SELECT NUMERIC_SCALE FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transactions' AND COLUMN_NAME = 'amount');
SET @sql = IF(@tx_exists = 0,
    'SELECT 1',
    IF(@tx_scale IS NULL OR @tx_scale < 18,
        'ALTER TABLE transactions MODIFY COLUMN amount DECIMAL(36,18) NOT NULL',
        'SELECT 1'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add transactions.amount_usd for USD-first deposit/withdraw flows
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transactions' AND COLUMN_NAME = 'amount_usd');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE transactions ADD COLUMN amount_usd DECIMAL(18,2) NULL AFTER amount',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- OTP Infrastructure (registration, login, disable_2fa)
CREATE TABLE IF NOT EXISTS email_otp_codes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  otp CHAR(6) NOT NULL,
  purpose ENUM('register','login','disable_2fa') NOT NULL,
  expires_at DATETIME NOT NULL,
  used TINYINT(1) DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_email_purpose (email, purpose),
  INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create kyc_submissions table
CREATE TABLE IF NOT EXISTS kyc_submissions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  document_type ENUM('passport', 'id_card', 'driver_license') NOT NULL,
  front_path VARCHAR(500) NOT NULL,
  back_path VARCHAR(500) NULL,
  full_name VARCHAR(255) NOT NULL,
  date_of_birth DATE NULL,
  address TEXT NULL,
  status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  rejection_reason TEXT NULL,
  reviewed_by INT UNSIGNED NULL,
  reviewed_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_kyc_user (user_id),
  INDEX idx_kyc_status (status),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Broadcast campaigns (for Communication Hub history)
CREATE TABLE IF NOT EXISTS broadcast_campaigns (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  subject VARCHAR(255) NOT NULL,
  recipients_filter VARCHAR(50) NOT NULL DEFAULT 'all',
  total_recipients INT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('sent','draft') NOT NULL DEFAULT 'sent',
  sent_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
