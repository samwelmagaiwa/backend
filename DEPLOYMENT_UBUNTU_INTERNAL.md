# Internal Deployment Guide (Ubuntu) – eabms.mloganzila.or.tz

This document describes how to deploy the EABMS frontend (Vue) and backend (Laravel API) on an Ubuntu server using Docker and Nginx, under the domain:

- **Domain**: `eabms.mloganzila.or.tz`

This project is **internal access only**. It will be accessed from within the MNH local network (intranet) and is **not exposed to the public internet**.

The server already hosts other apps on ports **5000, 3000, 3001**.  
This deployment is designed to **avoid any port conflict** with those services.

---

## 1. Overview of Port Usage (MNH Internal Network)

This setup is internal-only. The application is accessed from within the MNH local network using the hostname `eabms.mloganzila.or.tz`.

To avoid conflicts with existing apps on ports `5000`, `3000`, and `3001`, we use the following scheme:

- **External (LAN-exposed) ports on the MNH server**
  - **80** – HTTP for `eabms.mloganzila.or.tz` (served by Nginx in Docker, reachable only inside the MNH network)
  - **443** – HTTPS for `eabms.mloganzila.or.tz` (optional, recommended for secure access on the LAN)

- **Internal (inside Docker network, not visible on host)**
  - **backend (Laravel)**: `8000`
  - **frontend (static files)**: served directly by Nginx from `/var/www/frontend` (no extra port)
  - Internal Docker ports are on a private Docker network and **do not** conflict with host ports `5000`, `3000`, `3001`.

**Important:** Only the Nginx container binds to host port `80` (and optionally `443`).  
All other containers communicate over an internal Docker network.

---

## 2. Internal DNS / Hostname Configuration (MNH Network)

You must make sure that **all MNH client machines** (PCs, laptops) can resolve:

- `eabms.mloganzila.or.tz` ? the **internal IP** of the Ubuntu server (for example `10.x.x.x` or `192.168.x.x`).

There are two common options:

### Option A – Configure MNH internal DNS (recommended)

1. On the **MNH internal DNS server** (or the Windows Domain Controller / central DNS used by MNH):
   - Open the DNS management console.
   - Locate or create the zone for `mloganzila.or.tz`.
   - Add an **A record**:
     - Name: `eabms`
     - Type: `A`
     - Value: `<INTERNAL_MNH_SERVER_IP>` (IP of the Ubuntu server hosting Docker)
     - TTL: `300` (or your standard value).
2. Apply/save the DNS changes.
3. From a client in the MNH network, verify resolution:
   - `ping eabms.mloganzila.or.tz`
   - The ping should resolve to the **internal** IP of your Ubuntu server.

### Option B – Use local hosts file (for a few test machines only)

If you cannot change the central DNS yet and only need testing on a few PCs:

1. On each client machine, edit the hosts file:
   - **Linux / macOS**: `/etc/hosts`
   - **Windows**: `C:\Windows\System32\drivers\etc\hosts`
2. Add a line like:

   ```text
   <INTERNAL_MNH_SERVER_IP>  eabms.mloganzila.or.tz
   ```

3. Save the file and test in a browser:
   - Open `http://eabms.mloganzila.or.tz` from that machine.

> In production for all MNH users, **use Option A (internal DNS)** so that every machine on the network can reach the system without manual hosts changes.

---

## 3. Directory Layout on the Server

On the Ubuntu server, use a single deployment directory:

- `/opt/eabms` – root for all Docker-related files

Inside it:

- `/opt/eabms/docker-compose.yml`
- `/opt/eabms/nginx/conf.d/eabms.conf` (Nginx virtual host)
- `/opt/eabms/backend` – Laravel API app (code or built image context)
- `/opt/eabms/frontend` – Vue app (built assets or build context)

Adjust actual paths if your project is in a different location, but keep them consistent in all configs.

---

## 4. Backend (Laravel) Configuration

### 4.1. Laravel `.env` settings

In the backend `.env`, set values for the production environment:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://eabms.mloganzila.or.tz

# If using Sanctum / SPA auth
SESSION_DOMAIN=.mloganzila.or.tz
SANCTUM_STATEFUL_DOMAINS=eabms.mloganzila.or.tz

