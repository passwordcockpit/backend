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

# install php extensions
RUN set -ex; \
    \
    savedAptMark="$(apt-mark showmanual)"; \
    \
    apt-get update; \
    # opcache
    docker-php-ext-install opcache; \
    # mod_rewrite extension
    a2enmod rewrite; \
    # zip
    apt-get install -y libzip-dev; \
    docker-php-ext-install zip; \
    # intl
    apt-get install -y --no-install-recommends libicu-dev; \
    docker-php-ext-install intl; \
    # ldap
    apt-get install -y libldap2-dev; \
    docker-php-ext-install ldap;\
    # pdo_mysql
    docker-php-ext-install pdo_mysql; \
    # install composer
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer; \
    # reset apt-mark's "manual" list so that "purge --auto-remove" will remove all build dependencies
    apt-mark auto '.*' > /dev/null; \
    apt-mark manual $savedAptMark; \
    ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
    | awk '/=>/ { print $3 }' \
    | sort -u \
    | xargs -r dpkg-query -S \
    | cut -d: -f1 \
    | sort -u \
    | xargs -rt apt-mark manual; \
    \
    apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
    rm -rf /var/lib/apt/lists/*

RUN set -ex; \
    apt-get update; \
    apt-get install -y \
    git

# clone the source of the backend
RUN set -ex; \
mkdir /usr/src/passwordcockpit; \
cd /usr/src/passwordcockpit; \
git clone -v https://github.com/passwordcockpit/backend.git /usr/src/passwordcockpit; \
git checkout 1.3.0-dev; \
git pull origin 1.3.0-dev

WORKDIR /usr/src/passwordcockpit

# install and build
RUN composer install

# generate swagger documentation
# create constants.local.php
RUN { \
	echo "<?php"; \
    echo "define('SWAGGER_API_HOST', 'PASSWORDCOCKPIT_BASEHOST');"; \
} > config/constants.local.php
ENV COMPOSER_ALLOW_SUPERUSER 1 
RUN composer swagger

# apache configurations
# set log dir
ENV APACHE_LOG_DIR /var/log/apache2/
# set public like webroot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
# set localtime
RUN rm -rf /etc/localtime \
    && ln -s /usr/share/zoneinfo/Europe/Berlin /etc/localtime
# Hide Apache Version and Linux OS From HTTP Headers
RUN sed -ri -e 's!ServerTokens OS!ServerTokens Prod!g' /etc/apache2/conf-enabled/security.conf
RUN sed -ri -e 's!ServerSignature On!ServerSignature Off!g' /etc/apache2/conf-enabled/security.conf

# PHP configurations
# set development configuration
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# global PHP configuration changes
RUN sed -i \
    -e 's~^;date.timezone =$~date.timezone = Europe/Zurich~g' \
    -e 's~^display_errors = Off$~display_errors = On~g' \
    -e 's~^display_startup_errors = Off$~display_startup_errors = On~g' \
    -e 's~^memory_limit = .*$~memory_limit = 256M~g' \
    -e 's~^post_max_size = .*$~post_max_size = 128M~g' \
    -e 's~^max_execution_time = .*$~max_execution_time = 60~g' \
    /usr/local/etc/php/php.ini

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

VOLUME /var/www/html
WORKDIR /var/www/html

# copy entrypoint script
COPY entrypoint.sh /usr/local/bin/
ENTRYPOINT ["entrypoint.sh"]

CMD ["apache2-foreground"]