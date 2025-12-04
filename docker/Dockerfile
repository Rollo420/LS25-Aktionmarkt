FROM php:8.4-cli as composer
ARG DEBIAN_FRONTEND=noninteractive
RUN apt-get update \
	&& apt-get install -y --no-install-recommends \
	   git curl unzip zip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
	   libicu-dev libonig-dev libxml2-dev libcurl4-openssl-dev pkg-config ca-certificates \
	&& rm -rf /var/lib/apt/lists/*

# Install common PHP extensions required by Laravel and dependencies
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j"$(nproc)" gd mbstring xml zip bcmath pcntl sockets curl intl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
	&& composer --version

RUN git config --global --add safe.directory /var/www/html

RUN mkdir /var/www/html -p 

COPY ../ /var/www/html/

WORKDIR /var/www/html
# Run composer install within a PHP 8.4 environment so lockfile platform checks pass
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-interaction --prefer-dist --optimize-autoloader
RUN composer require mistic100/randomcolor --no-interaction --with-all-dependencies || true
RUN composer require tightenco/parental --no-interaction --with-all-dependencies || true
RUN composer require laravel/scout --no-interaction --with-all-dependencies || true
RUN composer require meilisearch/meilisearch-php http-interop/http-factory-guzzle --no-interaction --with-all-dependencies || true
RUN composer require spatie/laravel-permission --no-interaction --with-all-dependencies

FROM node:latest as node

WORKDIR /app
COPY ../package.json /app/package.json
COPY ../package-lock.json /app/package-lock.json

RUN npm install

COPY ../docker/copy-vendor-moduls.sh /app/copy-vendor-moduls.sh
#RUN chmod +x /app/copy-vendor-moduls.sh
RUN chmod -R 777 /app/copy-vendor-moduls.sh
COPY --from=composer /var/www/html/vendor /app/vendor

#RUN chmod -R 777 /var/www/html/node_modules

ENTRYPOINT [ "/app/copy-vendor-moduls.sh" ]