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