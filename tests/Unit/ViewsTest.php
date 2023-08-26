<?php
use KalimahApps\Daleel\{Config, ViewBuilder, BladeUtil};

/**
 * Get a specific snapshot.
 *
 * @param string $snapshot_file Snapshot file name
 * @return string               Snapshot content
 */
function get_snapshot(string $snapshot_file): string {
	// Get snapshot folder path (one level up from this file)
	$snapshot_folder = dirname(__DIR__) . '/snapshots';

	// Get snapshot file path
	$snapshot_path = "{$snapshot_folder}/{$snapshot_file}.txt";

	// Get snapshot content
	$snapshot_content = file_get_contents($snapshot_path);

	return $snapshot_content;
}

test('Single view', function() {
		$config = Config::getInstance();
		$config->defineConfig([
				'title'    => 'Daleel',
				'versions' => [],
			]);

		$view_builder = ViewBuilder::getInstance();
		$view_builder->setBuildFolder();
		$view_builder->shareMultiple([
			'toc'          => [],
			'page_title'   => 'Test page',
			'active_route' => 'test/data',
		]);

		$data = $view_builder->getBladeOne()->run('single', [
			'content' => 'Sample content',
		]);

		$snapshot = get_snapshot('single');
		expect($data)->toBe($snapshot);
	});

test('Object view', function() {
		$config = Config::getInstance();
		$config->defineConfig([
			'title'    => 'Daleel',
			'versions' => [],
		]);

		$view_builder = ViewBuilder::getInstance();
		$view_builder->setBuildFolder();
		$view_builder->shareMultiple([
			'toc'          => [],
			'active_route' => 'test/data',
		]);

		$data = $view_builder->getBladeOne()->run('object', [
			'link'       => 'test/data',
			'kind'       => 'class',
			'docblock'   => [
				'summary'     => 'Object summary',
				'description' => 'Object description',
			],
			'extends'    => [],
			'methods'    => [
				'isActiveRoute' => [
					'visibility' => 9,
					'docblock'   => [
						'summary' => 'Check if the current route is the displayed route.',
					],
				],
			],
			'properties' => [
				'active_path' => [
					'visibility' => 9,
					'docblock'   => [
						'summary' => 'Current displayed route',
					],
				],
			],
		]);

		$snapshot = get_snapshot('object');
		expect($data)->toBe($snapshot);

	});