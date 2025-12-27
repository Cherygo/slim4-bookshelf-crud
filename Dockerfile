FROM php:8.4-cli


RUN apt-get update && apt-get install -y libzip-dev libpq-dev default-mysql-client
RUN docker-php-ext-install zip pdo pdo_mysql

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

WORKDIR /app

COPY . .

RUN composer install

CMD ["bash", "-c", "mysql -h db -u root -prootpassword Bookshelf < dbDump.sql && make start"]