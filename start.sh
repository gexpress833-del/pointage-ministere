#!/usr/bin/env bash
set -e

# Fix APP_KEY: Laravel requires base64: prefix
if [ -n "$APP_KEY" ] && [ "${APP_KEY#base64:}" = "$APP_KEY" ]; then
    echo "Fixing APP_KEY format (adding base64: prefix)..."
    export APP_KEY="base64:$APP_KEY"
elif [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    export APP_KEY=$(php artisan key:generate --show)
fi
echo "APP_KEY is set (starts with: ${APP_KEY:0:7})"

# Run migrations (non-fatal: DB might not be ready yet on first deploy)
echo "Running migrations..."
php artisan migrate --force --no-interaction || echo "WARNING: migrations failed, continuing..."

# Run seeders (non-fatal)
echo "Running seeders..."
php artisan db:seed --class=ParametresSeeder --force --no-interaction || echo "WARNING: ParametresSeeder failed, continuing..."
php artisan db:seed --class=AdminSeeder --force --no-interaction || echo "WARNING: AdminSeeder failed, continuing..."

# Create storage link
echo "Creating storage link..."
php artisan storage:link || true

# Ensure storage directories exist and are writable
echo "Ensuring storage directories..."
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs
mkdir -p storage/app/private storage/app/private/photos_reference
mkdir -p storage/app/livewire-tmp
mkdir -p storage/app/public
chmod -R 775 storage bootstrap/cache

# Cache config, routes, views (non-fatal)
echo "Caching config..."
php artisan config:cache || echo "WARNING: config:cache failed, continuing..."
php artisan route:cache || echo "WARNING: route:cache failed, continuing..."
php artisan view:cache || echo "WARNING: view:cache failed, continuing..."

# Start the application on Render's PORT (default 80)
PORT=${PORT:-80}
echo "Starting Laravel server on port $PORT..."
exec php artisan serve --host=0.0.0.0 --port=$PORT
