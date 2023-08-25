<?php
use KalimahApps\Daleel\Config;

$head = '<meta name="og:image" content="https://daleel.kalimah-apps.com/docs/logo.png">
	<meta name="twitter:image" content="https://daleel.kalimah-apps.com/docs/logo.png">';

$config = Config::getInstance();
$config->defineConfig([
		'output_path'    => './build',
		'title'          => 'Daleel',
		'favicon'        => './media/favicon.png',
		'logo'           => './media/logo.png',
		'social_links'   => [
			'github'  => [
				'link' => 'https://github.com/kalimahapps/daleel',
			],
			'twitter' => [
				'link' => 'https://twitter.com/KalimahApps',
			],
		],
		'gtag'           => 'G-FGLP92B854',
		'head'           => $head,
		'base_path'      => 'docs',
		'main'           => [
			'subtitle' => 'Generate beautiful documentation for your PHP projects.',
			'buttons'  => [
				[
					'label' => 'Get Started',
					'link'  => '/docs/{{latest_version}}/introduction.html',
				],
				[
					'label' => 'GitHub',
					'link'  => 'https://github.com/kalimahapps/daleel',
				],
			],
		],
		'latest_version' => '1.x',
		'versions'       => [
			'1.x' => [
				'project_path' => '.',
				'docs_path'    => 'daleel',
				'docs_index'   => 'introduction',
				'assets_path'  => 'images',
				'exclude'      => [
					'vendor',
					'test',
				],
				'sidebar'      => [
					[
						'label' => 'Getting Started',
						'items' => [
							[
								'label' => 'Introduction',
								'link'  => 'introduction',
							],
							[
								'label' => 'Usage',
								'link'  => 'usage',
							],
							[
								'label' => 'Configuration',
								'link'  => 'configuration',
							],
							[
								'label' => 'Writing',
								'link'  => 'writing',
							],
						],
					],
					[
						'label' => 'Contributing',
						'items' => [
							[
								'label' => 'Development',
								'link'  => 'development',
							],
							[
								'label' => 'Stack',
								'link'  => 'stack',
							],
						],
					],
				],
				'nav'          => [
					[
						'label' => 'Getting Started',
						'link'  => 'usage',
					],
				],
				'edit_url'     => 'https://github.com/abdul-alhasany/daleel/edit/master/daleel/',
			],
		],
	]);