#!/bin/sh
set -e
cd /var/www/html
php artisan config:clear || true
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
exec apache2-foreground
