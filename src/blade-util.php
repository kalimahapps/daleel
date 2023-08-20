<?php
namespace KalimahApps\Daleel;
use const KalimahApps\Daleel\PHP_TYPES as PHP_TYPES;

/**
 * Blade template engine utilities.
 */
class BladeUtil {
	/**
	 * Check if the current route is the displayed route.
	 *
	 * @param string $active_path   Current displayed route
	 * @param string $current_route Current rendered route
	 * @param bool   $identical     If true, then compare the routes as is, otherwise remove extension from both routes
	 * @return bool                 True if the current route is the displayed route
	 */
	public static function isActiveRoute(string $active_path, string $current_route, $identical = false) {
		// Remove extension from both paths if identical is false
		if ($identical === false) {
			$active_path   = preg_replace('/\.[^.]+$/', '', $active_path);
			$current_route = preg_replace('/\.[^.]+$/', '', $current_route);
		}

		return strpos($active_path, $current_route) === 0;
	}

	/**
	 * Return data from template.
	 *
	 * `@include` directive does not return data, so this
	 * function is similar to `@include` but returns data.
	 *
	 * @param string $template_name Template name
	 * @param array  $data          Extra data to pass to template
	 * @return string               Template content
	 */
	public static function getTemplateContent(string $template_name, array $data = array()) {
		$view_builder = ViewBuilder::getInstance();
		return $view_builder->getView($template_name, $data);
	}

	/**
	 * Helper method to get config value.
	 *
	 * Use `.` notation to get nested config values.
	 *
	 * @param string $key Config key
	 * @return mixed      Config value
	 */
	public static function getConfig(string $key) {
		$config_instance = Config::getInstance();
		$config_value    = $config_instance->getConfig($key);

		if ($config_value === false) {
			return false;
		}

		// for logo and favicon return only the file name
		// with base_path prepended (if set)
		if (in_array($key, array('logo', 'favicon'))) {
			$config_value = basename($config_value);

			$base_path = $config_instance->getConfig('base_path');
			if ($base_path !== false) {
				$config_value = "{$base_path}/{$config_value}";
			}
		}

		return $config_value;
	}

