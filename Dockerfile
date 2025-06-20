# Base PHP image with FPM
FROM php:8.2-fpm

# Install system packages
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql zip

# Set working directory
WORKDIR /var/www

# Copy entire Laravel app (this works since Dockerfile is root-level)
COPY . /var/www

# Copy Nginx and Supervisor configs
COPY ./deploy/Ngix/nginx.conf /etc/nginx/sites-available/default
COPY ./deploy/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Expose port 80 for Nginx
EXPOSE 80

# Start Nginx + PHP-FPM via Supervisor
CMD ["/usr/bin/supervisord"]
