CREATE TABLE IF NOT EXISTS `coppa_consents` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_userid` INT UNSIGNED NOT NULL,
  `child_userid` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cc_parent` (`parent_userid`),
  KEY `idx_cc_child` (`child_userid`),
  CONSTRAINT `fk_cc_parent` FOREIGN KEY (`parent_userid`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cc_child` FOREIGN KEY (`child_userid`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
