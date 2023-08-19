# Usage
## Installation
Install through composer
```bash
composer require kalimahapps/daleel
```

## Configuration
To run Daleel, you need to define the configuration. Daleel uses a singleton pattern to define the configuration. Create a file named `daleel.php` in your working directory and add the following code to it:
```php
<?php
use KalimahApps\Daleel\Config;

$config = Config::getInstance();
$config->defineConfig(
	// Configuration array
	array()
);
```
The minimum requirement for the configuration is to define the `versions` key.

Check the [configuration](/configuration) section for more details.

## Building documentation
Once you have defined the configuration, you can run Daleel by calling:
```bash
./vendor/bin/daleel build
```

You can use a custom configuration file by passing the `--config` option:
```bash
./vendor/bin/daleel build --config=/path/to/config/file
```

If errors are encountered during the build process, Daleel will output a summary of the errors. To view the errors you can use `--show-errors` option:
```bash
./vendor/bin/daleel build --show-errors
```

To show errors with the full stack trace, use the `--show-errors-full` option:
```bash
./vendor/bin/daleel build --show-errors-full
```

## Viewing documentation
You can view a local version of the documentation by running:
```bash
./vendor/bin/daleel serve
```
This will create a local server on port 8000. You can view the documentation by visiting `http://localhost:8000` in your browser.