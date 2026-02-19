-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 19, 2026 at 05:45 PM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u502532383_bloombit`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_mailbox`
--

CREATE TABLE `admin_mailbox` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `direction` enum('in','out') NOT NULL,
  `source` varchar(32) NOT NULL DEFAULT 'system',
  `mailbox_folder` varchar(255) DEFAULT NULL,
  `imap_uid` bigint(20) UNSIGNED DEFAULT NULL,
  `message_id` varchar(255) DEFAULT NULL,
  `in_reply_to` varchar(255) DEFAULT NULL,
  `references` text DEFAULT NULL,
  `mail_date` datetime DEFAULT NULL,
  `from_email` varchar(255) DEFAULT NULL,
  `from_name` varchar(255) DEFAULT NULL,
  `to_emails` text DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `body_html` longtext DEFAULT NULL,
  `body_text` longtext DEFAULT NULL,
  `status` enum('received','sent','failed') NOT NULL DEFAULT 'sent',
  `error_text` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_mailbox`
--

INSERT INTO `admin_mailbox` (`id`, `direction`, `source`, `mailbox_folder`, `imap_uid`, `message_id`, `in_reply_to`, `references`, `mail_date`, `from_email`, `from_name`, `to_emails`, `subject`, `body_html`, `body_text`, `status`, `error_text`, `created_at`) VALUES
(1, 'out', 'admin_compose', NULL, NULL, NULL, NULL, NULL, NULL, 'support@bloombitfx.com', 'Bloombit', 'mr.carter.tech07@gmail.com', 'reminder of event meeeting', NULL, 'ggggs', 'sent', NULL, '2026-02-18 17:46:55'),
(2, 'out', 'admin_compose', NULL, NULL, NULL, NULL, NULL, NULL, 'support@bloombitfx.com', 'Bloombit', 'j.donovan@gmail.com, billyfredrickgibbons@gmail.com', 'reminder of event meeeting', NULL, 'ggggs', 'sent', NULL, '2026-02-18 17:46:56');

-- --------------------------------------------------------

--
-- Table structure for table `broadcast_campaigns`
--

CREATE TABLE `broadcast_campaigns` (
  `id` int(10) UNSIGNED NOT NULL,
  `subject` varchar(255) NOT NULL,
  `recipients_filter` varchar(50) NOT NULL DEFAULT 'all',
  `total_recipients` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` enum('sent','draft') NOT NULL DEFAULT 'sent',
  `sent_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `broadcast_campaigns`
--

INSERT INTO `broadcast_campaigns` (`id`, `subject`, `recipients_filter`, `total_recipients`, `status`, `sent_at`, `created_at`) VALUES
(1, 'reminder of event meeeting', 'manual', 1, 'sent', '2026-02-18 17:46:55', '2026-02-18 17:46:55'),
(2, 'reminder of event meeeting', 'all', 2, 'sent', '2026-02-18 17:46:56', '2026-02-18 17:46:56');

-- --------------------------------------------------------

--
-- Table structure for table `coins`
--

CREATE TABLE `coins` (
  `id` int(10) UNSIGNED NOT NULL,
  `coin_key` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `symbol` varchar(20) NOT NULL,
  `logo` varchar(500) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coins`
--

