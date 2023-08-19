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
		$config->defineConfig(array(
				'title'    => 'Daleel',
				'versions' => array(),
			));

		$view_builder = ViewBuilder::getInstance();
		$view_builder->setBuildFolder();
		$view_builder->shareMultiple(
			array(
				'toc'          => array(),
				'page_title'   => 'Test page',
				'active_route' => 'test/data',
			)
		);

		$data = $view_builder->getBladeOne()->run('single', array(
				'content' => 'Sample content',
		));

		$snapshot = get_snapshot('single');
		expect($data)->toBe($snapshot);
	});

test('Object view', function() {
		$config = Config::getInstance();
		$config->defineConfig(array(
				'title'    => 'Daleel',
				'versions' => array(),
			));

		$view_builder = ViewBuilder::getInstance();
		$view_builder->setBuildFolder();
		$view_builder->shareMultiple(
			array(
				'toc'          => array(),
				'active_route' => 'test/data',
			)
		);

		$data = $view_builder->getBladeOne()->run('object', array(
				'link'       => 'test/data',
				'kind'       => 'class',
				'docblock'   => array(
					'summary'     => 'Object summary',
					'description' => 'Object description',
				),
				'extends'    => array(),
				'methods'    => array(
					'isActiveRoute' => array(
						'visibility' => 9,
						'docblock'   => array(
							'summary' => 'Check if the current route is the displayed route.',
						),
					),
				),
				'properties' => array(
					'active_path' => array(
						'visibility' => 9,
						'docblock'   => array(
							'summary' => 'Current displayed route',
						),
					),
				),
			)
		);

		$snapshot = get_snapshot('object');
		expect($data)->toBe($snapshot);

	});