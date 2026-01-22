-- Schema for phpizza (SQLite)
-- Creates database and pages table with SQLite-compatible syntax

PRAGMA foreign_keys=ON;

-- Pages
CREATE TABLE IF NOT EXISTS pages (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP
);
INSERT INTO pages (title, content) VALUES ('home', '# <img src="/assets/phpizza-cms-branding/logo.png" alt="PHPizza logo" width="64" height="auto" /> PHPizza has just been installed!\nYou just installed a powerful tool for site building.  \nSure, it might look like any old WordPress site could do this page, but this page is just the "Hello, World" of development with PHPizza.  \nOpen the editor. Change this page up a little bit.   See what you can do with this.\n\nIf you manage to do something impressive, [email me](mailto:woods.johnny.charles@gmail.com)');

-- User data
CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT NOT NULL,
  password_hash TEXT NOT NULL,
  email TEXT NOT NULL,
  signed_up_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  date_of_birth TEXT NOT NULL,
  coppa_approved_status INTEGER NOT NULL,
  is_child INTEGER,
  gender TEXT NOT NULL,
  is_blocked INTEGER NOT NULL,
  is_email_verified INTEGER NOT NULL,
  UNIQUE(username),
  UNIQUE(email)
);
INSERT INTO users (username, password_hash, email, signed_up_at, date_of_birth, coppa_approved_status, is_child, gender, is_blocked, is_email_verified) VALUES ('Guest', '$2y$10$O9yU1wkYAA2Q3dvEKAcmau6nY0raZVJlSQyqtOTtj96HBgcPP4OB6', 'phpizza-guest-account@example.org', CURRENT_TIMESTAMP, '1970-01-01', 0, 0, 'other', 0, 1);

CREATE TABLE IF NOT EXISTS user_tokens (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  userid INTEGER NOT NULL,
  token TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  expires_at TIMESTAMP,
  FOREIGN KEY(userid) REFERENCES users(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_user_tokens_userid ON user_tokens(userid);

-- User groups
DROP TABLE IF EXISTS user_groups;
CREATE TABLE user_groups (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  members TEXT NOT NULL,
  permissions TEXT NOT NULL
);
INSERT INTO user_groups (id, name, members, permissions) VALUES (1, 'admin', '2', 'edit,change_permissions'); -- userid 2 will be created by the installer

-- COPPA compliance
CREATE TABLE IF NOT EXISTS coppa_consents (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  parent_userid INTEGER NOT NULL,
  child_userid INTEGER NOT NULL,
  FOREIGN KEY(parent_userid) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY(child_userid) REFERENCES users(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_cc_parent ON coppa_consents(parent_userid);
CREATE INDEX IF NOT EXISTS idx_cc_child ON coppa_consents(child_userid);

CREATE TABLE IF NOT EXISTS coppa_consent_requests (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  parent_userid INTEGER NOT NULL,
  child_userid INTEGER NOT NULL,
  status TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(parent_userid) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY(child_userid) REFERENCES users(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_ccr_parent ON coppa_consent_requests(parent_userid);
CREATE INDEX IF NOT EXISTS idx_ccr_child ON coppa_consent_requests(child_userid);

-- Per-user settings
CREATE TABLE IF NOT EXISTS user_settings (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  userid INTEGER NOT NULL,
  theme TEXT NOT NULL,
  language_id VARCHAR(2) NOT NULL,
  notifications_enabled INTEGER NOT NULL,
  preffered_skin_name TEXT NOT NULL,
  UNIQUE(userid),
  FOREIGN KEY(userid) REFERENCES users(id) ON DELETE CASCADE
);

-- Site-wide settings
CREATE TABLE IF NOT EXISTS site_settings (
  key VARCHAR(191) NOT NULL PRIMARY KEY,
  value TEXT NOT NULL,
  type VARCHAR(16) NOT NULL
);