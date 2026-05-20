FROM php:8.4-cli-bookworm AS base

ARG UID
ARG GID

RUN addgroup --gid=$GID php \
    && adduser --uid=$UID --gid=$GID --shell=/bin/bash --no-create-home php

RUN apt-get update --quiet --assume-yes \
    && apt-get install --quiet --assume-yes --no-install-recommends --no-install-suggests unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer/composer:2.9-bin --chmod=0555 /composer /usr/bin/composer

WORKDIR /root/app

FROM base AS develop

RUN pecl install xdebug
