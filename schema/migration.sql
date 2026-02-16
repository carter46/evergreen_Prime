-- Bloombit - Migration (Update Existing Database)
-- Use this to update your server database with the latest schema and defaults.
-- Safe to run on existing databases - creates missing tables, updates defaults, does not delete data.
--
-- In phpMyAdmin: Select your database → Import → Choose this file → Execute

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

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

-- Default site settings (admins can change these later)
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
  ('support_email', 'support@bloombit.com'),
  ('footer_description', 'Leading the future of decentralized finance with advanced artificial intelligence and machine learning technologies.'),
  ('hero_title', 'Smarter Crypto Investing Powered by Advanced AI'),
  ('hero_subtitle', 'Automate your wealth with institutional-grade machine learning algorithms. Deploy sophisticated bots that trade 24/7 while you sleep.'),
  ('min_withdrawal_limit', '10'),
  ('max_active_plans_per_user', '3'),
  ('compounding_enabled', '0')
ON DUPLICATE KEY UPDATE value = VALUES(value);

-- Default admin user (email: admin@mail.com)
INSERT INTO users (email, password_hash, name, role, email_verified, active) VALUES
  ('admin@mail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'admin', 1, 1)
ON DUPLICATE KEY UPDATE role = 'admin', email_verified = 1, active = 1;

-- Add two_factor_enabled and admin_notes (MySQL 8.0.29+). For older MySQL, run manually: ALTER TABLE users ADD COLUMN two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER active; ALTER TABLE users ADD COLUMN admin_notes TEXT NULL AFTER updated_at; (ignore duplicate column errors)
ALTER TABLE users ADD COLUMN IF NOT EXISTS two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER active;
ALTER TABLE users ADD COLUMN IF NOT EXISTS admin_notes TEXT NULL AFTER updated_at;
ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar_url VARCHAR(500) NULL AFTER admin_notes;
ALTER TABLE users ADD COLUMN IF NOT EXISTS phone_number VARCHAR(50) NULL AFTER avatar_url;
ALTER TABLE users ADD COLUMN IF NOT EXISTS referral_code VARCHAR(100) NULL AFTER phone_number;

-- Create coins table (if not exists)
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

-- Create wallet_addresses table (if not exists)
CREATE TABLE IF NOT EXISTS wallet_addresses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  coin_id INT UNSIGNED NOT NULL,
  address VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_wallet_addresses_coin (coin_id),
  FOREIGN KEY (coin_id) REFERENCES coins(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed default coins (CoinGecko IDs + logos; 30 popular coins)
INSERT INTO coins (coin_key, display_name, symbol, logo, enabled, sort_order) VALUES
  ('bitcoin', 'Bitcoin', 'BTC', 'https://assets.coingecko.com/coins/images/1/large/bitcoin.png', 1, 1),
  ('ethereum', 'Ethereum', 'ETH', 'https://assets.coingecko.com/coins/images/279/large/ethereum.png', 1, 2),
  ('tether', 'Tether', 'USDT', 'https://assets.coingecko.com/coins/images/325/large/Tether.png', 1, 3),
  ('solana', 'Solana', 'SOL', 'https://assets.coingecko.com/coins/images/4128/large/solana.png', 1, 4),
  ('bnb', 'BNB', 'BNB', 'https://assets.coingecko.com/coins/images/825/large/bnb-icon2_2x.png', 1, 5),
  ('ripple', 'XRP', 'XRP', 'https://assets.coingecko.com/coins/images/44/large/xrp-symbol-white-128.png', 1, 6),
  ('cardano', 'Cardano', 'ADA', 'https://assets.coingecko.com/coins/images/975/large/cardano.png', 1, 7),
  ('dogecoin', 'Dogecoin', 'DOGE', 'https://assets.coingecko.com/coins/images/5/large/dogecoin.png', 1, 8),
  ('polkadot', 'Polkadot', 'DOT', 'https://assets.coingecko.com/coins/images/12171/large/polkadot.png', 1, 9),
  ('avalanche-2', 'Avalanche', 'AVAX', 'https://assets.coingecko.com/coins/images/12559/large/Avalanche_Circle_RedWhite_Trans.png', 1, 10),
  ('matic-network', 'Polygon', 'MATIC', 'https://assets.coingecko.com/coins/images/4713/large/matic-token-icon.png', 1, 11),
  ('chainlink', 'Chainlink', 'LINK', 'https://assets.coingecko.com/coins/images/877/large/chainlink-new-logo.png', 1, 12),
  ('uniswap', 'Uniswap', 'UNI', 'https://assets.coingecko.com/coins/images/12504/large/uni.jpg', 1, 13),
  ('cosmos', 'Cosmos', 'ATOM', 'https://assets.coingecko.com/coins/images/1481/large/cosmos_hub.png', 1, 14),
  ('litecoin', 'Litecoin', 'LTC', 'https://assets.coingecko.com/coins/images/2/large/litecoin.png', 1, 15),
  ('bitcoin-cash', 'Bitcoin Cash', 'BCH', 'https://assets.coingecko.com/coins/images/780/large/bitcoin-cash-circle.png', 1, 16),
  ('stellar', 'Stellar', 'XLM', 'https://assets.coingecko.com/coins/images/100/large/Stellar_symbol_black_RGB.png', 1, 17),
  ('algorand', 'Algorand', 'ALGO', 'https://assets.coingecko.com/coins/images/4380/large/download.png', 1, 18),
  ('tron', 'TRON', 'TRX', 'https://assets.coingecko.com/coins/images/1094/large/tron-logo.png', 1, 19),
  ('monero', 'Monero', 'XMR', 'https://assets.coingecko.com/coins/images/69/large/monero_logo.png', 1, 20),
  ('ethereum-classic', 'Ethereum Classic', 'ETC', 'https://assets.coingecko.com/coins/images/453/large/ethereum-classic-logo.png', 1, 21),
  ('filecoin', 'Filecoin', 'FIL', 'https://assets.coingecko.com/coins/images/12817/large/filecoin.png', 1, 22),
  ('dai', 'Dai', 'DAI', 'https://assets.coingecko.com/coins/images/9956/large/Badge_Dai.png', 1, 23),
  ('shiba-inu', 'Shiba Inu', 'SHIB', 'https://assets.coingecko.com/coins/images/11939/large/shiba.png', 1, 24),
  ('near', 'NEAR Protocol', 'NEAR', 'https://assets.coingecko.com/coins/images/10365/large/near.jpg', 1, 25),
  ('aptos', 'Aptos', 'APT', 'https://assets.coingecko.com/coins/images/26455/large/aptos_round.png', 1, 26),
  ('arbitrum', 'Arbitrum', 'ARB', 'https://assets.coingecko.com/coins/images/16547/large/photo_2023-03-29_21.47.00.jpeg', 1, 27),
  ('optimism', 'Optimism', 'OP', 'https://assets.coingecko.com/coins/images/25244/large/Optimism.png', 1, 28),
  ('vechain', 'VeChain', 'VET', 'https://assets.coingecko.com/coins/images/1167/large/VeChain-Logo-768x725.png', 1, 29),
  ('hedera-hashgraph', 'Hedera', 'HBAR', 'https://assets.coingecko.com/coins/images/3688/large/hbar.png', 1, 30)
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), symbol = VALUES(symbol), logo = VALUES(logo), enabled = VALUES(enabled), sort_order = VALUES(sort_order);

-- Add plan fields: description, icon, min_duration_months, max_duration_months
ALTER TABLE plans ADD COLUMN IF NOT EXISTS description TEXT NULL AFTER slug;
ALTER TABLE plans ADD COLUMN IF NOT EXISTS icon VARCHAR(50) NULL AFTER description;
ALTER TABLE plans ADD COLUMN IF NOT EXISTS min_duration_months INT UNSIGNED NULL AFTER withdrawal_days;
ALTER TABLE plans ADD COLUMN IF NOT EXISTS max_duration_months INT UNSIGNED NULL AFTER min_duration_months;

-- Demo users (password: password - same as Laravel default hash)
INSERT INTO users (email, password_hash, name, role, email_verified, active, two_factor_enabled) VALUES
  ('j.donovan@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'James Donovan', 'user', 1, 1, 1),
  ('sarah.j@outlook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah Jenkins', 'user', 0, 1, 0),
  ('stoic.trader@proton.me', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Marcus Aurelius', 'user', 0, 0, 0),
  ('dchen.finance@yahoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David Chen', 'user', 1, 1, 0)
ON DUPLICATE KEY UPDATE name = VALUES(name), active = VALUES(active);

-- Demo wallet balances (assumes demo user ids - we use subqueries to get ids by email)
INSERT INTO wallet_balances (user_id, currency, amount)
SELECT id, 'BTC', 1.2482 FROM users WHERE email = 'j.donovan@gmail.com' AND role = 'user' LIMIT 1
ON DUPLICATE KEY UPDATE amount = VALUES(amount);
INSERT INTO wallet_balances (user_id, currency, amount)
SELECT id, 'ETH', 4.821 FROM users WHERE email = 'j.donovan@gmail.com' AND role = 'user' LIMIT 1
ON DUPLICATE KEY UPDATE amount = VALUES(amount);
INSERT INTO wallet_balances (user_id, currency, amount)
SELECT id, 'BTC', 0.241 FROM users WHERE email = 'sarah.j@outlook.com' AND role = 'user' LIMIT 1
ON DUPLICATE KEY UPDATE amount = VALUES(amount);
INSERT INTO wallet_balances (user_id, currency, amount)
SELECT id, 'BTC', 3.52 FROM users WHERE email = 'dchen.finance@yahoo.com' AND role = 'user' LIMIT 1
ON DUPLICATE KEY UPDATE amount = VALUES(amount);

-- Demo investments (link users to plans) - only if not already present
INSERT INTO user_investments (user_id, plan_id, amount, start_date, status)
SELECT u.id, 1, 1200, CURDATE() - INTERVAL 62 DAY, 'active' FROM users u
WHERE u.email = 'j.donovan@gmail.com' AND u.role = 'user'
AND NOT EXISTS (SELECT 1 FROM user_investments ui WHERE ui.user_id = u.id AND ui.plan_id = 1) LIMIT 1;
INSERT INTO user_investments (user_id, plan_id, amount, start_date, status)
SELECT u.id, 2, 25000, CURDATE() - INTERVAL 14 DAY, 'active' FROM users u
WHERE u.email = 'j.donovan@gmail.com' AND u.role = 'user'
AND NOT EXISTS (SELECT 1 FROM user_investments ui WHERE ui.user_id = u.id AND ui.plan_id = 2) LIMIT 1;
INSERT INTO user_investments (user_id, plan_id, amount, start_date, status)
SELECT u.id, 1, 5000, CURDATE() - INTERVAL 30 DAY, 'active' FROM users u
WHERE u.email = 'sarah.j@outlook.com' AND u.role = 'user'
AND NOT EXISTS (SELECT 1 FROM user_investments ui WHERE ui.user_id = u.id AND ui.plan_id = 1) LIMIT 1;
INSERT INTO user_investments (user_id, plan_id, amount, start_date, status)
SELECT u.id, 3, 50000, CURDATE() - INTERVAL 90 DAY, 'active' FROM users u
WHERE u.email = 'dchen.finance@yahoo.com' AND u.role = 'user'
AND NOT EXISTS (SELECT 1 FROM user_investments ui WHERE ui.user_id = u.id AND ui.plan_id = 3) LIMIT 1;

-- Demo transactions for Recent Activity
INSERT INTO transactions (user_id, type, amount, currency, status)
SELECT id, 'deposit', 500, 'USD', 'completed' FROM users WHERE email = 'j.donovan@gmail.com' AND role = 'user' LIMIT 1;
INSERT INTO transactions (user_id, type, amount, currency, status)
SELECT id, 'withdrawal', 500, 'USD', 'pending' FROM users WHERE email = 'j.donovan@gmail.com' AND role = 'user' LIMIT 1;
INSERT INTO transactions (user_id, type, amount, currency, status)
SELECT id, 'deposit', 1200, 'USD', 'completed' FROM users WHERE email = 'sarah.j@outlook.com' AND role = 'user' LIMIT 1;

-- Default plans (admins can edit in plan management)
INSERT INTO plans (name, slug, min_deposit, max_deposit, yield_min, yield_max, duration_days, withdrawal_days, features_json, enabled, sort_order) VALUES
  ('Starter', 'starter', 100, 2500, 0.5, 1.2, 30, 7, '["$100 - $2,500 Deposit Range","Basic AI Trading Strategy","Weekly Withdrawals","2 Active Trading Bots","Email Support","Standard Execution Speed"]', 1, 1),
  ('Growth', 'growth', 2501, 10000, 1.5, 2.5, 30, 3, '["$2,501 - $10,000 Deposit Range","Advanced AI Strategy","Bi-Weekly Withdrawals","10 Active Trading Bots","Priority AI Sentiment Core","24/7 Live Chat Support","Advanced Analytics Pro"]', 1, 2),
  ('Premium', 'premium', 10001, NULL, 3.0, 5.0, 30, 0, '["$10,001+ Deposit (No Cap)","Institutional AI Strategy","Instant Withdrawals","Unlimited Trading Bots","Dedicated Portfolio Manager","Custom Strategy API Access","Low-Latency Node Direct"]', 1, 3)
ON DUPLICATE KEY UPDATE
  name = VALUES(name),
  min_deposit = VALUES(min_deposit),
  max_deposit = VALUES(max_deposit),
  yield_min = VALUES(yield_min),
  yield_max = VALUES(yield_max),
  duration_days = VALUES(duration_days),
  withdrawal_days = VALUES(withdrawal_days),
  features_json = VALUES(features_json),
  enabled = VALUES(enabled),
  sort_order = VALUES(sort_order);
