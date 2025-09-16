# Dockerfile pour Apache
FROM httpd:2.4

# Activer proxy/proxy_fcgi/rewrite + créer le dossier extra
RUN set -e; \
  sed -i 's/^#LoadModule proxy_module/LoadModule proxy_module/' /usr/local/apache2/conf/httpd.conf && \
  sed -i 's/^#LoadModule proxy_fcgi_module/LoadModule proxy_fcgi_module/' /usr/local/apache2/conf/httpd.conf && \
  sed -i 's/^#LoadModule rewrite_module/LoadModule rewrite_module/' /usr/local/apache2/conf/httpd.conf && \
  mkdir -p /usr/local/apache2/conf/extra

COPY ./docker/front/httpd-php.conf /usr/local/apache2/conf/extra/httpd-php.conf

RUN echo "Include /usr/local/apache2/conf/extra/httpd-php.conf" >> /usr/local/apache2/conf/httpd.conf && \
    echo "ServerName localhost" >> /usr/local/apache2/conf/httpd.conf

WORKDIR /var/www

EXPOSE 80
