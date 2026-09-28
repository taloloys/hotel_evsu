# EVSU Hotel System

A modern hotel management system built with Laravel and Vite.

## Requirements

Before you begin, ensure you have the following installed on your computer:

- **PHP 8.2** or higher
- **Composer** (PHP dependency manager) - [Download](https://getcomposer.org)
- **Node.js** and **npm** (v16+) - [Download](https://nodejs.org)
- **MySQL** or **MariaDB** (or any supported database)
- **Git** (optional, for cloning the repository)

## Installation & Setup

### Step 1: Clone or Download the Project

If using Git:
```bash
git clone <repository-url>
cd hotel_don_felipe
```

Or manually download and extract the project folder.

### Step 2: Install PHP Dependencies

Navigate to the project directory and run:
```bash
composer install
```

This will install all Laravel packages and dependencies listed in `composer.json`.

### Step 3: Install Node.js Dependencies

```bash
npm install
```

This installs frontend build tools and JavaScript dependencies.

### Step 4: Configure Environment

Copy the example environment file:
```bash
cp .env.example .env
```

If `.env.example` doesn't exist, create a new `.env` file in the project root with the following database configuration:

```env
APP_NAME="EVSU Hotel System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=evsu_hotel_db
DB_USERNAME=root
DB_PASSWORD=
```

Adjust the database credentials to match your MySQL setup.

### Step 5: Generate Application Key

```bash
php artisan key:generate
```

### Step 6: Create Database

Create a new MySQL database named `evsu_hotel_db`:

**Using MySQL command line:**
```sql
CREATE DATABASE hotel_don_felipe;
```

Or use your MySQL GUI (phpMyAdmin, MySQL Workbench, etc.).

### Step 7: Run Migrations

```bash
php artisan migrate
```

This creates all necessary database tables.

### Step 8: Seed the Database (Optional)

To populate the database with sample data:

```bash
php artisan db:seed
```

## Running the Application

### Option 1: Using Laravel's Built-in Server

```bash
php artisan serve
```

The application will be available at: **http://localhost:8000**

### Option 2: Using XAMPP or Local Web Server

1. Place the project in your web server's root directory (e.g., `htdocs` for XAMPP)
2. Start Apache and MySQL from XAMPP Control Panel
3. Update your `.env` with the correct `APP_URL` (e.g., `http://localhost/evsu_hotel`)
4. Access via: **http://localhost/evsu_hotel**

### Step 9: Build Frontend Assets

In a separate terminal, run:
```bash
npm run dev
```

For production:
```bash
npm run build
```

## Common Issues

### Database Connection Error
- Verify MySQL is running
- Check `DB_HOST`, `DB_USERNAME`, and `DB_PASSWORD` in `.env`
- Ensure the database exists

### "Vite manifest not found" Error
- Run `npm run build` or keep `npm run dev` running in a separate terminal

### Port 8000 Already in Use
- Use a different port: `php artisan serve --port=8001`

### Permission Denied Errors
- Ensure the `storage/` and `bootstrap/cache/` directories are writable

## Database Structure

The application includes the following main tables:
- **users** - User accounts
- **guests** - Guest information
- **bookings** - Room reservations
- **rooms** - Hotel rooms
- **folios** - Guest billing records
- **transactions** - Payment records
- **shifts** - Staff shifts
- **activity_logs** - System activity tracking
- **charge_codes** - Billing codes

## Development Commands

```bash
# Run tests
php artisan test

# Format code
vendor/bin/pint

# Check code style
vendor/bin/pint --test

# View routes
php artisan route:list

# Clear cache
php artisan cache:clear
```

## Storage & Deployment Architecture

This application supports dual storage configurations: local filesystem for development (XAMPP / local PHP) and S3-compatible Object Storage for production (Laravel Cloud).

### Environment Configuration

#### Local Development
```env
FILESYSTEM_DISK=local
UPLOADS_DISK=public
BACKUP_DISK=local
```
- Temporary and system files remain in `storage/app/private`.
- Product and landing page showcase uploads are stored in `storage/app/public` (served via `php artisan storage:link`).
- Database backups are stored locally in `storage/backups`.

#### Production (Laravel Cloud)
```env
FILESYSTEM_DISK=local
UPLOADS_DISK=s3
BACKUP_DISK=s3
```
- Standard application temporary/session storage remains local.
- User uploads and database backups persist in Laravel Cloud Object Storage (or any S3-compatible bucket).
- Note: When Object Storage is attached to your environment in Laravel Cloud, inspect the storage credentials and configuration provided in your Cloud environment settings and map them to the standard Laravel S3 environment variables:
  - `AWS_ACCESS_KEY_ID`
  - `AWS_SECRET_ACCESS_KEY`
  - `AWS_DEFAULT_REGION`
  - `AWS_BUCKET`
  - `AWS_URL`
  - `AWS_ENDPOINT`
  - `AWS_USE_PATH_STYLE_ENDPOINT`
  Do not assume fixed variable names; inspect the attached storage configuration in Laravel Cloud and set the corresponding standard Laravel AWS_* variables in your environment.

### Laravel Cloud Deployment Sequence

1. **Create Production Environment**: In the Laravel Cloud console, create a new project/environment.
2. **Attach Cloud MySQL**: Provision a managed MySQL database instance in Laravel Cloud and link it to the environment.
3. **Attach Object Storage**: Attach a Laravel Cloud Object Storage bucket for persistent file storage.
4. **Configure Environment Variables**: Set `APP_ENV=production`, `APP_DEBUG=false`, `FILESYSTEM_DISK=local`, `UPLOADS_DISK=s3`, `BACKUP_DISK=s3`, and the database/S3 credentials.
5. **Deploy Application**: Trigger the deployment from your repository.
6. **Run Migrations**: Execute `php artisan migrate --force` as part of the post-deployment step.
7. **Verify Environment**: Access the application via the Cloud-assigned environment URL.
8. **Test Uploads**: Test product and showcase image uploads to verify S3 persistence and URL resolution.
9. **Test Backups**: Trigger a manual backup from the Admin Backup & Restore console to confirm dump creation and S3 storage.
10. **Verify Scheduled Tasks**: Ensure the Laravel Cloud scheduler is invoking `php artisan schedule:run` every minute (e.g. `db:auto-backup`, `db:clean-backups`).
11. **Configure Custom Domain**: Attach and route your production domain name once staging verification is complete.

---

**Last Updated:** 2026-09-28
