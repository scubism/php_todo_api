FROM scubism/php7-nginx:0.5

RUN curl -L -k -o /tmp/dockerize.tar.gz https://github.com/jwilder/dockerize/releases/download/v0.2.0/dockerize-linux-amd64-v0.2.0.tar.gz && \
    tar -C /usr/local/bin -xzvf /tmp/dockerize.tar.gz && \
    rm /tmp/dockerize.tar.gz

WORKDIR /var/www/api

COPY . .

RUN rm -f /etc/nginx/conf.d/default.conf && \
    rm -rf /var/www/html

COPY default.conf /etc/nginx/conf.d/default.conf

COPY docker-entrypoint.sh /

RUN composer install --no-dev

ENTRYPOINT ["/docker-entrypoint.sh"]
