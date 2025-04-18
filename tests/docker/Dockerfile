FROM php:8.0-cli

# Install Composer
RUN curl https://getcomposer.org/composer.phar > /usr/local/bin/composer && chmod a+x /usr/local/bin/composer

# Install zip ext
RUN apt-get update
RUN apt-get install -y libzip-dev
RUN pecl install zip
RUN docker-php-ext-enable zip

WORKDIR "/app"
