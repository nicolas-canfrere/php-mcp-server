FROM php:8.4.12-fpm-alpine3.22 AS base-platform

# Set timezone
ENV TZ=UTC
RUN apk add --update --no-cache tzdata
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ >/etc/timezone
RUN printf '[PHP]\ndate.timezone = "%s"\n', $TZ >/usr/local/etc/php/conf.d/tzone.ini

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
    intl \
    pdo_pgsql \
    opcache

ENV LOG_STREAM="php://stdout"

# Disabling fpm access logs since we already have them with nginx
RUN sed -i '/access.log/s/^/; /' /usr/local/etc/php-fpm.d/docker.conf

WORKDIR /app

## ==============================================
FROM base-platform AS dev
## ==============================================

RUN install-php-extensions xdebug-3.4.3

ENV APP_ENV=dev
