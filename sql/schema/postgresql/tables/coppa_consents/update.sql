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