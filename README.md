# Larahex

Quickly sets up a new Laravel project with all the things I usually need. Among them:

* Replaces Blade with Twig as the templating engine
* Makes `./artisan` executable
* Removes Vue.js/Lodash scaffolding, but leaves Twitter Bootstrap on
* Injects authentication (`artisan make:auth`)
* Adds packages:
  * doctrine/dbal
  * litipk/php-bignumbers
* Adds [`app/helpers.php`](helpers/helpers.php) and [`app/precision_helpers.php`](helpers/precision_helpers.php)
* Performs `npm install` at the end

Take a look at [larahex.sh](larahex.sh) to see full functionality

## Usage

Usage: `./larahex.sh your_project_name`. In your current directory a folder with this name will be created, which will contain the project code.