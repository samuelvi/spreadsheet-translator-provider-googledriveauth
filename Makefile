.PHONY: help install update test test-coverage rector rector-dry phpstan clean ci

# Variables
PHP = php
COMPOSER = composer
PHPUNIT = vendor/bin/phpunit
RECTOR = vendor/bin/rector

## help: Display this help message
help:
	@echo "Available commands:"
	@echo "  make install         - Install all dependencies"
	@echo "  make update          - Update all dependencies"
	@echo "  make test            - Run tests"
	@echo "  make test-coverage   - Run tests with coverage report"
	@echo "  make rector          - Run Rector (apply changes)"
	@echo "  make rector-dry      - Run Rector in dry-run mode"
	@echo "  make clean           - Remove vendor and cache directories"
	@echo "  make ci              - Run CI checks (rector-dry + tests)"

## install: Install all dependencies
install:
	$(COMPOSER) install

## update: Update all dependencies
update:
	$(COMPOSER) update

## test: Run tests
test:
	$(PHPUNIT)

## test-coverage: Run tests with coverage report (HTML)
test-coverage:
	XDEBUG_MODE=coverage $(PHPUNIT) --coverage-html coverage

## rector: Run Rector and apply changes
rector:
	$(RECTOR) process

## rector-dry: Run Rector in dry-run mode (no changes applied)
rector-dry:
	$(RECTOR) process --dry-run

## clean: Remove vendor and cache directories
clean:
	rm -rf vendor
	rm -rf .phpunit.cache
	rm -rf coverage

## ci: Run all CI checks
ci: rector-dry test
	@echo "All CI checks passed!"
