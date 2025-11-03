-- Schema for phpizza
-- Creates database and pages table
-- CREATE DATABASE IF NOT EXISTS `phpizza` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE `phpizza`;

-- Pages
CREATE TABLE IF NOT EXISTS `pages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyRocks DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO pages (title, content) VALUES ('home', '# <img src="/assets/phpizza-cms-branding/logo.png" alt="PHPizza logo" width="64" height="auto" /> PHPizza has just been installed!\nYou just installed a powerful tool for site building.  \nSure, it might look like any old WordPress site could do this page, but this page is just the "Hello, World" of development with PHPizza.  \nOpen the editor. Change this page up a little bit.   See what you can do with this.\n\nIf you manage to do something impressive, [email me](mailto:woods.johnny.charles@gmail.com)');

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
) ENGINE=MyRocks DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO users (username, password_hash, email, signed_up_at, date_of_birth, coppa_approved_status, is_child, gender, is_blocked, is_email_verified) VALUES ('Guest', '$2y$10$O9yU1wkYAA2Q3dvEKAcmau6nY0raZVJlSQyqtOTtj96HBgcPP4OB6', 'phpizza-guest-account@example.org', 0000-00-00, 1, 0, 'other', 0, 1);

CREATE TABLE IF NOT EXISTS `user_tokens` (
   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
   `userid` INT UNSIGNED NOT NULL,
   `token` VARCHAR(255) NOT NULL,
   `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   `expires_at` TIMESTAMP
) ENGINE=MyRocks DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- COPPA compliance

CREATE TABLE IF NOT EXISTS `coppa_consents` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_userid` INT UNSIGNED NOT NULL,
  `child_userid` INT UNSIGNED NOT NULL
) ENGINE=MyRocks DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `coppa_consent_requests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_userid` INT UNSIGNED NOT NULL,
  `child_userid` INT UNSIGNED NOT NULL,
  `status` ENUM('pending', 'approved', 'denied') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyRocks DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Per-user settings
CREATE TABLE IF NOT EXISTS `user_settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` INT UNSIGNED NOT NULL,
  `theme` ENUM('light', 'dark', 'system') NOT NULL,
  `language_id` VARCHAR(2) NOT NULL,
  `notifications_enabled` BOOLEAN NOT NULL,
  `preffered_skin_name` VARCHAR(255) NOT NULL
) ENGINE=MyRocks DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Site-wide settings
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(191) PRIMARY KEY,
  `value` TEXT NOT NULL,
  `type` VARCHAR(16) NOT NULL
) ENGINE=MyRocks CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;