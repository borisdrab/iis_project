# Fáza 1: PHP závislosti (bez dev balíkov), rovnaká verzia PHP ako v image
FROM php:8.4-cli AS vendor
RUN apt-get update && apt-get install -y --no-install-recommends git unzip libzip-dev \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist
COPY . .
RUN composer dump-autoload --optimize --no-dev --no-scripts --no-interaction

# Fáza 2: frontend build. Vite spúšťa artisan (wayfinder), preto treba PHP aj vendor.
FROM php:8.4-cli AS assets
RUN apt-get update && apt-get install -y --no-install-recommends libpq-dev libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && rm -rf /var/lib/apt/lists/*
COPY --from=node:22-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && npm run build

# Výsledný image: iba kód, vendor a build. Bez .env, storage a lokálnych súborov.
FROM php:8.4-cli
RUN apt-get update && apt-get install -y --no-install-recommends libpq-dev libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor
COPY --from=assets --chown=www-data:www-data /app/public/build ./public/build

RUN mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache public

# Nie sú to tajomstvá: heslá a APP_KEY sa odovzdávajú až pri docker run (-e / --env-file)
ENV APP_ENV=production \
    APP_DEBUG=false \
    DB_CONNECTION=pgsql \
    DB_PORT=5432 \
    SESSION_DRIVER=file \
    CACHE_STORE=file \
    QUEUE_CONNECTION=sync \
    MAIL_MAILER=log

USER www-data

EXPOSE 10000

CMD ["sh", "-c", "rm -f public/storage && php artisan storage:link && php artisan config:cache && php artisan route:cache && php -S 0.0.0.0:${PORT:-10000} -t public"]
