cs_dry_run:
	./vendor/bin/php-cs-fixer fix --verbose --dry-run src

test:
	./vendor/bin/phpunit --coverage-text