FROM php:7.4-fpm

WORKDIR /var/www

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
    supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl gd

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application and websockets
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Make the supervisor directories
RUN mkdir -p /var/log/supervisor
RUN mkdir -p /etc/supervisor
RUN chown www:www /var/log/supervisor

# Copy supervisor scripts
COPY ./etc/supervisor /etc/supervisor/

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["/usr/bin/supervisord"]
