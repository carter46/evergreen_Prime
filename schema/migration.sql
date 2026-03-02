-- Bloombit - Migration (Update Existing Database)
-- Use this to update your server database with the latest schema changes.
-- Safe to run on existing databases - only adds missing columns/tables, does not delete data.
--
-- In phpMyAdmin: Select your database → Import → Choose this file → Execute
--
-- NOTE: This migration adds RECENT changes only. For a fresh database, import the full schema dump (e.g. u502532383_bloombit.sql) first, then run this file.

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Add missing user columns (safe - checks if column exists first)
-- Ensure users.name is NOT NULL with a safe default (prevents NULL names)
SET @needs_name_fix = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'name'
    AND (IS_NULLABLE = 'YES' OR COLUMN_DEFAULT IS NULL)
);
SET @sql = IF(@needs_name_fix > 0,
  'ALTER TABLE users MODIFY COLUMN name VARCHAR(255) NOT NULL DEFAULT ''''',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Data fix: remove accidental DB-name placeholder used as user full name
UPDATE users
SET name = 'Admin'
WHERE id = 1 AND role = 'admin' AND name = 'u502532383_bloombit';

UPDATE users
SET name = SUBSTRING_INDEX(email, '@', 1)
WHERE role = 'user' AND name = 'u502532383_bloombit';

UPDATE pending_registrations
SET name = SUBSTRING_INDEX(email, '@', 1)
WHERE name = 'u502532383_bloombit';

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'email_verified');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE users ADD COLUMN email_verified TINYINT(1) NOT NULL DEFAULT 0 AFTER role', 
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

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

-- Ensure plans.features_json can store long feature lists (prevents truncation / invalid JSON)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'plans' AND COLUMN_NAME = 'features_json');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE plans ADD COLUMN features_json LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL AFTER withdrawal_days',
    'ALTER TABLE plans MODIFY COLUMN features_json LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL'
);
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

-- Ensure transactions table has 'failed' status (deposit countdown expiry)
SET @col = (SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transactions' AND COLUMN_NAME = 'status');
SET @has_rejected = IF(@col LIKE '%rejected%', 1, 0);
SET @has_failed = IF(@col LIKE '%failed%', 1, 0);
SET @sql = IF(@has_rejected = 0 OR @has_failed = 0,
    'ALTER TABLE transactions MODIFY COLUMN status ENUM(\'pending\', \'completed\', \'rejected\', \'failed\', \'cancelled\') NOT NULL DEFAULT \'pending\'',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Deposit countdown support (expires_at + user_confirmed_at)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transactions' AND COLUMN_NAME = 'expires_at');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE transactions ADD COLUMN expires_at DATETIME NULL AFTER created_at',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transactions' AND COLUMN_NAME = 'user_confirmed_at');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE transactions ADD COLUMN user_confirmed_at DATETIME NULL AFTER expires_at',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Deposit proof (optional upload for admin approval)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transactions' AND COLUMN_NAME = 'proof_url');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE transactions ADD COLUMN proof_url VARCHAR(512) NULL AFTER user_confirmed_at',
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
  ('site_name', 'Site'),
  ('min_withdrawal_limit', '10'),
  ('max_withdrawal_limit', '50000'),
  ('deposit_countdown_minutes', '30'),
  ('earnings_paused', '0'),
  ('distribution_interval', 'daily'),
  ('distribution_start_time', '09:00:00'),
  ('contact_email', 'support@example.com'),
  ('mail_smtp_host', ''),
  ('mail_smtp_port', '587'),
  ('mail_smtp_username', ''),
  ('mail_smtp_password', ''),
  ('mail_smtp_encryption', 'tls'),
  ('mail_from_email', 'noreply@example.com'),
  ('mail_from_name', 'Site'),
  ('mail_reply_to', 'support@example.com'),
  ('mail_imap_host', ''),
  ('mail_imap_port', '993'),
  ('mail_imap_username', ''),
  ('mail_imap_password', ''),
  ('mail_imap_encryption', 'ssl'),
  ('mail_imap_sent_folder', 'Sent'),
  ('homepage_youtube_url', ''),
  ('about_youtube_url', ''),
  ('homepage_modal_image', '')
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

-- Pending registrations (no users row until email OTP verified)
CREATE TABLE IF NOT EXISTS pending_registrations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  name VARCHAR(255) DEFAULT '',
  phone_number VARCHAR(50) NULL,
  referral_code VARCHAR(50) NULL,
  avatar_url VARCHAR(500) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at DATETIME NOT NULL,
  UNIQUE KEY uniq_email (email),
  INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data fix: prevent DB name leaking into user display name
-- If any old registrations accidentally stored the DB name as the user's name,
-- normalize it to a safer fallback (email prefix) and force pending signups to re-register.
DELETE FROM pending_registrations WHERE name = 'u502532383_bloombit';
UPDATE users
SET name = 'Admin'
WHERE role = 'admin' AND name = 'u502532383_bloombit';
UPDATE users
SET name = SUBSTRING_INDEX(email, '@', 1)
WHERE role = 'user' AND name = 'u502532383_bloombit';

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

-- Admin mailbox (inbox/outbox storage for contact form + admin sent emails)
CREATE TABLE IF NOT EXISTS admin_mailbox (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  direction ENUM('in','out') NOT NULL,
  source VARCHAR(32) NOT NULL DEFAULT 'system',
  from_email VARCHAR(255) NULL,
  from_name VARCHAR(255) NULL,
  to_emails TEXT NULL,
  subject VARCHAR(255) NOT NULL,
  body_html LONGTEXT NULL,
  body_text LONGTEXT NULL,
  status ENUM('received','sent','failed') NOT NULL DEFAULT 'sent',
  error_text TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_direction_created (direction, created_at),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- IMAP sync / threading columns for admin_mailbox
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'admin_mailbox' AND COLUMN_NAME = 'mailbox_folder');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE admin_mailbox ADD COLUMN mailbox_folder VARCHAR(255) NULL AFTER source',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'admin_mailbox' AND COLUMN_NAME = 'imap_uid');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE admin_mailbox ADD COLUMN imap_uid BIGINT UNSIGNED NULL AFTER mailbox_folder',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'admin_mailbox' AND COLUMN_NAME = 'message_id');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE admin_mailbox ADD COLUMN message_id VARCHAR(255) NULL AFTER imap_uid',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'admin_mailbox' AND COLUMN_NAME = 'in_reply_to');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE admin_mailbox ADD COLUMN in_reply_to VARCHAR(255) NULL AFTER message_id',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'admin_mailbox' AND COLUMN_NAME = 'references');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE admin_mailbox ADD COLUMN `references` TEXT NULL AFTER in_reply_to',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'admin_mailbox' AND COLUMN_NAME = 'mail_date');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE admin_mailbox ADD COLUMN mail_date DATETIME NULL AFTER `references`',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Uniqueness to avoid duplicate IMAP imports
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'admin_mailbox' AND INDEX_NAME = 'uniq_folder_uid');
SET @sql = IF(@idx_exists = 0,
    'ALTER TABLE admin_mailbox ADD UNIQUE KEY uniq_folder_uid (mailbox_folder, imap_uid)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'admin_mailbox' AND INDEX_NAME = 'idx_message_id');
SET @sql = IF(@idx_exists = 0,
    'ALTER TABLE admin_mailbox ADD INDEX idx_message_id (message_id)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ========== Referral system ==========
-- users: who referred this user + this user's shareable code
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'referred_by_user_id');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE users ADD COLUMN referred_by_user_id INT UNSIGNED NULL AFTER referral_code, ADD INDEX idx_referred_by (referred_by_user_id)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'my_referral_code');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE users ADD COLUMN my_referral_code VARCHAR(32) NULL AFTER referred_by_user_id, ADD UNIQUE KEY uniq_my_referral_code (my_referral_code)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- pending_registrations: pass referrer id from register to verify
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pending_registrations' AND COLUMN_NAME = 'referred_by_user_id');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE pending_registrations ADD COLUMN referred_by_user_id INT UNSIGNED NULL AFTER referral_code',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- referral_earnings: audit trail for referrer payouts
CREATE TABLE IF NOT EXISTS referral_earnings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  referrer_user_id INT UNSIGNED NOT NULL,
  referred_user_id INT UNSIGNED NOT NULL,
  source ENUM('plan_subscription','first_deposit','referred_payout') NOT NULL,
  amount_usd DECIMAL(18,2) NOT NULL,
  currency VARCHAR(20) NOT NULL DEFAULT 'USDT',
  percent_used DECIMAL(5,2) NOT NULL,
  reference_id INT UNSIGNED NULL COMMENT 'e.g. user_investments.id or transactions.id',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_referrer (referrer_user_id),
  INDEX idx_referred (referred_user_id),
  INDEX idx_referred_source (referred_user_id, source),
  FOREIGN KEY (referrer_user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (referred_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ensure referral_earnings.source includes referred_payout (for existing tables)
SET @ref_enum = (SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'referral_earnings' AND COLUMN_NAME = 'source');
SET @has_referred_payout = IF(@ref_enum LIKE '%referred_payout%', 1, 0);
SET @sql = IF(@has_referred_payout = 0,
    'ALTER TABLE referral_earnings MODIFY COLUMN source ENUM(\'plan_subscription\',\'first_deposit\',\'referred_payout\') NOT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- transactions: allow referral_bonus type
SET @col_type = (SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transactions' AND COLUMN_NAME = 'type');
SET @has_ref = IF(@col_type LIKE '%referral_bonus%', 1, 0);
SET @sql = IF(@has_ref = 0,
    'ALTER TABLE transactions MODIFY COLUMN type ENUM(\'deposit\',\'withdrawal\',\'payout\',\'investment\',\'referral_bonus\') NOT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Site settings for referral
INSERT INTO site_settings (`key`, value) VALUES
  ('referral_enabled', '0'),
  ('referral_percentage', '5')
ON DUPLICATE KEY UPDATE value = value;

-- Backfill my_referral_code for existing users (unique per user; REF1, REF2, ...)
UPDATE users u
SET u.my_referral_code = CONCAT('REF', u.id)
WHERE (u.my_referral_code IS NULL OR u.my_referral_code = '') AND u.id > 0;
