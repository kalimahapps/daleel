# Configuration

Configuration for the project should be add to `daleel.php` file in the working directory. If you want to use a different file name, you can pass the name using `--config` switch when running the generator.

```bash
./vendor/bin/daleel build --config=custom.php
```

## Sample configuration
```php
use KalimahApps\Daleel\Config;

$config = Config::getInstance();
$config->defineConfig(array(
		'output_path'    => './build',
		'title'          => 'Daleel',
		'favicon'        => './media/favicon.ico',
		'logo'           => './media/logo.png',
		'footer'         => array(
			'Hello from footer',
		),
		'base_path'      => '',
		'social_links' => array(),
		'main'           => array(
			'subtitle' => 'Generate beautiful documentation for your PHP projects.',
			'buttons'  => array(
				array(
					'label' => 'Get Started',
					'link'  => '/1.0/introduction.html',
				),
				array(
					'label' => 'GitHub',
					'link'  => '',
				),
			),
		),
		'latest_version' => '1.0',
		'versions'       => array(
			'1.0' => array()
		),
	)
);
```
## Top-level configuration
These configuration options are relevant to the whole project.

### output_path
The path to the output directory. This is where the generated files will be saved. The path is relative to the working directory.

Default: `build`

### title
The title of the project. It will be used in areas like the home page, the page title, and sidebar.

Default: `''`

### favicon
The path to the favicon file. The path should be relative to the working directory.

Default: `null`

### logo
The path to the logo file. The path should be relative to the working directory.

Default: `null`

### clean_url
If set to `true`, the generated URLs will be clean. For example, instead of `/1.0/introduction.html`, the URL will be `/1.0/introduction`. This is useful if you want to remove the `.html` extension from the URLs and let the web server handle the extension.

Default: `false`

### head
Add custom HTML to the `<head>` tag.

Default: `null`

example:
```php
'head' => '<meta name="description" content="Daleel is a PHP documentation generator. It both generates documentation from PHP source code and also from Markdown files. It creates a beautiful documentation website for your project.">',
```


### footer
The footer text. It should be an array of strings. Each string will be displayed in a separate line. You can use HTML tags in the strings.

Default: `array()`

### base_path
If documentation is not in the root directory of the project, you can set the base path here. The path should be relative to the domain root.
For example, if the documentation is in `http://example.com/docs`, then the base path should be `docs`.

Default: `null`

### main
An array of properties related to the main page. The main page is the home page of the documentation.

#### main.subtitle
The subtitle of the main page. It will be displayed below the title.

Default: `''`

#### main.buttons
An array of buttons to be displayed below the subtitle. Each button should be an array with two keys: `label` and `link`. The `label` key is the text of the button, and the `link` key is the link to the button. The link can be relative or absolute.

Default: `array()`

example:
```php
array(
	array(
		'label' => 'Get Started',
		'link'  => '/1.0/introduction.html',
	),
	array(
		'label' => 'GitHub',
		'link'  => 'https://github.com/kalimahapps',
	),
)
```
:::tip
You can use `{{latest_version}}` tag in the link to build a link to the latest version. For example, if you want to build a link to the latest version of the introduction page, you can use `/{{latest_version}}/introduction.html`.
:::

### social_links
An array of social links to be displayed in the header. Each link should be an array with two keys: `label` and `link`. The `label` key is the tooltip title of the link, and the `link` is the href of the link.

```php
'social_links' => array(
	array(
		'label' => 'GitHub',
		'link'  => 'LINK',
	),
	array(
		'label' => 'Twitter',
		'link'  => 'LINK',
	),
)
```

Daleel supports the following social links:
- github
- twitter
- facebook
- linkedin
- youtube
- instagram
- discord
- slack

Relevant icons will be displayed for each link.


### latest_version
The latest version of the project. This will be used to build link with the latest version.

Default: `null`

## Version configuration
These configuration options are relevant to a specific version of the project. You can add as many versions as you want. `versions` is an array of versions. For example, if you want to add version `1.0` and `2.0`, you can do it like this:

```php
'versions' => array(
	'1.0' => array(
		'project_path' => '.',
		'docs_path' => './docs',
		'exclude' => array(),
		'search' => array(),
		'nav' => array(),
		'sidebar' => array(),
		'edit_url' => '',
	),
	'2.0' => array(),
)
```

Version key can be any string. It will be used to build links to the version. For example, if you have a version `1.0`, then the link to the version will be `/1.0/`, or if you have a version `2.x`, then the link to the version will be `/2.x/`.

### project_path
The path to the project directory (where PHP files are located). This is used to generate the API documentation.
This can be a string representing the path relative to the working directory. Also, it can be an array of `url` and `dir` keys. If an array is used, the `url` should point to a remote zip file (currently supporting only GitHub), and the `dir` should point to the directory inside the repository.

example:
```php
array(
	'project_path' => './src'
)
```

or
```php
array(
	'project_path' => array(
		'url' => 'https://github.com/laravel/framework/archive/refs/heads/10.x.zip',
		'dir' => './src',
	)
)
```
The above example will download the `10.x` branch of Laravel framework from GitHub and then process the files inside the `src` directory.

