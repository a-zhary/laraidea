# ===========================================
# Stage 1: Dependencies (install only production deps)
# ===========================================
FROM php:8.4-fpm-alpine AS deps

WORKDIR /var/www/html

RUN apk add --no-cache \
    composer \
    && rm -rf /var/cache/apk/*

COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-interaction --optimize-autoloader

# ===========================================
# Stage 2: Builder (install all deps + build)
# ===========================================
FROM deps AS builder

RUN apk add --no-cache \
    nodejs \
    npm \
    && rm -rf /var/cache/apk/*

COPY package.json package-lock.json* ./
RUN npm ci --legacy-peer-deps

COPY --from=deps /var/www/html/vendor ./vendor
COPY . .
RUN npm run build

# ===========================================
# Stage 3: Development (hot reload)
# ===========================================
FROM php:8.4-fpm-alpine AS development

WORKDIR /var/www/html

RUN apk add --no-cache \
    composer \
    nodejs \
    npm \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    sqlite-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath gd \
    && rm -rf /var/cache/apk/*

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-interaction --prefer-dist
RUN npm ci --legacy-peer-deps

EXPOSE 5173 8000

CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=8000 & npm run dev & wait"]

# ===========================================
# Stage 4: Production (minimal, non-root)
# ===========================================
FROM php:8.4-fpm-alpine AS production

WORKDIR /var/www/html

RUN apk add --no-cache \
    libpng-dev \
    oniguruma-dev \
    sqlite-dev \
    curl \
    && docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath gd \
    && rm -rf /var/cache/apk/*

RUN addgroup -g 1001 -S appgroup && \
    adduser -u 1001 -S appuser -G appgroup

COPY --from=builder /var/www/html/vendor ./vendor
COPY --from=builder /var/www/html/public ./public
COPY --from=builder /var/www/html/storage ./storage
COPY --from=builder /var/www/html/bootstrap ./bootstrap
COPY --from=builder /var/www/html/routes ./routes
COPY --from=builder /var/www/html/resources ./resources
COPY --from=builder /var/www/html/config ./config
COPY --from=builder /var/www/html/.env.production ./.env

RUN mkdir -p database && touch database/database.sqlite

RUN chown -R appuser:appgroup /var/www/html

USER appuser

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