# CORS (if using Laravel CORS config)
FRONTEND_URL=http://eabms.mloganzila.or.tz

# Database, cache, queue, mail etc. as per your environment
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=eabms
DB_USERNAME=eabms_user
DB_PASSWORD=your_strong_password
```

> If you enable HTTPS later, change `APP_URL` and `FRONTEND_URL` to `https://eabms.mloganzila.or.tz`.

Make sure `storage` and `bootstrap/cache` have correct permissions **inside the container** (the Dockerfile or entrypoint should handle this with `chown`/`chmod`).

---

## 5. Frontend (Vue) Configuration

Before building the production bundle, set the API base URL so that the SPA uses the domain:

```env
VITE_API_BASE_URL=http://eabms.mloganzila.or.tz/api
# or for Vue CLI:
# VUE_APP_API_BASE_URL=http://eabms.mloganzila.or.tz/api
```

> If you enable HTTPS later, change this to `https://eabms.mloganzila.or.tz/api`.

Rebuild the frontend for production:

```bash
npm install
npm run build
```

The build output (e.g. `dist/`) will be copied into the Nginx container and served from `/var/www/frontend`.

---

## 6. Docker Compose Setup

Create `/opt/eabms/docker-compose.yml` with the following content (adapt as needed):

```yaml
version: "3.8"

services:
  backend:
    image: your-registry/eabms-backend:latest
    # OR use build:
    # build:
    #   context: ./backend
    #   dockerfile: Dockerfile
    container_name: eabms-backend
    env_file:
      - ./backend/.env
    networks:
      - eabms-net
    expose:
      - "8000"   # internal port only, not mapped to host
    # Example if you run php-fpm:
    # command: php-fpm
    # Or if you have a built-in server in the container:
    # command: php artisan serve --host=0.0.0.0 --port=8000

  frontend:
    image: your-registry/eabms-frontend:latest
    # OR use build:
    # build:
    #   context: ./frontend
    #   dockerfile: Dockerfile
    container_name: eabms-frontend
    networks:
      - eabms-net
    # This container only prepares static assets;
    # Nginx will serve from a shared volume.
    volumes:
      - frontend-build:/var/www/frontend:ro

  nginx:
    image: nginx:stable
    container_name: eabms-nginx
    depends_on:
      - backend
      - frontend
    networks:
      - eabms-net
    ports:
      - "80:80"    # Only port exposed to host; does NOT conflict with 5000/3000/3001
      # - "443:443"  # Enable if you configure TLS
    volumes:
      - ./nginx/conf.d:/etc/nginx/conf.d:ro
      - frontend-build:/var/www/frontend:ro

networks:
  eabms-net:
    driver: bridge

volumes:
  frontend-build:
```

**Key points:**

- Only `nginx` exposes a port to the host: `80:80`.
- Other containers use internal ports (e.g. `8000`) and are only accessible via `eabms-net`.
- Existing services on `5000`, `3000`, `3001` remain untouched and conflict-free.

---

## 7. Nginx Virtual Host Configuration

Create directory:  
`mkdir -p /opt/eabms/nginx/conf.d`

Create `/opt/eabms/nginx/conf.d/eabms.conf`:

```nginx
server {
    listen 80;
    server_name eabms.mloganzila.or.tz;

    # Frontend (Vue SPA) – served as static files
    root /var/www/frontend;
    index index.html;

    # Log files (inside container)
    access_log /var/log/nginx/eabms_access.log;
    error_log  /var/log/nginx/eabms_error.log;

    # Serve static files and SPA routes
    location / {
        try_files $uri $uri/ /index.html;
    }

    # API – proxy to Laravel backend
    location /api/ {
        proxy_pass         http://backend:8000/;
        proxy_http_version 1.1;
        proxy_set_header   Host $host;
        proxy_set_header   X-Real-IP $remote_addr;
        proxy_set_header   X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header   X-Forwarded-Proto $scheme;
    }

    # Optional: increase upload limits
    client_max_body_size 20M;
}
```

**Notes:**

