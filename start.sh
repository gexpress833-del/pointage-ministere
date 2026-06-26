#!/usr/bin/env bash
set -e

# Wait for database to be ready
echo "Waiting for database connection..."
until php artisan db:show 2>/dev/null; do
    echo "Database not ready yet, waiting..."
    sleep 2
done

echo "Database is ready!"

# Run migrations
echo "Running migrations..."
php artisan migrate --force --no-interaction

# Run seeders
echo "Running seeders..."
php artisan db:seed --class=ParametresSeeder --force --no-interaction

# Create storage link
echo "Creating storage link..."
php artisan storage:link || true

# Start the application
echo "Starting Laravel server..."
exec php artisan serve --host=0.0.0.0 --port=80
