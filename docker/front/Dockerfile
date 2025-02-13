# Dockerfile pour Apache
FROM httpd:latest

ARG PUID=1000
ARG PGID=1000

RUN usermod -u ${PUID} www-data && groupmod -g ${PGID} www-data

# Activation du module proxy et proxy_fcgi pour la communication avec PHP-FPM
RUN sed -i '/LoadModule proxy_module/s/^#//g' /usr/local/apache2/conf/httpd.conf
RUN sed -i '/LoadModule proxy_fcgi_module/s/^#//g' /usr/local/apache2/conf/httpd.conf

USER root
# Ajout de la configuration pour le proxy vers PHP-FPM
RUN mkdir -p /usr/local/apache2/conf/extra
COPY ./docker/front/httpd-php.conf /usr/local/apache2/conf/extra/httpd-php.conf
RUN echo "Include /usr/local/apache2/conf/extra/httpd-php.conf" >> /usr/local/apache2/conf/httpd.conf
RUN echo "ServerName localhost" >> /usr/local/apache2/conf/httpd.conf

RUN chmod -R 777 /usr/local/apache2/logs

USER www-data
WORKDIR /var/www/html

EXPOSE 80
