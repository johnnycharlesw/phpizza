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