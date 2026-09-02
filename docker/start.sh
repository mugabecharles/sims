#!/bin/sh
echo "==> SIMS starting..."
cd /var/www/html

# 1. Create .env
[ ! -f .env ] && cp .env.example .env && echo "==> .env created"

# 2. Write env vars using PHP (handles special chars safely)
php -r "
\$f = '/var/www/html/.env';
\$env = file_get_contents(\$f);
\$vars = [
    'APP_NAME'         => getenv('APP_NAME')         ?: 'SIMS',
    'APP_ENV'          => 'production',
    'APP_DEBUG'        => 'false',
    'APP_URL'          => getenv('APP_URL')           ?: 'http://localhost:8080',
    'APP_TIMEZONE'     => 'Africa/Kampala',
    'APP_KEY'          => getenv('APP_KEY')           ?: '',
    'DB_CONNECTION'    => 'mysql',
    'DB_HOST'          => getenv('DB_HOST')           ?: getenv('MYSQLHOST')      ?: '127.0.0.1',
    'DB_PORT'          => getenv('DB_PORT')           ?: getenv('MYSQLPORT')      ?: '3306',
    'DB_DATABASE'      => getenv('DB_DATABASE')       ?: getenv('MYSQLDATABASE')  ?: 'railway',
    'DB_USERNAME'      => getenv('DB_USERNAME')       ?: getenv('MYSQLUSER')      ?: 'root',
    'DB_PASSWORD'      => getenv('DB_PASSWORD')       ?: getenv('MYSQLPASSWORD')  ?: '',
    'SESSION_DRIVER'   => 'database',
    'CACHE_STORE'      => 'file',
    'QUEUE_CONNECTION' => 'database',
    'LOG_LEVEL'        => 'error',
];
foreach (\$vars as \$k => \$v) {
    if (preg_match('/^'.preg_quote(\$k,'/').'=/m', \$env)) {
        \$env = preg_replace('/^'.preg_quote(\$k,'/').'=.*/m', \$k.'='.\$v, \$env);
    } else {
        \$env .= \"\n\$k=\$v\";
    }
}
file_put_contents(\$f, \$env);
echo 'DB_HOST='.\$vars['DB_HOST'].PHP_EOL;
echo 'DB_DATABASE='.\$vars['DB_DATABASE'].PHP_EOL;
echo 'DB_PASSWORD set: '.(strlen(\$vars['DB_PASSWORD'])>0?'YES':'NO').PHP_EOL;
"

# 3. APP_KEY
grep -q '^APP_KEY=base64' .env || php artisan key:generate --force

# 4. Cache
php artisan config:cache  2>/dev/null || true
php artisan route:cache   2>/dev/null || true
php artisan view:cache    2>/dev/null || true

# 5. Migrations
DB=$(php -r "\$e=parse_ini_file('.env'); echo \$e['DB_HOST']??'';" 2>/dev/null)
echo "==> DB_HOST: $DB"
if [ -n "$DB" ] && [ "$DB" != "127.0.0.1" ]; then
    php artisan migrate --force 2>&1 && echo "==> Migrated OK" || echo "==> Migration warning"
    php artisan db:seed --force  2>&1 && echo "==> Seeded OK"   || echo "==> Seed skipped"
fi

# 6. Permissions
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# 7. Start Laravel built-in server (no Apache/Nginx needed)
echo "==> Starting Laravel on 0.0.0.0:8080..."
exec php artisan serve --host=0.0.0.0 --port=8080
