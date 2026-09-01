# ─────────────────────────────────────────────────────────────
# Stage 1: Node — build Vite/React frontend
# ─────────────────────────────────────────────────────────────
FROM node:20-slim AS node_builder

WORKDIR /app
COPY package*.json ./
RUN npm ci --legacy-peer-deps

COPY resources/js   ./resources/js
COPY resources/css  ./resources/css
COPY vite.config.js tsconfig.json ./
RUN mkdir -p resources/views && echo "" > resources/views/app.blade.php
RUN npm run build

# ─────────────────────────────────────────────────────────────
# Stage 2: PHP on Debian (more stable than Alpine for production)
# ─────────────────────────────────────────────────────────────
FROM php:8.3-fpm-bookworm

# Install system packages
RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx \
        supervisor \
        curl \
        unzip \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libzip-dev \
        libicu-dev \
        libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --optimize-autoloader \
        --no-scripts \
        --no-interaction \
        --prefer-dist

# Copy application
COPY . .

# Copy built assets
COPY --from=node_builder /app/public/build ./public/build

# Post-install
RUN composer run-script post-autoload-dump --no-interaction 2>/dev/null || true

# Nginx
COPY docker/nginx.conf /etc/nginx/sites-available/default
RUN rm -f /etc/nginx/sites-enabled/default \
    && ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# PHP-FPM: TCP socket
RUN sed -i 's|listen = /run/php/php8.3-fpm.sock|listen = 127.0.0.1:9000|g' \
        /etc/php/8.3/fpm/pool.d/www.conf 2>/dev/null || \
    sed -i 's|listen = /run/php-fpm/www.sock|listen = 127.0.0.1:9000|g' \
        /usr/local/etc/php-fpm.d/www.conf 2>/dev/null || true

# PHP opcache
RUN { \
    echo "opcache.enable=1"; \
    echo "opcache.memory_consumption=128"; \
    echo "opcache.max_accelerated_files=10000"; \
    echo "opcache.validate_timestamps=0"; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

# Startup script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]
