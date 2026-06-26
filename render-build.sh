#!/usr/bin/env bash
set -euo pipefail

composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
npm ci
npm run build

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force --no-interaction
php artisan db:seed --class=ParametresSeeder --force --no-interaction
php artisan storage:link
