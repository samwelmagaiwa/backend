# Ubuntu Internal Deployment Guide (MNH On‑Prem)
This project is a Laravel backend (in `backend/`) and a Vue frontend (in `frontend/`).
The recommended production setup is:
- Nginx as reverse proxy / static server
- PHP-FPM for Laravel
- MySQL/MariaDB database
- Supervisor for Laravel queues
- Cron for Laravel scheduler
- Internal DNS name (or `/etc/hosts`) + optional internal TLS

## 1) Target server assumptions
- Ubuntu Server 22.04/24.04 LTS (similar steps for other Ubuntu versions)
- Internal hostname examples:
  - Frontend: `eabms.mnh.local`
  - API: `api.eabms.mnh.local`

If MNH uses an internal DNS, create A records. Otherwise, test using `/etc/hosts` on clients.

## 2) Install system packages
Update OS:
```bash
sudo apt update && sudo apt -y upgrade
```

Install Nginx + PHP + required PHP extensions + Git + unzip:
```bash
sudo apt -y install nginx git unzip curl
sudo apt -y install php-fpm php-cli php-mysql php-xml php-mbstring php-curl php-zip php-bcmath php-intl
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

### Option B: Copy from your workstation
Copy project folder into `/var/www/eabms` and ensure ownership:
```bash
sudo chown -R eabms:eabms /var/www/eabms
```

## 5) Database setup (MySQL)
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

## 7) Frontend (Vue) setup
Build the Vue app and serve it with Nginx.

### 7.1 Install dependencies and build
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms/frontend && npm ci'
sudo -u eabms -H bash -lc 'cd /var/www/eabms/frontend && npm run build'
```

This should create a production build (commonly `frontend/dist/`).

### 7.2 Configure frontend API base URL
Make sure the frontend is configured to call the correct internal API URL.
Where this is set depends on how your Vue app is written (often `.env` or build-time variables).
Typical patterns:
- `.env.production` containing something like `VITE_API_BASE_URL=https://api.eabms.mnh.local`
- or a config inside `frontend/src/...`

Confirm this before building, otherwise the UI will point to the wrong backend.

## 8) Nginx configuration
Recommended: **separate virtual hosts** for frontend and API.

### 8.1 PHP-FPM socket
Check PHP-FPM service name and socket:
```bash
systemctl status php8.2-fpm
ls -la /run/php/
```
Typical socket: `/run/php/php8.2-fpm.sock`

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
sudo nginx -t
sudo systemctl reload nginx
```

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

## 9) HTTPS (internal)
If MNH uses an internal CA, install the CA cert on clients and use real TLS certs.
If not, you can use a self-signed certificate (clients must trust it).

Typical approach:
- Terminate TLS at Nginx for both `eabms.mnh.local` and `api.eabms.mnh.local`.
- Update `APP_URL` and frontend API base URL to `https://...`.

## 10) Queues (important for notifications)
Some notifications/listeners implement `ShouldQueue`. Ensure queue worker runs.

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

Restart services:
```bash
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx
sudo supervisorctl restart eabms-queue:*
```

## 15) Deployment update procedure (safe)
1) Pull code updates:
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms && git pull'
```
2) Backend:
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms/backend && composer install --no-dev --optimize-autoloader'
sudo -u eabms -H bash -lc 'cd /var/www/eabms/backend && php artisan migrate --force'
sudo -u eabms -H bash -lc 'cd /var/www/eabms/backend && php artisan config:cache && php artisan route:cache && php artisan view:cache'
```
3) Frontend:
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms/frontend && npm ci'
sudo -u eabms -H bash -lc 'cd /var/www/eabms/frontend && npm run build'
```
4) Reload services:
```bash
sudo systemctl reload nginx
sudo supervisorctl restart eabms-queue:*
```

## 16) Notes for MNH internal hosting
- Prefer internal DNS names and internal CA-issued certificates.
- Keep database backups (nightly) and store them securely.
- Monitor:
  - Nginx access/error logs
  - Laravel logs
  - Queue worker logs
  - Disk usage for `storage/` and database
