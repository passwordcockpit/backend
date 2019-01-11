#!/bin/bash

# check volume
count=$(ls -f | wc -l)

# remove .DS_Store if is the only file
if [ $count -eq "3" ] && [ -a .DS_Store ]; then
    rm -rf .DS_Store
    echo >&2 ".DS_Store removed from $PWD"
elif [ $count -gt "2" ]; then
        echo >&2 "ERROR: $PWD is not empty! Passwrdcockpit will not be installed"
        exit 1
fi

# move passwordcockpit source in container
shopt -s dotglob
mv /usr/src/passwordcockpit/* ./
echo >&2 "Source copied in $PWD"

# configuration files
filename=config/autoload/db.local.php
if [ ! -e $filename ]; then
    {
        echo "<?php"
        echo "return ["
        echo "    'dbadapter' => ["
        echo "        'username' => '${PASSWORDCOCKPIT_BACKEND_DATABASE_USERNAME}',"
        echo "        'password' => '${PASSWORDCOCKPIT_BACKEND_DATABASE_PASSWORD}'," 
        echo "        'hostname' => '${PASSWORDCOCKPIT_BACKEND_DATABASE_HOSTNAME}',"
        echo "        'database' => '${PASSWORDCOCKPIT_BACKEND_DATABASE_DATABASE}'"
        echo "    ]"
        echo "];"
    } >> $filename

fi

filename=config/autoload/doctrine.local.php
if [ ! -e $filename ]; then
    {
        echo "<?php"
        echo "return ["
        echo "    'doctrine' => ["
        echo "        'connection' => ["
        echo "            'orm_default' => [" 
        echo "                'params' => ["
        echo "                    'url' =>"
        echo "                        'mysql://${PASSWORDCOCKPIT_BACKEND_DATABASE_USERNAME}:${PASSWORDCOCKPIT_BACKEND_DATABASE_PASSWORD}@${PASSWORDCOCKPIT_BACKEND_DATABASE_HOSTNAME}/${PASSWORDCOCKPIT_BACKEND_DATABASE_DATABASE}'"
        echo "                ]"
        echo "            ]"
        echo "        ]"
        echo "    ]"
        echo "];"
    } >> $filename

fi

filename=config/autoload/crypt.local.php
if [ ! -e $filename ]; then
    {
        echo "<?php"
        echo "return ["
        echo "    'block_cipher' => ["
        echo "        'key' => '${PASSWORDCOCKPIT_BACKEND_BLOCK_CIPHER_KEY}'"
        echo "    ]" 
        echo "];"
    } >> $filename

fi

filename=config/autoload/authentication.local.php
if [ ! -e $filename ]; then
    {
        echo "<?php"
        echo "return ["
        echo "    'authentication' => ["
        echo "        'secret_key' => '${PASSWORDCOCKPIT_BACKEND_AUTHENTICATION_SECRET_KEY}'"
        echo "    ]" 
        echo "];"
    } >> $filename

fi

filename=config/constants.local.php
if [ ! -e $filename ]; then
    {
        echo "<?php"
        echo "define('SWAGGER_API_HOST', '${PASSWORDCOCKPIT_BACKEND_SWAGGER_API_HOST}');"
    } >> $filename

fi
echo >&2 "Configuration files created"

# database schema
vendor/bin/doctrine orm:schema-tool:create
echo >&2 "DB schema created"

if [ "${PASSWORDCOCKPIT_BACKEND_DEVELOPMENTMODE}" -eq 1 ]; then
    # development mode
    vendor/bin/doctrine dbal:import database/create-tests-environment.sql
    echo >&2 "Test data installed"
else
    # production mode
    vendor/bin/doctrine dbal:import database/create-production-environment.sql.sql
    echo >&2 "Production data installed"

    # php configuration for the production
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
    echo >&2 "PHP configuration for the production installed"
fi

exec "$@"