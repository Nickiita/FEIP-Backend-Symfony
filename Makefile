.PHONY: cs-check cs-fix psalm-check

# Проверка стиля кода
cs-check:
	./vendor/bin/phpcs || true

# Автоисправление стиля кода
cs-fix:
	PHP_CS_FIXER_IGNORE_ENV=1 ./vendor/bin/phpcbf || true
	PHP_CS_FIXER_IGNORE_ENV=1 ./vendor/bin/php-cs-fixer fix --verbose

# Статический анализ
psalm-check:
	./vendor/bin/psalm --show-info=false

# Полная проверка перед коммитом
check: cs-check psalm-check

# Установка всех инструментов
setup-tools:
	composer require --dev squizlabs/php_codesniffer friendsofphp/php-cs-fixer vimeo/psalm
	@if [ ! -f psalm.xml ]; then ./vendor/bin/psalm --init src 4; fi