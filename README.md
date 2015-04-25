# ZF2 Modules Site [![Build Status](https://travis-ci.org/zendframework/modules.zendframework.com.svg?branch=master)](https://travis-ci.org/zendframework/modules.zendframework.com) [![Dependency Status](https://www.versioneye.com/user/projects/54885d5a746eb514b0000279/badge.svg?style=flat)](https://www.versioneye.com/user/projects/54885d5a746eb514b0000279) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/zendframework/modules.zendframework.com/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/zendframework/modules.zendframework.com/?branch=develop) [![Build Status](https://scrutinizer-ci.com/g/zendframework/modules.zendframework.com/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/zendframework/modules.zendframework.com/build-status/develop)

## Introduction

This site will eventually be a community site for publishing and sharing Zend Framework modules.

##Installation

###Main Setup

 * Clone this project `git clone git://github.com/zendframework/modules.zendframework.com.git`
 * Run `composer install` to initialize your vendors
 * Import `data/0.sql` into your database
 * [Create a new application in GitHub](https://github.com/settings/applications/new)
    * Main URL and CALLBACK url must be the same without any routing. e.g. http://modules.zendframework.com
 * Copy `config/autoload/github.local.php.dist` to `config/autoload/github.local.php` and enter the Id and Secret provided during the application registration on GitHub
 * Copy `config/autoload/database.local.php.dist` to `config/autoload/database.local.php` and enter your database credentials here
 * Copy `config/autoload/cache.local.php.dist` to `config/autoload/cache.local.php`. It's optional

###Vagrant Setup

 * Clone this project: `git clone git://github.com/zendframework/modules.zendframework.com.git`
 * [Create a new application in GitHub](https://github.com/settings/applications/new)
 * Main URL and CALLBACK url must be the same without any routing. e.g. http://modules.zendframework.com
 * Remove the `.dist` suffix from the files in `config/autoload` and edit them according to your own credentials
    - The database information for vagrant is:
    - Username: `modules`
    - Database name: `modules`
    - Password: `modules`
 * Run `vagrant up` (You will need [Vagrant](http://www.vagrantup.com/))
 * Add an entry in your hosts (`/etc/hosts` or `C:\Windows\system32\drivers\etc\hosts`):
    - `192.168.56.101 modules.zendframework.dev`
 * Browse to http://modules.zendframework.dev/

### Development Mode

To enable development mode:

 * Toggle the development mode flag: `php public/index.php development enable`
 * Copy `config/autoload/development.local.php.dist` to `config/autoload/development.local.php`
 * Copy `vendor/zendframework/zend-developer-tools/config/zenddevelopertools.local.php.dist` to `config/autoload/zenddevelopertools.local.php`

## Deployment

The master branch of this repository is manually deployed live to [modules.zendframework.com](http://modules.zendframework.com/) by [@GeeH](https://github.com/GeeH).

:bulb: After deployment, a tag is created, so the latest of the [releases](https://github.com/zendframework/modules.zendframework.com/releases)
represents what is deployed to production.
