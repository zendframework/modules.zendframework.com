.PHONY: composer cs

composer:
	composer validate
	composer install --prefer-dist

cs: composer
	vendor/bin/php-cs-fixer fix --config-file=.php_cs --diff --verbose
