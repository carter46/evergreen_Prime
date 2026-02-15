# Bloombit Database

## Files

- **database.sql** – Full setup for a new database (tables + default data)
- **migration.sql** – Update an existing database on your server (safe, no data loss)

## Setup (New Install)

1. Configure database credentials in `config.php` or via environment variables:
   - `DB_HOST` (default: localhost)
   - `DB_NAME` (default: bloombit)
   - `DB_USER`
   - `DB_PASS`

2. Import the database via phpMyAdmin:
   - Select your database from the left sidebar (e.g. u502532383_bloombit on shared hosting)
   - Go to phpMyAdmin → Import
   - Choose `database.sql` (creates all tables and default site settings + plans)
   - Execute

3. Set the admin password (required for login):
   Visit **`/scripts/create-admin.php`** in your browser. This creates or updates the admin account with the correct password.
   - **Email:** admin@mail.com
   - **Password:** Secretpass0721//

The admin is pre-created in the database with role `admin`, but you must visit the script once to set the password so you can log in.

## Update Existing Server

To sync schema and defaults on an existing database:
1. In phpMyAdmin, select your database
2. Import **migration.sql**
3. Visit `/scripts/create-admin.php` if you need to reset the admin password
