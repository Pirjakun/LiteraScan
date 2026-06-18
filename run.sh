#!/bin/sh

# Run migrations in production
php artisan migrate --force

# Start Nginx & PHP-FPM (webdevops default entrypoint)
exec /opt/docker/bin/entrypoint.sh supervisord