- `server_name` is set to `eabms.mloganzila.or.tz`.
- `/` serves the Vue app from `/var/www/frontend` (the mounted volume).
- `/api/` proxies to the `backend` service on port `8000` within the Docker network.
- No usage of host ports `5000`, `3000`, or `3001`.

---

## 8. Building Docker Images

If you are building images on the same server:

### Backend Dockerfile (example)

```dockerfile
FROM php:8.2-fpm

# Install system dependencies, php extensions, etc.
RUN apt-get update && apt-get install -y \
    git unzip libonig-dev libzip-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip

WORKDIR /var/www

COPY . /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Ensure correct permissions for storage and cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage/bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
```

### Frontend Dockerfile (example)

```dockerfile
FROM node:20-alpine AS build

WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Nginx-only stage is not strictly needed if using shared volume;
# you can also copy /app/dist into a volume or build image that Nginx uses.
```

Tag and push images to your registry (if using one), or reference `build:` in `docker-compose.yml` to build locally.

---

## 9. Deployment Steps (Step-by-Step)

1. **Prepare the Ubuntu server** (internal MNH server)
   - Install Docker and Docker Compose Plugin.
   - Ensure the server has a **static internal IP** (e.g. reserved in DHCP or manually configured).

2. **Copy project files** to the server  
   - Place backend code under `/opt/eabms/backend`  
   - Place frontend code under `/opt/eabms/frontend`  
   - Place `.env` for backend at `/opt/eabms/backend/.env` with production settings.

3. **Create Docker Compose and Nginx config**  
   - Create `/opt/eabms/docker-compose.yml` as shown above.  
   - Create `/opt/eabms/nginx/conf.d/eabms.conf` with the server configuration.

4. **Configure MNH internal DNS**  
   - Follow **Section 2 (Internal DNS / Hostname Configuration)** to point `eabms.mloganzila.or.tz` to the server internal IP.

5. **Login to the server** and go to the deployment directory:

   ```bash
   cd /opt/eabms
   ```

6. **Build or pull Docker images**  
   - If using `build:` in `docker-compose.yml`:

     ```bash
     docker compose build
     ```

   - If pulling from a registry:

     ```bash
     docker compose pull
     ```

7. **Start the stack in detached mode**:

   ```bash
   docker compose up -d
   ```

8. **Check container status**:

   ```bash
   docker compose ps
   ```

   Ensure `backend`, `frontend`, and `nginx` are `Up`.

9. **Verify Nginx is listening on port 80**:

   ```bash
   sudo lsof -i:80
   ```

   You should see the Docker `nginx` process bound to port 80.

10. **Test locally on the server**:

    ```bash
    curl -I http://localhost
    curl -I http://eabms.mloganzila.or.tz
    curl http://eabms.mloganzila.or.tz/api/health  # if you have a health endpoint
    ```

11. **Test from a client machine inside MNH network**  
    On a workstation that uses MNH DNS:

    - Open a browser and go to: `http://eabms.mloganzila.or.tz`

    Confirm:
    - The Vue SPA loads.
    - Network calls to `/api/...` return `200` and not `500`.
    - No conflicts or failures on existing apps on ports `5000`, `3000`, `3001`.

---

## 10. Notes on TLS (HTTPS)

For internal use you can initially run over HTTP only. If you later want to secure traffic with HTTPS **inside the MNH network**:

- Option 1: Terminate TLS on the host (e.g. host Nginx or a reverse proxy) and forward to Docker’s Nginx on port 80.
- Option 2: Use a containerized solution such as `nginx + certbot` or `traefik` with an **internal CA** or self-signed certificates, and configure:

  ```nginx
  listen 443 ssl;
  server_name eabms.mloganzila.or.tz;

  ssl_certificate     /etc/letsencrypt/live/eabms.mloganzila.or.tz/fullchain.pem;
  ssl_certificate_key /etc/letsencrypt/live/eabms.mloganzila.or.tz/privkey.pem;
  ```

Update `APP_URL`, `FRONTEND_URL`, and API base URLs to use `https://` if HTTPS is enabled.

---

## 11. Maintenance

- To view logs:

  ```bash
  docker compose logs -f nginx
  docker compose logs -f backend
  ```

