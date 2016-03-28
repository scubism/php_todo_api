#!/bin/env bash
set -e

DEV_MODE=${DEV_MODE:-'0'}

if [ $DEV_MODE == 1 ]; then
  adduser --system --uid=$(stat -c %u .) devuser
  sed -i 's/User apache/User devuser/g' /etc/httpd/conf/httpd.conf
  sed -i 's/Group apache/Group devuser/g' /etc/httpd/conf/httpd.conf
  chown root:devuser -R . && chmod 775 -R .
else
  chown root:apache -R . && chmod 775 -R .
fi

if [ "$1" = '' ]; then
  rm -rf /run/httpd/* /tmp/httpd*
  set -- /usr/sbin/apachectl -D FOREGROUND
fi

echo "$@"
exec "$@"
