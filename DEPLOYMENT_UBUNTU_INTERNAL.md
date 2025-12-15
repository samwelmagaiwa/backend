# Ubuntu Internal Deployment Guide (MNH On‑Prem)
This project is a Laravel backend (in `backend/`) and a Vue frontend (in `frontend/`).
The recommended production setup is:
- Nginx as reverse proxy / static server
- PHP-FPM for Laravel
- MySQL/MariaDB database
- Supervisor for Laravel queues
- Cron for Laravel scheduler
- Internal DNS name (or `/etc/hosts`) + optional internal TLS

## 0) Quick checklist (recommended order)
1) Prepare DNS/hostnames and confirm server IP is reachable from MNH internal network
2) Install packages (Nginx, PHP-FPM, MySQL, Node.js, Composer)
3) Create Linux user + deploy folder `/var/www/eabms`
4) Put code on server (git clone / copy)
5) Create DB + DB user
6) Configure Laravel `.env`, generate `APP_KEY`, migrate DB, set permissions
7) Configure frontend `.env.production`, build `frontend/dist`
8) Configure Nginx vhosts (API + Frontend), reload Nginx
9) Setup queue worker (Supervisor) + scheduler (cron)
10) Verify: API responds + frontend loads + login works

## 0.1) What "successful deployment" looks like
- Visiting `http(s)://eabms.mnh.local` loads the Vue app
- Vue app API calls go to `http(s)://api.eabms.mnh.local/api/...` (or your chosen URL)
- API responds with 200 for health checks (example: `/api/user`, `/api/login` depending on auth)
- Laravel can write to `backend/storage/` (no 500 errors due to permissions)
- Background jobs run (if your app uses queues for notifications)

## 1) Target server assumptions
- Ubuntu Server 22.04/24.04 LTS (similar steps for other Ubuntu versions)
- Internal hostname examples:
  - Frontend: `eabms.mnh.local`
  - API: `api.eabms.mnh.local`
- You have sudo access
- The server can install packages (internet access or internal apt mirror)

If MNH uses an internal DNS, create A records. Otherwise, test using `/etc/hosts` on clients.
Example client entry:
```text
10.10.10.50 eabms.mnh.local api.eabms.mnh.local
```

Decide early whether you will use:
- Option A (recommended): **2 hostnames** (frontend + API), or
- Option B: **single hostname** where API is served from the same domain under `/api`

## 2) Install system packages
Update OS:
```bash
sudo apt update && sudo apt -y upgrade
```

Install Nginx + PHP + required PHP extensions + Git + unzip:
```bash
sudo apt -y install nginx git unzip curl
sudo apt -y install php-fpm php-cli php-mysql php-xml php-mbstring php-curl php-zip php-bcmath php-intl

# optional but helpful
sudo apt -y install ca-certificates
```

Confirm PHP version and PHP-FPM service name (you will need it for Nginx config):
```bash
php -v
systemctl list-units --type=service | grep -E 'php.*fpm'
ls -la /run/php/
```

Install MySQL (or MariaDB):
```bash
sudo apt -y install mysql-server
```

Install Node.js (choose one approach):
- Option A (recommended): NodeSource LTS
- Option B: Ubuntu repo packages

Example (NodeSource LTS):
```bash
curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -
sudo apt -y install nodejs
```

Install Composer:
```bash
cd /tmp
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

## 3) Create app user + directories
Create a dedicated Linux user:
```bash
sudo adduser --disabled-password --gecos "" eabms
```

Create app directory:
```bash
sudo mkdir -p /var/www/eabms
sudo chown -R eabms:eabms /var/www/eabms
```

## 4) Get the code onto the server
### Option A: Git clone (recommended)
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms && git clone <YOUR_REPO_URL> .'
```

After cloning, confirm structure:
```bash
ls -la /var/www/eabms
ls -la /var/www/eabms/backend
ls -la /var/www/eabms/frontend
```

