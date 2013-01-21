#!/bin/sh

set -e

cd /var/www/zerebral

echo "GIT PULL..."
git pull

echo "Composer update"
/usr/bin/composer.phar update

echo "Preset permissions..."
#chmod -R 775 app/cache
#chmod 775 app/bootstrap.php.cache
#chmod -R 775 web/js/*.js
#chmod -R 755 web/js/compiled

echo "Run migrations..."
#php app/console doctrine:migrations:migrate --env=dev --no-interaction
php app/console propel:migration:migrate --env=prod --no-interaction

echo "Rebuild cache..."
php app/console cache:clear --env=stage
php app/console assetic:dump --env=stage

echo "Setup proper permisssions..."

chown -R dev:www-data app/cache
#chown -R dev:www-data app/tmp
chown -R dev:www-data app/bootstrap.php.cache

chmod -R 775 app/cache
#chmod -R 777 app/tmp

chmod 775 app/bootstrap.php.cache
#chmod -R 775 web/js/*.js
#chmod -R 755 web/js/compiled

