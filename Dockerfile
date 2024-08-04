FROM php:8.1-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install Redis extension using Pecl
# RUN pecl install redis
# RUN && docker-php-ext-enable redis

RUN apt-get update && apt-get install -y redis

# Copie o código do seu projeto para o diretório raiz do servidor
COPY src/ /var/www/html/

# COPY redis.conf redis.conf

VOLUME /usr/local/etc/redis

# Copie o arquivo de configuração personalizado do PHP
COPY php.ini /usr/local/etc/php/

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install

# Exponha a porta padrão do Apache
EXPOSE 80