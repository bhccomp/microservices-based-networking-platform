#!/bin/sh

# Run composer install
cd /app && composer install

# Comment this out after first run
bin/console doctrine:migrations:migrate --no-interaction

# Start nginx and php-fpm
service nginx start
php-fpm


