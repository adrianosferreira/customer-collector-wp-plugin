build:
	@echo "Building the production version of the plugin"
	mkdir build
	cp -r admin build
	cp -r includes build
	cp -r languages build
	cp -r public build
	cp -r templates build
	cp -r composer.json build
	cp -r index.php build
	cp -r customer-collector.php build
	composer install --no-dev --optimize-autoloader -d build/
	rm -rf build/composer.json
	rm -rf build/composer.lock

dev:
	@echo "Installing development version of the plugin"
	composer install

test:
	@echo "Running unit tests"
	vendor/bin/phpunit