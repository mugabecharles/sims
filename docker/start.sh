#!/bin/sh
set -e

echo "==> Starting SIMS deployment..."

cd /var/www/html

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "==> Generating application key..."
    php artisan key:generate --force
fi

# Clear and cache config for production
echo "==> Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "==> Running migrations..."
php artisan migrate --force

# Create storage symlink
echo "==> Linking storage..."
php artisan storage:link 2>/dev/null || true

# Set permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "==> Starting services..."
mkdir -p /var/log/supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
