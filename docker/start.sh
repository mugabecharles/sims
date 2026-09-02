#!/bin/sh
echo "==> SIMS starting on Railway..."
cd /var/www/html

# ── 1. Create .env ─────────────────────────────────────────────
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
    echo "==> .env created from .env.example"
fi

# ── 2. Write env vars into .env ───────────────────────────────
write_env() {
    KEY="$1"
    VALUE="$2"
    if [ -n "$VALUE" ]; then
        if grep -q "^${KEY}=" /var/www/html/.env; then
            sed -i "s|^${KEY}=.*|${KEY}=${VALUE}|" /var/www/html/.env
        else
            echo "${KEY}=${VALUE}" >> /var/www/html/.env
        fi
    fi
}

write_env "APP_NAME"         "${APP_NAME:-SIMS}"
write_env "APP_ENV"          "production"
write_env "APP_DEBUG"        "false"
write_env "APP_URL"          "${APP_URL:-http://localhost:8080}"
write_env "APP_TIMEZONE"     "Africa/Kampala"
write_env "DB_CONNECTION"    "mysql"
write_env "DB_HOST"          "${DB_HOST:-${MYSQLHOST:-${MYSQL_HOST:-127.0.0.1}}}"
write_env "DB_PORT"          "${DB_PORT:-${MYSQLPORT:-${MYSQL_PORT:-3306}}}"
write_env "DB_DATABASE"      "${DB_DATABASE:-${MYSQLDATABASE:-${MYSQL_DATABASE:-sims}}}"
write_env "DB_USERNAME"      "${DB_USERNAME:-${MYSQLUSER:-${MYSQL_USER:-root}}}"
write_env "DB_PASSWORD"      "${DB_PASSWORD:-${MYSQLPASSWORD:-${MYSQL_PASSWORD:-}}}"
write_env "SESSION_DRIVER"   "database"
write_env "CACHE_STORE"      "file"
write_env "QUEUE_CONNECTION" "database"
write_env "LOG_LEVEL"        "error"

# ── 3. APP_KEY ─────────────────────────────────────────────────
if [ -n "$APP_KEY" ]; then
    write_env "APP_KEY" "$APP_KEY"
    echo "==> APP_KEY loaded from Railway variables"
else
    php artisan key:generate --force
    echo "==> APP_KEY generated"
fi

# ── 4. Cache ───────────────────────────────────────────────────
php artisan config:cache  2>/dev/null && echo "==> config cached"  || true
php artisan route:cache   2>/dev/null && echo "==> routes cached"  || true
php artisan view:cache    2>/dev/null && echo "==> views cached"   || true

# ── 5. Migrations ──────────────────────────────────────────────
DB=$(grep "^DB_HOST=" /var/www/html/.env | cut -d= -f2)
if [ "$DB" != "127.0.0.1" ] && [ -n "$DB" ]; then
    echo "==> Running migrations (DB: $DB)..."
    php artisan migrate --force 2>&1 && echo "==> Migrations OK" || echo "==> Migration warning"
    php artisan db:seed --force 2>&1 && echo "==> Seeded OK"      || echo "==> Seed skipped"
else
    echo "==> Skipping migrations (no external DB)"
fi

# ── 6. Permissions ─────────────────────────────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# ── 7. Start Apache ────────────────────────────────────────────
echo "==> Starting Apache on port 8080..."
exec apache2-foreground
