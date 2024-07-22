#!/bin/bash

while ! mysql -h "$DB_HOST" -u "$DB_USER" -p"$MYSQL_ROOT_PASSWORD" -e "SELECT 1" > /dev/null 2>&1; do
    echo "Waiting for MySQL to be available..."
    sleep 1
done

echo "Creating databases and running migrations..."

php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction

php bin/console doctrine:database:create --if-not-exists --env=test
php bin/console doctrine:migrations:migrate --no-interaction --env=test
exec apache2-foreground
