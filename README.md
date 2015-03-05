#PHPStream

## Tests
Tests are run using PHPUnit v4.7 outside of PHPStorm as it has a bug regarding PHPUnit.

### Installing PHPUnit
Run the following commands to install phpunit:

```sh
wget https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit
```

### Running tests
Navigate to the project directory and run:

```sh
phpunit --bootstrap vendor/autoload.php src/
```
