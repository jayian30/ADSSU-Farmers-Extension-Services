FROM php:8.2-apache

# Install PDO MySQL extension and other required extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Install zip and other useful extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite for clean URLs
RUN a2enmod rewrite

# Disable conflicting MPMs and ensure mpm_prefork is enabled for mod_php compatibility
RUN a2dismod mpm_event mpm_worker || true && a2enmod mpm_prefork

# Enable .htaccess support by allowing overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Set default port to 80 (in case PORT environment variable is not set)
ENV PORT=80

# Configure Apache to listen on the dynamic port provided by Railway ($PORT)
RUN sed -i 's/Listen 80/Listen ${PORT}/g' /etc/apache2/ports.conf && \
    sed -i 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g' /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html

# Copy all project files to the Apache web root
COPY . /var/www/html/

# Create uploads directory and set permissions
RUN mkdir -p /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 777 /var/www/html/uploads

# Expose port 80
EXPOSE 80
