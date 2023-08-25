<?php
namespace KalimahApps\Daleel;

use eftec\bladeone\BladeOne;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Builder for blade views.
 */
class ViewBuilder {
	/**
	 * @var BladeOne BladeOne instance
	 */
	private BladeOne $blade_one;

	/**
	 * @var string Path of the folder where views will be compiled to
	 */
	private string $output_path;

	/**
	 * @var
	 */
	private string $build_path;

	/**
	 * @var
	 */
	private array $shared_data = [];

	/**
	 * @var
	 */
	private Config $config;

	/**
	 * @var
	 */
	public string $build_folder = '';

	/**
	 * @var self $instance Singleton instance
	 */
	private static $instance = null;

	/**
	 * Initiate directory traversal and process elements.
	 */
	public function __construct() {
		// Set up blade template
		$views      = __DIR__ . '/template/views';
		$cache_path = Common::getTempPath('views');

		$this->config    = Config::getInstance();
		$this->blade_one = new BladeOne($views, $cache_path, BladeOne::MODE_DEBUG);

		$this->blade_one->throwOnError = true;

		// Set paths
		$this->output_path = $this->config->getConfig('output_path');
	}

	/**
	 * Set the build folder where views will be compiled to.
	 *
	 * @param string $build_folder Build folder name
	 */
	public function setBuildFolder($build_folder = null) {
		$current_version  = $this->config->getCurrentVersion();
		$version_path     = "{$this->output_path}/{$current_version}";
		$this->build_path = $build_folder === null ? $version_path : "{$version_path}/{$build_folder}";

		// Set build folder for use by other classes
		$this->build_folder = $build_folder ?? '';

		$show_breadcrumbs = $build_folder === 'api';

		$this->share('config', [
				'show_breadcrumbs' => $show_breadcrumbs,
				'current_version'  => $current_version,
			], false);
	}

	/**
	 * Get singleton instance.
	 *
	 * @param string $build_type Set the build type
	 */
	public static function getInstance($build_type = 'api') {
		if (self::$instance === null) {
			self::$instance = new ViewBuilder($build_type);
		}

		return self::$instance;
	}

	/**
	 * Share data with all views.
	 *
	 * @param string $key      Key to share
	 * @param mixed  $value    Value to share
	 * @param bool   $override Override existing value (for arrays)
	 */
	public function share(string $key, string|bool|array $value, $override = true) {
		if ($override || !is_array($value) || !isset($this->shared_data[$key])) {
			$this->blade_one->share($key, $value);
			$this->shared_data[$key] = $value;
			return;
		}

		$this->shared_data[$key] = array_merge($this->shared_data[$key], $value);
		$this->blade_one->share($key, $this->shared_data[$key]);
	}

	/**
	 * Share multiple data with all views.
	 *
	 * @param array $data Data to share
	 */
	public function shareMultiple(array $data) {
		foreach ($data as $key => $value) {
			$this->share($key, $value);
		}
	}

	/**
	 * Build view using blade template engine.
	 *
	 * @param string $path      Path to save file
	 * @param string $file_name File name
	 * @param string $template  Template name
	 * @param array  $data      Extra data to pass to template
	 */
	public function buildView(
		string $path,
		string $file_name,
		string $template,
		array $data = []
	) {
		$content = $this->blade_one->run($template, $data);

		$file_path = "{$this->build_path}/{$path}/{$file_name}.html";
		$file_path = Common::getPosixPath($file_path);

		file_put_contents($file_path, $content);
	}

	/**
	 * Build index view at the root of the build folder.
	 *
	 * @param array $data Extra data to pass to template
	 */
	public function buildIndexView(array $data = []) {
		$content = $this->blade_one->run('index', $data);

		$file_path = "{$this->output_path}/index.html";
		$file_path = Common::getPosixPath($file_path);

		file_put_contents($file_path, $content);
	}

	/**
	 * Return view using blade template engine.
	 *
	 * @param string $template Template name
	 * @param array  $data     Extra data to pass to template
	 */
	public function getView(string $template, array $data = []) {
		return $this->blade_one->run($template, $data);
	}

	/**
	 * Copy assest (css, js, images .. etc) to output folder.
	 */
	public function copyAssets() {
		$css_file        = __DIR__ . '/template/css/output.css';
		$target_css_path = Common::getPosixPath("{$this->output_path}/css/output.css");
		copy($css_file, $target_css_path);

		$js_file        = __DIR__ . '/template/js/index.js';
		$target_js_path = Common::getPosixPath("{$this->output_path}/js/index.js");
		copy($js_file, $target_js_path);

		// Copy media (logo and favicon)
		$logo_file = $this->config->getConfig('logo');
		if (!empty($logo_file)) {
			// Does file exist
			if (!file_exists($logo_file)) {
				throw new \Exception("Logo file not found: $logo_file");
			}

			$extension        = pathinfo($logo_file, PATHINFO_EXTENSION);
			$target_logo_path = Common::getPosixPath("{$this->output_path}/logo.{$extension}");
			copy($logo_file, $target_logo_path);
		}

		$favicon_file = $this->config->getConfig('favicon');
		if (!empty($favicon_file)) {
			// Does file exist
			if (!file_exists($favicon_file)) {
				throw new \Exception("Favicon file not found: $favicon_file");
			}

			$extension           = pathinfo($favicon_file, PATHINFO_EXTENSION);
			$target_favicon_path = Common::getPosixPath("{$this->output_path}/favicon.{$extension}");
			copy($favicon_file, $target_favicon_path);
		}
	}

	/**
	 * Copy version assets (css, js, images .. etc) to output folder.
	 */
	public function copyVersionAssets() {
		$assets_folder = $this->config->getConfig('assets_path');
		if (!empty($assets_folder)) {
			// Does folder exist
			if (!file_exists($assets_folder)) {
				throw new \Exception("Assets folder not found: $assets_folder");
			}

			$asset_folder_name = basename($assets_folder);
			$current_version   = $this->config->getCurrentVersion();
			$version_path      = "{$this->output_path}/{$current_version}";

			$target_assets_path = Common::getPosixPath("{$version_path}/{$asset_folder_name}");

			$file_system = new Filesystem();
			$file_system->mirror($assets_folder, $target_assets_path);
		}
	}

	/**
	 * Get value from the shared data.
	 *
	 * @param string $key Key to get
	 * @return mixed      Value if found, false otherwise
	 */
	public function getSharedData(string $key): mixed {
		return $this->shared_data[$key] ?? false;
	}

	/**
	 * Get BladeOne instance.
	 */
	public function getBladeOne(): BladeOne {
		return $this->blade_one;
	}
}