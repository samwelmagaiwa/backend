# Ubuntu Internal Deployment Guide (MNH On‑Prem)
This project is a Laravel backend (in `backend/`) and a Vue frontend (in `frontend/`).
The recommended production setup is:
- Apache (aaPanel-managed) as web server / static server
- PHP-FPM for Laravel
- MySQL/MariaDB database
- Supervisor for Laravel queues
- Cron for Laravel scheduler
- Internal DNS name (or `/etc/hosts`) + optional internal TLS

## 0) Quick checklist (recommended order)
1) Prepare DNS/hostnames and confirm server IP is reachable from MNH internal network
2) Install packages (Apache, PHP-FPM, MySQL, Node.js, Composer)
3) Create Linux user + deploy folder `/var/www/eabms`
4) Put code on server (git clone / copy)
5) Create DB + DB user
6) Configure Laravel `.env`, generate `APP_KEY`, migrate DB, set permissions
7) Configure frontend `.env.production`, build `frontend/dist`
8) Configure Apache sites in aaPanel (API + Frontend)
9) Setup queue worker (Supervisor) + scheduler (cron)
10) Verify: API responds + frontend loads + login works

## 0.1) What "successful deployment" looks like
- Visiting `http(s)://eabms.mloganzila.or.tz` loads the Vue app
- Vue app API calls go to `http(s)://api.eabms.mloganzila.or.tz/api/...` (or your chosen URL)
- API responds with 200 for health checks (example: `/api/user`, `/api/login` depending on auth)
- Laravel can write to `backend/storage/` (no 500 errors due to permissions)
- Background jobs run (if your app uses queues for notifications)

