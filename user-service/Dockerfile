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
    supervisor \
    && docker-php-ext-install \
    gd \
    intl \
    pdo_mysql \
    zip \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && rm /etc/nginx/sites-enabled/default \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory in the container
WORKDIR /app

# Copying the PHP application to the container.
COPY . /app

# Copying Nginx site configuration to the container. 
COPY ./docker/app.conf /etc/nginx/sites-available/
RUN ln -s /etc/nginx/sites-available/app.conf /etc/nginx/sites-enabled/

RUN chown -R www-data:www-data /app/var

# Custom entrypoint
COPY ./entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
