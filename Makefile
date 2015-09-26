.PHONY: composer cs

composer:
	composer validate
	composer install --prefer-dist

cs: composer
	vendor/bin/php-cs-fixer fix --config-file=.php_cs --diff --verbose

test: composer
	cp config/autoload/travis.php.local.dist config/autoload/travis.local.php
	mysql -uroot -e 'DROP DATABASE IF EXISTS modules_test;'
	mysql -uroot -e 'CREATE DATABASE modules_test;'
	php public/index.php migrations:migrate --no-interaction --verbose
	vendor/bin/phpunit --configuration phpunit.xml
	rm config/autoload/travis.local.php
