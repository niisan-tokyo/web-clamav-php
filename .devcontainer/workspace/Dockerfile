FROM php:7.4

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y clamav clamav-daemon \
 && mkdir /var/run/clamav \
 && chown -R clamav /var/run/clamav \
 && docker-php-ext-install sockets
 && freshclam