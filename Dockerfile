# Use the official PHP 8.2 Apache image as the base
FROM php:8.2-apache

# Install PDO and pdo_mysql extensions
# The docker-php-ext-install command handles installation and enabling
RUN docker-php-ext-install pdo pdo_mysql

# Optionally, install other extensions if needed (e.g., mysqli)
# RUN docker-php-ext-install mysqli

# Copy your application code into the container (assuming it's in a 'src' directory)
# COPY ./src /var/www/html/

# Expose port 80 (default for Apache)
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]