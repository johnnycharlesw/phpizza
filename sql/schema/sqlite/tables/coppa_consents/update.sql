
ALTER TABLE coppa_consents
ADD COLUMN IF NOT EXISTS id INTEGER PRIMARY KEY AUTOINCREMENT
ADD COLUMN IF NOT EXISTS parent_userid INTEGER NOT NULL
ADD COLUMN IF NOT EXISTS child_userid INTEGER NOT NULL;
CREATE INDEX IF NOT EXISTS idx_cc_parent ON coppa_consents(parent_userid);
CREATE INDEX IF NOT EXISTS idx_cc_child ON coppa_consents(child_userid);