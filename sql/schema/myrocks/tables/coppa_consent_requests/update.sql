CREATE TABLE IF NOT EXISTS `coppa_consent_requests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_userid` INT UNSIGNED NOT NULL,
  `child_userid` INT UNSIGNED NOT NULL,
  `status` ENUM('pending', 'approved', 'denied') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyRocks DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;