- To restart the stack after code/config changes:

  ```bash
  docker compose down
  docker compose up -d --build
  ```

- Existing services on ports `5000`, `3000`, `3001` remain unaffected as this stack only binds to port `80` (and optionally `443`).

---

## 12. What to upload to the aaPanel server

When deploying to the MNH internal server managed by aaPanel, you do **not** need to upload your entire local working copy. Use this as a guide.

### 12.1. Recommended directory on the server

On the Ubuntu server (SSH or aaPanel file manager), keep all EABMS files in **one directory**, for example:

- `/opt/eabms`  (recommended in this document)

Create it if it does not exist:

```bash
sudo mkdir -p /opt/eabms
sudo chown $USER:$USER /opt/eabms
```

You will upload project files into `/opt/eabms`.

> If you prefer an aaPanel-style path (e.g. `/www/wwwroot/eabms`), you can use that instead, but keep all paths consistent in `docker-compose.yml` and this document.

### 12.2. Files and folders to upload

From your local project root (`lara-API-vue`), upload **only** the following to the server directory (e.g. `/opt/eabms`):

**Backend (Laravel API)**

- `backend/` directory, including:
  - `backend/app/`, `backend/bootstrap/`, `backend/config/`, `backend/database/`, `backend/public/`, `backend/resources/`, `backend/routes/`, `backend/storage/`
  - `backend/composer.json`, `backend/composer.lock`
  - `backend/Dockerfile`
  - `backend/.env.production` (or another production `.env` you prepare)

> You do **not** need to upload `backend/vendor/` – it will be created on the server when `composer install` runs inside the container or on the host.

**Frontend (Vue SPA)**

- `frontend/` directory, including:
  - `frontend/src/`, `frontend/public/`, `frontend/scripts/`
  - `frontend/package.json`, `frontend/package-lock.json`
  - `frontend/vue.config.js`, `frontend/tailwind.config.js`, `frontend/postcss.config.js`
  - `frontend/Dockerfile`
  - `frontend/.env.production` (for build-time API URL)

> You do **not** need to upload `frontend/node_modules/` – it will be created on the server during the Docker build or `npm install`.

**Docker & deployment files**

- `docker-compose.yml` (or your production variant, e.g. `docker-compose.prod.yml` if you create one)
- `docker/` directory (especially Nginx configs like `docker/nginx/default.conf` if used)
- `DEPLOYMENT_UBUNTU_INTERNAL.md` (this guide, for reference on the server)

Optional but useful:

- `.dockerignore` files
- `.env.example` files (for documentation only)

### 12.3. Files and folders that should NOT be uploaded

These are **local/dev artifacts** and should generally not be uploaded to the production aaPanel server:

- Git metadata:
  - `.git/`
  - `.gitignore`
- Local dependency caches:
  - Root-level `node_modules/` (if present)
  - `frontend/node_modules/`
  - `backend/vendor/` (let the server/composer recreate it)
- Local and dev env files:
  - `backend/.env` (local dev secrets)
  - `backend/.env.docker.dev`
  - `backend/.env.sms.example`
  - `frontend/.env`
  - `frontend/.env.docker`
  - `frontend/.env.example`
- Local tooling and editor-only files (optional to upload, not required):
  - `warp.md`
  - Any IDE/Editor folders like `.vscode/`, `.idea/` (if they exist)

Only **production-ready** env files (`backend/.env.production`, `frontend/.env.production`) and the actual application source code need to exist on the server for Docker to build and run your containers.

> Keep your real secrets (DB passwords, SMS keys) out of Git. Create or edit the `.env.production` files **on the server** or via secure upload, and never commit them to the repository.

---

## 13. Recommended project structure on the server (step by step)

This section shows how your local project (`lara-API-vue`) should be arranged **on the MNH aaPanel server**, and where each file or folder goes.

We will assume the deployment directory on the server is:

- `/opt/eabms`

If you prefer an aaPanel path like `/www/wwwroot/eabms`, replace `/opt/eabms` with your chosen path consistently.

### 13.1. Final directory tree on the server

The goal is to have this structure on the server:

