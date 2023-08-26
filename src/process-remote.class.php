<?php
namespace KalimahApps\Daleel;

use KalimahApps\Daleel\Exceptions\ConfigException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Download and extract files from remote server.
 */
class ProcessRemote {
	/**
	 * @var
	 */
	private Config $config;

	/**
	 * @var
	 */
	private string $downloaded_file_path;

	/**
	 * @var
	 */
	private string $config_key;

	/**
	 * Build link tree and create HTML files.
	 *
	 * @param SymfonyStyle $console input/output interface
	 */
	private SymfonyStyle $console;

	/**
	 * @var string Path to the extracted files
	 */
	private string $extraction_path;

	/**
	 * Constructor.
	 *
	 * @param SymfonyStyle $console input/output interface
	 */
	public function __construct(SymfonyStyle $console) {
		$this->config  = Config::getInstance();
		$this->console = $console;
	}

	/**
	 * Download and extract files.
	 *
	 * This function will check if the config path is a url,
	 * and if so, download, extract and set the config to the
	 * extracted path.
	 *
	 * @param string $config_key Config key to fetch
	 */
	public function fetch(string $config_key) {
		$this->config_key = $config_key;

		$url_data = $this->config->getConfig($this->config_key);
		if (!is_array($url_data)) {
			return;
		}

		$url = $url_data['url'];

		if (!str_starts_with($url, 'http')) {
			return;
		}

		$is_valid_url = filter_var($url, FILTER_VALIDATE_URL);
		if (!$is_valid_url) {
			throw new \Exception("Invalid URL: {$url}");
		}

		// Start progress bar with 1 max step so it does not
		// throw an error when setting the format.
		$progress_bar = $this->console->createProgressBar(1);
		$progress_bar->setFormat(Common::getProgressBarFormat('Downloading', $this->console));

		$client   = HttpClient::create();
		$response = $client->request(
			'GET',
			$url,
			['on_progress' => function(int $dl_now, int $dl_size, array $info) use ($progress_bar) {
					// Update max if not already set
					if ($progress_bar->getMaxSteps() === 0 && $dl_size > 0) {
						$progress_bar->setMaxSteps($dl_size);
					}

					// Only update progress if download size is known
					if ($dl_now > 0) {
						$progress_bar->setProgress($dl_now);
					}
				},
			]
		);

		$progress_bar->finish();

		// get file name
		$parse_url  = parse_url($url);
		$path_parts = pathinfo($parse_url['path']);
		$file_name  = $path_parts['basename'];

		$content       = $response->getContent();
		$download_path = Common::getTempPath('download');

		$this->downloaded_file_path = "{$download_path}/{$file_name}";

		file_put_contents($this->downloaded_file_path, $content);

		$this->extractFile();
		$this->updateConfig();
	}

	/**
	 * Update config with extracted path.
	 */
	private function updateConfig() {
		$url_data = $this->config->getConfig($this->config_key);
		$dir      = $url_data['dir'];

		// Only paths inside extracted folder are allowed
		if (strpos($dir, '../') === 0) {
			throw new ConfigException('Path must not start with ../');
		}

		$clean_path = str_replace('./', '', $dir);
		rtrim($clean_path, '/');

		if ($clean_path !== '.') {
			$this->extraction_path = "{$this->extraction_path}/{$clean_path}";
		}

		$this->config->updateVersionsConfig([
			$this->config_key => $this->extraction_path,
		]);
	}

	/**
	 * Extract downloaded file.
	 */
	private function extractFile() {
		// Extract file
		$current_version = $this->config->getCurrentVersion();
		$extract_path    = Common::getTempPath($current_version);
		$sub_folder      = $this->config_key === 'project_path' ? 'api' : 'docs';

		$zip = new \ZipArchive();
		$res = $zip->open($this->downloaded_file_path);

		$zip_files_length = $zip->numFiles;

		$progress_bar = $this->console->createProgressBar($zip_files_length);
		$progress_bar->setFormat(Common::getProgressBarFormat('Extracting', $this->console));

		if ($res !== true) {
			throw new \Exception("Failed to extract {$this->downloaded_file_path}");
		}

		for ($index = 0; $index < $zip_files_length; $index++) {
			$progress_bar->advance();

			// Get file path
			$stat      = $zip->statIndex($index);
			$file_name = $stat['name'];
			$dist      = "{$extract_path}/{$sub_folder}/{$file_name}";

			// Check if the extracted file is a directory (ends with /)
			$is_dir = str_ends_with($file_name, '/');

			if ($is_dir) {
				// Create directory if it does not exist
				if (!is_dir($dist)) {
					mkdir($dist, 0777, true);
				}
				continue;
			}

			// Create file parent directories if they do not exist
			$dir_name = dirname($dist);
			if (!is_dir($dir_name)) {
				mkdir($dir_name, 0777, true);
			}

			// Create file
			$content = $zip->getFromName($file_name);
			file_put_contents("{$extract_path}/{$sub_folder}/{$file_name}", $content);
		}

		$root_dir = trim($zip->getNameIndex(0), '/');
		$zip->close();

		$progress_bar->finish();

		$this->extraction_path = "{$extract_path}/{$sub_folder}/{$root_dir}";
	}
}