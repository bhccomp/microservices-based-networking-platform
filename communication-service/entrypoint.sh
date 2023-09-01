#!/bin/sh

# Run composer install
cd /app && composer install

# Comment this out after first run
bin/console doctrine:migrations:migrate --no-interaction

# Start nginx 
service nginx start

# Start supervisord in the foreground. This will also manage php-fpm if it's configured in supervisor.
/usr/bin/supervisord -n