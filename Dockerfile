FROM php:8.2-cli

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev \
    libxml2-dev libsqlite3-dev sqlite3 \
    nodejs npm \
    && docker-php-ext-install pdo pdo_sqlite zip gd bcmath \
    && apt-get clean

# Instalar Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www

# Copiar archivos
COPY . .

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Instalar dependencias Node y compilar
RUN npm install && npm run build

# Preparar Laravel
RUN touch database/database.sqlite
RUN cp .env.example .env
RUN php artisan key:generate --force
RUN php artisan migrate --force --seed
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache
RUN php artisan storage:link

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=$PORT
