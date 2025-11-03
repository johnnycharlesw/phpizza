Database setup for phpizza

1) Ensure MariaDB/MySQL is running (use XAMPP Control Panel to start MySQL).
2) If you use a firewall, allow port 3306 or start MySQL on localhost only and allow connections locally.
3) The project expects the database credentials to be in `config.php` (the password is read from `passwd.b64`).
4) To create the database and `pages` table, run from project root (PowerShell):

```powershell
php .\bin\init_db.php
```

This will execute `sql/create_schema_mariadb.sql` using the credentials in `config.php`. If the DB user lacks permission to create databases, run the SQL as a user with sufficient privileges (for example the root account in XAMPP).
