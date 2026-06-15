#!/usr/bin/env sh
set -eu

PORT="${PORT:-8080}"

mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

php artisan config:clear || true
php artisan view:clear || true
php artisan storage:link || true

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force || echo "Migration failed; check Railway database variables and run migrations again."
fi

php artisan config:cache || true

sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
