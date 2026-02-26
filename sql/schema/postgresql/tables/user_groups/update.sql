CREATE TABLE user_groups (
  id int NOT NULL GENERATED ALWAYS AS IDENTITY,
  name text NOT NULL,
  members text NOT NULL,
  permissions text NOT NULL,
  PRIMARY KEY (id)
) ;