<?php
use Symfony\Component\Console\Tester\CommandTester;
use KalimahApps\Daleel\{Commands, ProcessDocs, Config, ViewBuilder, ParseDocBlock};
use KalimahApps\Daleel\Commands\ConsoleStyle;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use phpDocumentor\Reflection\DocBlockFactory;

test('Build doc', function() {
		$command        = new Commands\BuildDoc();
		$command_tester = new CommandTester($command);
		$command_tester->execute([]);
		$command_tester->assertCommandIsSuccessful();
		$output       = $command_tester->getDisplay();
		$output_lines = explode(\PHP_EOL, $output);
		expect($output_lines[1])->toBe(' :: Daleel :: ');
		expect($output_lines[2])->toBe('==============');
		expect($output_lines[4])->toStartWith(' Deleting build folder');
	});

test('Build doc with missing config file', function() {
		chdir('tests');
		$command        = new Commands\BuildDoc();
		$command_tester = new CommandTester($command);
		$command_tester->execute([]);
		$command_tester->assertCommandIsSuccessful();
		$output       = $command_tester->getDisplay();
		$output_lines = explode(\PHP_EOL, $output);

		expect($output_lines[1])->toBe(' :: Daleel :: ');
		expect($output_lines[2])->toBe('==============');
		expect($output_lines[8])->toStartWith(' [INFO] Config file not found:');
	});

test('Build doc with custom config', function() {
		$command        = new Commands\BuildDoc();
		$command_tester = new CommandTester($command);
		$command_tester->execute([
				'--config' => 'tests/custom-config.php',
			]);
		$command_tester->assertCommandIsSuccessful();
		$output       = $command_tester->getDisplay();
		$output_lines = explode(\PHP_EOL, $output);

		// print_r($output_lines);
		expect($output_lines[1])->toBe(' :: Daleel :: ');
		expect($output_lines[2])->toBe('==============');
		expect($output_lines[4])->toStartWith(' Building version');
	});

test('ProcessDocs', function() {
		$config_instance = Config::getInstance();
		$view_builder    = ViewBuilder::getInstance();
		$view_builder->setBuildFolder();

		// Test non existing folder
		$config_instance->setCurrentVersion('test');
		$console      = new ConsoleStyle(new StringInput(''), new NullOutput());
		$process_docs = new ProcessDocs($console);
		expect(fn() =>
			$process_docs->start()
		)->toThrow('docs_path not found');

		// Test empty folder
		$versions                              = $config_instance->getConfig('versions');
		$first_version                         = array_key_first($versions);
		$versions[$first_version]['docs_path'] = 'tests';
		$config_instance->setCurrentVersion($first_version);
		$config_instance->defineConfig(['versions' => $versions]);
		expect($process_docs->start())->toBeFalse();

		// Test create index not created if main property is not provided
		$config_instance->defineConfig(['versions' => $versions, 'main' => false]);
		expect($process_docs->createIndex())->toBeFalse();
	});

test('Docblock parser', function() {
		$docblock = '/**
			* @param string $test Hi
			* @deprecated 1.0.0 This is deprecated
			* @internal This is internal
			* @link https://google.com
			*/';
		$docblock_factory = DocBlockFactory::createInstance();
		$docblock         = $docblock_factory->create($docblock);
		$parse_docblock   = new ParseDocBlock($docblock, []);
		$data             = $parse_docblock->getParsedDocblockData();

		expect($data)->toHaveKey('tags');
		expect($data['tags'])->toHaveKey('deprecated');
		expect($data['tags'])->toHaveKey('link');
		expect($data['tags'])->toHaveKey('internal');
	});