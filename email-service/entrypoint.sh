#!/bin/sh

# Run composer install
cd /app && composer install

# Run Symfony migrations. Set environment variable on docker-compose to run this.
if [ "$RUN_MIGRATIONS" = "true" ]; then
    bin/console doctrine:migrations:migrate --no-interaction
fi

# Start nginx 
service nginx start

# Start supervisord in the foreground. This will also manage php-fpm if it's configured in supervisor.
/usr/bin/supervisord -n