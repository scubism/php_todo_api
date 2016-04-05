FROM php:7-fpm

RUN apt-get update \
    && apt-get install libmcrypt-dev nano bash git -y \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo pdo_mysql mcrypt iconv mbstring

COPY . /var/www/html

ADD https://getcomposer.org/composer.phar /usr/bin/composer

RUN chmod +x /usr/bin/composer

COPY docker-entrypoint.sh /

EXPOSE 9000

ENTRYPOINT ["/docker-entrypoint.sh"]
