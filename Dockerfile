# ─────────────────────────────────────────────────────────────
# Stage 1: Node — build the Vite/React frontend
# ─────────────────────────────────────────────────────────────
FROM node:20-alpine AS node_builder

WORKDIR /app

COPY package*.json ./
RUN npm ci --legacy-peer-deps

COPY resources/js ./resources/js
COPY resources/css ./resources/css
COPY vite.config.js tsconfig.json ./

# Provide a minimal app.blade.php so the vite plugin doesn't error
RUN mkdir -p resources/views && echo "" > resources/views/app.blade.php

RUN npm run build

# ─────────────────────────────────────────────────────────────
# Stage 2: PHP — production Laravel image
# ─────────────────────────────────────────────────────────────
FROM php:8.3-fpm-alpine AS php_base

# Install system dependencies
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
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first for better layer caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy application source
COPY . .

# Copy built frontend assets from node stage
COPY --from=node_builder /app/public/build ./public/build

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Run post-install scripts now that full app is present
RUN composer run-script post-autoload-dump --no-interaction 2>/dev/null || true

# ─────────────────────────────────────────────────────────────
# Nginx config
# ─────────────────────────────────────────────────────────────
RUN mkdir -p /etc/nginx/http.d
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# ─────────────────────────────────────────────────────────────
# Supervisor config (runs both nginx + php-fpm)
# ─────────────────────────────────────────────────────────────
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# PHP opcache config for production
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=60" >> /usr/local/etc/php/conf.d/opcache.ini

# Startup script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]
