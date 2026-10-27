FROM php:7.4-fpm-bullseye

# Bullseye is archived, so point apt to archive.debian.org
RUN sed -i 's|deb.debian.org|archive.debian.org|g; s|security.debian.org/debian-security|archive.debian.org/debian-security|g; /bullseye-updates/d' /etc/apt/sources.list \
    && echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99archive

# Install modules
RUN apt-get update; \
    apt-get install -y --no-install-recommends \
        zlib1g-dev \
        curl \
        wget \
        git \
        unzip \
        libpng-dev \
        libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# mbstring, pdo, tokenizer are already built into the php image
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install gd
RUN docker-php-ext-install pcntl
RUN docker-php-ext-install zip

# Install composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Install dockerize. Needed to make php container wait for services it depends on.
ENV DOCKERIZE_VERSION v0.3.0
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz

ENV TERM xterm

# Prevent permission issues when using shared volumes. Do not use in production!
RUN adduser www-data root