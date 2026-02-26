CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` INT UNSIGNED NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `expires_at` TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_tokens_userid` (`userid`),
  CONSTRAINT `fk_user_tokens_user` FOREIGN KEY (`userid`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE IF EXISTS `user_tokens` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` INT UNSIGNED NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `expires_at` TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_tokens_userid` (`userid`),
  CONSTRAINT `fk_user_tokens_user` FOREIGN KEY (`userid`) REFERENCES `users`(`id`) ON DELETE CASCADE
);