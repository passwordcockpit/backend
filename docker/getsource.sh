#!/bin/bash
# $1 = PASSWORDCOCKPIT_BACKEND_DEVELOPMENTMODE
# $2 = PASSWORDCOCKPIT_BACKEND_VERSION

# clone te git repository, if the folder is not empty it not work
git clone -v git://github.com/password-cockpit/backend.git /usr/src/password-cockpit
cd /usr/src/password-cockpit

if [ $1 -eq 1 ]; then
    echo >&2 "Development mode"

    git checkout develop
    git pull origin develop

    # swagger
    composer swagger
    ln -s docs/swagger public/swagger
else
    echo >&2 "Production mode"

    # checkout the version of the application
    git checkout $1
    
    # remove al git files
    find . -name ".git*" -exec rm -R {} \;
    # clean application
    rm -rf tests
    rm -rf docs
fi

# composer install
composer install