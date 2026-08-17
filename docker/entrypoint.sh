#!/usr/bin/env bash
set -e

chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

echo "Esperando a que MySQL esté disponible..."
until php -r "try { new PDO('mysql:host='.getenv('DB_HOST').';port='.getenv('DB_PORT'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); } catch (Exception \$e) { exit(1); }" 2>/dev/null; do
    sleep 2
done
echo "MySQL disponible."

if [ ! -f vendor/autoload.php ]; then
    echo "Instalando dependencias (composer)..."
    composer install --no-interaction --prefer-dist --no-progress
fi

echo "Configurando la aplicación..."
php artisan key:generate --force

echo "Ejecutando migraciones..."
php artisan migrate --force

USERS=$(php -r "try { \$pdo = new PDO('mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); echo \$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(); } catch (Exception \$e) { echo 'ERR'; }")
if [ "$USERS" = "0" ]; then
    echo "Base de datos vacía, ejecutando seeders..."
    php artisan db:seed --class=DatabaseSeeder --force
else
    echo "Los datos ya existen, se omiten los seeders."
fi

echo "Arrancando php-fpm..."
exec "$@"