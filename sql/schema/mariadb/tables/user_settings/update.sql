-- User-specific settings
CREATE TABLE IF NOT EXISTS `user_settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` INT UNSIGNED NOT NULL,
  `theme` ENUM('light', 'dark', 'system') NOT NULL,
  `language_id` VARCHAR(2) NOT NULL,
  `notifications_enabled` BOOLEAN NOT NULL,
  `preffered_skin_name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_settings_userid` (`userid`),
  CONSTRAINT `fk_user_settings_user` FOREIGN KEY (`userid`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE IF EXISTS `user_settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` INT UNSIGNED NOT NULL,
  `theme` ENUM('light', 'dark', 'system') NOT NULL,
  `language_id` VARCHAR(2) NOT NULL,
  `notifications_enabled` BOOLEAN NOT NULL,
  `preffered_skin_name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_settings_userid` (`userid`),
  CONSTRAINT `fk_user_settings_user` FOREIGN KEY (`userid`) REFERENCES `users`(`id`) ON DELETE CASCADE
);