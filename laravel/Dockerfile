FROM php:8.1-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libzip-dev \
    zip \
    unzip \
    nginx \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql bcmath opcache

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Generate application key
RUN php artisan key:generate --force

# Run migrations and seed the database
RUN php artisan migrate --force --seed

# Expose port 8000 for Laravel development server
EXPOSE 8000

# Start Laravel development server
CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port", "8000"]