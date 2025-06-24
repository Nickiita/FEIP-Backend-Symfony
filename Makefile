.PHONY: csfix cscheck psalm

# Запуск PHP CS Fixer
csfix:
	./vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php

# Запуск PHP CodeSniffer (проверка)
cscheck:
	./vendor/bin/phpcs --standard=PSR12 src tests

# Запуск Psalm
psalm:
	./vendor/bin/psalm
