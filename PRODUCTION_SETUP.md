# Production Setup Guide

This guide walks you through deploying the SMK Internship Management System to a production server.

---

## Requirements

| Requirement | Version |
|-------------|---------|
| PHP | 8.3+ |
| Composer | 2.x |
| Node.js | 18+ |
| npm | 9+ |
| Database | MySQL 8+ / PostgreSQL 14+ |
| Web Server | Nginx / Apache |

> **Note:** The default config uses SQLite, but MySQL or PostgreSQL is strongly recommended for production.

---

## 1. Clone the Repository

```bash
git clone <your-repo-url> /var/www/smk-intern
cd /var/www/smk-intern
```

---

## 2. Install Dependencies

```bash
# PHP dependencies
composer install --optimize-autoloader --no-dev

# Node dependencies and build frontend assets
npm install
npm run build
```

---

## 3. Configure Environment

Copy the example env file and fill in your production values:

```bash
cp .env.example .env
```

Edit `.env` with your editor and set the following:

```env
APP_NAME="SMK Intern"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_LEVEL=error
```

### Database (MySQL example)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smk_intern
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### Session, Cache & Queue

```env
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

> For high-traffic deployments, consider switching `SESSION_DRIVER`, `CACHE_STORE`, and `QUEUE_CONNECTION` to `redis`.

### Mail

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourprovider.com
MAIL_PORT=587
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="SMK Intern"
```

---

## 4. Generate Application Keys

```bash
# Laravel app key
php artisan key:generate

# JWT secret key (required for API authentication)
php artisan jwt:secret
```

---

## 5. Set Up the Database

Create the database first (MySQL example):

```sql
CREATE DATABASE smk_intern CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Then run all migrations:

```bash
php artisan migrate --force
```

---

## 6. Set File Permissions

The web server user (usually `www-data`) needs write access to `storage` and `bootstrap/cache`:

```bash
chown -R www-data:www-data /var/www/smk-intern
chmod -R 775 /var/www/smk-intern/storage
chmod -R 775 /var/www/smk-intern/bootstrap/cache
```

---

## 7. Create Storage Symlink

This links `public/storage` to `storage/app/public` so uploaded files (attendance photos, daily log images, etc.) are publicly accessible:

```bash
php artisan storage:link
```

---

## 8. Optimize for Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

> To clear all caches (e.g., after a deployment): `php artisan optimize:clear`

---

## 9. Configure the Web Server

### Nginx

Create a new site config at `/etc/nginx/sites-available/smk-intern`:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/smk-intern/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:

```bash
ln -s /etc/nginx/sites-available/smk-intern /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

---

## 10. Set Up the Queue Worker

The app uses a database-backed queue. Run a persistent queue worker using **Supervisor**.

Install Supervisor:

```bash
apt install supervisor
```

Create a config at `/etc/supervisor/conf.d/smk-intern-worker.conf`:

```ini
[program:smk-intern-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/smk-intern/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/smk-intern/storage/logs/worker.log
stopwaitsecs=3600
```

Start Supervisor:

```bash
supervisorctl reread
supervisorctl update
supervisorctl start smk-intern-worker:*
```

---

## 11. Set Up HTTPS (SSL)

Using Certbot with Nginx:

```bash
apt install certbot python3-certbot-nginx
certbot --nginx -d yourdomain.com
```

Certbot will automatically update your Nginx config to use HTTPS and set up auto-renewal.

---

## 12. Deployment Script (Optional)

For future deployments, create a `deploy.sh` in the project root:

```bash
#!/bin/bash
set -e

echo "Pulling latest code..."
git pull origin main

echo "Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev

echo "Installing and building frontend..."
npm install
npm run build

echo "Running migrations..."
php artisan migrate --force

echo "Clearing and re-caching..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "Restarting queue workers..."
php artisan queue:restart

echo "Done."
```

Make it executable:

```bash
chmod +x deploy.sh
```

---

## Checklist

- [ ] `APP_ENV=production` and `APP_DEBUG=false`
- [ ] `APP_KEY` generated
- [ ] `JWT_SECRET` generated
- [ ] Database credentials set and migrations run
- [ ] `php artisan storage:link` executed
- [ ] File permissions set for `storage/` and `bootstrap/cache/`
- [ ] Production caches built (`config`, `route`, `view`, `event`)
- [ ] Web server configured and pointing to `/public`
- [ ] HTTPS/SSL configured
- [ ] Queue worker running via Supervisor
- [ ] Mail credentials configured

---

## Troubleshooting

**500 Internal Server Error**
- Check `storage/logs/laravel.log` for the real error.
- Ensure `APP_KEY` is set.
- Verify file permissions on `storage/` and `bootstrap/cache/`.

**JWT Token errors**
- Make sure `php artisan jwt:secret` has been run and `JWT_SECRET` is in `.env`.

**Uploaded files not accessible**
- Run `php artisan storage:link` and confirm `public/storage` is a symlink.

**Queue jobs not processing**
- Confirm the Supervisor worker is running: `supervisorctl status`.
- After any code change, restart workers: `php artisan queue:restart`.
