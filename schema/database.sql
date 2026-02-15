-- Bloombit - Complete Database
-- Import this single file via phpMyAdmin to create all tables and default data

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE DATABASE IF NOT EXISTS bloombit
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;
USE bloombit;

-- Users
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  name VARCHAR(255) DEFAULT '',
  role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  email_verified TINYINT(1) NOT NULL DEFAULT 0,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_email (email),
  INDEX idx_users_role (role)
) ENGINE=InnoDB;

-- Site settings (key-value)
CREATE TABLE IF NOT EXISTS site_settings (
  `key` VARCHAR(100) NOT NULL PRIMARY KEY,
  value TEXT,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Investment plans
CREATE TABLE IF NOT EXISTS plans (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  min_deposit DECIMAL(18,2) NOT NULL DEFAULT 0,
  max_deposit DECIMAL(18,2) DEFAULT NULL,
  yield_min DECIMAL(5,2) NOT NULL DEFAULT 0,
  yield_max DECIMAL(5,2) NOT NULL DEFAULT 0,
  duration_days INT UNSIGNED NOT NULL DEFAULT 30,
  withdrawal_days INT UNSIGNED DEFAULT 7,
  features_json JSON DEFAULT NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_plans_enabled (enabled),
  INDEX idx_plans_sort (sort_order)
) ENGINE=InnoDB;

-- User wallet balances
CREATE TABLE IF NOT EXISTS wallet_balances (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  currency VARCHAR(20) NOT NULL,
  amount DECIMAL(36,18) NOT NULL DEFAULT 0,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_wallet_user_currency (user_id, currency),
  INDEX idx_wallet_user (user_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- User investments (active plan subscriptions)
CREATE TABLE IF NOT EXISTS user_investments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  plan_id INT UNSIGNED NOT NULL,
  amount DECIMAL(18,2) NOT NULL,
  start_date DATE NOT NULL,
  status ENUM('active', 'completed', 'cancelled') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_inv_user (user_id),
  INDEX idx_inv_plan (plan_id),
  INDEX idx_inv_status (status),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Transactions (deposits, withdrawals, payouts)
CREATE TABLE IF NOT EXISTS transactions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  type ENUM('deposit', 'withdrawal', 'payout', 'investment') NOT NULL,
  amount DECIMAL(18,2) NOT NULL,
  currency VARCHAR(20) NOT NULL DEFAULT 'USD',
  status ENUM('pending', 'completed', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
  reference VARCHAR(255) DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_tx_user (user_id),
  INDEX idx_tx_type (type),
  INDEX idx_tx_status (status),
  INDEX idx_tx_created (created_at),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Default site settings
INSERT INTO site_settings (`key`, value) VALUES
  ('site_name', 'Bloombit'),
  ('tagline', 'AI Crypto Trading'),
  ('stats_assets', '$4.2B+'),
  ('stats_bots', '85k+'),
  ('stats_uptime', '99.9%'),
  ('stats_roi', '12.4%'),
  ('market_cap', '$2.45T'),
  ('volume_24h', '$84.2B'),
  ('btc_dominance', '52.4%'),
  ('active_traders', '12.8M+'),
  ('investors_count', '45000'),
  ('hero_badge', 'AI ENGINE V4.0 NOW LIVE'),
  ('contact_email', 'legal@bloombit.com'),
  ('footer_description', 'Leading the future of decentralized finance with advanced artificial intelligence and machine learning technologies.')
ON DUPLICATE KEY UPDATE value = VALUES(value);

-- Default plans
INSERT INTO plans (name, slug, min_deposit, max_deposit, yield_min, yield_max, duration_days, withdrawal_days, features_json, enabled, sort_order) VALUES
  ('Starter', 'starter', 100, 2500, 0.5, 1.2, 30, 7, '["$100 - $2,500 Deposit","Basic AI Strategy","Weekly Withdrawals"]', 1, 1),
  ('Growth', 'growth', 2501, 10000, 1.5, 2.5, 30, 3, '["$2,501 - $10,000 Deposit","Advanced AI Strategy","Bi-Weekly Withdrawals","Priority Support"]', 1, 2),
  ('Premium', 'premium', 10001, NULL, 3.0, 5.0, 30, 0, '["$10,001+ Deposit","Institutional AI Strategy","Instant Withdrawals","Dedicated Portfolio Manager"]', 1, 3)
ON DUPLICATE KEY UPDATE
  name = VALUES(name),
  min_deposit = VALUES(min_deposit),
  max_deposit = VALUES(max_deposit),
  yield_min = VALUES(yield_min),
  yield_max = VALUES(yield_max),
  features_json = VALUES(features_json);
