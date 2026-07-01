ALTER TABLE `coppa_consent_requests`
ADD COLUMN IF NOT EXISTS `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
ADD COLUMN IF NOT EXISTS `parent_userid` INT UNSIGNED NOT NULL,
ADD COLUMN IF NOT EXISTS `child_userid` INT UNSIGNED NOT NULL,
ADD COLUMN IF NOT EXISTS `status` TEXT NOT NULL,
ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD PRIMARY KEY IF NOT EXISTS (`id`)CREATE INDEX IF NOT EXISTS idx_ccr_parent ON coppa_consent_requests(parent_userid);
CREATE INDEX IF NOT EXISTS idx_ccr_parent ON coppa_consent_requests(parent_userid);
CREATE INDEX IF NOT EXISTS idx_ccr_child ON coppa_consent_requests(child_userid);
