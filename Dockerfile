# ============================================================
#  Laravel 12 + Vue 3 + Inertia – Docker
#  Base: php:8.2-cli-alpine (tokenizer, mbstring, etc. ya incluidos)
#  Node instalado desde nodesource via curl
# ============================================================
FROM php:8.2-cli-alpine

# --- Dependencias del sistema ---
RUN apk add --no-cache \
    bash curl git zip unzip \
    libpng libjpeg-turbo freetype \
    libzip libxml2 \
    sqlite sqlite-libs \
    oniguruma \
    # dev headers para compilar extensiones PHP
    libpng-dev libjpeg-turbo-dev freetype-dev \
    libzip-dev libxml2-dev \
    sqlite-dev \
    oniguruma-dev \
    # Node.js 20 desde repo Alpine (nodejs-current está en edge/community)
    nodejs npm

# --- Extensiones PHP (tokenizer, mbstring, xml ya están built-in en esta imagen) ---
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_sqlite zip gd bcmath opcache

# Limpiar dev headers para ahorrar espacio
RUN apk del --no-cache \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    libzip-dev libxml2-dev sqlite-dev oniguruma-dev \
    && rm -rf /var/cache/apk/*

# --- Composer ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# --- Código fuente ---
COPY . .

# --- Dependencias PHP (sin dev) ---
RUN composer install --no-dev --optimize-autoloader --no-interaction

# --- Setup previo a Vite (wayfinder necesita artisan) ---
RUN cp .env.docker .env \
    && php artisan key:generate --force \
    && touch database/database.sqlite \
    && php artisan migrate --force

# --- Build de assets con Vite ---
RUN npm install \
    && npm run build \
    && rm -rf node_modules

# --- Permisos ---
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# --- Entrypoint ---
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