## 1) Target server assumptions
- Ubuntu Server 24.04.1 LTS (these steps also work on Ubuntu 22.04 with small version changes)
- Server is shared (you already have 2+ production systems and this project becomes site #3)
- You have a GUI hosting panel installed (aaPanel / BT Panel–like) managing Apache, PHP-FPM and sites
- Internal hostname examples:
  - Frontend: `eabms.mloganzila.or.tz`
  - API: `api.eabms.mloganzila.or.tz`
- You have sudo access
- The server can install packages (internet access or internal apt mirror)

If MNH uses an internal DNS, create A records. Otherwise, test using `/etc/hosts` on clients.
Example client entry:
```text
10.10.10.50 eabms.mloganzila.or.tz api.eabms.mloganzila.or.tz
```

Decide early whether you will use:
- Option A (recommended): **2 hostnames** (frontend + API), or
- Option B: **single hostname** where API is served from the same domain under `/api`

## 2) Install system packages
If you use aaPanel/BT Panel, it may already manage Apache/PHP/MySQL/Node for you.

Recommended approach on a shared production server:
- Prefer installing/upgrading packages **inside the panel**, to avoid breaking existing sites.
- Only use `apt install` if you are sure it will not change Apache/PHP versions used by other production systems.

Update OS:
```bash
sudo apt update && sudo apt -y upgrade
```

Install Apache + PHP + required PHP extensions + Git + unzip.

If aaPanel already installed Apache/PHP for other production sites, do NOT reinstall them with apt unless you know it won’t change versions.

```bash
# Apache (if not already installed)
sudo apt -y install apache2

# Common tools
sudo apt -y install git unzip curl ca-certificates

# PHP + extensions
sudo apt -y install php-fpm php-cli php-mysql php-xml php-mbstring php-curl php-zip php-bcmath php-intl
```

Confirm PHP version and PHP-FPM service name (useful for troubleshooting):
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
On a shared server (3+ sites), keep every app isolated.

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
On a shared server, each system must have its own database + DB user.
Never reuse the database user/password from other production systems.

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
- `APP_URL=https://api.eabms.mloganzila.or.tz` (or your internal URL)
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
    - `SMS_DELIVERY_REPORT_URL=https://api.eabms.mloganzila.or.tz/api/sms/delivery-report`
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
Build the Vue app and serve it with Apache.

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
VUE_APP_API_URL=https://api.eabms.mloganzila.or.tz/api

# example: single-host deployment (same domain), if you choose that approach
# VUE_APP_API_URL=https://eabms.mloganzila.or.tz/api
```

3) Rebuild after changing `.env.production`:
```bash
sudo -u eabms -H bash -lc 'cd /var/www/eabms/frontend && npm run build'
```

If you deploy frontend and API on different hostnames, ensure backend CORS allows the frontend origin.

## 8) Apache configuration (aaPanel) — two hostnames (recommended)
Because aaPanel on your server is running **Apache** (not Nginx) and the server already hosts other production systems, the lowest-risk setup is:
- Frontend site: `eabms.mloganzila.or.tz`
- API site: `api.eabms.mloganzila.or.tz`

### 8.0 aaPanel multi-site notes (IMPORTANT)
1) In aaPanel, confirm your existing sites so you don’t reuse a hostname already mapped to another system.
2) Create TWO new websites in aaPanel:
   - Site A: `eabms.mloganzila.or.tz`
   - Site B: `api.eabms.mloganzila.or.tz`
3) Do not change global Apache/PHP settings that could affect the other production sites.
4) Prefer enabling extensions / changing PHP version for **only this site** using aaPanel’s per-site PHP settings.

### 8.1 Frontend site (Vue static)
In aaPanel → Website → Add Site:
- Domain: `eabms.mloganzila.or.tz`
- Document Root: `/var/www/eabms/frontend/dist`
- PHP: **Static** (no PHP)

Vue is an SPA (single page app). Ensure Apache rewrites unknown routes to `index.html`.
Create `/var/www/eabms/frontend/dist/.htaccess`:
```apache
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /

  # If the request is a real file or directory, serve it
  RewriteCond %{REQUEST_FILENAME} -f [OR]
  RewriteCond %{REQUEST_FILENAME} -d
  RewriteRule ^ - [L]

  # Otherwise, send everything to the SPA entry
  RewriteRule ^ index.html [L]
</IfModule>
```

### 8.2 API site (Laravel)
In aaPanel → Website → Add Site:
- Domain: `api.eabms.mloganzila.or.tz`
- Document Root: `/var/www/eabms/backend/public`
- PHP Version: PHP 8.3 (Ubuntu 24.04 default is commonly 8.3)

aaPanel/Apache must allow Laravel’s `.htaccess` to work.
Make sure Apache modules are enabled (aaPanel usually handles this):
- `rewrite`
- PHP handler (php-fpm)

Laravel’s rewrite rules are already included in `backend/public/.htaccess`.

### 8.3 Upload limits (important for signatures/attachments)
In the **API site** configuration:
- Increase upload size limits if needed:
  - Apache: `LimitRequestBody` (if you set it)
  - PHP: `upload_max_filesize`, `post_max_size`, `max_execution_time`

### 8.4 Quick verification
From the server:
```bash
curl -I http://eabms.mloganzila.or.tz
curl -I http://api.eabms.mloganzila.or.tz
```
If using HTTPS, test with `https://...`.

## 9) HTTPS (internal) — Self-signed (your choice)
Because you are using **self-signed certificates**, client devices/browsers must trust the certificate; otherwise you will see:
- browser "Not secure" warnings
- API calls failing due to TLS errors (especially if the browser blocks mixed content)

### 9.1 Create self-signed certificates (recommended: one cert per hostname)
You can create certificates on the server using OpenSSL.

Frontend cert:
```bash
sudo mkdir -p /etc/ssl/localcerts
sudo openssl req -x509 -nodes -newkey rsa:2048 -days 825 \
  -keyout /etc/ssl/localcerts/eabms.mloganzila.or.tz.key \
  -out /etc/ssl/localcerts/eabms.mloganzila.or.tz.crt \
  -subj "/C=TZ/ST=Dar/L=Dar/O=Mloganzila/OU=ICT/CN=eabms.mloganzila.or.tz"
```

API cert:
```bash
sudo openssl req -x509 -nodes -newkey rsa:2048 -days 825 \
  -keyout /etc/ssl/localcerts/api.eabms.mloganzila.or.tz.key \
  -out /etc/ssl/localcerts/api.eabms.mloganzila.or.tz.crt \
  -subj "/C=TZ/ST=Dar/L=Dar/O=Mloganzila/OU=ICT/CN=api.eabms.mloganzila.or.tz"
```

### 9.2 Install/enable SSL in aaPanel (Apache)
In aaPanel, for each website:
1) Open the site settings
2) SSL → enable SSL
3) Choose **Other certificate** / **Custom certificate**
4) Paste:
   - Certificate (CRT content)
   - Private key (KEY content)
