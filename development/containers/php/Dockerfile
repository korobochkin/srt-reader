FROM php:8.5.7-cli-alpine AS php-base

SHELL ["/bin/sh", "-euo", "pipefail", "-c"]

ARG UID
ARG GID

USER $UID:$GID

WORKDIR /home/www-data/app

FROM php-base AS php-base-with-composer

USER root

RUN \
    --mount=type=cache,target=/var/cache/apk,sharing=locked \
    apk update --progress=no \
    && apk upgrade --progress=no \
    && apk add --progress=no unzip make

COPY --from=composer/composer:2.10-bin --chmod=0555 /composer /usr/bin/composer

FROM php-base-with-composer AS dependencies

USER $UID:$GID

RUN \
    --mount=type=bind,source=source,destination=./source \
    --mount=type=bind,source=tests,destination=./tests \
    --mount=type=bind,source=composer.json,destination=./composer.json \
    --mount=type=bind,source=composer.lock,destination=./composer.lock \
    --mount=type=bind,source=Makefile,destination=./Makefile \
    --mount=type=cache,id=composer,mode=0755,uid=$UID,gid=$GID,destination=/home/www-data/.composer \
    make vendor

FROM php-base AS tests-runner

COPY --from=dependencies /home/www-data/app/vendor /home/www-data/app/vendor

FROM tests-runner AS tests-runner-integration

ENTRYPOINT ["make", "tests-integration"]

FROM tests-runner AS tests-runner-unit

ENTRYPOINT ["make", "tests-unit"]

FROM tests-runner AS psalm

ENTRYPOINT ["make", "psalm"]

FROM tests-runner AS php-cs-fixer

ENTRYPOINT ["make", "php-cs-fixer-check"]

FROM php-base-with-composer AS develop

RUN \
    --mount=type=cache,target=/var/cache/apk,sharing=locked \
    apk add --progress=no autoconf build-base linux-headers \
    && pecl install xdebug

USER $UID:$GID
