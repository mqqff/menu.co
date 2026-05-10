FROM php:8.4-fpm AS builder

RUN apt-get update && apt-get upgrade -yqq && \
    apt-get install -yqq --no-install-recommends \
        apt-utils \
        build-essential \
        curl \
        wget \
        vim \
        git \
        ncdu \
        procps \
        unzip \
        ca-certificates \
        libsodium-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        pkg-config \
        zlib1g-dev \
        libzip-dev && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
        mbstring \
        sockets \
        opcache \
        exif \
        zip \
        intl \
        gd \
        pdo_mysql

RUN docker-php-source delete && \
    rm -rf /tmp/* /var/tmp/* /var/lib/apt/lists/*

WORKDIR /var/www

COPY . .

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist

FROM node:22-alpine AS frontend

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY resources ./resources
COPY public ./public
COPY vite.config.js ./

RUN npm run build

FROM php:8.4-fpm AS production

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    libfcgi-bin \
    procps \
    curl \
    libpng16-16 \
    libjpeg62-turbo \
    libfreetype6 \
    && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN curl -o /usr/local/bin/php-fpm-healthcheck \
    https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/master/php-fpm-healthcheck \
    && chmod +x /usr/local/bin/php-fpm-healthcheck

COPY ./docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

COPY ./storage /var/www/storage-init

COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/bin/docker-php-ext-* /usr/local/bin/

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN echo '[www]\npm.status_path = /status' > /usr/local/etc/php-fpm.d/status.conf

COPY --from=builder /var/www /var/www

WORKDIR /var/www

RUN chmod -R 777 storage/
RUN chmod -R 777 bootstrap/cache/
RUN chmod -R 777 database/

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

EXPOSE 9000
CMD ["php-fpm"]
