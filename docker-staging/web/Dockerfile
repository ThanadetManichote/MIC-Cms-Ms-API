FROM eggdevop/docker-apache-php:php7-phalcon

WORKDIR /var/www/html

RUN mkdir -p /data/import/user
RUN chmod -R 755 /data

# ## Add apache config
ADD sites-enabled/vhost.conf /etc/apache2/sites-enabled/