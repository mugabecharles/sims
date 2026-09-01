#!/bin/sh
# No "set -e" — we handle errors manually so one failure doesn't crash the container

echo "==> SIMS starting..."
cd /var/www/html

# ── 1. Create .env ─────────────────────────────────────────────
if [ ! -f /var/www/html/.env ]; then
    echo "==> Creating .env from .env.example"
    cp /var/www/html/.env.example /var/www/html/.env
fi

# ── 2. Write Railway env vars into .env ───────────────────────
write_env() {
    KEY="$1"
    VALUE="$2"
    if [ -n "$VALUE" ]; then
        # Remove old value, append new
        if grep -q "^${KEY}=" /var/www/html/.env; then
            sed -i "s|^${KEY}=.*|${KEY}=${VALUE}|" /var/www/html/.env
        else
            echo "${KEY}=${VALUE}" >> /var/www/html/.env
        fi
    fi
}

write_env "APP_NAME"         "${APP_NAME}"
write_env "APP_ENV"          "${APP_ENV:-production}"
write_env "APP_DEBUG"        "${APP_DEBUG:-false}"
write_env "APP_URL"          "${APP_URL:-http://localhost:8080}"
write_env "APP_TIMEZONE"     "${APP_TIMEZONE:-Africa/Kampala}"

# DB — Railway MySQL plugin provides MYSQLHOST, MYSQLPORT etc.
write_env "DB_CONNECTION"    "mysql"
write_env "DB_HOST"          "${DB_HOST:-${MYSQLHOST:-${MYSQL_HOST:-127.0.0.1}}}"
write_env "DB_PORT"          "${DB_PORT:-${MYSQLPORT:-${MYSQL_PORT:-3306}}}"
write_env "DB_DATABASE"      "${DB_DATABASE:-${MYSQLDATABASE:-${MYSQL_DATABASE:-sims}}}"
write_env "DB_USERNAME"      "${DB_USERNAME:-${MYSQLUSER:-${MYSQL_USER:-root}}}"
write_env "DB_PASSWORD"      "${DB_PASSWORD:-${MYSQLPASSWORD:-${MYSQL_PASSWORD:-}}}"

write_env "SESSION_DRIVER"   "${SESSION_DRIVER:-database}"
write_env "CACHE_STORE"      "${CACHE_STORE:-file}"
write_env "QUEUE_CONNECTION" "${QUEUE_CONNECTION:-database}"
write_env "LOG_LEVEL"        "${LOG_LEVEL:-error}"

# ── 3. APP_KEY ─────────────────────────────────────────────────
if [ -n "$APP_KEY" ]; then
    write_env "APP_KEY" "$APP_KEY"
    echo "==> APP_KEY set from environment"
else
    echo "==> Generating APP_KEY..."
    php artisan key:generate --force
    echo "==> APP_KEY generated (copy it from .env and set as Railway variable)"
fi

# ── 4. Cache ───────────────────────────────────────────────────
echo "==> Caching config/routes/views..."
php artisan config:clear  2>/dev/null
php artisan config:cache  2>/dev/null && echo "==> config cached" || echo "==> config cache skipped"
php artisan route:cache   2>/dev/null && echo "==> routes cached" || echo "==> route cache skipped"
php artisan view:cache    2>/dev/null && echo "==> views cached"  || echo "==> view cache skipped"

# ── 5. Migrations ──────────────────────────────────────────────
DB_HOST_VAL=$(grep "^DB_HOST=" /var/www/html/.env | cut -d= -f2)
if [ "$DB_HOST_VAL" != "127.0.0.1" ] && [ -n "$DB_HOST_VAL" ]; then
    echo "==> Running migrations (DB: $DB_HOST_VAL)..."
    php artisan migrate --force 2>&1 && echo "==> Migrations OK" || echo "==> Migration warning — check logs"
    echo "==> Seeding..."
    php artisan db:seed --force 2>&1 && echo "==> Seeded OK" || echo "==> Seed skipped (already seeded?)"
else
    echo "==> No external DB — skipping migrations"
fi

# ── 6. Storage link ────────────────────────────────────────────
php artisan storage:link 2>/dev/null || true

# ── 7. Permissions ─────────────────────────────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# ── 8. Start Nginx + PHP-FPM ───────────────────────────────────
echo "==> Starting services..."
mkdir -p /var/log/supervisor /run/nginx /tmp

exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
