-- Schema for phpizza
-- Creates database and pages table
-- CREATE DATABASE IF NOT EXISTS `phpizza` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE `phpizza`;

-- Pages
CREATE TABLE IF NOT EXISTS pages (
  id INT CHECK (id > 0) NOT NULL GENERATED ALWAYS AS IDENTITY,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL /* ON UPDATE CURRENT_TIMESTAMP */,
  PRIMARY KEY (id)
);
INSERT INTO pages (title, content) VALUES ('home', '# <img src="/assets/phpizza-cms-branding/logo.png" alt="PHPizza logo" width="64" height="auto" /> PHPizza has just been installed!nYou just installed a powerful tool for site building.  nSure, it might look like any old WordPress site could do this page, but this page is just the "Hello, World" of development with PHPizza.  nOpen the editor. Change this page up a little bit.   See what you can do with this.nnIf you manage to do something impressive, [email me](mailto:woods.johnny.charles@gmail.com)');

-- User data
CREATE TABLE IF NOT EXISTS users (
  id INT CHECK (id > 0) NOT NULL GENERATED ALWAYS AS IDENTITY,
  username VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  signed_up_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  date_of_birth DATE NOT NULL,
  coppa_approved_status SMALLINT NOT NULL,
  is_child BOOLEAN,
  gender VARCHAR(30) CHECK (GENDER IN ('male', 'female', 'other')) NOT NULL,
  is_blocked BOOLEAN NOT NULL,
  is_email_verified BOOLEAN NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT uniq_users_username UNIQUE (username),
  CONSTRAINT uniq_users_email UNIQUE (email)
);
INSERT INTO users (username, password_hash, email, signed_up_at, date_of_birth, coppa_approved_status, is_child, gender, is_blocked, is_email_verified) VALUES ('Guest', '$2y$10$O9yU1wkYAA2Q3dvEKAcmau6nY0raZVJlSQyqtOTtj96HBgcPP4OB6', 'phpizza-guest-account@example.org', 0000-00-00, 1, 0, 'other', 0, 1);

CREATE TABLE IF NOT EXISTS user_tokens (
  id INT CHECK (id > 0) NOT NULL GENERATED ALWAYS AS IDENTITY,
  userid INT CHECK (userid > 0) NOT NULL,
  token VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  expires_at TIMESTAMP,
  PRIMARY KEY (id)
,
  CONSTRAINT fk_user_tokens_user FOREIGN KEY (userid) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_user_tokens_userid ON user_tokens (userid);

SET NAMES utf8;
time_zone := '+00:00';
foreign_key_checks := 0;
sql_mode := 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS user_groups;
CREATE TABLE user_groups (
  id int NOT NULL GENERATED ALWAYS AS IDENTITY,
  name text NOT NULL,
  members text NOT NULL,
  permissions text NOT NULL,
  PRIMARY KEY (id)
) ;

ALTER SEQUENCE user_groups_seq RESTART WITH 2;

INSERT INTO user_groups (id, name, members, permissions) VALUES
(1,	'admin',	'2',	'edit,change_permissions'); -- SQLINES DEMO *** created by the installer

-- COPPA compliance
CREATE TABLE IF NOT EXISTS coppa_consents (
  id INT CHECK (id > 0) NOT NULL GENERATED ALWAYS AS IDENTITY,
  parent_userid INT CHECK (parent_userid > 0) NOT NULL,
  child_userid INT CHECK (child_userid > 0) NOT NULL,
  PRIMARY KEY (id)
,
  CONSTRAINT fk_cc_parent FOREIGN KEY (parent_userid) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_cc_child FOREIGN KEY (child_userid) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_cc_parent ON coppa_consents (parent_userid);
CREATE INDEX idx_cc_child ON coppa_consents (child_userid);

CREATE TABLE IF NOT EXISTS coppa_consent_requests (
  id INT CHECK (id > 0) NOT NULL GENERATED ALWAYS AS IDENTITY,
  parent_userid INT CHECK (parent_userid > 0) NOT NULL,
  child_userid INT CHECK (child_userid > 0) NOT NULL,
  status VARCHAR(30) CHECK (STATUS IN ('pending', 'approved', 'denied')) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
,
  CONSTRAINT fk_ccr_parent FOREIGN KEY (parent_userid) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_ccr_child FOREIGN KEY (child_userid) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_ccr_parent ON coppa_consent_requests (parent_userid);
CREATE INDEX idx_ccr_child ON coppa_consent_requests (child_userid);

-- Per-user settings
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

-- Site-wide settings
CREATE TABLE IF NOT EXISTS site_settings (
  key VARCHAR(191) NOT NULL,
  value TEXT NOT NULL,
  type VARCHAR(16) NOT NULL,
  PRIMARY KEY (key)
) ;