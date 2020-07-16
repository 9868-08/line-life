FROM php:5.6-apache
RUN a2enmod rewrite



# disable interactive functions
ENV DEBIAN_FRONTEND noninteractive

#RUN apt-get update && \
#apt-get install php7.2-mysql -y
#RUN rm -rf /var/lib/apt/lists/* && \
#cd /tmp && curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

RUN apt-get update                                                                                                                                                              
RUN docker-php-ext-install pdo pdo_mysql mysqli                                                                                                                                 
RUN docker-php-ext-enable pdo pdo_mysql mysqli  

