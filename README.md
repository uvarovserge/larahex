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
* Creates `app/Models` directory and moves `User` model there
* Adds `User::this()` as an alias for `Auth::user()`
* Performs `npm install` at the end

Take a look at [larahex](larahex) to see full functionality

## Usage

Install it with: `composer global require uvarovserge/larahex`

Usage: `larahex your_project_name`. In your current directory a folder with this name will be created, which will contain the project code.

Upgrade to the latest version with: `composer global update`