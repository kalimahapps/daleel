<?php
namespace KalimahApps\Daleel\Commands;

use KalimahApps\Daleel\{Common, Config, ProcessDocs, ProcessApiDocs, ProcessRemote, ViewBuilder};
use KalimahApps\Daleel\Exceptions\ConfigException;
use KalimahApps\Daleel\Commands\ConsoleStyle;

use Symfony\Component\Console\{
	Command\Command,
	Input\InputInterface,
	Input\InputOption,
	Output\OutputInterface
};
use Symfony\Component\Filesystem\{
	Exception\IOExceptionInterface,
	Filesystem
};

/**
 * Handle command and related options.
 */
class BuildDoc extends Command {
	/**
	 * @var InputInterface $input Input interface.
	 */
	private InputInterface $input;

	/**
	 * @var ConsoleStyle $console Input output interface.
	 */
	private ConsoleStyle $console;

	/**
	 * Configure the command options.
	 */
	protected function configure() {
		$this->setName('build')
		->setDescription('Build documentation')
		->addOption('config', null, InputOption::VALUE_REQUIRED, 'Config file')
		->addOption('show-errors', null, InputOption::VALUE_NONE, 'Show errors')
		->addOption('show-full-errors', null, InputOption::VALUE_NONE, 'Show full errors');
	}

	/**
	 * Execute the command.
	 *
	 * @param InputInterface  $input  Input interface.
	 * @param OutputInterface $output Output interface.
	 * @return int                    Exit code.
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->input = $input;

		$start_time = microtime(true);

		$this->console = new ConsoleStyle($input, $output);
		$this->console->title(' :: Daleel :: ');

		try {
			Common::getConfigFile($input);
			$config_instance = Config::getInstance();
			$remote          = new ProcessRemote($this->console);

			// Delete folders before building
			$output_path = $config_instance->getConfig('output_path');
			$temp_path   = Common::getTempPath();
			$this->deleteFolder($output_path, 'Deleting build folder ...');
			$this->deleteFolder($temp_path, 'Deleting temp folder ...');

			// Get view builder instance after deleting folders
			// because it creates a temp 'view' folder
			$view_builder = ViewBuilder::getInstance();

			// Hold errors to display them at the end (if any)
			$errors = [];

			$versions = $config_instance->getConfig('versions');
			foreach ($versions as $version => $version_data) {
				// Set current version so config can reterive the correct data
				// for each iteration
				$config_instance->setCurrentVersion($version);

				// If both docs and project path are not set, skip this version
				if (!$this->hasPath('docs_path') && !$this->hasPath('project_path')) {
					$this->console->warning("No docs or project path set in config file for version $version");
					break;
				}

				// Reset shared data before each version
				$view_builder->shareMultiple(array(
						'docs_sidebar' => [],
						'api_sidebar'  => [],
					));

				// reset build folder to default
				$view_builder->setBuildFolder();

				$this->console->newLine(3);
				$this->console->write("Building version $version");
				$view_builder->share('navbar', $config_instance->getNavbar());

				// Set sidebar if docs path is set
				if ($this->hasPath('docs_path')) {
					$view_builder->share('docs_sidebar', $config_instance->getSidebar());
				}

				// Process api if project_path is set
				if ($this->hasPath('project_path')) {
					$this->console->subTitle('Project');
					$remote->fetch('project_path');

					$api_docs = new ProcessApiDocs($this->console);
					$view_builder->setBuildFolder('api');

					// Share api sidebar so it can be accessed by docs views
					$view_builder->share('api_sidebar', $api_docs->getSidebarTree());

					// Start processing api if project_path is set
					$api_docs->start();

					// Get errors from api docs
					$errors = array_merge($errors, $api_docs->getErrors());
				}

				// Start only if docs path is set
				if ($this->hasPath('docs_path')) {
					$this->console->subTitle('Docs');
					$remote->fetch('docs_path');

					$markdown_docs = new ProcessDocs($this->console);
					$view_builder->setBuildFolder();
					$markdown_docs->start();

					// Get errors from markdown docs
					$errors = array_merge($errors, $markdown_docs->getErrors());
				}

				ViewBuilder::getInstance()->copyVersionAssets();
			}

			ViewBuilder::getInstance()->copyAssets();
			$this->console->newLine(3);

			// Cleanup
			$this->deleteFolder($temp_path, 'Deleting temp folder ...');

			$time_elapsed = $this->timeElapsed($start_time);
			$this->console->text("Execution time: {$time_elapsed}");

			$error_count = count($errors);
			if ($error_count > 0) {
				$this->processErrors($errors, $input->getOption('show-errors'));
			} else {
				$this->console->success('Documentation generated successfully!');
			}
		} catch (ConfigException $error) {
			Common::createError($this->console, 'Config Error', $error);
		} catch (\Throwable $error) {
			Common::createError($this->console, 'Error', $error);
		}

		return Command::SUCCESS;
	}

	/**
	 * Handle errors.
	 *
	 * Either show a message to notify the user that there are errors,
	 * or show the errors if --show-errors option is set.
	 *
	 * @param array $errors Array of errors.
	 */
	private function processErrors(array $errors) {
		$show_full_errors = $this->input->getOption('show-full-errors');
		$show_errors      = $this->input->getOption('show-errors');

		$error_count = count($errors);
		if ($show_errors !== true && $show_full_errors !== true) {
			$this->console->warning("Documentation generated with {$error_count} errors!");
			return;
		}

		$this->console->section(" Errors ({$error_count}) ");

		// Show errors if --errors option is set
		foreach ($errors as $error) {
			$message = $error->getMessage();
			$this->console->block(" - {$message}", null, 'fg=red', '');

			if ($show_full_errors === true) {
				$this->console->text($error->getTraceAsString());
				$this->console->newLine(0);
			}
		}
	}

	/**
	 * Check if path is set in config.
	 *
	 * @param string $path Path to check
	 * @return bool        True if path is set, false otherwise
	 */
	private function hasPath(string $path): bool {
		$config_instance = Config::getInstance();
		return $config_instance->getConfig($path) !== false;
	}

	/**
	 * Delete build folder if it exists.
	 *
	 * @param string $target_path Path to delete.
	 * @param string $title       Title to display to console.
	 */
	private function deleteFolder(string $target_path, string $title) {
		// Delete build folder if it exists
		if (!file_exists($target_path)) {
			return;
		}

		$style = 'fg=magenta';
		$title = sprintf(' <%s>%s</>', $style, $title);

		$this->console->text($title);
		$filesystem = new Filesystem();
		try {
			$filesystem->remove($target_path);
		} catch (IOExceptionInterface $error) {
			$this->console->error($error->getMessage());
			exit;
		}
	}

	/**
	 * Get time elapsed since start time.
	 *
	 * @param float $start_time Start time.
	 * @return string           Time elapsed.
	 */
	private function timeElapsed(float $start_time) {
		$end_time = microtime(true);
		$seconds  = round($end_time - $start_time, 2);
		$minutes  = round($seconds / 60, 2);
		$hours    = round($minutes / 60, 2);

		if ($seconds < 60) {
			return "{$seconds} seconds";
		}

		if ($minutes < 60) {
			return "{$minutes} minutes";
		}

		return "{$hours} hours and {$minutes} minutes";
	}
}