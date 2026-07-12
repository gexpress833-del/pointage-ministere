#!/usr/bin/env bash
set -e

# Fix APP_KEY: Laravel requires base64: prefix
if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY is missing. Configure a persistent Laravel application key."
    exit 1
fi

if [ "${APP_KEY#base64:}" = "$APP_KEY" ] && [ "${#APP_KEY}" -ne 32 ]; then
    if php -r '$decoded = base64_decode(getenv("APP_KEY"), true); exit(is_string($decoded) && strlen($decoded) === 32 ? 0 : 1);'; then
        export APP_KEY="base64:$APP_KEY"
    else
        echo "ERROR: APP_KEY is neither a 32-byte raw key nor valid base64 for 32 bytes."
        exit 1
    fi
fi

php -r '$key = getenv("APP_KEY"); $raw = str_starts_with($key, "base64:") ? base64_decode(substr($key, 7), true) : $key; exit(is_string($raw) && strlen($raw) === 32 ? 0 : 1);' || {
    echo "ERROR: APP_KEY is invalid for AES-256-CBC."
    exit 1
}
echo "APP_KEY is valid."

# Run migrations (non-fatal: DB might not be ready yet on first deploy)
echo "Running migrations..."
php artisan migrate --force --no-interaction

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