### Option B: Copy from your workstation
Copy project folder into `/var/www/eabms` and ensure ownership:
```bash
sudo chown -R eabms:eabms /var/www/eabms
```

## 5) Database setup (MySQL)
Harden MySQL basics (recommended):
```bash
sudo mysql_secure_installation
```

Create database and user:
```bash
sudo mysql
```
Inside MySQL shell:
```sql
CREATE DATABASE eabms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'eabms_user'@'localhost' IDENTIFIED BY 'CHANGE_ME_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON eabms.* TO 'eabms_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 6) Backend (Laravel) setup
All Laravel commands below are executed inside `backend/`.

### 6.1 Install PHP dependencies
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms/backend && composer install --no-dev --optimize-autoloader'
```

### 6.2 Configure environment
Create `.env`:
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms/backend && cp .env.example .env'
```

Edit `/var/www/eabms/backend/.env` and set at least:
- `APP_NAME`, `APP_ENV=production`, `APP_DEBUG=false`
- `APP_URL=https://api.eabms.mnh.local` (or your internal URL)
- Logging best practice (keeps logs small in production):
  - `LOG_CHANNEL=daily`
  - `LOG_LEVEL=warning`
- DB settings:
  - `DB_HOST=127.0.0.1`
  - `DB_DATABASE=eabms`
  - `DB_USERNAME=eabms_user`
  - `DB_PASSWORD=...`
- SMS settings (example):
  - `SMS_ENABLED=true`
  - `SMS_TEST_MODE=false`
  - `SMS_API_URL=...`
  - `SMS_API_KEY=...`
  - `SMS_API_SECRET=...`
  - `SMS_SENDER_ID=...`
  - (optional) delivery reports:
    - `SMS_DELIVERY_REPORT_URL=https://api.eabms.mnh.local/api/sms/delivery-report`
    - `SMS_DELIVERY_REPORT_TOKEN=...`

Generate application key:
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms/backend && php artisan key:generate'
```

### 6.3 Storage + permissions
Create storage symlink:
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms/backend && php artisan storage:link'
```

Ensure writable directories:
```bash
sudo chown -R eabms:www-data /var/www/eabms/backend/storage /var/www/eabms/backend/bootstrap/cache
sudo chmod -R 775 /var/www/eabms/backend/storage /var/www/eabms/backend/bootstrap/cache
```

### 6.4 Run migrations
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms/backend && php artisan migrate --force'
```

### 6.5 Optimize for production
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms/backend && php artisan config:cache && php artisan route:cache && php artisan view:cache'
```

(Optional) Verify Laravel can boot and connect to DB:
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms/backend && php artisan about'
```

## 7) Frontend (Vue) setup
Build the Vue app and serve it with Nginx.

### 7.1 Install dependencies and build
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms/frontend && npm ci'
sudo -u eabms -H bash -lc 'cd /var/www/eabms/frontend && npm run build'
```

This should create a production build (commonly `frontend/dist/`).

### 7.2 Configure frontend API base URL (VERY IMPORTANT)
This project is **Vue CLI**, so production API URL is typically set via `VUE_APP_*` variables.

1) Create `frontend/.env.production` (if it does not exist):
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms/frontend && (test -f .env.production || touch .env.production)'
```

2) Set the API base URL (note the `/api` at the end):
```bash
# example: API is on a separate hostname
VUE_APP_API_URL=https://api.eabms.mnh.local/api

# example: single-host deployment (same domain), if you choose that approach
# VUE_APP_API_URL=https://eabms.mnh.local/api
```

3) Rebuild after changing `.env.production`:
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms/frontend && npm run build'
```

If you deploy frontend and API on different hostnames, ensure backend CORS allows the frontend origin.

## 8) Nginx configuration
Recommended: **separate virtual hosts** for frontend and API.

