FROM php:8.4-cli-bookworm AS base

ARG UID
ARG GID

RUN addgroup --gid=$GID php \
    && adduser --uid=$UID --gid=$GID --shell=/bin/bash --no-create-home php

RUN apt-get update --quiet --assume-yes \
    && apt-get install --quiet --assume-yes --no-install-recommends --no-install-suggests unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer/composer:2.9-bin --chmod=0555 /composer /usr/bin/composer

WORKDIR /home/php/app

FROM base AS dependencies

RUN \
    --mount=type=bind,source=source,destination=./source \
    --mount=type=bind,source=tests,destination=./tests \
    --mount=type=bind,source=composer.json,destination=./composer.json \
    --mount=type=bind,source=composer.lock,destination=./composer.lock \
    --mount=type=bind,source=Makefile,destination=./Makefile \
    --mount=type=cache,id=composer,destination=/root/.composer \
    make vendor

FROM php:8.4-cli-bookworm AS tests-runner

WORKDIR /home/php/app

COPY --from=dependencies /home/php/app/vendor /home/php/app/vendor

FROM tests-runner AS tests-runner-integraion

ENTRYPOINT ["make", "tests-integration"]

FROM tests-runner AS tests-runner-unit

ENTRYPOINT ["make", "tests-unit"]

FROM base AS develop

RUN pecl install xdebug
