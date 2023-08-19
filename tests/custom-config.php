<?php
use KalimahApps\Daleel\Config;

$config = Config::getInstance();
$config->defineConfig(array(
		'output_path'    => './building',
		'exclude'        => array(
			'vendor',
			'cache',
		),
		'title'          => 'Documentation',
		'favicon'        => './media/favicon.ico',
		'logo'           => './media/logoa.png',
		'footer'         => array(
			'Hello from footer',
		),
		'main'           => array(
			'subtitle' => 'Daleel is a simple, fast, and secure PHP framework.',
			'buttons'  => array(
				array(
					'label' => 'Get Started',
					'link'  => '/1.0/getting-started/installation',
				),
				array(
					'label' => 'GitHub',
					'link'  => '',
				),
			),
		),
		'latest_version' => '1.0',
		'versions'       => array(
			'1.0' => array(
				'project_path' => '.',
				'docs_path'    => 'daleel',
				'search'       => array(
					'options' => array(
						'app_id'     => 'a',
						'api_key'    => 'a',
						'index_name' => 'a',
					),
				),
				'social_links' => array(
					'github'   => array(
						'link' => '',
					),
					'twitter'  => array(
						'link' => '',
					),
					'linkedin' => array(
						'link' => '',
					),
					'facebook' => array(
						'link' => '',
					),
					'discord'  => array(
						'link' => '',
					),
					'youtube'  => array(
						'link' => '',
					),
					'slack'    => array(
						'link' => '',
					),
					'intagram' => array(
						'link' => '',
					),
				),
				'nav'          => array(
					array(
						'label' => 'Getting Started',
						'items' => array(
							array(
								'label' => 'Installation',
								'link'  => 'getting-started/installation',
							),
							array(
								'label' => 'Configuration',
								'link'  => 'getting-started/configuration',
							),
							array(
								'label' => 'Directory Structure',
								'link'  => 'getting-started/structure',
							),

						),
					),
					array(
						'label' => 'Changeloc',
						'link'  => 'changelog',
					),
				),
				'sidebar'      => array(
					array(
						'label' => 'Getting Started',
						'items' => array(
							array(
								'label' => 'Artisan',
								'link'  => 'artisan',
							),
							array(
								'label' => 'Installation',
								'link'  => 'getting-started/installation',
							),
							array(
								'label' => 'Configuration',
								'link'  => 'getting-started/configuration',
								'items' => array(
									array(
										'label' => 'Frontend',
										'link'  => 'getting-started/frontend',
									),
								),
							),
							array(
								'label' => 'Directory Structure',
								'link'  => 'getting-started/structure',
							),

						),
					),
					array(
						'label' => 'Development',
						'items' => array(
							array(
								'label' => 'Controllers',
								'link'  => 'development/controllers',
								'items' => array(
									array(
										'label' => 'Routing',
										'link'  => 'development/controllers/routing',
									),
									array(
										'label' => 'Actions',
										'link'  => 'development/controllers/actions',
									),
									array(
										'label' => 'Filters',
										'link'  => 'development/controllers/filters',
									),
									array(
										'label' => 'Views',
										'link'  => 'development/controllers/views',
									),
								),
							),

							array(
								'label' => 'Plugins',
								'link'  => 'development/plugins',
							),
							array(
								'label' => 'Modules',
								'link'  => 'development/modules',
								'items' => array(array(
										'label' => 'Widgets',
										'link'  => 'development/widgets',
									),
									array(
										'label' => 'Events',
										'link'  => 'development/events',
										'items' => array(array(
												'label' => 'Database',
												'link'  => 'development/database',
											),
											array(
												'label' => 'Migrations',
												'link'  => 'development/migrations',
												'items' => array(array(
														'label' => 'Caching',
														'link'  => 'development/caching',
													),
													array(
														'label' => 'Helpers',
														'link'  => 'development/helpers',
													),
													array(
														'label' => 'Security',
														'link'  => 'development/security',
													),
												),
											),
											array(
												'label' => 'Seeds',
												'link'  => 'development/seeds',
											),
										),
									),
									array(
										'label' => 'Routes',
										'link'  => 'development/routes',
									),
								),
							),

							array(
								'label' => 'Validation',
								'link'  => 'development/validation',
							),
							array(
								'label' => 'Localization',
								'link'  => 'development/localization',
							),

						),
					),
				),
			),
			'2.x' => array(
				'project_path' => '.',
				'docs_path'    => 'docs',
				'search'       => array(
					'options' => array(
						'app_id'     => '',
						'api_key'    => '',
						'index_name' => '',
					),
				),
			),
		),
	));