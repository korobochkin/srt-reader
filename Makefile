code:
	./vendor/bin/php-cs-fixer \
		fix \
		--config="development/php-cs-fixer/php-cs-fixer.dist.php" \
		--cache-file="development/php-cs-fixer/php-cs-fixer.cache" \
		--format=txt \
		--no-interaction
