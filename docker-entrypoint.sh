#!/bin/bash
set -e

usermod -u 1000 www-data

ENV=${APP_ENV:-'local'}
RANDOM_KEY=`< /dev/urandom tr -dc A-Za-z0-9 | head -c${1:-32};echo;`
FORCE=''

if [ -f "/var/run/php-fpm.pid" ]; then
   pid=`cat /var/run/php-fpm.pid`
   if [ ! -z ${pid} ]; then
     echo "PHP-FPM is running. Killing pid ${pid}"
     kill ${pid}
   fi
 fi

if [ $ENV == 'local' ]; then
  APP_DEBUG=true
  if [ -f "/usr/local/etc/php-fpm.d/zz-docker.conf" ]; then
    sed -i "s/daemonize = no/daemonize = yes/g" /usr/local/etc/php-fpm.d/zz-docker.conf
  fi
  mkdir -p /var/log/php-fpm
  touch /var/log/php-fpm/error.log
  touch /var/log/php-fpm/access.log
  if [ -f "/usr/local/etc/php-fpm.d/docker.conf" ]; then
    sed -i "s/error_log = \/proc\/self\/fd\/2/error_log = \/var\/log\/php-fpm\/error.log/g" /usr/local/etc/php-fpm.d/docker.conf
    sed -i "s/access.log = \/proc\/self\/fd\/2/access.log = \/var\/log\/php-fpm\/access.log/g" /usr/local/etc/php-fpm.d/docker.conf
  fi
  composer install # For install dev packages
else
  APP_DEBUG=false
  FORCE='--force'
  if [ -f "/usr/local/etc/php-fpm.d/zz-docker.conf" ]; then
    sed -i "s/daemonize = yes/daemonize = no/g" /usr/local/etc/php-fpm.d/zz-docker.conf
  fi
  if [ -f "/usr/local/etc/php-fpm.d/docker.conf" ]; then
    sed -i "s/error_log = \/var\/log\/php-fpm\/error.log/error_log = \/proc\/self\/fd\/2/g" /usr/local/etc/php-fpm.d/docker.conf
    sed -i "s/access.log = \/var\/log\/php-fpm\/access.log/access.log = \/proc\/self\/fd\/2/g" /usr/local/etc/php-fpm.d/docker.conf
  fi
fi

if [ ! -f ".env" ]; then
  sed -e "s/DB_HOST=localhost/DB_HOST=${DB_HOST}/g" \
    -e "s/DB_PORT=3306/DB_PORT=${DB_PORT}/g" \
    -e "s/DB_DATABASE=homestead/DB_DATABASE=${DB_NAME}/g" \
    -e "s/DB_USERNAME=homestead/DB_USERNAME=${DB_USER}/g" \
    -e "s/DB_PASSWORD=secret/DB_PASSWORD=${DB_PASSWORD}/g" \
    -e "s/APP_ENV=local/APP_ENV=${ENV}/g" \
    -e "s/APP_DEBUG=true/APP_DEBUG=${APP_DEBUG}/g" \
    -e "s/APP_KEY=SomeRandomKey!!!/APP_KEY=${RANDOM_KEY}/g" \
    < .env.example \
    > .env
fi

chown -R www-data:www-data /var/www/html
chmod 777 -R /var/www/html/storage

composer dump-autoload
php artisan migrate ${FORCE} & wait
php artisan db:seed --class=TodoGroupSeeder ${FORCE} & wait

exec "php-fpm" -g /var/run/php-fpm.pid
