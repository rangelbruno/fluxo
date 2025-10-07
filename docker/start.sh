#!/bin/bash

# Wait for PostgreSQL to be ready
until pg_isready -h postgres -p 5432 -U ${DB_USERNAME:-laravel}
do
  echo "Waiting for PostgreSQL..."
  sleep 2
done

# Run migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start supervisor
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
