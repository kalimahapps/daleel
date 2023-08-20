<?php
namespace KalimahApps\Daleel;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\{
	Input\InputInterface,
};
use KalimahApps\Daleel\Exceptions\ConfigException;

/**
 * Common utilities.
 */
class Common {
	/**
	 * Write to log file.
	 * @param mixed ...$messages Messages to write to log file
	 */
	static public function debug() {
		$messages = func_get_args();
		foreach ($messages as $message) {
			if (is_array($message) || is_object($message)) {
				file_put_contents(__DIR__ . '/logs/dev-debug.log', print_r($message, 1) . PHP_EOL, FILE_APPEND);
			} else {
				file_put_contents(__DIR__ . '/logs/dev-debug.log', $message . PHP_EOL, FILE_APPEND);
			}
		}
	}

	/**
	 * Write to console.
	 * @param mixed ...$messages Messages to write to console
	 */
	static public function console() {
		$messages = func_get_args();
		$output   = new ConsoleOutput();
		foreach ($messages as $message) {
			if (is_array($message) || is_object($message)) {
				$output->writeln('<info>' . print_r($message, 1) . '</info>');
			} else {
				$output->writeln("<info>{$message}</info>");
			}
		}
	}

	/**
	 * Make changes to the link to make it viewable in the browser.
	 *
	 * This function will prepend the `/{version}/{build_folder}` to the link.
	 * It will also add `.html` to the end of the link if configured.
	 *
	 * @param array $path              Path to the file
	 * @param mixed $version           Version to add to the start of the path
	 * @param bool  $process_extension Whether to add check if extension should
	 *                                 be added to the end of the path
	 *
	 * @return string Link to the file
	 */
	static public function prepareLink(array $path, $version = null, $process_extension = true): string {
		// Add version and folder name to the beginning of the path
		$current_version = $version ?? Config::getInstance()->getCurrentVersion();
		$base_path       = Config::getInstance()->getConfig('base_path');

		$view_build_instance = ViewBuilder::getInstance();
		$build_folder        = $view_build_instance->build_folder;

		if (!empty($build_folder)) {
			array_unshift($path, $build_folder);
		}

		array_unshift($path, $current_version);

		if (!empty($base_path)) {
			array_unshift($path, $base_path);
		}

		if ($process_extension === false) {
			return '/' . implode('/', $path);
		}

		$clear_url = Config::getInstance()->getConfig('clean_url');

		// Add .html to the end of the path if configured
		$extension = '.html';
		if ($clear_url === true) {
			$extension = '';
		}

		return '/' . implode('/', $path) . $extension;
	}

	/**
	 * Get path in posix format.
	 *
	 * @param string $path   Path to convert to posix format
	 * @param bool   $create Create path if not exists
	 * @return string        Path in posix format with extra path appended
	 */
	static public function getPosixPath($path, $create = true) {
		// Replace all backslashes with forward slashes
		$path = str_replace('\\', '/', $path);

		// Replace multiple forward slashes with single forward slash
		$path = preg_replace('/\/+/', '/', $path);

		// is it a file or a directory
		$is_file = pathinfo($path, PATHINFO_EXTENSION);

		$dir = $path;
		if ($is_file !== '') {
			$dir = dirname($path);
		}

		// Create cache folder if not exists
		if (!file_exists($dir) && $create === true) {
			mkdir($dir, 0777, true);
		}

		return $path;
	}

	/**
	 * Get temp folder path in posix format.
	 *
	 * @param string $extra Extra path to append to temp folder path
	 * @return string       Temp folder path in posix format with extra path appended
	 */
	static public function getTempPath($extra = '') {
		$dir = 'daleel-temp';
		if ($extra !== '') {
			$dir .= "/$extra";
		}

		return Common::getPosixCwd($dir);
	}

	/**
	 * Get CWD in posix format.
	 *
	 * @param string $extra  string Extra path to append to CWD
	 * @param bool   $create Create path if not exists
	 * @return string        CWD in posix format with extra path appended
	 */
	static public function getPosixCwd($extra = '', $create = true) {
		$dir = getcwd();

		if ($extra !== '') {
			$dir .= "/$extra";
		}

		return Common::getPosixPath($dir, $create);
	}

	/**
	 * Check if path is absolute.
	 *
	 * @param string $path Path to check
	 * @see                https://developer.wordpress.org/reference/functions/path_is_absolute/
	 * @return bool        True if path is absolute, false otherwise
	 */
	static public function isAbsolutePath(string $path): bool {
		if ((is_dir($path) || is_file($path))) {
			return true;
		}

		/*
		* This is definitive if true but fails if $path does not exist or contains
		* a symbolic link.
		*/
		if (realpath($path) === $path) {
			return true;
		}

		if (strlen($path) === 0 || '.' === $path[0]) {
			return false;
		}

		// Windows allows absolute paths like this.
		if (preg_match('#^[a-zA-Z]:\\\\#', $path)) {
			return true;
		}

		// A path starting with / or \ is absolute; anything else is relative.
		return ('/' === $path[0] || '\\' === $path[0]);
	}

	/**
	 * Get the format for progress bar.
	 *
	 * @param string       $title   Title of the progress bar
	 * @param SymfonyStyle $console Console instance
	 * @return string               Format for progress bar
	 */
	static public function getProgressBarFormat(string $title, SymfonyStyle $console): string {
		$console->newLine(1);

		$padding = 30 - strlen($title);
		$padding = str_repeat('.', $padding);

		$style = 'fg=cyan';
		$title = sprintf('  <%s>‣ %s</> <fg=gray>%s</>', $style, $title, $padding);

		return "{$title} %current%/%max% %percent:3s%% | %elapsed:6s% | %estimated:-6s% | %memory:6s%";
	}

	/**
	 * Get config file.
	 *
	 * Check if config file is provided, and check if it exists.
	 * If not, use config file in current working directory.
	 *
	 * @param InputInterface $input Input interface.
	 * @return bool|string          Config file path or false if not found.
	 */
	static  public function getConfigFile(InputInterface $input) {
		$config_file = $input->getOption('config');
		if (!$config_file) {
			$config_file = Common::getPosixCwd('daleel.php', false);
		} else {
			$config_file = realpath($config_file);
		}

		if ($config_file === false) {
			throw new ConfigException('Config file not found');
		}

		if (!file_exists($config_file)) {
			throw new ConfigException("Config file not found: $config_file");
		}

		// include file
		require_once $config_file;
	}

	/**
	 * Create error message.
	 *
	 * @param SymfonyStyle $console Console instance.
	 * @param string       $title   Error title.
	 * @param \Throwable   $error   Error object.
	 */
	static public function createError($console, string $title, \Throwable $error = null) {
		$console->newLine(2);
		$console->error($title);
		if ($error !== null) {
			$console->info($error->getMessage());
			$console->newLine(1);
			$console->writeln($error->getTraceAsString());
		}
	}

	/**
	 * Replace tags in content.
	 *
	 * Replace tags like {{tag}} with their values.
	 *
	 * @param string $content Content to replace tags in.
	 * @return string         Content with tags replaced.
	 */
	static public function replaceTags(string $content): string {
		$tags = array(
			'{{latest_version}}' => Config::getInstance()->getConfig('latest_version'),
		);

		return str_replace(array_keys($tags), array_values($tags), $content);
	}
}