-- Schema for phpizza
-- Creates database and pages table
CREATE DATABASE IF NOT EXISTS `phpizza` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `phpizza`;

-- Pages
CREATE TABLE IF NOT EXISTS `pages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User data
CREATE TABLE IF NOT EXISTS `users` (
   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
   UNIQUE(`username`) VARCHAR(255) NOT NULL,
   `password_hash` VARCHAR(255) NOT NULL,
   INDEX(`email`) VARCHAR(255) NOT NULL,
   `signed_up_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   `date_of_birth` DATE NOT NULL,
   `coppa_approved_status` TINYINT(1) NOT NULL,
   `is_child` BOOLEAN,
   `gender` ENUM('male', 'female', 'other') NOT NULL,
   `is_blocked` BOOLEAN NOT NULL,
   `is_email_verified` BOOLEAN NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `user_tokens` (
   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
   `userid` INT UNSIGNED NOT NULL,
   `token` VARCHAR(255) NOT NULL,
   `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   `expires_at` TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- COPPA compliance

CREATE TABLE IF NOT EXISTS `coppa_consents` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_userid` INT UNSIGNED NOT NULL,
  `child_userid` INT UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `coppa_consent_requests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_userid` INT UNSIGNED NOT NULL,
  `child_userid` INT UNSIGNED NOT NULL,
  `status` ENUM('pending', 'approved', 'denied') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Per-user settings
CREATE TABLE IF NOT EXISTS `user_settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` INT UNSIGNED NOT NULL,
  `theme` ENUM('light', 'dark', 'system') NOT NULL,
  `language_id` VARCHAR(2) NOT NULL,
  `notifications_enabled` BOOLEAN NOT NULL,
  `preffered_skin_name` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Site-wide settings
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(191) PRIMARY KEY,
  `value` TEXT NOT NULL,
  `type` VARCHAR(16) NOT NULL
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;