```text
/opt/eabms
+-- backend
¦   +-- app
¦   +-- bootstrap
¦   +-- config
¦   +-- database
¦   +-- public
¦   +-- resources
¦   +-- routes
¦   +-- storage
¦   +-- composer.json
¦   +-- composer.lock
¦   +-- Dockerfile
¦   +-- .env        (created from .env.production for production)
¦
+-- frontend
¦   +-- src
¦   +-- public
¦   +-- scripts
¦   +-- package.json
¦   +-- package-lock.json
¦   +-- vue.config.js
¦   +-- tailwind.config.js
¦   +-- postcss.config.js
¦   +-- Dockerfile
¦   +-- .env.production
¦
+-- docker-compose.yml
+-- docker
¦   +-- nginx
¦       +-- default.conf    (if you use this Nginx config inside Docker)
¦
+-- DEPLOYMENT_UBUNTU_INTERNAL.md
+-- .dockerignore           (optional, for root-level Docker builds)
```

> Note: `backend/vendor/` and `frontend/node_modules/` are **not** copied from local; they will be created on the server by Docker or install commands.

### 13.2. Step-by-step: placing files on the server

**Step 1 – Create base directory**

On the server (SSH or aaPanel terminal):

```bash
sudo mkdir -p /opt/eabms
sudo chown $USER:$USER /opt/eabms
```

**Step 2 – Upload backend (Laravel API)**

From your local machine, upload the following **into** `/opt/eabms/backend`:

- Folders:
  - `backend/app/`
  - `backend/bootstrap/`
  - `backend/config/`
  - `backend/database/`
  - `backend/public/`
  - `backend/resources/`
  - `backend/routes/`
  - `backend/storage/`
- Files:
  - `backend/composer.json`
  - `backend/composer.lock`
  - `backend/Dockerfile`
  - `backend/.env.production` (or copy it and rename as `.env` after upload)

On the server, after upload, you should have:

```text
/opt/eabms/backend/composer.json
/opt/eabms/backend/composer.lock
/opt/eabms/backend/Dockerfile
/opt/eabms/backend/.env.production   (or .env for runtime)
```

> Do **not** upload `backend/vendor/` – it will be recreated by `composer install` inside the container or on the host.

**Step 3 – Upload frontend (Vue SPA)**

Upload the following into `/opt/eabms/frontend`:

- Folders:
  - `frontend/src/`
  - `frontend/public/`
  - `frontend/scripts/`
- Files:
  - `frontend/package.json`
  - `frontend/package-lock.json`
  - `frontend/vue.config.js`
  - `frontend/tailwind.config.js`
  - `frontend/postcss.config.js`
  - `frontend/Dockerfile`
  - `frontend/.env.production`

After upload, you should have:

```text
/opt/eabms/frontend/package.json
/opt/eabms/frontend/package-lock.json
/opt/eabms/frontend/Dockerfile
/opt/eabms/frontend/.env.production
```

> Do **not** upload `frontend/node_modules/` – Docker or `npm install` will recreate it.

**Step 4 – Upload Docker and deployment files**

At the root of `/opt/eabms`, upload:

- `docker-compose.yml`  ? `/opt/eabms/docker-compose.yml`
- `docker/` directory (if you use Docker Nginx configs)  ? `/opt/eabms/docker/...`
- `DEPLOYMENT_UBUNTU_INTERNAL.md`  ? `/opt/eabms/DEPLOYMENT_UBUNTU_INTERNAL.md`
- `.dockerignore` (optional)  ? `/opt/eabms/.dockerignore`

This makes it easy to run from `/opt/eabms`:

```bash
cd /opt/eabms
docker compose up -d
```

**Step 5 – Things you should NOT upload**

From the local project root, do **not** upload these to the aaPanel server (they are dev-only):

- `node_modules/` (any level)
- `backend/vendor/`
- `.git/`, `.gitignore`
- `backend/.env`, `backend/.env.docker.dev`, `backend/.env.sms.example`
- `frontend/.env`, `frontend/.env.docker`, `frontend/.env.example`
- `warp.md` and any editor/IDE directories such as `.vscode/`, `.idea/`

Keeping these out of the server keeps your deployment clean, smaller, and avoids leaking development secrets.

---