### 8.1 PHP-FPM socket
Check PHP-FPM service name and socket (the version may differ, e.g. 8.1/8.2/8.3):
```bash
systemctl list-units --type=service | grep -E 'php.*fpm'
ls -la /run/php/
```
Pick the correct socket from `/run/php/` (example: `/run/php/php8.2-fpm.sock`).

### 8.2 API vhost (Laravel)
Create:
- `/etc/nginx/sites-available/eabms-api`
- then symlink to `sites-enabled/`

Example config:
```nginx
server {
    listen 80;
    server_name api.eabms.mnh.local;

    root /var/www/eabms/backend/public;
    index index.php;

    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        # IMPORTANT: replace with the socket that exists on your server (see section 8.1)
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```
Enable and reload:
```bash
sudo ln -s /etc/nginx/sites-available/eabms-api /etc/nginx/sites-enabled/

# optional: disable the default site to avoid conflicts
sudo rm -f /etc/nginx/sites-enabled/default

sudo nginx -t
sudo systemctl reload nginx
```

Quick API test from the server:
```bash
curl -I http://127.0.0.1
curl -I http://api.eabms.mnh.local
```
If you have HTTPS enabled, test with `https://...`.

### 8.3 Frontend vhost (Vue static)
Create `/etc/nginx/sites-available/eabms-frontend`:
```nginx
server {
    listen 80;
    server_name eabms.mnh.local;

    root /var/www/eabms/frontend/dist;
    index index.html;

    # Vue SPA routing
    location / {
        try_files $uri $uri/ /index.html;
    }
}
```
Enable and reload:
```bash
sudo ln -s /etc/nginx/sites-available/eabms-frontend /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

Quick frontend test:
```bash
curl -I http://eabms.mnh.local
```

### 8.4 Optional: Single-host deployment (frontend + API on same hostname)
If you prefer one hostname (example: `eabms.mnh.local`) you can serve the Vue build and proxy API requests to Laravel.

High-level idea:
- Vue app served from `/`
- Laravel API served from `/api` (proxied to Laravel public index)

Example (replace PHP-FPM socket to match your server):
```nginx
server {
    listen 80;
    server_name eabms.mnh.local;

    # Frontend
    root /var/www/eabms/frontend/dist;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    # API (Laravel) - served from the backend public folder
    location /api {
        alias /var/www/eabms/backend/public;
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \\.php$ {
        root /var/www/eabms/backend/public;
        include snippets/fastcgi-php.conf;
        # IMPORTANT: replace with the socket that exists on your server (see section 8.1)
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
    }
}
```

If you use this option, set:
- Laravel: `APP_URL=https://eabms.mnh.local`
- Frontend: `VUE_APP_API_URL=https://eabms.mnh.local/api`

## 9) HTTPS (internal)
If MNH uses an internal CA, install the CA cert on clients and use real TLS certs.
If not, you can use a self-signed certificate (clients must trust it).

Typical approach:
- Terminate TLS at Nginx for both `eabms.mnh.local` and `api.eabms.mnh.local`.
- Update `APP_URL` and frontend API base URL to `https://...`.

## 10) Queues (important for notifications)
Some notifications/listeners implement `ShouldQueue`. Ensure queue worker runs.

If your app does not use queues, you can skip this section.

### 10.1 Choose queue driver
In `backend/.env`:
- `QUEUE_CONNECTION=database` (common)

If using `database` queue, run:
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms/backend && php artisan queue:table && php artisan migrate --force'
```
(Only if the queue table migration isn’t already present in your project.)

### 10.2 Supervisor
Install supervisor:
```bash
sudo apt -y install supervisor
```

Create `/etc/supervisor/conf.d/eabms-queue.conf`:
```ini
[program:eabms-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/eabms/backend/artisan queue:work --sleep=3 --tries=3 --timeout=120
autostart=true
autorestart=true
user=eabms
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/eabms/backend/storage/logs/queue-worker.log
stopwaitsecs=3600
```

Reload supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status
```

## 11) Scheduler (cron)
Laravel scheduler should run every minute:
```bash
sudo crontab -e
```
Add:
```cron
* * * * * cd /var/www/eabms/backend && php artisan schedule:run >> /dev/null 2>&1
```

## 12) File uploads & permissions checklist
- `backend/storage/` writable
- `backend/bootstrap/cache/` writable
- `backend/public/storage` symlink exists

## 13) Firewall / network
If UFW is used:
```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
sudo ufw status
```

If this is internal-only, ensure routing/firewall rules restrict access to MNH networks.

## 14) Common operational commands
Clear caches after changing `.env`:
```bash
cd /var/www/eabms/backend
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
```

Tail logs:
```bash
tail -f /var/www/eabms/backend/storage/logs/laravel-*.log
```

Restart services (adjust php-fpm version if needed):
```bash
# example only; replace 8.2 with your PHP version (8.1/8.2/8.3)
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx
sudo supervisorctl restart eabms-queue:*
```

## 15) Deployment update procedure (safe)
When updating an existing deployment, follow this order to avoid downtime and weird cache issues.

1) Pull code updates:
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms && git pull'
```

2) Backend (Laravel):
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms/backend && composer install --no-dev --optimize-autoloader'

# migrations
sudo -u eabms -H bash -lc 'cd /var/www/eabms/backend && php artisan migrate --force'

# clear + rebuild caches (important after .env / config changes)
sudo -u eabms -H bash -lc 'cd /var/www/eabms/backend && php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear'
sudo -u eabms -H bash -lc 'cd /var/www/eabms/backend && php artisan config:cache && php artisan route:cache && php artisan view:cache'
```

3) Frontend (Vue):
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms/frontend && npm ci'
sudo -u eabms -H bash -lc 'cd /var/www/eabms/frontend && npm run build'
```

4) Reload services:
```bash
sudo systemctl reload nginx
sudo supervisorctl restart eabms-queue:*
```

5) Quick post-update verification:
```bash
curl -I http://eabms.mnh.local
curl -I http://api.eabms.mnh.local
```

## 16) Final verification (go-live)
Do these checks before announcing the system is live:

1) From your workstation browser:
- Open the frontend: `http(s)://eabms.mnh.local`
- Log in and load at least:
  - dashboard
  - a request list page
  - a request details page

