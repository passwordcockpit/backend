FROM --platform=linux/amd64 php:8.1-apache-bullseye

# passwordcockpit variables
ENV PASSWORDCOCKPIT_DATABASE_USERNAME root
ENV PASSWORDCOCKPIT_DATABASE_PASSWORD password
ENV PASSWORDCOCKPIT_DATABASE_HOSTNAME passwordcockpit_database
ENV PASSWORDCOCKPIT_DATABASE_DATABASE passwordcockpit 
ENV PASSWORDCOCKPIT_BLOCK_CIPHER_KEY supersecretkey
ENV PASSWORDCOCKPIT_AUTHENTICATION_SECRET_KEY authenticationsecretkey
ENV PASSWORDCOCKPIT_SWAGGER_API_HOST http://[YOUR_IP]:4200
ENV PASSWORDCOCKPIT_CLIENT_ADDRESS http://[YOUR_IP]:4200
ENV PASSWORDCOCKPIT_AUTHENTICATION_TYPE db
# ldap or db
# ldap configuration
ENV PASSWORDCOCKPIT_LDAP_HOST ldap.domain.com
ENV PASSWORDCOCKPIT_LDAP_PORT 389
ENV PASSWORDCOCKPIT_LDAP_USERNAME uid=username,cn=users,dc=domain,dc=com
ENV PASSWORDCOCKPIT_LDAP_PASSWORD password
ENV PASSWORDCOCKPIT_LDAP_BASEDN cn=users,dc=domain,dc=com
ENV PASSWORDCOCKPIT_LDAP_ACCOUNTFILTERFORMAT (&(memberOf=cn=groups,cn=users,dc=domain,dc=com)(uid=%s))
ENV PASSWORDCOCKPIT_LDAP_BINDREQUIRESDN 'true'
# true or false

WORKDIR /usr/src/passwordcockpit

RUN set -ex; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
    git \
    ssl-cert
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN set -ex; \
    apt-get update; \
    # zip
    apt-get install -y libzip-dev; \
    docker-php-ext-install zip; \
    # ldap
    apt-get install -y libldap2-dev; \
    docker-php-ext-install ldap;\
    # intl
    apt-get install -y --no-install-recommends libicu-dev; \
    docker-php-ext-install intl; \
    # opcache
    docker-php-ext-install opcache; \
    # pdo_mysql
    docker-php-ext-install pdo_mysql; \
    # mod_rewrite extension
    a2enmod rewrite; \
    # ssl extension
    a2enmod ssl
    
# set public like webroot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# clone the source of the backend
RUN set -ex; \
git clone -v git://github.com/passwordcockpit/backend.git .; \
git checkout develop; \
git pull origin develop

# install and build
RUN composer install

# fix timezone
RUN rm -rf /etc/localtime \
 && ln -s /usr/share/zoneinfo/Europe/Berlin /etc/localtime

# generate swagger documentation
# create constants.local.php
RUN { \
	echo "<?php"; \
    echo "define('SWAGGER_API_HOST', 'PASSWORDCOCKPIT_BASEHOST');"; \
} > config/constants.local.php
RUN composer swagger

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# enable development mode
RUN composer development-enable

# copy apache ssl configuration
RUN cp /etc/apache2/sites-available/default-ssl.conf /etc/apache2/sites-enabled/default-ssl.conf

VOLUME /var/www/html
WORKDIR /var/www/html

COPY entrypoint.sh /usr/local/bin/
ENTRYPOINT ["entrypoint.sh"]

CMD ["apache2-foreground"]