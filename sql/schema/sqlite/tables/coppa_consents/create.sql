CREATE TABLE IF NOT EXISTS coppa_consents (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  parent_userid INTEGER NOT NULL,
  child_userid INTEGER NOT NULL,
  FOREIGN KEY(parent_userid) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY(child_userid) REFERENCES users(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_cc_parent ON coppa_consents(parent_userid);
CREATE INDEX IF NOT EXISTS idx_cc_child ON coppa_consents(child_userid);