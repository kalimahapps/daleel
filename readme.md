<p align="center">
<img src="media/logo.svg" width="100" alt="Daleel">
</p>
<h1 align="center">Daleel</h1>
<h3 align="center">PHP documentation generator</h3>

### Documentation
Documentation for Daleel can be found on the [website](https://daleel.kalimah-apps.com/docs).

### Quick start
- Install through composer
```bash
composer require kalimahapps/daleel
```

- Create `daleel.php` in your working directory and add the following code to it:
```php
<?php
use KalimahApps\Daleel\Config;

$config = Config::getInstance();
$config->defineConfig([
	// Configuration array
]);
```
The minimum requirement for the configuration is to define the `versions` key.

Check the [configuration](https://daleel.kalimah-apps.com/docs/1.x/configuration.html) section for more details.

- Build documentation
```bash
./vendor/bin/daleel build
```
Documentation will be generated in the `build` directory.

- View documentation
```bash
./vendor/bin/daleel serve
```

### License
Daleel is open-sourced software licensed under the [MIT license](LICENSE).