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

grammar: grammar/srt.php

.PHONY: grammar
