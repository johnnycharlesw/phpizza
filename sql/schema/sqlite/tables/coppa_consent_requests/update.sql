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
