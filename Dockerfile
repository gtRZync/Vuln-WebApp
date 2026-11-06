FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql \
    && a2enmod headers

# Apache permet aux fichiers .htaccess de protéger config, uploads et scripts.
RUN printf '<Directory /var/www/html>\n    AllowOverride All\n    Require all granted\n</Directory>\n' > /etc/apache2/conf-available/stageconnect.conf \
    && a2enconf stageconnect

COPY . /var/www/html/
RUN mkdir -p /var/www/html/uploads/cv \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 775 /var/www/html/uploads

EXPOSE 80
