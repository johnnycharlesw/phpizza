CREATE TABLE IF NOT EXISTS site_settings (
  key VARCHAR(191) NOT NULL,
  value TEXT NOT NULL,
  type VARCHAR(16) NOT NULL,
  PRIMARY KEY (key)
) ;