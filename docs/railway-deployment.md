# Railway Deployment

This repository uses a Docker-based Railway deployment because the app is split into `backend` and `frontend`.

## Required Railway variables

Set these variables in the Railway service:

```env
APP_NAME=InternHub
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-railway-domain.up.railway.app
APP_KEY=base64:replace_with_php_artisan_key_generate_show

LOG_CHANNEL=stderr
LOG_LEVEL=info

DB_CONNECTION=pgsql
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync

BROADCAST_CONNECTION=log
VITE_REVERB_ENABLED=false

FILESYSTEM_DISK=local
MAIL_MAILER=log
AI_PROVIDER=fake
RUN_MIGRATIONS=true
```

Railway's PostgreSQL plugin normally provides `DATABASE_URL` automatically. Keep `DB_CONNECTION=pgsql` so Laravel uses the PostgreSQL driver.

Generate `APP_KEY` locally from `backend`:

```bash
php artisan key:generate --show
```

## Deploy notes

- The Docker build installs frontend dependencies, builds Vite assets, installs Laravel dependencies, and copies the built assets into `backend/public/build`.
- The runtime starts from `backend/public` using Railway's `PORT`.
- On startup, the container runs `php artisan migrate --force`. Set `RUN_MIGRATIONS=false` if you want to run migrations manually.
- Reverb, Horizon, Redis, and queue workers are intentionally disabled for the first stable deploy. Add separate Railway workers later if needed.
