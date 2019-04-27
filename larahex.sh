#!/bin/bash

err() {
    echo "Error: $1"
    exit 1
}
warn() {
    echo "Warning: $1, continuing"
}
replacer() {
    php <<CODE
<?php
\$replace = preg_replace("$1", "$2", file_get_contents('$3')); file_put_contents('$3', \$replace);
CODE
    retVal=$?
    if [ $retVal -ne 0 ]; then
        echo "replacer() exited with non-zero code"
        echo "Arguments:"
        echo "1: $1"
        echo "2: $2"
        echo "3: $3"
        err "replacer() exited with non-zero code"
    fi
}

# Validation
[[ $# -eq 0 ]] && err "No arguments supplied"
[[ -z "$1" ]] && echo "No project name supplied"
PROJECTNAME="$1"
echo "Project Name: $PROJECTNAME"
[[ "${PROJECTNAME//[0-9A-Za-z_]/}" = "" ]] || err "Only alphanumeric characters and underscore allowed in a project name"

# Create empty laravel app
composer global update || err "Failed to global-update composer"
composer global require "laravel/installer" || err "Failed to install laravel/installer package"
composer create-project laravel/laravel $PROJECTNAME || err "Failed to create laravel/laravel project"

echo "Entering project directory $PROJECTNAME" && cd $PROJECTNAME
[[ $(pwd) =~ ${PROJECTNAME} ]] || err "Failed to enter into the ./$PROJECTNAME/ directory"

# Make artisan executable
chmod +x php ./artisan || warn "Failed to set +x on ./artisan"

# Install Twig
composer require rcrowe/twigbridge || err "Failed to install rcrowe/twigbridge package"

replacer "/(\* Package Service Providers...\n.*?\*\/\n)/" "\$1        TwigBridge\ServiceProvider::class,\n" config/app.php
replacer "/('Storage'\s*?=>\s*?Illuminate.Support.Facades.Storage::class,\s*?\n)/" "\$1        'Twig' => TwigBridge\\Facade\\Twig::class,\n" config/app.php
php artisan vendor:publish --provider="TwigBridge\ServiceProvider"
replacer "/('facades'\s*?=>\s*?\[\s*?\n)/" "\$1            'App', 'Auth', 'Route',\n" config/twigbridge.php
replacer "/('facades'\s*?=>\s*?\[)\s*?(],)/" "\$1'App', 'Auth', 'Route',\$2" config/twigbridge.php
replacer "/('functions'\s*?=>\s*?\[\n)/" "\$1            '__', 'app', 'str_replace',\n" config/twigbridge.php
replacer "/('strict_variables'\s*?=>)\s*?.*?,/" "\$1 true," config/twigbridge.php
replacer "/('autoescape'\s*?=>)\s*?.*?,/" "\$1 true," config/twigbridge.php

# Delete Vue.js/Lodash scaffolding
php artisan preset none || err "Failed to set artisan preset none"
php artisan preset bootstrap || err "Failed to set artisan preset bootstrap"

# Add authentication
php artisan make:auth || err "Failed to apply Laravel authentication scaffolding"

# Blade > Twig
wget https://raw.githubusercontent.com/uvarovserge/blade2twig/master/blade2twig.php || err "Could not download blade2twig.php"
php blade2twig.php || err "blade2twig.php failed"

# To make ->change() available in migrations
composer require doctrine/dbal || err "Failed to install doctrine/dbal package"

# Helpers
composer require litipk/php-bignumbers || err "Failed to install litipk/php-bignumbers package"
wget -O app/helpers.php https://raw.githubusercontent.com/uvarovserge/larahex/master/helpers/helpers.php || err "Could not download helpers.php"
wget -O app/precision_helpers.php https://raw.githubusercontent.com/uvarovserge/larahex/master/helpers/precision_helpers.php || err "Could not download precision_helpers.php"
replacer '/(\"autoload\"\: {\n)/' '\$1        \"files\": [\"app/helpers.php\", \"app/precision_helpers.php\"],\n' composer.json
composer dump-autoload || err "Something is wrong with composer"

# Install js packages
npm install

echo ""
echo "Your application is ready!"
echo ""

# TODO: should we do artisan publish everything?