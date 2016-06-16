FROM scubism/php7-nginx:0.3

WORKDIR /var/www/api

COPY . .

RUN rm -f /etc/nginx/conf.d/default.conf && \
    rm -rf /var/www/html

COPY default.conf /etc/nginx/conf.d/default.conf

COPY docker-entrypoint.sh /

RUN composer install --no-dev

ENTRYPOINT ["/docker-entrypoint.sh"]
