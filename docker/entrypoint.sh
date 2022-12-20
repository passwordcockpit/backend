#!/bin/bash

##############################################
# Check volume
##############################################
count=$(ls -f | wc -l)

# remove .DS_Store if is the only file
if [ $count -eq "3" ] && [ -a .DS_Store ]; then
    rm -rf .DS_Store
    echo >&2 ".DS_Store removed from $PWD"
    count=$((count - 1))
fi

# install passwordcockpit only if the volume is empty
if [ $count -gt "2" ]; then
        echo -e "\e[31mWARNING: $PWD is not empty! Passwordcockpit will not be installed\e[0m"
else
    # move passwordcockpit source in container
    shopt -s dotglob
    mv /usr/src/passwordcockpit/* ./
    echo -e "\e[32mSource copied in $PWD\e[0m"

    ##############################################
    # Configuration files
    ##############################################
    mv config/autoload/db.local.php.dist config/autoload/db.local.php
    mv config/autoload/client.local.php.dist config/autoload/client.local.php
    mv config/autoload/doctrine.local.php.dist config/autoload/doctrine.local.php
    mv config/autoload/crypt.local.php.dist config/autoload/crypt.local.php
    mv config/autoload/authentication.local.php.dist config/autoload/authentication.local.php

    if [ "${PASSWORDCOCKPIT_AUTHENTICATION_TYPE}" == "ldap" ]; then
        mv config/autoload/ldap.local.php.dist config/autoload/ldap.local.php
    fi

    mv config/constants.local.php.dist config/constants.local.php
    
    sed -ri -e 's!PASSWORDCOCKPIT_BASEHOST!'${PASSWORDCOCKPIT_SWAGGER_API_HOST}'!g' swagger/swagger.json
    
    echo -e "\e[32mConfiguration files created and modified\e[0m"

    ##############################################
    # Database
    ##############################################
    echo -e "\e[31mCheck database connection\e[0m"
    max_retries=10
    try=0

    while [ "$try" -lt "$max_retries" ]
    do
        connection=$(vendor/bin/doctrine dbal:run-sql "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '${PASSWORDCOCKPIT_DATABASE_DATABASE}'")
        # schema_name is unset or set to the empty string so connecting problem
        if [ -z "${connection}" ]; then
            echo -e "\e[31mRetrying connection...\e[0m"
            try=$((try+1))
            sleep 3s
            continue
        fi
        schema_exist=$(echo $connection | grep ${PASSWORDCOCKPIT_DATABASE_DATABASE} -c)
        # connection ok and schema exist
        if [ "$schema_exist" == "1" ]; then
            echo -e "\e[32mConnection ok\e[0m"
            echo -e "\e[32mSchema already exist\e[0m"
            # Tables exists
            number_of_tables=$(vendor/bin/doctrine dbal:run-sql "SELECT count(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '${PASSWORDCOCKPIT_DATABASE_DATABASE}'" | tr -d -c 0-9)
            if [ "$number_of_tables" == "0" ]; then
                # Create the tables and popolate it
                vendor/bin/doctrine orm:schema-tool:create
                vendor/bin/doctrine orm:generate-proxies
                echo -e "\e[32mDatabase created\e[0m"
                sql=$(cat database/create-tests-environment.sql | sed '/^--/d')
                vendor/bin/doctrine dbal:run-sql "$sql"
                echo -e "\e[32mTest data installed\e[0m"
            else
                # Update scripts
                echo -e "\e[32mNo updates to be carried out\e[0m"
            fi
            break
        fi
        # connection ok and schema not exist: error
        if [ "$schema_exist" == "0" ]; then
            echo -e "\e[32mConnection ok\e[0m"
            echo -e "\e[31mSchema not exist\e[0m"
            echo -e "\e[31mInstalling failed!\e[0m"
            exit 1
            break
        fi
    done
    # Connection error
    if [ "$try" -gt "$max_retries" ]; then
        echo -e "\e[31mInstalling failed!\e[0m"
        exit 1
    fi

    echo -e "\e[32mPasswordcockpit ready\e[0m"
    
fi

exec "$@"
