#!/bin/sh
set -e
cd /var/www/html
PORT="${PORT:-10000}"
sed -ri "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \\*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf
php artisan config:clear || true
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
exec apache2-foreground
