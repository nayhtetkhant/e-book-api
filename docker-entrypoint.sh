#!/bin/sh
set -e

# Fix permissions for Laravel writable directories
mkdir -p /var/www/storage/framework/cache/data \
         /var/www/storage/framework/sessions \
         /var/www/storage/framework/views \
         /var/www/storage/logs \
         /var/www/bootstrap/cache

chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Wait for MySQL if DB_HOST is provided
if [ -n "$DB_HOST" ]; then
    echo "Waiting for database connection at $DB_HOST:${DB_PORT:-3306}..."
    until php -r '
        $host = getenv("DB_HOST");
        $port = getenv("DB_PORT") ?: "3306";
        $db = getenv("DB_DATABASE");
        $user = getenv("DB_USERNAME");
        $pass = getenv("DB_PASSWORD");
        try {
            new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
            exit(0);
        } catch (\Throwable $e) {
            exit(1);
        }
    ' 2>/dev/null; do
        sleep 2
    done
    echo "Database connected successfully!"
fi

# Run migrations
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || true
fi

# Cache optimization for production
if [ "$APP_ENV" = "production" ]; then
    echo "Optimizing application for production..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

exec "$@"