	/**
	 * Check if search is enabled.
	 */
	public static function isSearchEnabled() {
		$config_instance = Config::getInstance();
		$search_options  = array('app_id', 'api_key', 'index_name');

		// If any of the search options is empty, then search is disabled
		foreach ($search_options as $option) {
			$get_option = $config_instance->getConfig("search.options.{$option}");

			if ($get_option === false || empty($get_option)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Loop through array and return the next or previous sibling.
	 *
	 * @param array  $search_array Array to search
	 * @param string $active_route Current rendered route to search for
	 * @param string $direction    Direction of sibling 'prev | next'
	 *
	 * @return array|false Array of sibling or false if not found
	 */
	private static function findSibling(array $search_array, string $active_route, string $direction) {
		foreach ($search_array as $index => $data) {
			// echo "index :$index<br>";
			if (!empty($data['items'])) {
				$find_next = self::findSibling($data['items'], $active_route, $direction);
				if ($find_next !== false) {
					return $find_next;
				}
			}

			if (!empty($data['link']) && $data['link'] === $active_route) {
				$sibling_index = $direction === 'next' ? $index + 1 : $index - 1;
				$sibling       = isset($search_array[$sibling_index]) ? $search_array[$sibling_index] : false;
				return $sibling;
			}
		}

		return false;
	}

	/**
	 * Get sibling link (previous or next).
	 *
	 * @param string $direction Direction of sibling 'prev | next'
	 */
	public static function getNavLink($direction) {
		$view_builder = ViewBuilder::getInstance();
		$active_route = $view_builder->getSharedData('active_route');
		$docs_sidebar = $view_builder->getSharedData('docs_sidebar');

		$find_next = self::findSibling($docs_sidebar, $active_route, $direction);

		return $find_next;
	}

	/**
	 * Get edit link for the viewed page.
	 */
	public static function getEditLink() {
		$view_builder = ViewBuilder::getInstance();
		$file_path    = $view_builder->getSharedData('file_path');

		$edit_url = BladeUtil::getConfig('edit_url');
		if ($edit_url === false) {
			return;
		}

		$edit_url  = rtrim($edit_url, '/');
		$file_path = ltrim($file_path, '/');

		return "{$edit_url}/{$file_path}";
	}

	/**
	 * Get root link and optionally append extra path.
	 *
	 * @param string $extra_path Extra path to append to root link
	 */
	static public function getRootLink($extra_path = '') {
		$link = '/';

		$config_instance = Config::getInstance();
		$base_path       = $config_instance->getConfig('base_path');
		if ($base_path !== false) {
			$link = $base_path;
		}

		$link = rtrim($link, '/');
		$link = ltrim($link, '/');

		if (!empty($extra_path)) {
			return !empty($link) ? "/{$link}/{$extra_path}" : "/{$extra_path}";
		}

		return "/{$link}";
	}

	/**
	 * Recursively check if the current route is part of the given items.
	 *
	 * @param array  $items       List of items to check
	 * @param string $active_item Current rendered route
	 * @return boolean            True if the current route is part of the given items
	 */
	public static function hasActiveChild(array $items, string $active_item): bool {
		foreach ($items as $item) {
			if (self::isActiveRoute($active_item, $item['link'])) {
				return true;
			}

			if (isset($item['items']) && self::hasActiveChild($item['items'], $active_item)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Resolve links to either php.net or internal links or no links.
	 *
	 * @param array $types           Array of types
	 * @param array $namespaces_list List of namespaces
	 * @return array                 Array of types with resolved links
	 */
	public static function resolveTypes(array $types, $namespaces_list) {
		$output_types = array();
		foreach ($types as $type) {
			$type = ltrim($type, '\\');

			if (in_array(strtolower($type), PHP_TYPES)) {
				$output_types[] = array(
					'name' => $type,
					'link' => "https://www.php.net/$type",
					'kind' => 'php',
				);
			} elseif (in_array($type, $namespaces_list)) {
				// Modify the type so it can be used as a link
				$link = str_replace('\\', '/', $type);

				// Get last part of the type as the display name
				$display_name = explode('/', $link);
				$display_name = end($display_name);

				$output_types[] = array(
					'name' => $display_name,
					'link' => Common::prepareLink(explode('/', $link)),
					'kind' => 'use',
				);
			} else {
				/**
				 * Long name need to be shortened to avoid UI breaking.
				 * For example, `Illuminate\Contracts\Support\Arrayable`
				 * will be shortened to `Illuminate\...\Arrayable`.
				 */
				$name       = $type;
				$type_parts = explode('\\', $type);

				if (count($type_parts) > 2) {
					$name = $type_parts[0] . '\...\\' . $type_parts[count($type_parts) - 1];
				}

				$output_types[] = array(
					'name'  => $name,
					'title' => $type,
					'kind'  => 'other',
				);
			}
		}

		return $output_types;
	}

	/**
	 * Build and return the version url.
	 *
	 * This is used to build the version url for the sidebar.
	 *
	 * @param string $version_key Version key
	 * @return string             Version url
	 */
	public static function getVersionUrl(string $version_key): string {
		$clean_url = Config::getInstance()->getConfig('clean_url');
		$versions  = Config::getInstance()->getConfig('versions');

		$version_data = $versions[$version_key];

		$docs_index    = !empty($version_data['docs_index']) ? $version_data['docs_index'] : '';
		$project_index = !empty($version_data['project_index']) ? $version_data['project_index'] : '';
		$base_path     = Config::getInstance()->getConfig('base_path');

		// check if docs path is set, if so, then use docs index otherwise use project index
		$index_page = '';

		if (!empty($version_data['docs_path'])) {
			$index_page = "{$docs_index}";
		} elseif (!empty($project_index)) {
			$index_page = "api/{$project_index}";
		}

		// $index_page might be empty if both docs and project path index are not set
		if (!empty($index_page)) {
			$extention  = $clean_url ? '' : '.html';
			$index_page = "{$index_page}{$extention}";
		}

		if (!empty($base_path)) {
			$base_path = rtrim($base_path, '/');
			$base_path = ltrim($base_path, '/');
			$base_path = "/{$base_path}";

			return "{$base_path}/{$version_key}/{$index_page}";
		}

		return "/{$version_key}/{$index_page}";
	}
}