#!/usr/bin/env bash
set -e

# Generate APP_KEY if not set (Render generateValue doesn't produce base64: format)
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "Generating APP_KEY..."
    export APP_KEY=$(php artisan key:generate --show)
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force --no-interaction

# Run seeders
echo "Running seeders..."
php artisan db:seed --class=ParametresSeeder --force --no-interaction
php artisan db:seed --class=AdminSeeder --force --no-interaction

# Create storage link
echo "Creating storage link..."
php artisan storage:link || true

# Cache config with env vars now available
echo "Caching config..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start the application on Render's PORT (default 80)
PORT=${PORT:-80}
echo "Starting Laravel server on port $PORT..."
exec php artisan serve --host=0.0.0.0 --port=$PORT
