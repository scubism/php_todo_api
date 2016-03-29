FROM scubism/php56-apache:0.1

RUN rm -rf /var/www/html

ADD php_todo_api.conf /var/www/php_todo_api.conf

RUN cat /var/www/php_todo_api.conf >> /etc/httpd/conf/httpd.conf

RUN mkdir /var/www/php_todo_api

WORKDIR /var/www/php_todo_api

COPY . .

RUN php -r "readfile('https://getcomposer.org/installer');" > composer-setup.php && \
php -r "if (hash('SHA384', file_get_contents('composer-setup.php')) === '41e71d86b40f28e771d4bb662b997f79625196afcca95a5abf44391188c695c6c1456e16154c75a211d238cc3bc5cb47') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"  && \
php composer-setup.php --install-dir=/usr/bin --filename=composer && \
php -r "unlink('composer-setup.php');"

COPY docker-entrypoint.sh /

EXPOSE 80 443

ENTRYPOINT ["/docker-entrypoint.sh"]
