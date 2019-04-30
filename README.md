# ZF2 Modules Site 

> ## UNMAINTAINED
>
> This repository is no longer maintained.

## Introduction

This site will eventually be a community site for publishing and sharing Zend Framework modules.

## Installation

### Main Setup

 * Clone this project `git clone git://github.com/zendframework/modules.zendframework.com.git`
 * Run `composer install` to initialize your vendors
 * [Create a new application in GitHub](https://github.com/settings/applications/new)
    * Main URL and CALLBACK url must be the same without any routing. e.g. http://modules.zendframework.com
 * Copy `config/autoload/github.local.php.dist` to `config/autoload/github.local.php` and enter the Id and Secret provided during the application registration on GitHub
 * Copy `config/autoload/database.local.php.dist` to `config/autoload/database.local.php` and enter your database credentials here
 * Copy `config/autoload/cache.local.php.dist` to `config/autoload/cache.local.php`. It's optional
 * Build the Database with ``php public/index.php migrations:migrate`` 

### Development Mode

To enable development mode:

 * Toggle the development mode flag: `php public/index.php development enable`
 * Copy `config/autoload/development.local.php.dist` to `config/autoload/development.local.php`
 * Copy `vendor/zendframework/zend-developer-tools/config/zenddevelopertools.local.php.dist` to `config/autoload/zenddevelopertools.local.php`
 
 Additional hint for GitHub Login:
 
  * Make sure you create a new [application token in GitHub](https://github.com/settings/applications/new) where the CALLBACK url fits with your local url e.g. http://modules.zendframework.dev

## Deployment

The master branch of this repository is manually deployed live to [zfmodules.com](https://zfmodules.com/) by [@GeeH](https://github.com/GeeH).

:bulb: After deployment, a tag is created, so the latest of the [releases](https://github.com/zendframework/modules.zendframework.com/releases)
represents what is deployed to production.
