-- CREATE TABLE IF NOT EXISTS `coppa_consent_requests` ( -- COPPA consent requests
--   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
--   `parent_userid` INT UNSIGNED NOT NULL,
--   `child_userid` INT UNSIGNED NOT NULL,
--   `status` ENUM('pending', 'approved', 'denied') NOT NULL,
--   `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   KEY `idx_ccr_parent` (`parent_userid`),
--   KEY `idx_ccr_child` (`child_userid`),
--   CONSTRAINT `fk_ccr_parent` FOREIGN KEY (`parent_userid`) REFERENCES `users`(`id`) ON DELETE CASCADE,
--   CONSTRAINT `fk_ccr_child` FOREIGN KEY (`child_userid`) REFERENCES `users`(`id`) ON DELETE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ALTER TABLE IF EXISTS `coppa_consent_requests` ( -- COPPA consent requests
--   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
--   `parent_userid` INT UNSIGNED NOT NULL,
--   `child_userid` INT UNSIGNED NOT NULL,
--   `status` ENUM('pending', 'approved', 'denied') NOT NULL,
--   `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   KEY `idx_ccr_parent` (`parent_userid`),
--   KEY `idx_ccr_child` (`child_userid`),
--   CONSTRAINT `fk_ccr_parent` FOREIGN KEY (`parent_userid`) REFERENCES `users`(`id`) ON DELETE CASCADE,
--   CONSTRAINT `fk_ccr_child` FOREIGN KEY (`child_userid`) REFERENCES `users`(`id`) ON DELETE CASCADE
-- );

ALTER TABLE IF EXISTS `coppa_consent_requests`
ADD COLUMN IF NOT EXISTS `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
ADD COLUMN IF NOT EXISTS `parent_userid` INT UNSIGNED NOT NULL,
ADD COLUMN IF NOT EXISTS `child_userid` INT UNSIGNED NOT NULL,
ADD COLUMN IF NOT EXISTS `status` ENUM('pending', 'approved', 'denied') NOT NULL,
ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD PRIMARY KEY IF NOT EXISTS (`id`),
ADD KEY IF NOT EXISTS `idx_ccr_parent` (`parent_userid`),
ADD KEY IF NOT EXISTS `idx_ccr_child` (`child_userid`); 