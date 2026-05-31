FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev \
    libxml2-dev libsqlite3-dev sqlite3 \
    && docker-php-ext-install pdo pdo_sqlite zip gd bcmath \
    && apt-get clean

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN cp .env.example .env
RUN php artisan key:generate --force
RUN php artisan storage:link

EXPOSE 8000

CMD touch database/database.sqlite && php artisan config:clear && php artisan migrate:fresh --force --seed && php artisan tinker --execute="echo App\Models\User::count().' usuarios';" && php artisan serve --host=0.0.0.0 --port=$PORT
