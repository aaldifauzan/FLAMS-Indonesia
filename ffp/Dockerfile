FROM php:8.3-fpm

# Set your user name, e.g., user=carlos
ARG user=yourusername
ARG uid=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd sockets

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Install redis (specify stable version)
RUN pecl install -o -f redis-5.3.7 \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

# Set working directory
WORKDIR /app

# Copy the Laravel application files to the container
COPY . /app

# Set ownership and permissions for Laravel
RUN chown -R $user:www-data /app/storage /app/bootstrap/cache

# Copy custom PHP configurations
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

USER $user

EXPOSE 80

CMD ["php", "artisan", "serve", "--host=127.0.0.2", "--port=80"]
