FROM php:8.2-cli

# Установка необходимых инструментов и PHP-расширений
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libxml2-dev \
    && docker-php-ext-install \
    json \
    zip

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копирование исходного кода
COPY . /var/www/html

WORKDIR /var/www/html

# Установка прав для контейнера
RUN chown -R www-data:www-data /var/www/html