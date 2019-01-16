#!/bin/bash
# $1 = PASSWORDCOCKPIT_BACKEND_DEVELOPMENTMODE
# $2 = PASSWORDCOCKPIT_BACKEND_VERSION

# clone te git repository, if the folder is not empty it not work
git clone -v git://github.com/passwordcockpit/backend.git /usr/src/passwordcockpit
cd /usr/src/passwordcockpit

if [ $1 -eq 1 ]; then
    echo >&2 "Development mode"

    git checkout develop
    git pull origin develop

    # swagger
    composer swagger
else
    echo >&2 "Production mode"

    # checkout the version of the application
    git checkout $2
    
    # remove al git files
    find . -name ".git*" -exec rm -R {} \;
    # clean application
    rm -rf tests
    rm -rf docs
fi

# composer install
composer install