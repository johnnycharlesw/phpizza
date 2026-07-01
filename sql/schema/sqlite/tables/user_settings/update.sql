CREATE TABLE IF NOT EXISTS user_settings (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  userid INTEGER NOT NULL,
  theme TEXT NOT NULL,
  language_id TEXT NOT NULL,
  notifications_enabled INTEGER NOT NULL,
  preffered_skin_name TEXT NOT NULL,
  UNIQUE(userid),
  FOREIGN KEY(userid) REFERENCES users(id) ON DELETE CASCADE
);
