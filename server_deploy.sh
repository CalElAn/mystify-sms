#!/bin/sh
set -e

echo "Deploying application ..."

# activate maintenance mode
(php artisan down) || true

# Update codebase
git fetch origin deploy
git reset --hard origin/deploy

# update PHP dependencies
composer install --no-interaction --no-dev --prefer-dist

#optimize loading
php artisan config:cache
php artisan route:cache
php artisan view:cache

# update database
php artisan migrate --force

# stop maintenance mode
php artisan up
 
echo "Application deployed!"