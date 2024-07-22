# Use the official PHP with Apache image
FROM php:8.2-apache

# Install dependencies and mysql client for auto create db and execute migrations
RUN apt-get update && apt-get install -y \
    libzip-dev \
    default-mysql-client \
    && docker-php-ext-install zip pdo_mysql

# Define variables
ENV DB_HOST=db
ENV DB_USER=root
ENV MYSQL_ROOT_PASSWORD=password

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/html

# Copy the Symfony application code into the container
COPY . /var/www/html

RUN composer install --no-scripts --no-autoloader

RUN pecl install xdebug

RUN docker-php-ext-enable xdebug

# Config Xdebug
RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN composer dump-autoload --optimize

RUN a2enmod rewrite

COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf

COPY .env.test /var/www/html/.env

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

#Needed cause it caused bug cause windows format
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Expose port 80 to serve Symfony application with Apache
EXPOSE 80

# Default command to run Apache in foreground
CMD ["apache2-foreground"]
