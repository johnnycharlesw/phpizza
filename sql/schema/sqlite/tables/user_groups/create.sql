CREATE TABLE user_groups (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  members TEXT NOT NULL,
  permissions TEXT NOT NULL
);