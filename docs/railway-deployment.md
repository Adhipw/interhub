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
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=sync

BROADCAST_CONNECTION=log
VITE_REVERB_ENABLED=false
VITE_LOCALHOST_MODE=false

FILESYSTEM_DISK=local
MAIL_MAILER=log
AI_PROVIDER=fake
RUN_MIGRATIONS=true

RECAPTCHA_SITE_KEY=your_recaptcha_site_key
RECAPTCHA_SECRET_KEY=your_recaptcha_secret_key
RECAPTCHA_ALLOW_FALLBACK=false
VITE_RECAPTCHA_SITE_KEY=your_recaptcha_site_key
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

## Quick production checks

After Railway finishes deploying, open these URLs:

```text
https://your-railway-domain.up.railway.app/up
https://your-railway-domain.up.railway.app/api/v1/health
```

`/up` is the lightweight Railway health check. `/api/v1/health` checks database, storage, cache, and basic table access without requiring login.

If pages are blank or content is missing, check these first:

- `APP_URL` must match the exact Railway/custom domain, including `https://`.
- `APP_KEY` must be set once and must not change between deploys.
- `DB_CONNECTION=pgsql` and Railway's `DATABASE_URL` must both be present.
- `APP_DEBUG=false` should stay false in production, but Railway logs should show the real exception.
- `VITE_LOCALHOST_MODE=false`; otherwise production still behaves like local development.
- `RECAPTCHA_ALLOW_FALLBACK=false`; otherwise failed captcha rendering can be silently bypassed.

## Public user readiness

For real public users, do not keep email in `log` mode. Configure a real mail provider so OTP verification and password reset work:

```env
MAIL_MAILER=resend
RESEND_API_KEY=your_resend_api_key
RESEND_FROM_ADDRESS=no-reply@your-domain.com
RESEND_FROM_NAME=InternHub
MAIL_FROM_ADDRESS=no-reply@your-domain.com
MAIL_FROM_NAME=InternHub
```

If you use a custom domain, update `APP_URL` to that domain and set Google OAuth callback URLs to:

```text
https://your-domain.com/auth/google/callback
```

For durable file uploads across redeploys, configure Cloudflare R2 or S3 and set:

```env
FILESYSTEM_DISK=r2
R2_ACCESS_KEY_ID=...
R2_SECRET_ACCESS_KEY=...
R2_BUCKET=...
R2_ENDPOINT=...
R2_REGION=auto
```
