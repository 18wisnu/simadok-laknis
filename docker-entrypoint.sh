#!/bin/sh

# Install composer dependencies
composer install --no-interaction --optimize-autoloader

# Wait for database connection
echo "Waiting for database..."
while ! nc -z db 3306; do
  sleep 1
done
echo "Database is up!"

# Run migrations
php artisan migrate --force

# Start php-fpm
php-fpm