2) Confirm frontend is calling the correct API:
- Open DevTools → Network → check requests go to `.../api/...`
- If you see calls to `127.0.0.1:8000` or a wrong host, fix `frontend/.env.production` and rebuild.

3) From the server:
```bash
# nginx is healthy
sudo nginx -t
sudo systemctl status nginx --no-pager

# php-fpm is healthy
systemctl list-units --type=service | grep -E 'php.*fpm'

# queue worker (if enabled)
sudo supervisorctl status
```

## 17) Troubleshooting (common issues)
### 17.1 Frontend loads but API calls fail
- Confirm `frontend/.env.production` has:
  - `VUE_APP_API_URL=https://api.eabms.mnh.local/api`
- Rebuild frontend after edits: `npm run build`
- If API is on a different hostname, confirm backend CORS allows the frontend origin.

### 17.2 500 errors on API
- Check Laravel logs:
```bash
tail -n 200 /var/www/eabms/backend/storage/logs/laravel-*.log
```
- Common causes:
  - wrong DB credentials
  - missing `APP_KEY`
  - permissions on `storage/` or `bootstrap/cache`

### 17.3 Uploads failing (signatures / attachments)
- Increase limits:
  - Nginx `client_max_body_size`
  - PHP `upload_max_filesize` and `post_max_size` in `php.ini` (FPM)
- Reload services after changes.

## 18) Notes for MNH internal hosting
- Prefer internal DNS names and internal CA-issued certificates.
- Keep database backups (nightly) and store them securely.
- Monitor:
  - Nginx access/error logs
  - Laravel logs
  - Queue worker logs
  - Disk usage for `storage/` and database
