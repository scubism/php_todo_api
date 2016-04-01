#!/bin/sh
set -e

ENV=${APP_ENV:-'local'}
RANDOM_KEY=`< /dev/urandom tr -dc A-Za-z0-9 | head -c${1:-32};echo;`

if [ $ENV = 'local' ]; then
  APP_DEBUG=true
else
  APP_DEBUG=false
fi

chmod 775 -R /var/www/html

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

exec "php-fpm"
