# Use the official PHP 8.2 image with Apache
FROM php:8.2-apache

# Replace sh with bash so we can source files
RUN rm /bin/sh && ln -s /bin/bash /bin/sh

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libsqlite3-dev

# Enable mod_rewrite for Laravel's routing
RUN a2enmod rewrite

# Install PHP extensions (including SQLite since you're using SQLite)
RUN docker-php-ext-install pdo_mysql pdo_sqlite zip

# Set Apache Document Root to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# NVM and Node.js Installation
ENV NVM_DIR /usr/local/nvm
ENV NODE_VERSION 20
RUN mkdir -p $NVM_DIR

# Install NVM (Node Version Manager) and Node.js
RUN curl --silent -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.0/install.sh | bash \
    && . "$NVM_DIR/nvm.sh" \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default

# Add Node.js and npm to PATH
ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

# Verify Node.js and npm installation in the same RUN command where nvm is sourced
RUN bash -c "source $NVM_DIR/nvm.sh && node -v && npm -v"

# Set the working directory to the application root
WORKDIR /var/www/html

# Copy the application code into the container
COPY . /var/www/html

# Ensure the SQLite database file exists
RUN touch /var/www/html/database/database.sqlite

# Install Composer globally from the official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP project dependencies with Composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Generate Laravel application key
RUN php artisan key:generate

# Install Node.js dependencies and build frontend assets
RUN bash -c "source $NVM_DIR/nvm.sh && npm install"

# Set permissions for Laravel storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Expose port 80 for the Apache server
EXPOSE 80

# Start Apache server in the foreground
CMD ["apache2-foreground"]
