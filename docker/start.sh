#!/bin/sh
set -e

echo "==> Starting SIMS..."

cd /var/www/html

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "==> Generating application key..."
    php artisan key:generate --force
fi

# Cache config
echo "==> Caching configuration..."
php artisan config:cache 2>/dev/null || true
php artisan route:cache  2>/dev/null || true
php artisan view:cache   2>/dev/null || true

# Run migrations only if DB is configured
if [ -n "$DB_HOST" ] || [ -n "$MYSQL_URL" ] || [ -n "$DATABASE_URL" ]; then
    echo "==> Running migrations..."
    php artisan migrate --force 2>/dev/null || echo "Migration failed — check DB variables"
    echo "==> Seeding database..."
    php artisan db:seed --force 2>/dev/null || echo "Seeding skipped"
else
    echo "==> No DB configured yet, skipping migrations"
fi

# Storage
php artisan storage:link 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "==> Launching Nginx + PHP-FPM..."
mkdir -p /var/log/supervisor /run/nginx
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
