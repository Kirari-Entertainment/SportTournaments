FROM php:8.2-fpm

# Installing dependencies for building the PHP modules
RUN apt-get update && \
    apt-get install -y zip libzip-dev libpng-dev libicu-dev libxml2-dev dos2unix

# Installing additional PHP modules
RUN docker-php-ext-install mysqli pdo pdo_mysql gd zip intl xml

# Cleaning APT cache
RUN apt clean

# Setting up a CLI interface
COPY CLIApp.sh /usr/bin/app
RUN dos2unix /usr/bin/app
RUN chmod +x /usr/bin/app

# Install dependencies required for GD with full feature support
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j$(nproc) gd