INSERT INTO `coins` (`id`, `coin_key`, `display_name`, `symbol`, `logo`, `enabled`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'bitcoin', 'Bitcoin', 'BTC', 'https://assets.coingecko.com/coins/images/1/large/bitcoin.png', 1, 1, '2026-02-15 23:56:22', '2026-02-15 23:56:22'),
(2, 'ethereum', 'Ethereum', 'ETH', 'https://assets.coingecko.com/coins/images/279/large/ethereum.png', 1, 2, '2026-02-15 23:56:22', '2026-02-15 23:56:22'),
(3, 'tether', 'Tether', 'USDT', 'https://assets.coingecko.com/coins/images/325/large/Tether.png', 1, 3, '2026-02-15 23:56:22', '2026-02-15 23:56:22'),
(4, 'solana', 'Solana', 'SOL', 'https://assets.coingecko.com/coins/images/4128/large/solana.png', 1, 4, '2026-02-15 23:56:22', '2026-02-15 23:56:22'),
(5, 'bnb', 'BNB', 'BNB', 'https://assets.coingecko.com/coins/images/825/large/bnb-icon2_2x.png', 1, 5, '2026-02-15 23:56:22', '2026-02-15 23:56:22'),
(6, 'ripple', 'XRP', 'XRP', 'https://assets.coingecko.com/coins/images/44/large/xrp-symbol-white-128.png', 1, 6, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(7, 'cardano', 'Cardano', 'ADA', 'https://assets.coingecko.com/coins/images/975/large/cardano.png', 1, 7, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(8, 'dogecoin', 'Dogecoin', 'DOGE', 'https://assets.coingecko.com/coins/images/5/large/dogecoin.png', 1, 8, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(9, 'polkadot', 'Polkadot', 'DOT', 'https://assets.coingecko.com/coins/images/12171/large/polkadot.png', 1, 9, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(10, 'avalanche-2', 'Avalanche', 'AVAX', 'https://assets.coingecko.com/coins/images/12559/large/Avalanche_Circle_RedWhite_Trans.png', 1, 10, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(11, 'matic-network', 'Polygon', 'MATIC', 'https://assets.coingecko.com/coins/images/4713/large/matic-token-icon.png', 1, 11, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(12, 'chainlink', 'Chainlink', 'LINK', 'https://assets.coingecko.com/coins/images/877/large/chainlink-new-logo.png', 1, 12, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(13, 'uniswap', 'Uniswap', 'UNI', 'https://assets.coingecko.com/coins/images/12504/large/uni.jpg', 1, 13, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(14, 'cosmos', 'Cosmos', 'ATOM', 'https://assets.coingecko.com/coins/images/1481/large/cosmos_hub.png', 1, 14, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(15, 'litecoin', 'Litecoin', 'LTC', 'https://assets.coingecko.com/coins/images/2/large/litecoin.png', 1, 15, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(16, 'bitcoin-cash', 'Bitcoin Cash', 'BCH', 'https://assets.coingecko.com/coins/images/780/large/bitcoin-cash-circle.png', 1, 16, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(17, 'stellar', 'Stellar', 'XLM', 'https://assets.coingecko.com/coins/images/100/large/Stellar_symbol_black_RGB.png', 1, 17, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(18, 'algorand', 'Algorand', 'ALGO', 'https://assets.coingecko.com/coins/images/4380/large/download.png', 1, 18, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(19, 'tron', 'TRON', 'TRX', 'https://assets.coingecko.com/coins/images/1094/large/tron-logo.png', 1, 19, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(20, 'monero', 'Monero', 'XMR', 'https://assets.coingecko.com/coins/images/69/large/monero_logo.png', 1, 20, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(21, 'ethereum-classic', 'Ethereum Classic', 'ETC', 'https://assets.coingecko.com/coins/images/453/large/ethereum-classic-logo.png', 1, 21, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(22, 'filecoin', 'Filecoin', 'FIL', 'https://assets.coingecko.com/coins/images/12817/large/filecoin.png', 1, 22, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(23, 'dai', 'Dai', 'DAI', 'https://assets.coingecko.com/coins/images/9956/large/Badge_Dai.png', 1, 23, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(24, 'shiba-inu', 'Shiba Inu', 'SHIB', 'https://assets.coingecko.com/coins/images/11939/large/shiba.png', 1, 24, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(25, 'near', 'NEAR Protocol', 'NEAR', 'https://assets.coingecko.com/coins/images/10365/large/near.jpg', 1, 25, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(26, 'aptos', 'Aptos', 'APT', 'https://assets.coingecko.com/coins/images/26455/large/aptos_round.png', 1, 26, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(27, 'arbitrum', 'Arbitrum', 'ARB', 'https://assets.coingecko.com/coins/images/16547/large/photo_2023-03-29_21.47.00.jpeg', 1, 27, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(28, 'optimism', 'Optimism', 'OP', 'https://assets.coingecko.com/coins/images/25244/large/Optimism.png', 1, 28, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(29, 'vechain', 'VeChain', 'VET', 'https://assets.coingecko.com/coins/images/1167/large/VeChain-Logo-768x725.png', 1, 29, '2026-02-16 00:42:41', '2026-02-16 00:42:41'),
(30, 'hedera-hashgraph', 'Hedera', 'HBAR', 'https://assets.coingecko.com/coins/images/3688/large/hbar.png', 1, 30, '2026-02-16 00:42:41', '2026-02-16 00:42:41');

-- --------------------------------------------------------

--
-- Table structure for table `email_otp_codes`
--

CREATE TABLE `email_otp_codes` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` char(6) NOT NULL,
  `purpose` enum('register','login','disable_2fa') NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_otp_codes`
--

INSERT INTO `email_otp_codes` (`id`, `email`, `otp`, `purpose`, `expires_at`, `used`, `created_at`) VALUES
(1, 'billyfredrickgibbons@gmail.com', '577084', 'disable_2fa', '2026-02-18 03:43:20', 0, '2026-02-18 03:33:20'),
(2, 'j.ani.cem.endo.zz.a.a@gmail.com', '585178', 'register', '2026-02-18 21:34:03', 0, '2026-02-18 21:24:03'),
(3, 'e.l.ian.tr.a.v.is.s.s@gmail.com', '571074', 'register', '2026-02-18 21:43:51', 0, '2026-02-18 21:33:51'),
(4, 'j.o.n.n.a.r.uel.in@gmail.com', '974977', 'register', '2026-02-18 21:53:44', 0, '2026-02-18 21:43:44'),
(5, 'mr.carter.tech07@gmail.com', '738084', 'register', '2026-02-19 10:12:30', 1, '2026-02-19 10:02:30'),
(6, 'mr.carter.tech07@gmail.com', '567563', 'register', '2026-02-19 17:37:57', 1, '2026-02-19 17:27:57');

-- --------------------------------------------------------

--
-- Table structure for table `kyc_submissions`
--

CREATE TABLE `kyc_submissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `document_type` enum('passport','id_card','driver_license') NOT NULL,
  `front_path` varchar(500) NOT NULL,
  `back_path` varchar(500) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kyc_submissions`
--

INSERT INTO `kyc_submissions` (`id`, `user_id`, `document_type`, `front_path`, `back_path`, `full_name`, `date_of_birth`, `address`, `status`, `rejection_reason`, `reviewed_by`, `reviewed_at`, `created_at`) VALUES
(1, 9, 'passport', 'uploads/kyc/9/front_1771331346_32c66798.jpg', 'uploads/kyc/9/back_1771331346_498d4063.jpg', 'James Donovan', '2026-02-11', 'fssfsf', 'approved', NULL, 1, '2026-02-17 12:29:32', '2026-02-17 12:29:06');

-- --------------------------------------------------------

--
-- Table structure for table `pending_registrations`
--

CREATE TABLE `pending_registrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT '',
  `phone_number` varchar(50) DEFAULT NULL,
  `referral_code` varchar(50) DEFAULT NULL,
  `avatar_url` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `min_deposit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `max_deposit` decimal(18,2) DEFAULT NULL,
  `yield_min` decimal(5,2) NOT NULL DEFAULT 0.00,
  `yield_max` decimal(5,2) NOT NULL DEFAULT 0.00,
  `duration_days` int(10) UNSIGNED NOT NULL DEFAULT 30,
  `withdrawal_days` int(10) UNSIGNED DEFAULT 7,
  `min_duration_months` int(10) UNSIGNED DEFAULT NULL,
  `max_duration_months` int(10) UNSIGNED DEFAULT NULL,
  `min_duration_days` int(10) UNSIGNED DEFAULT NULL,
  `max_duration_days` int(10) UNSIGNED DEFAULT NULL,
  `features_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features_json`)),
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `name`, `slug`, `description`, `icon`, `min_deposit`, `max_deposit`, `yield_min`, `yield_max`, `duration_days`, `withdrawal_days`, `min_duration_months`, `max_duration_months`, `min_duration_days`, `max_duration_days`, `features_json`, `enabled`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Basic', 'basic', NULL, 'trending_up', 100.00, 2500.00, 10.00, 10.00, 60, 7, 1, 3, 30, 90, '[]', 1, 0, '2026-02-15 01:07:56', '2026-02-18 01:51:16'),
(2, 'Growth', 'growth', NULL, NULL, 2501.00, 10000.00, 1.50, 2.50, 30, 3, NULL, NULL, NULL, NULL, '[\"$2,501 - $10,000 Deposit Range\",\"Advanced AI Strategy\",\"Bi-Weekly Withdrawals\",\"10 Active Trading Bots\",\"Priority AI Sentiment Core\",\"24/7 Live Chat Support\",\"Advanced Analytics Pro\"]', 1, 2, '2026-02-15 01:07:56', '2026-02-15 01:33:55'),
(3, 'Premium', 'premium', NULL, NULL, 10001.00, NULL, 3.00, 5.00, 30, 0, NULL, NULL, NULL, NULL, '[\"$10,001+ Deposit (No Cap)\",\"Institutional AI Strategy\",\"Instant Withdrawals\",\"Unlimited Trading Bots\",\"Dedicated Portfolio Manager\",\"Custom Strategy API Access\",\"Low-Latency Node Direct\"]', 1, 3, '2026-02-15 01:07:56', '2026-02-15 01:33:55');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`key`, `value`, `updated_at`) VALUES
('about_youtube_url', 'https://www.youtube.com/watch?v=c-ZQL2VxSrE', '2026-02-19 08:45:01'),
('active_traders', '12.8M+', '2026-02-15 01:07:56'),
('btc_dominance', '52.4%', '2026-02-15 01:07:56'),
('compounding_enabled', '0', '2026-02-15 21:51:02'),
('contact_email', 'legal@bloombit.com', '2026-02-15 01:07:56'),
('distribution_interval', 'daily', '2026-02-18 02:31:54'),
('distribution_start_time', '09:00:00', '2026-02-17 21:53:51'),
('earnings_paused', '0', '2026-02-17 21:53:51'),
('footer_description', 'Leading the future of decentralized finance with advanced artificial intelligence and machine learning technologies.', '2026-02-15 01:07:56'),
('header_image', '/uploads/site/header_image_1771519971.jpg', '2026-02-19 16:52:51'),
('hero_badge', 'AI ENGINE V4.0 NOW LIVE', '2026-02-15 01:07:56'),
('hero_subtitle', 'Automate your wealth with institutional-grade machine learning algorithms. Deploy sophisticated bots that trade 24/7 while you sleep.', '2026-02-15 01:33:55'),
('hero_title', 'Smarter Crypto Investing Powered by Advanced AI', '2026-02-15 01:33:55'),
('homepage_modal_image', '/uploads/site/modal_image_1771494314.png', '2026-02-19 09:45:14'),
('homepage_youtube_url', 'https://www.youtube.com/watch?v=c-ZQL2VxSrE', '2026-02-19 08:39:02'),
('investors_count', '45000', '2026-02-15 01:07:56'),
('mail_from_email', 'support@bloombitfx.com', '2026-02-18 17:35:18'),
('mail_from_name', 'Bloombit', '2026-02-18 16:48:37'),
('mail_imap_encryption', 'ssl', '2026-02-18 16:48:37'),
('mail_imap_host', 'imap.hostinger.com', '2026-02-18 17:35:18'),
('mail_imap_password', 'Secretpass0721//', '2026-02-18 17:35:18'),
('mail_imap_port', '993', '2026-02-18 16:48:37'),
('mail_imap_sent_folder', 'Sent', '2026-02-18 16:48:37'),
('mail_imap_username', 'support@bloombitfx.com', '2026-02-18 17:35:18'),
('mail_reply_to', 'support@bloombitfx.com', '2026-02-18 17:35:18'),
('mail_smtp_encryption', 'ssl', '2026-02-18 17:35:18'),
('mail_smtp_host', 'smtp.hostinger.com', '2026-02-18 17:35:18'),
('mail_smtp_password', 'Secretpass0721//', '2026-02-18 17:35:18'),
('mail_smtp_port', '465', '2026-02-18 17:35:18'),
('mail_smtp_username', 'support@bloombitfx.com', '2026-02-18 17:35:18'),
('market_cap', '$2.45T', '2026-02-15 01:07:56'),
('max_active_plans_per_user', '3', '2026-02-15 21:51:02'),
('max_withdrawal_limit', '50000', '2026-02-17 21:53:51'),
('min_withdrawal_limit', '10', '2026-02-15 21:51:02'),
('deposit_countdown_minutes', '30', '2026-02-19 18:00:00'),
('office_address', '40 Bank Street, Canary Wharf<br/>London, E14 5NR<br/>United Kingdom', '2026-02-19 16:52:54'),
('office_title', 'London Office', '2026-02-19 16:52:54'),
('site_favicon', '/uploads/site/favicon_1771518481.png', '2026-02-19 16:28:01'),
('site_name', 'Bloombit', '2026-02-15 01:07:56'),
('smartsupp_key', '6fe6ebe5789e92d09f1a2fd405bd5b7d7967835d', '2026-02-19 16:52:54'),
('stats_assets', '$4.2B+', '2026-02-15 01:07:56'),
('stats_bots', '85k+', '2026-02-15 01:07:56'),
('stats_roi', '12.4%', '2026-02-15 01:07:56'),
('stats_uptime', '99.9%', '2026-02-15 01:07:56'),
('support_email', 'support@bloombit.com', '2026-02-15 01:33:55'),
('tagline', 'AI Crypto Trading', '2026-02-15 01:07:56'),
('volume_24h', '$84.2B', '2026-02-15 01:07:56');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `type` enum('deposit','withdrawal','payout','investment') NOT NULL,
  `amount` decimal(36,18) NOT NULL,
  `amount_usd` decimal(18,2) DEFAULT NULL,
  `currency` varchar(20) NOT NULL DEFAULT 'USD',
  `status` enum('pending','completed','rejected','failed','cancelled') NOT NULL DEFAULT 'pending',
  `reference` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL,
  `user_confirmed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `type`, `amount`, `amount_usd`, `currency`, `status`, `reference`, `created_at`) VALUES
(4, 9, 'deposit', 500.000000000000000000, NULL, 'USD', 'completed', NULL, '2026-02-15 14:50:08'),
(5, 9, 'withdrawal', 500.000000000000000000, NULL, 'USD', 'pending', NULL, '2026-02-15 14:50:08'),
(7, 9, 'deposit', 500.000000000000000000, NULL, 'USD', 'completed', NULL, '2026-02-15 18:17:55'),
(8, 9, 'withdrawal', 500.000000000000000000, NULL, 'USD', 'pending', NULL, '2026-02-15 18:17:55'),
(10, 9, 'deposit', 500.000000000000000000, NULL, 'USD', 'completed', NULL, '2026-02-15 20:59:03'),
(11, 9, 'withdrawal', 500.000000000000000000, NULL, 'USD', 'pending', NULL, '2026-02-15 20:59:03'),
(13, 9, 'deposit', 500.000000000000000000, NULL, 'USD', 'completed', NULL, '2026-02-15 21:51:03'),
(14, 9, 'withdrawal', 500.000000000000000000, NULL, 'USD', 'pending', NULL, '2026-02-15 21:51:03'),
(16, 9, 'deposit', 500.000000000000000000, NULL, 'USD', 'completed', NULL, '2026-02-15 23:56:22'),
(17, 9, 'withdrawal', 500.000000000000000000, NULL, 'USD', 'pending', NULL, '2026-02-15 23:56:22'),
(19, 9, 'deposit', 500.000000000000000000, NULL, 'USD', 'completed', NULL, '2026-02-16 00:42:41'),
(20, 9, 'withdrawal', 500.000000000000000000, NULL, 'USD', 'pending', NULL, '2026-02-16 00:42:41'),
(22, 9, 'deposit', 500.000000000000000000, NULL, 'ETH', 'rejected', NULL, '2026-02-16 11:40:11'),
(23, 9, 'deposit', 2000.000000000000000000, NULL, 'USDT', 'rejected', NULL, '2026-02-17 00:32:33'),
(24, 9, 'investment', 0.010000000000000000, NULL, 'BTC', 'completed', NULL, '2026-02-17 01:00:09'),
(26, 9, 'deposit', 25000.000000000000000000, NULL, 'USD', 'completed', NULL, '2026-02-17 01:27:55'),
(29, 9, 'payout', 384.000000000000000000, NULL, 'USDT', 'completed', 'earnings_inv_5', '2026-02-17 21:59:03'),
(30, 9, 'payout', 2.995000000000000000, NULL, 'USDT', 'completed', 'earnings_inv_7', '2026-02-17 21:59:03'),
(33, 9, 'deposit', 599.000000000000000000, NULL, 'USDT', 'completed', NULL, '2026-02-18 00:32:12'),
(34, 9, 'deposit', 1200.000000000000000000, NULL, 'USDT', 'completed', NULL, '2026-02-18 00:32:14'),
(35, 9, 'withdrawal', 1.239500000000000046, NULL, 'BTC', 'completed', 'admin_debit_1_9_20260218_003310', '2026-02-18 00:33:10'),
(36, 9, 'withdrawal', 25000.000000000000000000, NULL, 'USD', 'completed', 'admin_debit_1_9_20260218_003326', '2026-02-18 00:33:26'),
(37, 9, 'withdrawal', 2185.994999999999890861, NULL, 'USDT', 'completed', 'admin_debit_1_9_20260218_003346', '2026-02-18 00:33:46'),
(38, 9, 'withdrawal', 4.820999999999999730, NULL, 'ETH', 'completed', 'admin_debit_1_9_20260218_003405', '2026-02-18 00:34:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT '',
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin_notes` text DEFAULT NULL,
  `avatar_url` varchar(500) DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `referral_code` varchar(100) DEFAULT NULL,
  `last_balance_usd` decimal(18,2) NOT NULL DEFAULT 0.00,
  `last_balance_usd_updated_at` datetime DEFAULT NULL,
  `kyc_status` enum('none','pending','verified','rejected') NOT NULL DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `name`, `role`, `email_verified`, `active`, `two_factor_enabled`, `created_at`, `updated_at`, `admin_notes`, `avatar_url`, `phone_number`, `country`, `referral_code`, `last_balance_usd`, `last_balance_usd_updated_at`, `kyc_status`) VALUES
(1, 'admin@mail.com', '$2y$10$MuSCe0zBsM6nKIvNGzT5Mu8D0JlcoDmdR7lU.xyZtkJFVZeAKB0c6', 'Admin', 'admin', 1, 1, 0, '2026-02-15 01:25:45', '2026-02-15 01:36:16', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 'none'),
(9, 'j.donovan@gmail.com', '$2y$10$JTg6nQYebZOEaKheTc.O0u3xuTML8itKcWuq18p8q/zqHLAVjx.3e', 'James Donovan', 'user', 1, 1, 1, '2026-02-15 14:50:08', '2026-02-18 00:34:05', NULL, '/uploads/avatars/9_1771167884.jpg', NULL, 'United States', NULL, 0.01, '2026-02-18 00:34:05', 'verified'),
(43, 'mr.carter.tech07@gmail.com', '$2y$10$hOGUvQ7qSuGb4XpmFQf8p.6Y4NvJCcMzG4BY1CheBKUeK2kPUOWW6', 'mr.carter.tech07', 'user', 1, 1, 0, '2026-02-19 17:29:45', '2026-02-19 17:29:45', NULL, NULL, '+23491347593', NULL, NULL, 0.00, NULL, 'none');

-- --------------------------------------------------------

--
-- Table structure for table `user_investments`
--

CREATE TABLE `user_investments` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `duration_days` int(10) UNSIGNED DEFAULT NULL,
  `start_date` date NOT NULL,
  `status` enum('active','paused','completed','cancelled') NOT NULL DEFAULT 'active',
  `last_earnings_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_investments`
--

INSERT INTO `user_investments` (`id`, `user_id`, `plan_id`, `amount`, `duration_days`, `start_date`, `status`, `last_earnings_at`, `created_at`) VALUES
(5, 9, 1, 1200.00, NULL, '2025-12-15', 'cancelled', '2026-02-17 21:59:03', '2026-02-15 14:50:08'),
(6, 9, 2, 25000.00, NULL, '2026-02-01', 'cancelled', NULL, '2026-02-15 14:50:08'),
(7, 9, 1, 599.00, 45, '2026-02-17', 'cancelled', '2026-02-17 21:59:03', '2026-02-17 01:00:09');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_addresses`
--

CREATE TABLE `wallet_addresses` (
  `id` int(10) UNSIGNED NOT NULL,
  `coin_id` int(10) UNSIGNED NOT NULL,
  `address` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallet_addresses`
--

INSERT INTO `wallet_addresses` (`id`, `coin_id`, `address`, `created_at`) VALUES
(1, 1, 'adadadfadsfssgsgdgdg', '2026-02-15 23:57:29'),
(2, 2, 'stghgikyuterettete', '2026-02-15 23:57:49'),
(3, 3, 'cfsgdgdgdg', '2026-02-15 23:57:58'),
(4, 4, 'afdfafa', '2026-02-16 00:21:13'),
(5, 5, 'adfafsf', '2026-02-16 00:21:20'),
(6, 6, 'sfsghuyr', '2026-02-16 00:44:25'),
(7, 15, 'aghkrtd', '2026-02-16 00:44:41'),
(8, 16, 'lkjhgfe', '2026-02-16 00:44:57'),
(9, 19, 'ertgvcx', '2026-02-16 00:45:09'),
(10, 8, 'wdfgjio', '2026-02-16 00:45:19'),
(11, 11, 'hgtrdcvby', '2026-02-16 00:45:43');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_balances`
--

CREATE TABLE `wallet_balances` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `currency` varchar(20) NOT NULL,
  `amount` decimal(36,18) NOT NULL DEFAULT 0.000000000000000000,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallet_balances`
--

INSERT INTO `wallet_balances` (`id`, `user_id`, `currency`, `amount`, `updated_at`) VALUES
(9, 9, 'BTC', 0.000000180096438651, '2026-02-18 00:33:10'),
(10, 9, 'ETH', 0.000000000000000000, '2026-02-18 00:34:05'),
(39, 9, 'USD', 0.000000000000000000, '2026-02-18 00:33:26'),
(44, 9, 'USDT', 0.000000000000000000, '2026-02-18 00:33:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_mailbox`
--
ALTER TABLE `admin_mailbox`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_folder_uid` (`mailbox_folder`,`imap_uid`),
  ADD KEY `idx_direction_created` (`direction`,`created_at`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_message_id` (`message_id`);

--
-- Indexes for table `broadcast_campaigns`
--
ALTER TABLE `broadcast_campaigns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coins`
--
ALTER TABLE `coins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coin_key` (`coin_key`),
  ADD KEY `idx_coins_enabled` (`enabled`),
  ADD KEY `idx_coins_sort` (`sort_order`);

--
-- Indexes for table `email_otp_codes`
--
ALTER TABLE `email_otp_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email_purpose` (`email`,`purpose`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `kyc_submissions`
--
ALTER TABLE `kyc_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_kyc_user` (`user_id`),
  ADD KEY `idx_kyc_status` (`status`);

--
-- Indexes for table `pending_registrations`
--
ALTER TABLE `pending_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_email` (`email`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_plans_enabled` (`enabled`),
  ADD KEY `idx_plans_sort` (`sort_order`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tx_user` (`user_id`),
  ADD KEY `idx_tx_type` (`type`),
  ADD KEY `idx_tx_status` (`status`),
  ADD KEY `idx_tx_created` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_role` (`role`);

--
-- Indexes for table `user_investments`
--
ALTER TABLE `user_investments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_inv_user` (`user_id`),
  ADD KEY `idx_inv_plan` (`plan_id`),
  ADD KEY `idx_inv_status` (`status`);

--
-- Indexes for table `wallet_addresses`
--
ALTER TABLE `wallet_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wallet_addresses_coin` (`coin_id`);

--
-- Indexes for table `wallet_balances`
--
ALTER TABLE `wallet_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_wallet_user_currency` (`user_id`,`currency`),
  ADD KEY `idx_wallet_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_mailbox`
--
ALTER TABLE `admin_mailbox`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `broadcast_campaigns`
--
ALTER TABLE `broadcast_campaigns`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `coins`
--
ALTER TABLE `coins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `email_otp_codes`
--
ALTER TABLE `email_otp_codes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kyc_submissions`
--
ALTER TABLE `kyc_submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pending_registrations`
--
ALTER TABLE `pending_registrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `user_investments`
--
ALTER TABLE `user_investments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `wallet_addresses`
--
ALTER TABLE `wallet_addresses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `wallet_balances`
--
ALTER TABLE `wallet_balances`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kyc_submissions`
--
ALTER TABLE `kyc_submissions`
  ADD CONSTRAINT `kyc_submissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_investments`
--
ALTER TABLE `user_investments`
  ADD CONSTRAINT `user_investments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_investments_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallet_addresses`
--
ALTER TABLE `wallet_addresses`
  ADD CONSTRAINT `wallet_addresses_ibfk_1` FOREIGN KEY (`coin_id`) REFERENCES `coins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallet_balances`
--
ALTER TABLE `wallet_balances`
  ADD CONSTRAINT `wallet_balances_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
