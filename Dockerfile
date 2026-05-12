# Imagem única, leve e suficiente para Render / Fly / qualquer host de Docker.
# Stack: PHP 8.2 + Apache + PDO_pgsql. Sem MySQL, sem Redis, sem postgres-client em runtime.

FROM composer:2 AS deps
WORKDIR /app
COPY src/composer.json src/composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

FROM php:8.2-apache

# Pacotes do sistema necessários para construir as extensões PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpq-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configurações de produção
COPY php.ini /usr/local/etc/php/conf.d/zz-app.ini

# Habilita mod_rewrite (não é estritamente necessário hoje, mas barato)
RUN a2enmod rewrite headers

# Código da aplicação
WORKDIR /var/www/html
COPY src/ /var/www/html/
COPY --from=deps /app/vendor /var/www/html/vendor

# Render injeta a porta via $PORT. Apache escuta nela em vez de 80 fixa.
# A substituição é feita no entrypoint.
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Permissões mínimas
RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
