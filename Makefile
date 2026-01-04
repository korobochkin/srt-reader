build:
	docker compose build --quiet

up:
	docker compose up --detach --no-build --quiet-pull --remove-orphans --timeout=120 --wait --yes

down:
	docker compose down --remove-orphans --volumes

code:
	./vendor/bin/php-cs-fixer \
		fix \
		--config="development/php-cs-fixer/php-cs-fixer.dist.php" \
		--cache-file="development/php-cs-fixer/php-cs-fixer.cache" \
		--format=txt \
		--no-interaction

vendor: composer.json composer.lock
	composer install

grammar/srt.php: grammar/srt.pp vendor
	php ./development/grammar-generator/grammar-generator.php

grammar: grammar/srt.php code

.PHONY: \
	build \
	up \
	down \
	code \
	grammar
