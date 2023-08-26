<?php
namespace KalimahApps\Daleel\Commands;

use KalimahApps\Daleel\{Common, Config, ProcessDocs, ProcessApiDocs, ViewBuilder};
use KalimahApps\Daleel\Exceptions\ConfigException;
use KalimahApps\Daleel\Commands\ConsoleStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use Symfony\Component\Console\{
	Command\Command,
	Input\InputInterface,
	Input\InputOption,
	Output\OutputInterface
};

/**
 * Handle command and related options.
 */
class ServeDoc extends Command {
	/**
	 * @var OutputInterface $output Output interface.
	 */
	private OutputInterface $output;

	/**
	 * @var ConsoleStyle $console Input output interface.
	 */
	private ConsoleStyle $console;

	/**
	 * Configure the command options.
	 */
	protected function configure() {
		$this->setName('serve')
		->setDescription('Run documentation server')
		->addOption('config', null, InputOption::VALUE_REQUIRED, 'Config file');
	}

	/**
	 * Execute the command.
	 *
	 * @param InputInterface  $input  Input interface.
	 * @param OutputInterface $output Output interface.
	 * @return int                    Exit code.
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->console = new ConsoleStyle($input, $output);

		$this->console->title(' :: Daleel :: ');
		$this->console->text('Starting documentation server...');
		$this->console->newLine(1);

		Common::getConfigFile($input);

		$process = new Process(['php', '-S', 'localhost:8000', '-t', 'build']);
		$process->setTimeout(null);
		$process->setIdleTimeout(null);
		$process->start();
		foreach ($process as $type => $data) {
			if ($process::OUT === $type) {
				echo "\nRead from stdout: {$data}";
			} else {
				// $process::ERR === $type
				echo "\nRead from stderr: {$data}";
			}
		}

		$this->console->text('Server started at http://localhost:8000');

		return Command::SUCCESS;
	}

	/**
	 * Create error message.
	 *
	 * @param string     $title Error title.
	 * @param \Throwable $error Error object.
	 */
	private function createError(string $title, \Throwable $error) {
		$this->console->newLine(2);
		$this->console->error($title);
		$this->console->info($error->getMessage());
		$this->console->newLine(1);
		$this->console->writeln($error->getTraceAsString());
	}
}