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
# Stage 2: PHP with Apache (simplest production setup)
# ─────────────────────────────────────────────────────────────
FROM php:8.3-apache

# Enable Apache mod_rewrite and disable conflicting MPM modules
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true \
    && a2enmod mpm_prefork rewrite

# Install PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libzip-dev \
        libicu-dev \
        libonig-dev \
        unzip \
        curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set document root to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/000-default.conf \
        /etc/apache2/apache2.conf \
        /etc/apache2/conf-available/docker-php.conf

# Allow .htaccess overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/g' \
        /etc/apache2/apache2.conf

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

# Copy built frontend assets
COPY --from=node_builder /app/public/build ./public/build

# Post-install scripts
RUN composer run-script post-autoload-dump --no-interaction 2>/dev/null || true

# PHP opcache settings
RUN { \
    echo "opcache.enable=1"; \
    echo "opcache.memory_consumption=128"; \
    echo "opcache.max_accelerated_files=10000"; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Startup script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Apache listens on 8080 for Railway
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
    && sed -i 's/:80>/:8080>/' /etc/apache2/sites-available/000-default.conf

EXPOSE 8080

CMD ["/start.sh"]
