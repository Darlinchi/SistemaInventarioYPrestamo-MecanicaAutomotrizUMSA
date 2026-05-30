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

RUN npm install

RUN touch database/database.sqlite
RUN cp .env.example .env
RUN sed -i 's/APP_ENV=.*/APP_ENV=production/' .env
RUN sed -i 's/APP_DEBUG=.*/APP_DEBUG=true/' .env
RUN php artisan key:generate --force
RUN php artisan migrate --force --seed
RUN php artisan storage:link

RUN php artisan migrate:fresh --force --seed

EXPOSE 8000

CMD php artisan config:clear && php artisan serve --host=0.0.0.0 --port=$PORT