:::warning
Please note that you need to set either `project_path` or `docs_path` or both. If you don't set any of them, the generator will not generate any files.
:::

### docs_path
The path to the documentation directory (where markdown files are located). This is used to generate the documentation pages.
This can be a string representing the path relative to the working directory. Also, it can be an array of `url` and `dir` keys. If an array is used, the `url` should point to a remote zip file (currently supporting only GitHub), and the `dir` should point to the directory inside the repository.

example:
```php
array(
	'docs_path' => './docs'
)
```

or
```php
array(
	'docs_path' => array(
		'url' => 'https://github.com/laravel/docs/archive/refs/heads/10.x.zip',
		'dir' => './docs',
	)
)
```
:::warning
Please note that `sidebar` will be ignored if `docs_path` is not set.
:::

### docs_index
Set the index page of the documentation. This is used to build the versions index. No need to add `.md` extension as it will be handled using `clean_url` configuration.

Default: `''`

example:
```php
array(
	'docs_index' => 'introduction'
)
```

### project_index
Set the index page of the project. This is used to build the versions index. No need to add `.md` extension as it will be handled using `clean_url` configuration.

Default: `''`

example:
```php
array(
	'project_index' => 'KalimahApps'
)
```
:::warning
Please note that `docs_index` has priority over `project_index`. If both are set, `docs_index` will be used.
:::

### exclude
An array of files and directories to be excluded from the documentation. The paths should be relative to the project directory. Glob patterns are not supported.

Default: `array()`

example:
```php
array(
	'exclude' => array(
		'Console',
		'Exceptions',
	)
)
```

### search
This is the configuration to enable algolia search. If you don't configure this, search will not be enabled.

First, you need to apply for access to the [Algolia DocSearch](https://docsearch.algolia.com/) program. Once you get access, you will get an `app_id`, `api_key` and `index_name`. You need to add these to the configuration. Example:

```php
'search' => array(
	'options' => array(
		'app_id'     => 'APP ID',
		'api_key'    => 'API KEY',
		'index_name' => 'INDEX NAME',
	)
)
```

### nav
An array of navigation links to be displayed in the header. Each link should be an array with two keys: `label` and `link`. The `label` key is the text of the link, and the `link` is the href of the link.

```php
'nav' => array(
	array(
		'label' => 'Changelog',
		'link'  => 'changelog',
	),
	array(
		'label' => 'Contributing',
		'link'  => 'contributing',
	),
)
```

You can add a dropdown menu by adding a `items` to the array. The `items` key should be an array of links.

```php
'nav' => array(
	array(
		'label' => 'Changelog',
		'link'  => 'changelog',
	),
	array(
		'label' => 'Contributing',
		'link'  => 'contributing',
	),
	array(
		'label' => 'Dropdown',
		'items' => array(
			array(
				'label' => 'Item 1',
				'link'  => 'item-1',
			),
			array(
				'label' => 'Item 2',
				'link'  => 'item-2',
			),
		),
	),
)
```

:::warning
Please note that `nav` only supports one level of dropdown menu.
:::

### sidebar
An array of arrays. Each array represents a sidebar section. Each section should have a `label` and `items` keys. The `label` key is the title of the section, and the `items` key is an array of links. Each link should have a `label` and `link` keys. The `label` key is the text of the link, and the `link` is the href of the link.

```php
'sidebar' => array(
	array(
		'label' => 'Section 1',
		'items' => array(
			array(
				'label' => 'Item 1',
				'link'  => 'item-1',
			),
			array(
				'label' => 'Item 2',
				'link'  => 'item-2',
			),
		),
	),
	array(
		'label' => 'Section 2',
		'items' => array(
			array(
				'label' => 'Item 1',
				'link'  => 'item-1',
			),
			array(
				'label' => 'Item 2',
				'link'  => 'item-2',
			),
		),
	),
)
```

:::info
Please note that while there is no limit on nesting sections in the sidebar, it might not look good if you have too many nested sections.
:::

:::warning
Please note that `sidebar` will be ignored if `docs_path` is not set.
:::

### edit_url
The URL to the edit page of the documentation. This will be used to build the edit link in the footer. The URL should point to the root of the repository.

Default: `null`

example:
```php
'edit_url' => 'Link to the root of the repository'
```

### assets_path
The path to the assets directory. This is where static assets like images are located. The folder will be copied as is to the output directory.

Default: `null`

```php
'assets_path' => 'images'
```

### notice
A notice to be displayed in the header. It can be used to display a warning message to the users. For example if the documentation is not complete yet or if it is an old version.

It is an array with two keys: `type` and `text`. The `type` key can be `tip`, `warning`, `info` or `danger`. The `text` key is the text of the notice and can contain HTML.

Default: `null`

example:
```php
'notice' => array(
	'type' => 'warning',
	'message' => 'This is a warning message',
)
```

:::tip
You can use `{{latest_version}}` tag in the `text` to build a link to the latest version. For example, if you want to build a link to the latest version of the introduction page, you can use `/{{latest_version}}/introduction.html`.
:::

### gtag
Google Analytics tracking ID. If added, Google Analytics will be enabled.

Default: `null`

example:
```php
'gtag' => 'UA-xxxxxxxxx-x'
```

