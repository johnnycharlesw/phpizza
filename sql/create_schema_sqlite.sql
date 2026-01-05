-- Schema for phpizza
-- Creates database and pages table

-- SQLite config
PRAGMA foreign_keys=ON;

-- Pages
CREATE TABLE IF NOT EXISTS `pages` (
  `id` INTEGER UNSIGNED NOT NULL AUTOINCREMENT,
  `title` TEXT NOT NULL,
  `content` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON TEXT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) DEFAULT;
INSERT INTO pages (title, content) VALUES ('home', '# <img src="/assets/phpizza-cms-branding/logo.png" alt="PHPizza logo" width="64" height="auto" /> PHPizza has just been installed!\nYou just installed a powerful tool for site building.  \nSure, it might look like any old WordPress site could do this page, but this page is just the "Hello, World" of development with PHPizza.  \nOpen the editor. Change this page up a little bit.   See what you can do with this.\n\nIf you manage to do something impressive, [email me](mailto:woods.johnny.charles@gmail.com)');

-- User data
CREATE TABLE IF NOT EXISTS `users` (
  `id` INTEGER UNSIGNED NOT NULL AUTOINCREMENT,
  `username` TEXT NOT NULL,
  `password_hash` TEXT NOT NULL,
  `email` TEXT NOT NULL,
  `signed_up_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `date_of_birth` TEXT NOT NULL,
  `coppa_approved_status` INTEGER NOT NULL,
  `is_child` BOOLEAN,
  `gender` ENUM('male', 'female', 'other') NOT NULL,
  `is_blocked` BOOLEAN NOT NULL,
  `is_email_verified` BOOLEAN NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_users_username` (`username`),
  UNIQUE KEY `uniq_users_email` (`email`)
) DEFAULT;
INSERT INTO users (username, password_hash, email, signed_up_at, date_of_birth, coppa_approved_status, is_child, gender, is_blocked, is_email_verified) VALUES ('Guest', '$2y$10$O9yU1wkYAA2Q3dvEKAcmau6nY0raZVJlSQyqtOTtj96HBgcPP4OB6', 'phpizza-guest-account@example.org', 0000-00-00, 1, 0, 'other', 0, 1);

CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` INTEGER UNSIGNED NOT NULL AUTOINCREMENT,
  `userid` INTEGER UNSIGNED NOT NULL,
  `token` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `expires_at` TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_tokens_userid` (`userid`),
  CONSTRAINT `fk_user_tokens_user` FOREIGN KEY (`userid`) REFERENCES `users`(`id`) ON DELETE CASCADE
) DEFAULT;

DROP TABLE IF EXISTS `user_groups`;
CREATE TABLE `user_groups` (
  `id` INTEGER(11) NOT NULL AUTOINCREMENT,
  `name` text NOT NULL,
  `members` text NOT NULL,
  `permissions` text NOT NULL,
  PRIMARY KEY (`id`)
) AUTOINCREMENT=2 DEFAULT;

INSERT INTO `user_groups` (`id`, `name`, `members`, `permissions`) VALUES
(1,	'admin',	'2',	'edit,change_permissions'); -- userid 2 will be created by the installer

-- COPPA compliance
CREATE TABLE IF NOT EXISTS `coppa_consents` (
  `id` INTEGER UNSIGNED NOT NULL AUTOINCREMENT,
  `parent_userid` INTEGER UNSIGNED NOT NULL,
  `child_userid` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cc_parent` (`parent_userid`),
  KEY `idx_cc_child` (`child_userid`),
  CONSTRAINT `fk_cc_parent` FOREIGN KEY (`parent_userid`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cc_child` FOREIGN KEY (`child_userid`) REFERENCES `users`(`id`) ON DELETE CASCADE
) DEFAULT;

CREATE TABLE IF NOT EXISTS `coppa_consent_requests` (
  `id` INTEGER UNSIGNED NOT NULL AUTOINCREMENT,
  `parent_userid` INTEGER UNSIGNED NOT NULL,
  `child_userid` INTEGER UNSIGNED NOT NULL,
  `status` ENUM('pending', 'approved', 'denied') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ccr_parent` (`parent_userid`),
  KEY `idx_ccr_child` (`child_userid`),
  CONSTRAINT `fk_ccr_parent` FOREIGN KEY (`parent_userid`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ccr_child` FOREIGN KEY (`child_userid`) REFERENCES `users`(`id`) ON DELETE CASCADE
) DEFAULT;

-- Per-user settings
CREATE TABLE IF NOT EXISTS `user_settings` (
  `id` INTEGER UNSIGNED NOT NULL AUTOINCREMENT,
  `userid` INTEGER UNSIGNED NOT NULL,
  `theme` ENUM('light', 'dark', 'system') NOT NULL,
  `language_id` VARCHAR(2) NOT NULL,
  `notifications_enabled` BOOLEAN NOT NULL,
  `preffered_skin_name` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_settings_userid` (`userid`),
  CONSTRAINT `fk_user_settings_user` FOREIGN KEY (`userid`) REFERENCES `users`(`id`) ON DELETE CASCADE
) DEFAULT;

-- Site-wide settings
CREATE TABLE IF NOT EXISTS `site_settings` (
  `key` VARCHAR(191) NOT NULL,
  `value` TEXT NOT NULL,
  `type` VARCHAR(16) NOT NULL,
  PRIMARY KEY (`key`)
);