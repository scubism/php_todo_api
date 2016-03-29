#!/bin/env bash
set -e

APP_ENV=${APP_ENV:-'local'}
RANDOM_KEY=`< /dev/urandom tr -dc A-Za-z0-9 | head -c${1:-32};echo;`

if [ $APP_ENV == 'local' ]; then
  APP_DEBUG=true
  HTTPD_STATE="BACKGROUD"
  if getent passwd devuser > /dev/null 2>&1; then
    echo 'User devuser is existed'
  else
    adduser --system --uid=$(stat -c %u .) devuser
    sed -i 's/User apache/User devuser/g' /etc/httpd/conf/httpd.conf
    sed -i 's/Group apache/Group devuser/g' /etc/httpd/conf/httpd.conf
  fi
  chown root:devuser -R . && chmod 775 -R .
else
  APP_DEBUG=false
  HTTPD_STATE="FOREGROUND"
  chown root:apache -R . && chmod 775 -R .
fi

if [ ! -f ".env" ]; then
  sed -e "s/DB_HOST=localhost/DB_HOST=${DB_HOST}/g" \
    -e "s/DB_PORT=3306/DB_PORT=${DB_PORT}/g" \
    -e "s/DB_DATABASE=homestead/DB_DATABASE=${DB_NAME}/g" \
    -e "s/DB_USERNAME=homestead/DB_USERNAME=${DB_USER}/g" \
    -e "s/DB_PASSWORD=secret/DB_PASSWORD=${DB_PASSWORD}/g" \
    -e "s/APP_ENV=local/APP_ENV=${APP_ENV}/g" \
    -e "s/APP_DEBUG=true/APP_DEBUG=${APP_DEBUG}/g" \
    -e "s/APP_KEY=SomeRandomKey!!!/APP_KEY=${RANDOM_KEY}/g" \
    < .env.example \
    > .env
fi

if [ "$1" = '' ]; then
  rm -rf /run/httpd/* /tmp/httpd*
  set -- /usr/sbin/apachectl -D $HTTPD_STATE
fi

echo "$@"
exec "$@"

