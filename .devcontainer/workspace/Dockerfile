FROM php:7.4

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y clamav \
 && mkdir /var/run/clamav \
 && chown -R clamav /var/run/clamav \
 && docker-php-ext-install sockets