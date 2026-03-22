-- Users
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL DEFAULT 'phpizza-noemail@example.org',
  `signed_up_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `date_of_birth` DATE NOT NULL DEFAULT 2026-03-02,
  `is_child` BOOLEAN,
  `gender` VARCHAR(255) NOT NULL DEFAULT 'unspecified', -- It is a VARCHAR(255) for maximum inclusivity
  `is_blocked` BOOLEAN NOT NULL DEFAULT 0,
  `is_email_verified` BOOLEAN NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_users_username` (`username`),
  UNIQUE KEY `uniq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE IF EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL DEFAULT 'phpizza-noemail@example.org',
  `signed_up_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `date_of_birth` DATE NOT NULL DEFAULT 2026-03-02,
  `is_child` BOOLEAN,
  `gender` VARCHAR(255) NOT NULL DEFAULT 'unspecified', -- It is a VARCHAR(255) for maximum inclusivity
  `is_blocked` BOOLEAN NOT NULL DEFAULT 0,
  `is_email_verified` BOOLEAN NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_users_username` (`username`),
  UNIQUE KEY `uniq_users_email` (`email`)
);