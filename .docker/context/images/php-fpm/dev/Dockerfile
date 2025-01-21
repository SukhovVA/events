FROM php:8.3-fpm

RUN apt-get update \
    && apt-get install -y curl git libicu-dev libmagickwand-dev libmemcached-dev \
    libpng-dev libpq-dev libssh-dev libzip-dev pv unzip zip \
    && pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install intl zip opcache pdo pdo_pgsql pgsql bcmath gd soap \
    && pecl install memcached \
    && echo extension=memcached.so >> /usr/local/etc/php/conf.d/memcached.ini \
    && pecl install apcu \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./conf/php-fpm/dev/php.ini /usr/local/etc/php/php.ini
COPY ./conf/php-fpm/dev/remote-xdebug.ini /usr/local/etc/php/conf.d/remote-xdebug.ini
COPY ./conf/php-fpm/dev/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY ./conf/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

### timezone ###
ARG TZ="Europe/Moscow"
RUN ln -snf "/usr/share/zoneinfo/$TZ" /etc/localtime && echo "$TZ" > /etc/timezone \
    && echo "\ndate.timezone = $TZ" >> /usr/local/etc/php/php.ini \
    && mkdir -p /var/www/var/cache /var/www/var/log && chmod -R 777 /var/www/var
