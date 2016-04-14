FROM scubism/php_api_base

COPY . /var/www/html

COPY docker-entrypoint.sh /

EXPOSE 9000

ENTRYPOINT ["/docker-entrypoint.sh"]
