# ===========================================
# Stage 1: Composer deps (production only)
# ===========================================
FROM php:8.4-fpm-alpine AS deps

WORKDIR /var/www/html

RUN apk add --no-cache composer && rm -rf /var/cache/apk/*

COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

# ===========================================
# Stage 2: Builder (assets + full copy)
# ===========================================
FROM php:8.4-fpm-alpine AS builder

WORKDIR /var/www/html

RUN apk add --no-cache nodejs npm && rm -rf /var/cache/apk/*

COPY package.json package-lock.json* ./
RUN npm ci --legacy-peer-deps

COPY --from=deps /var/www/html/vendor ./vendor
COPY . .

RUN npm run build

# ===========================================
# Stage 3: Development
# ===========================================
FROM php:8.4-fpm-alpine AS development

WORKDIR /var/www/html

RUN apk add --no-cache \
    composer nodejs npm git curl \
    libpng-dev oniguruma-dev sqlite-dev \
    zip unzip \
    && docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath gd \
    && rm -rf /var/cache/apk/*

COPY . .

RUN composer install --no-interaction --prefer-dist
RUN npm ci --legacy-peer-deps

EXPOSE 5173 8000

CMD ["sh", "-c", "\
    touch /var/www/html/database/database.sqlite && \
    php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=8000 & \
    npm run dev & \
    wait \
"]

# ===========================================
# Stage 4: Production (SQLite, non-root)
# ===========================================
FROM php:8.4-fpm-alpine AS production

WORKDIR /var/www/html

RUN apk add --no-cache \
    libpng-dev oniguruma-dev sqlite-dev curl \
    && docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath gd \
    && rm -rf /var/cache/apk/*

# Создаём пользователя
RUN addgroup -g 1001 -S appgroup && \
    adduser -u 1001 -S appuser -G appgroup

# Копируем всё приложение
COPY --from=builder /var/www/html/vendor       ./vendor
COPY --from=builder /var/www/html/public       ./public
COPY --from=builder /var/www/html/bootstrap    ./bootstrap
COPY --from=builder /var/www/html/storage      ./storage
COPY --from=builder /var/www/html/routes       ./routes
COPY --from=builder /var/www/html/resources    ./resources
COPY --from=builder /var/www/html/config       ./config
COPY --from=builder /var/www/html/app          ./app
COPY --from=builder /var/www/html/database     ./database
COPY --from=builder /var/www/html/artisan      ./artisan

# Создаём нужные директории и устанавливаем права
# ВАЖНО: делаем это ДО смены на non-root пользователя
RUN mkdir -p \
        /var/www/html/database \
        /var/www/html/storage/app/public \
        /var/www/html/storage/framework/cache/data \
        /var/www/html/storage/framework/sessions \
        /var/www/html/storage/framework/views \
        /var/www/html/storage/logs \
        /var/www/html/bootstrap/cache \
    && chown -R appuser:appgroup /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

USER appuser

EXPOSE 8000

# Entrypoint: создаём SQLite файл (если volume пустой), мигрируем, стартуем
CMD ["sh", "-c", "\
    touch /var/www/html/database/database.sqlite && \
    php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan serve --host=0.0.0.0 --port=8000 \
"]