5) Enable **Force HTTPS** if available (recommended)

Do this for BOTH:
- `eabms.mloganzila.or.tz`
- `api.eabms.mloganzila.or.tz`

### 9.3 Trust the self-signed cert on client devices (required)
Each client machine must trust the cert(s). Options:
- Best: import the `.crt` into the OS/browser trust store
- Alternative: accept the warning in browser (not recommended; some browsers still block API calls)

Windows (quick idea):
- Double-click `.crt` → Install Certificate → Local Machine → Trusted Root Certification Authorities

### 9.4 Application configuration for HTTPS
- Backend (`backend/.env`):
  - `APP_URL=https://api.eabms.mloganzila.or.tz`
- Frontend (`frontend/.env.production`):
  - `VUE_APP_API_URL=https://api.eabms.mloganzila.or.tz/api`

After editing backend `.env`, run:
```bash
cd /var/www/eabms/backend
php artisan config:clear
php artisan config:cache
```

### 9.5 Verify HTTPS
From a client (recommended):
- Open `https://eabms.mloganzila.or.tz` (should load)
- Open `https://api.eabms.mloganzila.or.tz` (should respond)

From the server:
```bash
curl -k -I https://eabms.mloganzila.or.tz
curl -k -I https://api.eabms.mloganzila.or.tz
```
Note: `-k` ignores certificate validation (server-side test only).

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
sudo ufw allow 'Apache Full'
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
# example only; replace 8.3 with your PHP version if different
sudo systemctl restart php8.3-fpm
sudo systemctl reload apache2
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
sudo systemctl reload apache2
```

## 16) Docker-based deployment (alternative)
If you prefer to isolate this system from other applications on the server, you can deploy it using Docker instead of installing Apache/PHP/MySQL directly.

> NOTE: The Docker `.env` examples in this section contain placeholders like `base64:********************************`, `CHANGE_ME_API_KEY`, and `CHANGE_ME_STRONG_RANDOM_TOKEN`.
> On a real server, you **must** replace these with strong, unique values and keep the actual `.env.docker` files out of version control.

The repository already contains a `docker-compose.yml` at the project root and Dockerfiles for `backend/` and `frontend/`.

### 16.1 When to use Docker
- You have a **dedicated VM/server** (recommended) or clear port planning so Docker does not clash with other services.
- You want a reproducible environment (same versions of PHP/MySQL/Node everywhere).
- You are comfortable managing Docker containers instead of Apache vhosts directly.

If this server already runs other critical systems (via aaPanel/Apache), consider using **another VM** for the Docker stack to avoid port and resource conflicts.

### 16.2 Prerequisites
On the target Ubuntu server:

```bash
sudo apt update
sudo apt install -y ca-certificates curl gnupg

# Install Docker Engine (summary only; follow official docs for details)
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# Allow your deployment user to run docker without sudo (optional)
sudo usermod -aG docker eabms
newgrp docker
```

Verify:

```bash
docker --version
docker compose version   # or: docker-compose --version
```

### 16.3 Directory layout (same as non‑Docker)
- Clone or copy the project to `/var/www/eabms` as before.

```bash
sudo mkdir -p /var/www/eabms
sudo chown -R eabms:eabms /var/www/eabms
sudo -u eabms -H bash -lc 'cd /var/www/eabms && git clone <YOUR_REPO_URL> .'
```

You should have:
- `/var/www/eabms/backend`
- `/var/www/eabms/frontend`
- `/var/www/eabms/docker-compose.yml`

### 16.4 Configure Docker environment files

#### 16.4.1 Backend (`backend/.env.docker`)
This file is used **only** by the Docker backend containers.

- Start from `backend/.env` or `.env.example` and create `backend/.env.docker`.
- Ensure at least:

```env
APP_NAME=LaravelDocker
APP_ENV=local
APP_KEY=base64:********************************    # generate with: php artisan key:generate
APP_DEBUG=true                                   # or false for production
APP_URL=http://localhost:8000                   # or http://api.your-domain/internal

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
PHP_CLI_SERVER_WORKERS=4
BCRYPT_ROUNDS=12

