# Bloombit Database Migration

## Setup

1. Configure database credentials in `config.php` or via environment variables:
   - `DB_HOST` (default: localhost)
   - `DB_NAME` (default: bloombit)
   - `DB_USER`
   - `DB_PASS`

2. Import the database via phpMyAdmin:
   - Go to phpMyAdmin → Import
   - Choose `database.sql` (creates database, all tables, and default site settings + plans)
   - Execute

3. Create the admin account:
   ```bash
   php scripts/create-admin.php
   ```
   This creates (or promotes) `admin@mail.com` with password `Secretpass0721//`.

## Manual Admin Creation

If you prefer to create the admin manually:
1. Register at `/register` with email `admin@mail.com` and password `Secretpass0721//`
2. Run: `UPDATE users SET role = 'admin' WHERE email = 'admin@mail.com';`
