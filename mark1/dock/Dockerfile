FROM php:7.2-apache
ENV PHP_IDE_CONFIG="serverName=localhost"

RUN a2enmod rewrite

# Install developer dependencies
RUN apt-get update -yqq && apt-get install -y git libsqlite3-dev nano libxml2-dev libicu-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev libcurl4-gnutls-dev libbz2-dev libssl-dev libzip-dev -yqq

RUN apt-get install -y libmcrypt-dev && \
pecl install mcrypt-1.0.1 && \
docker-php-ext-enable mcrypt

# Install xdebug for development
RUN    pecl install xdebug-2.7.0beta1\
    && docker-php-ext-enable xdebug

#Install other php extensions
RUN    docker-php-ext-install sockets \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install json \
    && docker-php-ext-install xml \
    && docker-php-ext-install zip \
    && docker-php-ext-install bz2 \
    && docker-php-ext-install curl

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/
RUN docker-php-ext-install gd

# Copy the configuration file into xdebug, if running phpinfo() you see the loaded file is not this one, change the path accordingly.
COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# install composer
RUN curl --silent --show-error https://getcomposer.org/installer | php

WORKDIR /var/www