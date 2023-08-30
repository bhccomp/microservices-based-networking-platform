FROM php:8.1-fpm

# Install system dependencies and PHP extensions required for Symfony
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    libicu-dev \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install \
    gd \
    intl \
    pdo_mysql \
    zip

# Remove default Nginx configuration
RUN rm /etc/nginx/sites-enabled/default

# Copy your Nginx site configuration to the container. This should be set up to forward PHP requests to php-fpm.
COPY ./docker/app.conf /etc/nginx/sites-available/

# Create a symbolic link to enable the site
RUN ln -s /etc/nginx/sites-available/app.conf /etc/nginx/sites-enabled/

# Start Nginx and PHP-FPM
CMD service nginx start && php-fpm

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory in the container
WORKDIR /app

# Copy the PHP application to the container.
COPY . /app

EXPOSE 80

COPY ./entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]