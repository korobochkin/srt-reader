FROM php:8.4-cli-bookworm

COPY --from=composer/composer:2.9-bin /composer /usr/bin/composer

RUN apt-get update --quiet --assume-yes \
    && apt-get install --quiet --assume-yes --no-install-recommends --no-install-suggests unzip \
    && apt-get clean

WORKDIR /root/app
