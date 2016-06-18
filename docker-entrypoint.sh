#!/bin/bash
set -e

ENV=${APP_ENV:-'local'}
RANDOM_KEY=`< /dev/urandom tr -dc A-Za-z0-9 | head -c${1:-32};echo;`
FORCE=''

if [ -f "/var/run/php-fpm.pid" ]; then
  pid=`cat /var/run/php-fpm.pid`
  if [ ! -z ${pid} ]; then
    echo "PHP-FPM is running. Killing pid ${pid}"
    kill -QUIT ${pid}
    echo "" > /var/run/php-fpm.pid
  fi
fi

if [ -f "/var/run/nginx.pid" ]; then
  pid=`cat /var/run/nginx.pid`
  if [ ! -z ${pid} ]; then
    echo "Nginx is running. Killing pid ${pid}"
    kill -QUIT ${pid}
    echo "" > /var/run/nginx.pid
  fi
fi

if [ $ENV == 'local' ]; then
  APP_DEBUG=true
  composer install # For install dev packages
  if [ -L "/var/log/nginx/access.log" ]; then
    unlink /var/log/nginx/access.log
    touch /var/log/nginx/access.log
  fi
  if [ -L "/var/log/nginx/error.log" ]; then
    unlink /var/log/nginx/error.log
    touch /var/log/nginx/error.log
  fi
else
  APP_DEBUG=false
  FORCE='--force'
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

touch /var/www/api/storage/logs/lumen.log
chown -R nginx:nginx /var/www/api
chmod 777 -R /var/www/api/storage

composer dump-autoload
/usr/local/bin/dockerize --wait tcp://${DB_HOST}:${DB_PORT} && php artisan migrate ${FORCE} & wait

php-fpm -g /var/run/php-fpm.pid
if [ $ENV == 'local' ]; then
  exec "nginx" -g "daemon on;"
else
  exec "nginx" -g "daemon off;"
fi
