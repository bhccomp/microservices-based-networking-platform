#!/bin/sh

# Run composer install
cd /app && composer install

# Run Symfony migrations. You might use an environment variable or some other mechanism to decide whether to run this.
if [ "$RUN_MIGRATIONS" = "true" ]; then
    bin/console doctrine:migrations:migrate --no-interaction
fi

# Start nginx and php-fpm
service nginx start
php-fpm
