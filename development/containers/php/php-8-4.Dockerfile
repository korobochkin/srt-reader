FROM php:8.5.8-cli-alpine AS php-base

SHELL ["/bin/sh", "-euo", "pipefail", "-c"]

RUN \
    --mount=type=cache,target=/var/cache/apk,sharing=locked \
    apk update --no-progress \
    && apk upgrade --no-progress \
    && apk add --no-progress make

ARG UID
ARG GID

RUN \
   addgroup -g $GID php \
   && adduser -u $UID -G php -D -s /bin/sh php

USER $UID:$GID

WORKDIR /home/php/app
