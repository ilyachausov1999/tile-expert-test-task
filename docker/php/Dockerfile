FROM php:8.2-fpm

RUN apt-get update \
    && apt-get install -y \
        git \
        curl \
        dpkg-dev \
        libpng-dev \
        libjpeg-dev \
        libonig-dev \
        libxml2-dev \
        libpq-dev \
        libzip-dev \
        zip \
        unzip \
        libmagickwand-dev \
        libssh2-1-dev \
        cron \
        default-mysql-client \
        libicu-dev \
        libcurl4-openssl-dev

RUN pecl install ssh2-1.3.1
RUN docker-php-ext-enable ssh2

RUN docker-php-ext-configure gd \
  --enable-gd \
  --with-jpeg

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    sockets \
    zip \
    intl \
    curl \
    soap

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY --from=ghcr.io/symfony-cli/symfony-cli:latest /usr/local/bin/symfony /usr/local/bin/symfony

RUN chmod +x /usr/local/bin/symfony

mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

RUN mkdir -p /var/www/html/var/cache /var/www/html/var/log \
    && chown -R www-data:www-data /var/www/html/var \
    && chmod -R 775 /var/www/html/var

WORKDIR /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
