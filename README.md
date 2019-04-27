# Larahex

Quickly sets up a new Laravel project with all the things I usually need. Among them:

* Replaces Blade with Twig as the templating engine
* Makes `./artisan` executable
* Removes Vue.js scaffolding
* Injects authentication (`artisan make:auth`)
* Adds packages:
  * doctrine/dbal
  * litipk/php-bignumbers
* Adds `app/helpers.php` and `app/precision_helpers.php`
* Performs `npm install` at the end

Take a look at [larahex.sh](larahex.sh) to see the full functionality
