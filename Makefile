SHELL := /bin/bash

build:
	docker compose --file development/docker-compose.yml build --provenance=false --sbom=false

bake-tests:
	docker buildx bake --file=development/tests-docker-bake.json

up:
	docker compose --file=development/docker-compose.yml up --detach --no-build --quiet-pull --remove-orphans --timeout=120 --wait --yes

up-tests:
	docker compose --file=development/tests-docker-compose.yml up --detach --no-build --quiet-pull --remove-orphans --timeout=120 --wait --yes

exec-tests-vendor:
	docker compose --file=development/tests-docker-compose.yml exec tests-runner make vendor

exec-tests-integration:
	docker compose --file=development/tests-docker-compose.yml exec tests-runner make tests-integration

down:
	docker compose --file=development/docker-compose.yml down --remove-orphans --volumes

down-tests:
	docker compose --file=development/tests-docker-compose.yml down --remove-orphans --volumes

code:
	./vendor/bin/php-cs-fixer \
		fix \
		--config="development/php-cs-fixer/php-cs-fixer.dist.php" \
		--cache-file="development/php-cs-fixer/php-cs-fixer.cache" \
		--format=txt \
		--no-interaction

php-cs-fixer-check:
	./vendor/bin/php-cs-fixer \
		check \
		--config="development/php-cs-fixer/php-cs-fixer.dist.php" \
		--cache-file="development/php-cs-fixer/php-cs-fixer.cache" \
		--format=@auto \
		--no-interaction

php-syntax:
	find . -type f -name "*.php" \( -path "./source/*" -o -path "./grammar/*" \) -print0 \
	| xargs --null --verbose --max-procs=4 --max-args=1 php --syntax-check

psalm:
	./vendor/bin/psalm --config="development/psalm/psalm.xml"

psalm-alter:
	./vendor/bin/psalm --config="development/psalm/psalm.xml" --alter --issues=all

vendor: composer.json composer.lock
	composer install --no-scripts --no-interaction --no-progress --classmap-authoritative

grammar/srt.php: grammar/srt.pp vendor
	php ./development/grammar-generator/grammar-generator.php

grammar: grammar/srt.php code

tests-unit:
	./vendor/bin/phpunit --no-progress --config=tests/unit/phpunit.xml --testsuite=unit

tests-integration:
	./vendor/bin/phpunit --no-progress --config=tests/integration/phpunit.xml --testsuite=integration

git-diff:
	mkdir -p tmp
	rm -f tmp/diff.txt
	git diff main --no-color -U10 --raw --output=tmp/diff.txt

.PHONY: \
	build \
	bake-tests \
	up \
	up-tests \
	exec-tests-vendor \
	exec-tests-integration \
	down \
	down-tests \
	code \
	php-cs-fixer-check \
	php-syntax \
	psalm \
	psalm-alter \
	grammar/srt.php \
	grammar \
	tests-unit \
	tests-integration \
	git-diff
