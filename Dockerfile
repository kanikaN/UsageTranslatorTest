FROM php:7.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip
# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# Create system user to run Composer and Artisan Commands.
RUN mkdir -p /home/phpuser/.composer && \
    useradd -G www-data -u 1001 -d /home/phpuser phpuser && \
    chown -R phpuser:phpuser /home/phpuser

# Install the application.
WORKDIR /tmp
RUN git clone https://github.com/kanikaN/UsageTranslatorTest.git && \
    cp -R /tmp/UsageTranslatorTest/* /var/www/html && \
    rm -rf /tmp/*

WORKDIR /var/www/html
RUN composer install
RUN php artisan migrate:install && \
    php artisan migrate:fresh

EXPOSE 8080
USER phpuser

ENTRYPOINT ["php", "-S", "app:8080", "-t", "public"]
