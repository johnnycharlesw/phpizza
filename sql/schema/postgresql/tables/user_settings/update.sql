CREATE TABLE IF NOT EXISTS user_settings (
  id INT CHECK (id > 0) NOT NULL GENERATED ALWAYS AS IDENTITY,
  userid INT CHECK (userid > 0) NOT NULL,
  theme VARCHAR(30) CHECK (THEME IN ('light', 'dark', 'system')) NOT NULL,
  language_id VARCHAR(2) NOT NULL,
  notifications_enabled BOOLEAN NOT NULL,
  preffered_skin_name VARCHAR(255) NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT uniq_user_settings_userid UNIQUE (userid),
  CONSTRAINT fk_user_settings_user FOREIGN KEY (userid) REFERENCES users(id) ON DELETE CASCADE
);
