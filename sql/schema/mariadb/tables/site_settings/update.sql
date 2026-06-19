-- Site configuration
-- CREATE TABLE IF NOT EXISTS `site_settings` (
--   `key` VARCHAR(191) NOT NULL,
--   `value` TEXT NOT NULL,
--   `type` VARCHAR(16) NOT NULL,
--   PRIMARY KEY (`key`)
-- ) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ALTER TABLE IF EXISTS `site_settings` (
--   `key` VARCHAR(191) NOT NULL,
--   `value` TEXT NOT NULL,
--   `type` VARCHAR(16) NOT NULL,
--   PRIMARY KEY (`key`)
-- );

ALTER TABLE IF EXISTS `site_settings`
ADD COLUMN IF NOT EXISTS `key` VARCHAR(191) NOT NULL,
ADD COLUMN IF NOT EXISTS `value` TEXT NOT NULL,
ADD COLUMN IF NOT EXISTS `type` VARCHAR(16) NOT NULL,
ADD PRIMARY KEY IF NOT EXISTS (`key`);