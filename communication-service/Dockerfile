FROM php:8.1-fpm

# Install system dependencies and PHP extensions required for Symfony
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    libicu-dev \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    librabbitmq-dev \
    unzip \
    && docker-php-ext-install \
    gd \
    intl \
    pdo_mysql \
    zip \
    && pecl install amqp \
    && docker-php-ext-enable amqp

# Remove default Nginx configuration
RUN rm /etc/nginx/sites-enabled/default

# Copy your Nginx site configuration to the container.
COPY ./docker/app.conf /etc/nginx/sites-available/

# Create a symbolic link to enable the site
RUN ln -s /etc/nginx/sites-available/app.conf /etc/nginx/sites-enabled/

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory in the container
WORKDIR /app

# Copy the PHP application to the container.
COPY . /app

# Install Supervisor
RUN apt-get update && apt-get install -y supervisor && rm -rf /var/lib/apt/lists/*

# Copy supervisor configuration
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN chown -R www-data:www-data /app/var

EXPOSE 80

COPY ./entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]