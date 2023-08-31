#!/bin/sh

# Run composer install
cd /app && composer install

# Run Symfony migrations. Set environment variable on docker-compose to run this.
if [ "$RUN_MIGRATIONS" = "true" ]; then
    bin/console doctrine:migrations:migrate --no-interaction
fi

service nginx start
php-fpm
