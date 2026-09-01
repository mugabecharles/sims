# ─────────────────────────────────────────────────────────────
# Stage 1: Node — build the Vite/React frontend
# ─────────────────────────────────────────────────────────────
FROM node:20-alpine AS node_builder

WORKDIR /app

COPY package*.json ./
RUN npm ci --legacy-peer-deps

COPY resources/js   ./resources/js
COPY resources/css  ./resources/css
COPY vite.config.js tsconfig.json ./
RUN mkdir -p resources/views && echo "" > resources/views/app.blade.php

RUN npm run build

# ─────────────────────────────────────────────────────────────
# Stage 2: PHP production image
# ─────────────────────────────────────────────────────────────
FROM php:8.3-fpm-alpine

# System dependencies
RUN apk add --no-cache \
        nginx \
        supervisor \
        curl \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        oniguruma-dev \
        icu-dev \
        shadow \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP deps (no dev, optimized)
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --optimize-autoloader \
        --no-scripts \
        --no-interaction \
        --prefer-dist

# Copy full application
COPY . .

# Copy built frontend from node stage
COPY --from=node_builder /app/public/build ./public/build

# Run composer post-install scripts
RUN composer run-script post-autoload-dump --no-interaction 2>/dev/null || true

# Nginx config
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Remove default nginx site
RUN rm -f /etc/nginx/http.d/default.conf.bak

# Supervisor config
RUN mkdir -p /etc/supervisor/conf.d
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# PHP opcache tuning
RUN { \
        echo "opcache.enable=1"; \
        echo "opcache.memory_consumption=128"; \
        echo "opcache.max_accelerated_files=10000"; \
        echo "opcache.revalidate_freq=60"; \
        echo "opcache.validate_timestamps=0"; \
    } >> /usr/local/etc/php/conf.d/opcache.ini

# PHP-FPM: listen on TCP instead of socket (easier with nginx on same container)
RUN sed -i 's|listen = /run/php-fpm.sock|listen = 127.0.0.1:9000|g' \
        /usr/local/etc/php-fpm.d/www.conf || true

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Startup script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD curl -f http://localhost:8080/up || exit 1

CMD ["/start.sh"]
