FROM php:8.2-fpm

WORKDIR /var/www/ukcp

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    supervisor \
    nginx \
    npm \
    libicu-dev \
    default-mysql-client

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl gd intl

# Setup PHP ini things for CLI
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY ./usr/local/etc/php/conf.d/local.ini /usr/local/etc/php/conf.d/local.ini

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Upgrade node to latest version and install things
RUN npm install -g yarn
RUN apt-get remove npm -y
RUN yarn global add n
RUN n latest
RUN yarn global add cross-env

# Add user for laravel application and websockets
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Setup nginx directories
RUN mkdir -p /var/log/nginx
RUN touch /var/log/nginx/access.log
RUN touch /var/log/nginx/error.log
RUN chown www:www /var/log/nginx
RUN chown www:www /var/log/nginx/*

RUN mkdir -p /var/lib/nginx
RUN chown www:www /var/lib/nginx

RUN mkdir -p /run
RUN touch /run/nginx.pid
RUN chown www:www /run/nginx.pid

# Make the supervisor directories
RUN mkdir -p /var/log/supervisor
RUN chown www:www /var/log/supervisor
RUN mkdir -p /etc/supervisor
RUN mkdir -p /run/supervisor
RUN chown www:www /run/supervisor

# Copy supervisor scripts
COPY ./etc/supervisor /etc/supervisor/

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["/usr/bin/supervisord"]
