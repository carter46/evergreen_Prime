# Bloombit Database

## Files

- **u502532383_bloombit.sql** – Full schema dump (tables + default data). Use for a new database.
- **migration.sql** – Update an existing database (adds missing columns/tables; safe, no data loss). Run after a full dump if the dump is older than the migration.

## Setup (New Install)

1. Configure database credentials in `config.php` or via environment variables:
   - `DB_HOST` (default: localhost)
   - `DB_NAME` (default: bloombit)
   - `DB_USER`
   - `DB_PASS`

2. Import the database via phpMyAdmin:
   - Select your database from the left sidebar (e.g. u502532383_bloombit on shared hosting)
   - Go to phpMyAdmin → Import
   - Choose **u502532383_bloombit.sql** (or your full schema dump) to create all tables and default data, then run **migration.sql** to apply any newer changes.
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
