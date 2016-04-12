FROM php:7-fpm

# Add unzip & git for composer
RUN apt-get update \
    && apt-get install libmcrypt-dev nano bash git unzip -y \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo pdo_mysql mcrypt iconv mbstring

WORKDIR /var/www/html

COPY . .

ADD https://getcomposer.org/composer.phar /usr/bin/composer

RUN chmod +x /usr/bin/composer

RUN composer install

COPY docker-entrypoint.sh /

EXPOSE 9000

ENTRYPOINT ["/docker-entrypoint.sh"]
