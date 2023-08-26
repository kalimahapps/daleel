<?php
use KalimahApps\Daleel\Config;

$config = Config::getInstance();
$config->defineConfig([
	'output_path'    => './building',
	'exclude'        => [
		'vendor',
		'cache',
	],
	'title'          => 'Documentation',
	'favicon'        => './media/favicon.ico',
	'logo'           => './media/logoa.png',
	'footer'         => [
		'Hello from footer',
	],
	'main'           => [
		'subtitle' => 'Daleel is a simple, fast, and secure PHP framework.',
		'buttons'  => [
			[
				'label' => 'Get Started',
				'link'  => '/1.0/getting-started/installation',
			],
			[
				'label' => 'GitHub',
				'link'  => '',
			],
		],
	],
	'latest_version' => '1.0',
	'versions'       => [
		'1.0' => [
			'project_path' => '.',
			'docs_path'    => 'daleel',
			'search'       => [
				'options' => [
					'app_id'     => 'a',
					'api_key'    => 'a',
					'index_name' => 'a',
				],
			],
			'social_links' => [
				'github'   => [
					'link' => '',
				],
				'twitter'  => [
					'link' => '',
				],
				'linkedin' => [
					'link' => '',
				],
				'facebook' => [
					'link' => '',
				],
				'discord'  => [
					'link' => '',
				],
				'youtube'  => [
					'link' => '',
				],
				'slack'    => [
					'link' => '',
				],
				'intagram' => [
					'link' => '',
				],
			],
			'nav'          => [
				[
					'label' => 'Getting Started',
					'items' => [
						[
							'label' => 'Installation',
							'link'  => 'getting-started/installation',
						],
						[
							'label' => 'Configuration',
							'link'  => 'getting-started/configuration',
						],
						[
							'label' => 'Directory Structure',
							'link'  => 'getting-started/structure',
						],

					],
				],
				[
					'label' => 'Changeloc',
					'link'  => 'changelog',
				],
			],
			'sidebar'      => [
				[
					'label' => 'Getting Started',
					'items' => [
						[
							'label' => 'Artisan',
							'link'  => 'artisan',
						],
						[
							'label' => 'Installation',
							'link'  => 'getting-started/installation',
						],
						[
							'label' => 'Configuration',
							'link'  => 'getting-started/configuration',
							'items' => [
								[
									'label' => 'Frontend',
									'link'  => 'getting-started/frontend',
								],
							],
						],
						[
							'label' => 'Directory Structure',
							'link'  => 'getting-started/structure',
						],

					],
				],
				[
					'label' => 'Development',
					'items' => [
						[
							'label' => 'Controllers',
							'link'  => 'development/controllers',
							'items' => [
								[
									'label' => 'Routing',
									'link'  => 'development/controllers/routing',
								],
								[
									'label' => 'Actions',
									'link'  => 'development/controllers/actions',
								],
								[
									'label' => 'Filters',
									'link'  => 'development/controllers/filters',
								],
								[
									'label' => 'Views',
									'link'  => 'development/controllers/views',
								],
							],
						],
						[
							'label' => 'Plugins',
							'link'  => 'development/plugins',
						],
						[
							'label' => 'Modules',
							'link'  => 'development/modules',
							'items' => [
								[
									'label' => 'Widgets',
									'link'  => 'development/widgets',
								],
								[
									'label' => 'Events',
									'link'  => 'development/events',
									'items' => [
										[
											'label' => 'Database',
											'link'  => 'development/database',
										],
										[
											'label' => 'Migrations',
											'link'  => 'development/migrations',
											'items' => [
												[
													'label' => 'Caching',
													'link'  => 'development/caching',
												],
												[
													'label' => 'Helpers',
													'link'  => 'development/helpers',
												],
												[
													'label' => 'Security',
													'link'  => 'development/security',
												],
											],
										],
										[
											'label' => 'Seeds',
											'link'  => 'development/seeds',
										],
									],
								],
								[
									'label' => 'Routes',
									'link'  => 'development/routes',
								],
							],
						],
						[
							'label' => 'Validation',
							'link'  => 'development/validation',
						],
						[
							'label' => 'Localization',
							'link'  => 'development/localization',
						],
					],
				],
			],
		],
		'2.x' => [
			'project_path' => '.',
			'docs_path'    => 'docs',
			'search'       => [
				'options' => [
					'app_id'     => '',
					'api_key'    => '',
					'index_name' => '',
				],
			],
		],
	],
]);