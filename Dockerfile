FROM php:8.2-fpm # Or another PHP base image
RUN docker-php-ext-install pdo pdo_mysql # Install pdo and pdo_mysql extension