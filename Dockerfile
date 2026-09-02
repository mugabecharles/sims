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
# Stage 2: Laravel with PHP built-in server (no Apache/Nginx)
# Simplest possible setup — zero MPM/config issues
# ─────────────────────────────────────────────────────────────
FROM php:8.3-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev libjpeg-dev libfreetype6-dev \
        libzip-dev libicu-dev libonig-dev unzip curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction --prefer-dist

COPY . .
COPY --from=node_builder /app/public/build ./public/build

RUN composer run-script post-autoload-dump --no-interaction 2>/dev/null || true

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]
