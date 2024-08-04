FROM php:8.1-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copie o código do seu projeto para o diretório raiz do servidor
COPY src/ /var/www/html/

# Copie o arquivo de configuração personalizado do PHP
COPY php.ini /usr/local/etc/php/

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install

# Exponha a porta padrão do Apache
EXPOSE 80