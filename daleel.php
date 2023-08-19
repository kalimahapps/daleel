<?php
use KalimahApps\Daleel\Config;

$head = '<meta name="og:image" content="https://daleel.kalimah-apps.com/docs/logo.png">
	<meta name="twitter:image" content="https://daleel.kalimah-apps.com/docs/logo.png">';

$config = Config::getInstance();
$config->defineConfig(array(
		'output_path'    => './build',
		'title'          => 'Daleel',
		'favicon'        => './media/favicon.png',
		'logo'           => './media/logo.svg',
		'social_links'   => array(
			'github'  => array(
				'link' => 'https://github.com/kalimahapps/daleel',
			),
			'twitter' => array(
				'link' => 'https://twitter.com/KalimahApps',
			),
		),
		'head'           => $head,
		'base_path'      => 'docs',
		'main'           => array(
			'subtitle' => 'Generate beautiful documentation for your PHP projects.',
			'buttons'  => array(
				array(
					'label' => 'Get Started',
					'link'  => '/docs/{{latest_version}}/introduction.html',
				),
				array(
					'label' => 'GitHub',
					'link'  => 'https://github.com/kalimahapps/daleel',
				),
			),
		),
		'latest_version' => '1.x',
		'versions'       => array(
			'1.x' => array(
				'project_path' => '.',
				'docs_path'    => 'daleel',
				'docs_index'   => 'introduction',
				'exclude'      => array(
					'vendor',
					'test',
				),
				'sidebar'      => array(
					array(
						'label' => 'Getting Started',
						'items' => array(
							array(
								'label' => 'Introduction',
								'link'  => 'introduction',
							),
							array(
								'label' => 'Usage',
								'link'  => 'usage',
							),
							array(
								'label' => 'Configuration',
								'link'  => 'configuration',
							),
							array(
								'label' => 'Writing',
								'link'  => 'writing',
							),
						),
					),
					array(
						'label' => 'Contributing',
						'items' => array(
							array(
								'label' => 'Development',
								'link'  => 'development',
							),
							array(
								'label' => 'Stack',
								'link'  => 'stack',
							),
						),
					),
				),
				'nav'          => array(
					array(
						'label' => 'Getting Started',
						'link'  => 'usage',
					),
				),
				'edit_url'     => 'https://github.com/abdul-alhasany/daleel/edit/master/daleel/',
			),
		),
	));