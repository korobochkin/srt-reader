FROM php:8.5.7-cli-bookworm AS php-base

SHELL ["/bin/bash", "-euo", "pipefail", "-c"]

ARG UID
ARG GID

RUN addgroup --gid=$GID php \
    && adduser --uid=$UID --gid=$GID --shell=/bin/bash --comment "" --disabled-password php

USER $UID:$GID

WORKDIR /home/php/app

FROM php-base AS php-base-with-composer

USER root

RUN \
    --mount=type=cache,target=/var/cache/apt,sharing=locked \
    --mount=type=cache,target=/var/lib/apt/lists,sharing=locked \
    apt-get update --quiet --assume-yes \
    && apt-get install --quiet --assume-yes --no-install-recommends --no-install-suggests unzip

COPY --from=composer/composer:2.10-bin --chmod=0555 /composer /usr/bin/composer

FROM php-base-with-composer AS dependencies

USER $UID:$GID

RUN \
    --mount=type=bind,source=source,destination=./source \
    --mount=type=bind,source=tests,destination=./tests \
    --mount=type=bind,source=composer.json,destination=./composer.json \
    --mount=type=bind,source=composer.lock,destination=./composer.lock \
    --mount=type=bind,source=Makefile,destination=./Makefile \
    --mount=type=cache,id=composer,mode=0755,uid=$UID,gid=$GID,destination=/home/php/.composer \
    make vendor

FROM php-base AS tests-runner

COPY --from=dependencies /home/php/app/vendor /home/php/app/vendor

FROM tests-runner AS tests-runner-integration

ENTRYPOINT ["make", "tests-integration"]

FROM tests-runner AS tests-runner-unit

ENTRYPOINT ["make", "tests-unit"]

FROM tests-runner AS psalm

ENTRYPOINT ["make", "psalm"]

FROM tests-runner AS php-cs-fixer

ENTRYPOINT ["make", "php-cs-fixer-check"]

FROM php-base-with-composer AS develop

RUN pecl install xdebug

USER $UID:$GID
