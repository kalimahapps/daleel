<?php
namespace KalimahApps\Daleel;

use KalimahApps\Daleel\Exceptions\ConfigException;

/**
 * Handle config file.
 */
class Config {
	/**
	 * @var array Default config
	 */
	private $default_config = [
		'output_path' => './build',
		'title'       => '',
		'footer'      => [],

		/**
		 * If `clean_url` is set to `true`, `.html` will be removed from links.
		 */
		'clean_url'   => false,
	];

	/**
	 * @var Config $instance Config instance
	 */
	private static $instance = null;

	/**
	 * @var array $final_config Final config
	 */
	private $final_config = [];

	/**
	 * The current version the docs are being built for.
	 *
	 * @var string $current_version Current version
	 */
	private $current_version = '';

	/**
	 * Load config file and merge with default config.
	 */
	public function __construct() {
		$this->updateConfig($this->default_config);
	}

	/**
	 * Define user config.
	 *
	 * @param array $config Config array to merge with default config
	 */
	public function defineConfig(array $config) {
		// Make sure there is a `versions` property
		if (!isset($config['versions'])) {
			throw new ConfigException('Config file must have a `versions` property');
		}

		// Merge config file with default config
		$config = array_merge($this->default_config, $config);

		$this->updateConfig($config);
	}

	/**
	 * Get config instance.
	 *
	 * @return Config Config instance
	 */
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new Config();
		}

		return self::$instance;
	}

	/**
	 * Update config.
	 *
	 * @param array $config Config array
	 */
	private function updateConfig(array $config) {
		foreach ($config as $key => $value) {
			if ($key === 'versions') {
				foreach ($value as $version => $version_config) {
					$this->updateVersionsConfig($version_config, $version);
				}
				continue;
			}

			$updated_value = match($key) {
					'output_path' => $this->updateOutputPath($value),
					'footer'      => $this->updateFooter($value),
					'logo'        => $this->updatePath($value),
					'favicon'     => $this->updatePath($value),
					default       => $this->updateDefaultConfig($value)
				};

			$this->final_config[$key] = $updated_value;
		}
	}

	/**
	 * Merge footer array with copyright.
	 *
	 * @param array $value Footer array
	 * @return array       Updated footer array
	 */
	private function updateFooter(array $value): array {
		return array_merge($value, ['Powered by <a target="_blank" href="https://daleel.kalimah-apps.com/docs">Daleel</a>']);
	}

	/**
	 * Update versions config.
	 *
	 * @param array  $config  Config array
	 * @param string $version Version number
	 */
	public function updateVersionsConfig(array $config, string $version = null) {
		if ($version === null) {
			$version = $this->getCurrentVersion();
		}

		foreach ($config as $key => $value) {
			$updated_value = match($key) {
					'project_path' => $this->updatePath($value),
					'docs_path'    => $this->updatePath($value),
					default        => $this->updateDefaultConfig($value)
				};
			$this->final_config['versions'][$version][$key] = $updated_value;
		}
	}

	/**
	 * Update default config.
	 *
	 * @param string|array $value Config value to update
	 */
	private function updateDefaultConfig(string|array $value) {
		return $value;
	}

	/**
	 * Update nested links like sidebar and navbar links.
	 *
	 * This will update links to include extra path
	 * segments like version number and base path.
	 *
	 * @param array $array Sidebar links
	 * @return array       Updated sidebar links
	 */
	private function updateNestedLinks($array, $version = null) {
		$updated_array = [];
		foreach ($array as $array_data) {
			if (!empty($array_data['link'])) {
				$link    = ltrim($array_data['link'], '/');
				$link    = explode('/', $link);
				$version = $version ?? $this->getCurrentVersion();

				$array_data['link'] = Common::prepareLink($link, $version);
			}

			if (!empty($array_data['items'])) {
				$array_data['items'] = $this->updateNestedLinks($array_data['items'], $version);
			}

			$updated_array[] = $array_data;
		}

		return $updated_array;
	}

	/**
	 * Update output path.
	 *
	 * Resolve path to real path and make sure it's inside project path.
	 *
	 * @param string|array $value Path to output
	 * @return string             Real path to output
	 */
	private function updateOutputPath(string|array $value) {
		// Only paths inside project path are allowed
		if (strpos($value, '../') === 0) {
			throw new ConfigException('Output path must be inside project path');
		}

		$clean_path = str_replace('./', '', $value);

		// make sure output path exists
		$real_path   = realpath($clean_path) ?: Common::getPosixCwd($clean_path);
		$output_path = Common::getPosixPath($real_path, true);

		// strip trailing slash
		return rtrim($output_path, '/');
	}

	/**
	 * Convert path to absolute path.
	 *
	 * Array is returned as is because it is only for
	 * remote URLs.
	 *
	 * @param string|array $value Path to update
	 * @return string             absolute path
	 */
	private function updatePath(string|array $value) {
		// No need to update if it's a URL
		if (is_array($value)) {
			return $value;
		}

		$real_path = realpath($value) ?: $value;
		return Common::getPosixPath($real_path, false);
	}

	/**
	 * Get config value.
	 *
	 * Use `.` notation to get nested values.
	 *
	 * @param string $config_key Config key
	 * @return mixed             Config value, or `false` if not found
	 */
	public function getConfig(string $config_key) {
		$versions = $this->final_config['versions'];

		// Handle nested config values
		$keys = explode('.', $config_key);

		$value = false;

		// Check if the config is part of the current version
		if (isset($versions[$this->current_version])) {
			$value = $versions[$this->current_version];

			foreach ($keys as $key) {
				$value = isset($value[$key]) ? $value[$key] : false;
			}

			if ($value !== false) {
				return $value;
			}
		}

		// Search in top level config
		$value = $this->final_config;
		foreach ($keys as $key) {
			$value = isset($value[$key]) ? $value[$key] : false;
		}

		return $value;
	}

	/**
	 * Get sidebar with updated links.
	 *
	 * @return array Sidebar links
	 */
	public function getSidebar() {
		$sidebar = $this->getConfig('sidebar');
		if ($sidebar === false) {
			return [];
		}
		return $this->updateNestedLinks($sidebar);
	}

	/**
	 * Get navbar with updated links.
	 */
	public function getNavbar() {
		$navbar = $this->getConfig('nav');
		if ($navbar === false) {
			return [];
		}
		return $this->updateNestedLinks($navbar);
	}

	/**
	 * Set the current version the docs are being built for.
	 *
	 * @param string $value Current version
	 */
	public function setCurrentVersion(string $value) {
		$this->current_version = $value;
	}

	/**
	 * Get the current version the docs are being built for.
	 *
	 * @return string Current version
	 */
	public function getCurrentVersion() {
		return $this->current_version;
	}
}