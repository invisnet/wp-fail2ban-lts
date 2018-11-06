.PHONY: default filters test release

default:
	@echo "release, filters, test"

release: test filters


filters:
	@php bin/filters.php
	@cp filters.d/*.conf docs/autogen/

test:
	@vendor/phpunit/phpunit/phpunit --coverage-html=test/output --coverage-text=docs/autogen/phpunit.txt --whitelist wp-fail2ban.php --bootstrap test/bootstrap.php --process-isolation test


