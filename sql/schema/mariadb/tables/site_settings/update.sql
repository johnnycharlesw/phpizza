-- Site configuration
CREATE TABLE IF NOT EXISTS `site_settings` (
  `key` VARCHAR(191) NOT NULL,
  `value` TEXT NOT NULL,
  `type` VARCHAR(16) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE IF EXISTS `site_settings` (
  `key` VARCHAR(191) NOT NULL,
  `value` TEXT NOT NULL,
  `type` VARCHAR(16) NOT NULL,
  PRIMARY KEY (`key`)
);