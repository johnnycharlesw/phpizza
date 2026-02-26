CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `signed_up_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `date_of_birth` DATE NOT NULL,
  `coppa_approved_status` TINYINT(1) NOT NULL,
  `is_child` BOOLEAN,
  `gender` ENUM('male', 'female', 'other') NOT NULL,
  `is_blocked` BOOLEAN NOT NULL,
  `is_email_verified` BOOLEAN NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_users_username` (`username`),
  UNIQUE KEY `uniq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;