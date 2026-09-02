#!/bin/sh
echo "==> SIMS starting on Railway..."
cd /var/www/html

# ── 1. Create .env ─────────────────────────────────────────────
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
    echo "==> .env created"
fi

# ── 2. Write ALL env vars to .env using PHP ────────────────────
# PHP handles special characters safely — no shell escaping issues
php -r "
\$env = file_get_contents('/var/www/html/.env');
\$vars = [
    'APP_NAME'         => getenv('APP_NAME')         ?: 'SIMS',
    'APP_ENV'          => getenv('APP_ENV')           ?: 'production',
    'APP_DEBUG'        => getenv('APP_DEBUG')         ?: 'false',
    'APP_URL'          => getenv('APP_URL')           ?: 'http://localhost:8080',
    'APP_TIMEZONE'     => getenv('APP_TIMEZONE')      ?: 'Africa/Kampala',
    'APP_KEY'          => getenv('APP_KEY')           ?: '',
    'DB_CONNECTION'    => 'mysql',
    'DB_HOST'          => getenv('DB_HOST')           ?: getenv('MYSQLHOST')     ?: '127.0.0.1',
    'DB_PORT'          => getenv('DB_PORT')           ?: getenv('MYSQLPORT')     ?: '3306',
    'DB_DATABASE'      => getenv('DB_DATABASE')       ?: getenv('MYSQLDATABASE') ?: 'railway',
    'DB_USERNAME'      => getenv('DB_USERNAME')       ?: getenv('MYSQLUSER')     ?: 'root',
    'DB_PASSWORD'      => getenv('DB_PASSWORD')       ?: getenv('MYSQLPASSWORD') ?: '',
    'SESSION_DRIVER'   => getenv('SESSION_DRIVER')    ?: 'database',
    'CACHE_STORE'      => getenv('CACHE_STORE')       ?: 'file',
    'QUEUE_CONNECTION' => getenv('QUEUE_CONNECTION')  ?: 'database',
    'LOG_LEVEL'        => getenv('LOG_LEVEL')         ?: 'error',
];
foreach (\$vars as \$key => \$value) {
    if (\$value === '' && \$key !== 'DB_PASSWORD' && \$key !== 'APP_KEY') continue;
    if (preg_match('/^' . preg_quote(\$key, '/') . '=/m', \$env)) {
        \$env = preg_replace('/^' . preg_quote(\$key, '/') . '=.*/m', \$key . '=' . \$value, \$env);
    } else {
        \$env .= PHP_EOL . \$key . '=' . \$value;
    }
}
file_put_contents('/var/www/html/.env', \$env);
echo 'DB_HOST=' . \$vars['DB_HOST'] . PHP_EOL;
echo 'DB_DATABASE=' . \$vars['DB_DATABASE'] . PHP_EOL;
echo 'DB_USERNAME=' . \$vars['DB_USERNAME'] . PHP_EOL;
echo 'DB_PASSWORD is set: ' . (strlen(\$vars['DB_PASSWORD']) > 0 ? 'YES' : 'NO') . PHP_EOL;
"

# ── 3. APP_KEY ─────────────────────────────────────────────────
if [ -z "$(grep '^APP_KEY=base64' /var/www/html/.env)" ]; then
    echo "==> Generating APP_KEY..."
    php artisan key:generate --force
fi

# ── 4. Cache ───────────────────────────────────────────────────
php artisan config:cache 2>/dev/null && echo "==> config cached" || true
php artisan route:cache  2>/dev/null && echo "==> routes cached" || true
php artisan view:cache   2>/dev/null && echo "==> views cached"  || true

# ── 5. Migrations ──────────────────────────────────────────────
DB_HOST_VAL=$(php -r "
\$env = parse_ini_file('/var/www/html/.env');
echo \$env['DB_HOST'] ?? '';
")
echo "==> DB_HOST resolved to: $DB_HOST_VAL"

if [ "$DB_HOST_VAL" != "127.0.0.1" ] && [ -n "$DB_HOST_VAL" ]; then
    echo "==> Running migrations..."
    php artisan migrate --force 2>&1 && echo "==> Migrations OK" || echo "==> Migration warning"
    echo "==> Seeding..."
    php artisan db:seed --force 2>&1 && echo "==> Seeded OK" || echo "==> Seed skipped"
else
    echo "==> No external DB — skipping migrations"
fi

# ── 6. Permissions ─────────────────────────────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# ── 7. Start Apache ────────────────────────────────────────────
echo "==> Starting Apache on port 8080..."
exec apache2-foreground
