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