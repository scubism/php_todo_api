FROM scubism/php_api_base

WORKDIR /var/www/api

COPY . .

COPY docker-entrypoint.sh /

RUN composer install --no-dev

ENTRYPOINT ["/docker-entrypoint.sh"]
