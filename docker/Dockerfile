FROM php:7.1-fpm

MAINTAINER Alessandro Minoccheri <alessandro.minoccheri@gmail.com>
LABEL Vendor="Alessandro Minoccheri"
LABEL Description="This is a new php-fpm image(version for now 7.1)"
LABEL Version="1.0.0"

ENV TIME_ZONE Europe/Rome

ENV HEALTHCHECK_INTERVAL_DURATION 40s
ENV HEALTHCHECK_TIMEOUT_DURATION 40s
ENV HEALTHCHECK_RETRIES 5

RUN apt-get update && apt-get install -y \
        zlib1g-dev \
        libmcrypt-dev \
        libicu-dev \
        git \
        unzip \
        mc \
        vim \
        wget \
    && docker-php-ext-install iconv \
    && docker-php-ext-install mcrypt \
    && docker-php-ext-install zip \
    && docker-php-ext-install bz2 \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install intl \
    && docker-php-ext-install pgsql pdo pdo_pgsql \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install opcache \
    && docker-php-ext-enable opcache

# Install GD
RUN apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
     && docker-php-ext-configure gd \
          --enable-gd-native-ttf \
          --with-freetype-dir=/usr/include/freetype2 \
          --with-png-dir=/usr/include \
          --with-jpeg-dir=/usr/include \
    && docker-php-ext-install gd \
    && docker-php-ext-enable gd


### Install Redis
#RUN echo "Install redis by pecl"
#RUN pecl install redis-3.1.0 \
#    && docker-php-ext-enable redis

# Change TimeZone
RUN echo "Set TIME_ZONE, by default - Europe/Rome"
RUN echo $TIME_ZONE > /etc/timezone

# Install composer globally
RUN echo "Install composer globally"

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

RUN printf "\n" | pecl install apcu-beta && echo extension=apcu.so > /usr/local/etc/php/conf.d/10-apcu.ini
RUN printf "\n" | pecl install apcu_bc-beta && echo extension=apc.so > /usr/local/etc/php/conf.d/apc.ini

RUN printf "\n" | pecl install channel://pecl.php.net/amqp-1.7.0alpha2 && echo extension=amqp.so > /usr/local/etc/php/conf.d/amqp.ini

RUN pecl install channel://pecl.php.net/ev-1.0.0RC3 && echo extension=ev.so > /usr/local/etc/php/conf.d/ev.ini

RUN ln -sf /dev/stdout /var/log/access.log && ln -sf /dev/stderr /var/log/error.log