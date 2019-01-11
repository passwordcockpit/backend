FROM php:7.2-apache

# passwordcockpit env
ENV PASSWORDCOCKPIT_BACKEND_DEVELOPMENTMODE 0
ENV PASSWORDCOCKPIT_BACKEND_VERSION 0.3.0
ENV PASSWORDCOCKPIT_BACKEND_DATABASE_USERNAME root
ENV PASSWORDCOCKPIT_BACKEND_DATABASE_PASSWORD password
ENV PASSWORDCOCKPIT_BACKEND_DATABASE_HOSTNAME 127.0.0.1:3306
ENV PASSWORDCOCKPIT_BACKEND_DATABASE_DATABASE passwordcockpit 
ENV PASSWORDCOCKPIT_BACKEND_BLOCK_CIPHER_KEY cipher_key
ENV PASSWORDCOCKPIT_BACKEND_AUTHENTICATION_SECRET_KEY auth_secret_key
ENV PASSWORDCOCKPIT_BACKEND_SWAGGER_API_HOST 127.0.0.1:8080

# apache configurations
ENV APACHE_LOG_DIR /var/log/apache2/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# install
COPY getsource.sh /usr/local/bin/

RUN set -ex; \
    \
    apt-get update; \
    \
    # install permanent application
    apt-get install -y --no-install-recommends \
    ssl-cert \
    ; \
    \
    # install apache modules
    a2enmod rewrite ssl; \
    \
    # enable HTTPS in apache (need ssl-cert and apache module ssl)
    ln -s /etc/apache2/sites-available/default-ssl.conf /etc/apache2/sites-enabled/default-ssl.conf; \
    \
    savedAptMark="$(apt-mark showmanual)"; \
    \
    # install temporary application
    apt-get install -y --no-install-recommends \
    libldap2-dev \
    curl \
    ssh-client \
    git \
    ; \
    \
    docker-php-ext-configure ldap --with-libdir="lib/$debMultiarch"; \
    # install the PHP extensions we need
    docker-php-ext-install \
    ldap \
    opcache \
    pdo_mysql \
    ; \
    \
    # install composer
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer; \
    # download source password-cockpit
    /usr/local/bin/getsource.sh $PASSWORDCOCKPIT_BACKEND_DEVELOPMENTMODE; \
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

VOLUME /var/www/html
WORKDIR /var/www/html

COPY entrypoint.sh /usr/local/bin/
ENTRYPOINT ["entrypoint.sh"]

CMD ["apache2-foreground"]