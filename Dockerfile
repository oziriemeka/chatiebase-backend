FROM php:8.2.0-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    # Reqires install in dev server
    libfreetype6-dev \
    libwebp-dev \
    libjpeg62-turbo-dev \
    # End
    zip \
    unzip
# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-webp=/usr/include/  --with-jpeg=/usr/include/
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY ./docker-compose/app/php-fpm.conf /etc/php/8.2.0/fpm/php-fpm.conf
COPY ./docker-compose/app/php.ini /etc/php/8.2.0/fpm/conf.d/99-cruise.ini
COPY ./docker-compose/app/php-fpm.conf /etc/php/8.2.0/fpm/php-fpm.conf
COPY ./docker-compose/app/www.conf /etc/php/8.2.0/fpm/pool.d/www.conf
#Install composer package

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# RUN usermod -u 1000 www-data

# Set working directory
WORKDIR /var/www

USER $user

# Let supervisord start nginx & php-fpm
COPY ./bin/entrypoint.sh /usr/local/bin/entrypoint.sh
ENTRYPOINT ["sh", "/usr/local/bin/entrypoint.sh"]


