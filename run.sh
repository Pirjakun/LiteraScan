#!/bin/sh

# Run migrations in production
php artisan migrate --force

# Start Nginx & PHP-FPM (webdevops default entrypoint)
exec /entrypoint.sh
