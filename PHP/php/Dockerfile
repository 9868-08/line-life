FROM php:7.4-fpm
RUN apt-get update
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN docker-php-ext-enable pdo pdo_mysql mysqli
