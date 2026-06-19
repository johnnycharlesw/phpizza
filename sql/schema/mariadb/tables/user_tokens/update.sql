-- CREATE TABLE IF NOT EXISTS `user_tokens` (
--   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
--   `userid` INT UNSIGNED NOT NULL,
--   `token` VARCHAR(255) NOT NULL,
--   `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--   `expires_at` TIMESTAMP,
--   PRIMARY KEY (`id`),
--   KEY `idx_user_tokens_userid` (`userid`),
--   CONSTRAINT `fk_user_tokens_user` FOREIGN KEY (`userid`) REFERENCES `users`(`id`) ON DELETE CASCADE
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ALTER TABLE IF EXISTS `user_tokens` (
--   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
--   `userid` INT UNSIGNED NOT NULL,
--   `token` VARCHAR(255) NOT NULL,
--   `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--   `expires_at` TIMESTAMP,
--   PRIMARY KEY (`id`),
--   KEY `idx_user_tokens_userid` (`userid`),
--   CONSTRAINT `fk_user_tokens_user` FOREIGN KEY (`userid`) REFERENCES `users`(`id`) ON DELETE CASCADE
-- );

ALTER TABLE IF EXISTS `user_tokens`
ADD COLUMN IF NOT EXISTS `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
ADD COLUMN IF NOT EXISTS `userid` INT UNSIGNED NOT NULL,
ADD COLUMN IF NOT EXISTS `token` VARCHAR(255) NOT NULL,
ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS `expires_at` TIMESTAMP,
ADD PRIMARY KEY IF NOT EXISTS (`id`),
ADD KEY IF NOT EXISTS `idx_user_tokens_userid` (`userid`);