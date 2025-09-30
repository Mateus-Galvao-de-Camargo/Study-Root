FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    mariadb-client \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql zip

# Install Redis extension using Pecl
# RUN pecl install redis
# RUN && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the application
COPY src/ /var/www/html/

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy PHP configuration
COPY php.ini /usr/local/etc/php/

# Copy database initialization script
COPY init-db.sh /usr/local/bin/init-db.sh
RUN chmod +x /usr/local/bin/init-db.sh

# Create startup script
RUN echo '#!/bin/bash\n\
# Start database initialization in background\n\
/usr/local/bin/init-db.sh &\n\
\n\
# Start Apache in foreground\n\
apache2-foreground' > /usr/local/bin/start.sh && \
chmod +x /usr/local/bin/start.sh

# Exponha a porta padrão do Apache
EXPOSE 80

# Use custom startup script
CMD ["/usr/local/bin/start.sh"]