LOG_CHANNEL=daily
LOG_STACK=daily
LOG_LEVEL=warning
LOG_DAILY_DAYS=7

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=backend-api-vue
DB_USERNAME=eabms_db
DB_PASSWORD=secret

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

MEMCACHED_HOST=memcached

REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

CORS_ALLOWED_ORIGINS="http://localhost:8080,http://127.0.0.1:8080,http://localhost:8081,http://127.0.0.1:8081,http://localhost:3000,http://127.0.0.1:3000"
CORS_SUPPORTS_CREDENTIALS=true

# SMS Service Configuration - Kilakona
SMS_ENABLED=true
SMS_API_URL=https://messaging.kilakona.co.tz/api/v1/vendor/message/send
SMS_API_KEY=CHANGE_ME_API_KEY
# Provide both API_SECRET (preferred) and SECRET_KEY (backward compatibility)
SMS_API_SECRET=CHANGE_ME_API_SECRET
SMS_SECRET_KEY=CHANGE_ME_SECRET_KEY
SMS_SENDER_ID=MLG
SMS_SENDER_NAME=MLG
SMS_BASE_URL=https://messaging.kilakona.co.tz/
SMS_DELIVERY_REPORT_URL=https://YOUR_PUBLIC_DOMAIN/api/sms/delivery-report
# optional but recommended:
SMS_DELIVERY_REPORT_TOKEN=CHANGE_ME_STRONG_RANDOM_TOKEN
SMS_TEST_MODE=false
SMS_MESSAGE_TYPE=text
# SSL verification control for cURL (false for Kilakona if required)
SMS_VERIFY_SSL=false

# Service Settings
SMS_TIMEOUT=30
SMS_RETRY_ATTEMPTS=3
SMS_RETRY_DELAY=60

# Rate Limiting
SMS_RATE_LIMIT_PER_HOUR=10
SMS_MAX_BULK_SIZE=100
SMS_BULK_DELAY=0.1

# Logging
SMS_LOGGING_ENABLED=true
SMS_LOG_SUCCESSFUL=true
SMS_LOG_FAILED=true
SMS_LOG_LEVEL=info

# Queue Settings
SMS_QUEUE_ENABLED=true
SMS_QUEUE_NAME=sms
SMS_MAX_TRIES=3

# Development/Testing
SMS_FAKE_SEND=false
SMS_LOG_TO_FILE=false
SMS_MOCK_RESPONSES=false

# Emergency Settings
SMS_FALLBACK_ENABLED=false
```

> IMPORTANT: do **not** commit real secrets (`APP_KEY`, DB password, SMS keys) to git. Commit only a template like `.env.docker.example`.

If you want SMS integrations to work from Docker, copy the `SMS_*` values as well; for a safe dev environment you can set:

```env
SMS_ENABLED=false
SMS_TEST_MODE=true
```

#### 16.4.2 Frontend (`frontend/.env.docker`)
This file controls the API URL seen by the Vue app running in Docker.

For a simple "all on one host" setup:

```env
# Vue CLI Environment Variables
# API Configuration
VUE_APP_API_URL=http://localhost:8000/api

# Application Configuration
VUE_APP_NAME=Mnh Access and booking Management System
VUE_APP_VERSION=1.0.0

# Development Configuration
VUE_APP_DEBUG=true
VUE_APP_LOG_LEVEL=debug

# Optional: API timeout (in milliseconds)
VUE_APP_API_TIMEOUT=30000
```

If you later put the Docker stack behind an internal reverse proxy (e.g. `https://api.eabms.mloganzila.or.tz`), change `VUE_APP_API_URL` accordingly.

### 16.5 Docker services overview
The `docker-compose.yml` in this project defines:

- `db`       – MySQL 8.0 (data persisted in `dbdata` volume)
- `backend`  – PHP‑FPM container running the Laravel app
- `queue`    – background queue worker (`php artisan queue:work`)
- `nginx`    – Nginx serving `backend/public` and proxying PHP to `backend`
- `frontend` – Vue dev server (Node) for the SPA

