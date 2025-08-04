FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    librdkafka-dev \
    pkg-config \
    build-essential

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

RUN pecl install rdkafka && docker-php-ext-enable rdkafka

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
 
COPY composer.json ./
COPY artisan ./
COPY bootstrap/ ./bootstrap/
COPY config/ ./config/
COPY app/ ./app/
COPY routes/ ./routes/
COPY resources/ ./resources/
COPY database/ ./database/
 
RUN composer install --optimize-autoloader --no-interaction
 
COPY . /var/www/html
 
RUN chown -R www-data:www-data /var/www/html

USER root
RUN git config --global --add safe.directory /var/www/html
USER www-data

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"] 