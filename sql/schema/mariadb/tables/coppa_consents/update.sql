-- CREATE TABLE IF NOT EXISTS `coppa_consents` ( -- COPPA consents
--   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
--   `parent_userid` INT UNSIGNED NOT NULL,
--   `child_userid` INT UNSIGNED NOT NULL,
--   PRIMARY KEY (`id`),
--   KEY `idx_cc_parent` (`parent_userid`),
--   KEY `idx_cc_child` (`child_userid`),
--   CONSTRAINT `fk_cc_parent` FOREIGN KEY (`parent_userid`) REFERENCES `users`(`id`) ON DELETE CASCADE,
--   CONSTRAINT `fk_cc_child` FOREIGN KEY (`child_userid`) REFERENCES `users`(`id`) ON DELETE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ALTER TABLE IF EXISTS `coppa_consents` ( -- COPPA consents
--   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
--   `parent_userid` INT UNSIGNED NOT NULL,
--   `child_userid` INT UNSIGNED NOT NULL,
--   PRIMARY KEY (`id`),
--   KEY `idx_cc_parent` (`parent_userid`),
--   KEY `idx_cc_child` (`child_userid`),
--   CONSTRAINT `fk_cc_parent` FOREIGN KEY (`parent_userid`) REFERENCES `users`(`id`) ON DELETE CASCADE,
--   CONSTRAINT `fk_cc_child` FOREIGN KEY (`child_userid`) REFERENCES `users`(`id`) ON DELETE CASCADE
-- );

ALTER TABLE IF EXISTS `coppa_consents`
ADD COLUMN IF NOT EXISTS `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
ADD COLUMN IF NOT EXISTS `parent_userid` INT UNSIGNED NOT NULL,
ADD COLUMN IF NOT EXISTS `child_userid` INT UNSIGNED NOT NULL,
ADD PRIMARY KEY IF NOT EXISTS (`id`),
ADD KEY IF NOT EXISTS `idx_cc_parent` (`parent_userid`),
ADD KEY IF NOT EXISTS `idx_cc_child` (`child_userid`);