Default published ports (can be adjusted):

- `8080` → frontend (Vue dev server)
- `8000` → nginx (Laravel API)
- `3307` → MySQL (optional host access; DB is also accessible inside the Docker network as `db:3306`)

Make sure these host ports are free, or change them in `docker-compose.yml` before starting.

### 16.6 Start the Docker stack
From `/var/www/eabms`:

```bash
cd /var/www/eabms
docker compose up -d --build
```

If your system uses the legacy `docker-compose` binary, run:

```bash
docker-compose up -d --build
```

Verify containers:

```bash
docker compose ps
```

### 16.7 Run Laravel one-time tasks inside containers
The first time you deploy, run migrations and storage link **inside** the backend container:

```bash
cd /var/www/eabms

docker compose exec backend php artisan migrate --force
docker compose exec backend php artisan storage:link
```

You can also run `php artisan about` to confirm environment details:

```bash
docker compose exec backend php artisan about
```

### 16.8 Scheduler (cron) with Docker
Laravel scheduler must still run every minute. Instead of calling `php` directly, call into the `backend` container.

Edit root crontab:

```bash
sudo crontab -e
```

Add:

```cron
* * * * * cd /var/www/eabms && docker compose exec -T backend php artisan schedule:run >> /var/log/eabms-scheduler.log 2>&1
```

The `-T` flag disables TTY allocation (required in cron).

### 16.9 Logs and troubleshooting in Docker

- See container logs:

```bash
cd /var/www/eabms
docker compose logs -f nginx
docker compose logs -f backend
docker compose logs -f frontend
docker compose logs -f queue
```

- Check application logs inside the backend container:

```bash
docker compose exec backend ls storage/logs
docker compose exec backend tail -f storage/logs/laravel-*.log
```

### 16.10 Updating a Docker deployment
When deploying a new version with Docker:

```bash
cd /var/www/eabms

# 1) Pull code
sudo -u eabms -H bash -lc 'cd /var/www/eabms && git pull'

# 2) Rebuild images (if Dockerfiles or dependencies changed)
docker compose build backend frontend

# 3) Apply changes
docker compose up -d

# 4) Run database migrations (if needed)
docker compose exec backend php artisan migrate --force
```

If you change environment variables in `backend/.env.docker` or `frontend/.env.docker`, restart the affected services:

```bash
docker compose up -d backend frontend nginx
```
sudo supervisorctl restart eabms-queue:*
```

5) Quick post-update verification:
```bash
curl -I http://eabms.mloganzila.or.tz
curl -I http://api.eabms.mloganzila.or.tz
```

## 16) Final verification (go-live)
Do these checks before announcing the system is live:

1) From your workstation browser:
- Open the frontend: `http(s)://eabms.mloganzila.or.tz`
- Log in and load at least:
  - dashboard
  - a request list page
  - a request details page

2) Confirm frontend is calling the correct API:
- Open DevTools → Network → check requests go to `.../api/...`
- If you see calls to `127.0.0.1:8000` or a wrong host, fix `frontend/.env.production` and rebuild.

3) From the server:
```bash
# apache is healthy
sudo apache2ctl -t
sudo systemctl status apache2 --no-pager

# php-fpm is healthy
systemctl list-units --type=service | grep -E 'php.*fpm'

# queue worker (if enabled)
sudo supervisorctl status
```

## 17) Troubleshooting (common issues)
### 17.1 Frontend loads but API calls fail
- Confirm `frontend/.env.production` has:
  - `VUE_APP_API_URL=https://api.eabms.mloganzila.or.tz/api`
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
  - Apache (optional) `LimitRequestBody` if set
  - PHP `upload_max_filesize` and `post_max_size` in `php.ini` (FPM)
- Reload services after changes.

## 18) Notes for MNH internal hosting
- Prefer internal DNS names and internal CA-issued certificates.
- This server hosts multiple systems: apply change control (schedule maintenance windows) to avoid affecting the other 2+ production sites.
- Keep database backups (nightly) and store them securely.
- Monitor:
  - Apache access/error logs
  - Laravel logs
  - Queue worker logs
  - Disk usage for `storage/` and database
  - CPU/RAM usage (3 systems on one server)
