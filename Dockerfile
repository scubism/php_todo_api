FROM scubism/php_api_base:latest

# === Set app specific settings ===

COPY . .

RUN composer install --no-dev

COPY docker-entrypoint.sh /

EXPOSE 9000

ENTRYPOINT ["/docker-entrypoint.sh"]
