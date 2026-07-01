CREATE TABLE IF NOT EXISTS site_settings (
  `key` TEXT NOT NULL PRIMARY KEY,
  `value` TEXT NOT NULL,
  `type` TEXT NOT NULL
);