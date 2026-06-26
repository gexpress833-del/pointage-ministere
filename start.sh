#!/usr/bin/env bash
set -e

# Start the application immediately
echo "Starting Laravel server..."
exec php artisan serve --host=0.0.0.0 --port=80
