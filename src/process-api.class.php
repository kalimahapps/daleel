<?php
namespace KalimahApps\Daleel;
use Symfony\Component\Console\{
	Helper\ProgressBar,
	Style\SymfonyStyle
};
use Symfony\Component\Finder\Finder;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;
use KalimahApps\Daleel\{Visitor, ViewBuilder, Config};
use phpDocumentor\Reflection\DocBlockFactory;
use Throwable;

/**
 * Handle generating API docs from docblocks.
 */
class ProcessApiDocs {
	/**
	 * @var array Array of namespaces
	 */
	private $namespaces_list = array();

	/**
	 * @var SymfonyStyle $console input/output interface
	 */
	private SymfonyStyle $console;

	/**
	 * @var array Array of errors
	 */
	private $errors = array();

	/**
	 * @var ProgressBar $view_builder_progress Progress bar for building views
	 */
	private $view_builder_progress = null;

	/**
	 * @var ViewBuilder $view_builder View builder instance
	 */
	private ViewBuilder $view_builder;

	/**
	 * @var array $tree Tree of namespaces
	 */
	private array $tree;

	/**
	 * @var int $views_count Number of views to build (used for progress bar)
	 */
	private $views_count = 1;

	/**
	 * Initiate directory traversal and process elements.
	 *
	 * @param SymfonyStyle $console input/output interface
	 */
	public function __construct(SymfonyStyle $console) {
		$this->console = $console;

		$config             = Config::getInstance();
		$this->view_builder = ViewBuilder::getInstance();

		$exclude      = $config->getConfig('exclude');
		$project_path = $config->getConfig('project_path');

		if (empty($exclude)) {
			$exclude = array();
		}
		$finder = new Finder();
		$finder->files()->in($project_path)->notPath($exclude)->name('*.php');

		if (!$finder->hasResults()) {
			throw new \Exception("No files found in $project_path");
		}

		$this->tree = $this->createTree($finder);
	}

	/**
	 * Get the tree of namespaces.
	 */
	public function getSidebarTree() {
		return $this->buildSidebarTree($this->tree);
	}

	/**
	 * Start the process.
	 */
	public function start() {
		// creates a new progress bar for building views
		$this->view_builder_progress = $this->console->createProgressBar($this->views_count);
		$this->view_builder_progress->setFormat(Common::getProgressBarFormat('Building views', $this->console));
		$this->view_builder_progress->start();
		$this->buildHtml($this->tree);
		$this->view_builder_progress->finish();
	}

	/**
	 * Loop through files and create a hierarchical tree.
	 *
	 * @param Finder $files Files to loop through
	 * @return array        Tree of all files
	 */
	private function createTree(Finder $files) {
		$files = iterator_to_array($files);

		// creates a new progress bar
		$progress_bar = $this->console->createProgressBar(count($files));
		$progress_bar->setFormat(Common::getProgressBarFormat('Creating tree', $this->console));

		$factory = new ParserFactory();
		$parser  = $factory->create(ParserFactory::PREFER_PHP7);

		$final_tree       = array();
		$docblock_factory = DocBlockFactory::createInstance();

		// Setup visitor with docblock factory dependency
		$visitor = new Visitor($docblock_factory);

		// Setup traverser with name resolver and visitor
		$traverser = new NodeTraverser();
		$traverser->addVisitor(new NameResolver());
		$traverser->addVisitor($visitor);

		foreach ($files as $file) {
			$absolute_file_path = $file->getRealPath();

			// Read file content
			$file_data = file_get_contents($absolute_file_path);

			try {
				// Get AST
				$ast = $parser->parse($file_data);

				$traverser->traverse($ast);

				// Recursively merge visitor tree with final tree
				// to get a single tree for all files
				$final_tree = array_merge_recursive($final_tree, $visitor->getTree());

				// Get list of namespaces to show a more accurate progress bar
				// when building views
				$this->namespaces_list = array_merge($this->namespaces_list, $visitor->getNamespaces());

				$progress_bar->advance();
			} catch (Throwable $error) {
				$this->errors[] = $error;
			}
		}

		// Share with blade template so types can be resolved
		// when compared against the list of namespaces
		$this->view_builder->share('namespaces_list', $this->namespaces_list);

		$progress_bar->finish();
		return $this->sortAndCountTree($final_tree);
	}

	/**
	 * Sort tree recursively.
	 *
	 * As a side effect, this function will also
	 * update the number of views to build.
	 *
	 * @param array $tree Tree to sort
	 * @return array      Sorted tree
	 */
	private function sortAndCountTree(array $tree): array {
		$extract_section = array(
			'namespaces' => 'Namespaces',
			'classes'    => 'Classes',
			'interfaces' => 'Interfaces',
			'traits'     => 'Traits',
			'functions'  => 'Functions',
		);

		foreach ($extract_section as $key => $label) {
			if (empty($tree[$key])) {
				continue;
			}

			// Update views count
			$this->views_count += count($tree[$key]);

			// Sort keys
			ksort($tree[$key]);

			foreach ($tree[$key] as $child_key => $child_data) {
				$tree[$key][$child_key] = $this->sortAndCountTree($child_data);
			}
		}

		return $tree;
	}

	/**
	 * Build a tree of namespaces to be used in the sidebar.
	 *
	 * This is a recursive function.
	 *
	 * @param array $tree Tree to build sidebar from
	 * @param array $path Path to current element
	 * @return array      aggregated tree
	 */
	private function buildSidebarTree(array $tree, array $path = array()) {
		$sidebar_tree = array();
		foreach ($tree as $key => $data) {
			if ($key !== 'namespaces') {
				continue;
			}

			foreach ($data as $namespace => $namespace_data) {
				$namespace_path   = $path;
				$namespace_path[] = $namespace;

				$nested_namespace = $this->buildSidebarTree($namespace_data, $namespace_path);

				$sidebar_tree[] = array(
					'label'    => $namespace,
					'children' => $nested_namespace,
					'link'     => Common::prepareLink($namespace_path),
				);
			}
		}
		return $sidebar_tree;
	}

	/**
	 * Build a table of contents for an object (class, trait, interface, etc.).
	 *
	 * @param array $object_data Object data
	 * @return array             Table of contents
	 */
	private function buildObjectToc($object_data) {
		$toc = array();

		$extract_section = array(
			'constants'  => 'Constants',
			'properties' => 'Properties',
			'methods'    => 'Methods',
		);

		foreach ($extract_section as $key => $label) {
			$section_data = $object_data[$key] ?? array();

			if (count($section_data) === 0) {
				continue;
			}

			$children = array();
			foreach ($object_data[$key] as $child_key => $child_data) {
				$children[$child_key] = array(
					'label' => $child_key,
				);
			}

			$toc[$key] = array(
				'label'    => $label,
				'children' => $children,
			);
		}

		return $toc;
	}

	/**
	 * Build a tree of namespaces to be used in namespace index page.
	 *
	 * @param array $namespace_data Namespace data
	 * @param array $path           Path to current element
	 * @param array $title          Title of current element
	 * @return array                aggregated tree
	 */
	private function buildNamespaceData($namespace_data, array $path, array $title = array()) {
		$data            = array();
		$extract_section = array(
			'namespaces' => 'Namespaces',
			'classes'    => 'Classes',
			'interfaces' => 'Interfaces',
			'traits'     => 'Traits',
			'functions'  => 'Functions',
		);

		foreach ($extract_section as $key => $section_title) {
			if (empty($namespace_data[$key])) {
				continue;
			}

			$children = array();
			foreach ($namespace_data[$key] as $label => $child_data) {
				$child_path       = array_merge($path, array($label));
				$full_child_label = array_merge($title, array($label));

				$children_data = array(
					'label' => implode('\\', $full_child_label),
					'link'  => Common::prepareLink($child_path),
				);

				// Find nested namespaces and add them to the children
				if (!empty($child_data['namespaces'])) {
					$nested_namespaces = $this->buildNamespaceData($child_data, $child_path, $full_child_label);

					$children_data['children'] = $nested_namespaces[0]['children'];
				}

				$children[] = $children_data;
			}

			$data[] = array(
				'label'    => $section_title,
				'children' => $children,
			);
		}

		return $data;
	}

	/**
	 * Build html files recursively.
	 *
	 * @param array $tree        Tree to build html files from
	 * @param array $path        Path to current element
	 * @param array $breadcrumbs Breadcrumbs to current element
	 */
	private function buildHtml($tree, $path = array(), $breadcrumbs = array()) {
		$this->view_builder_progress->advance();

		// Clear shared var before building new one
		// because it might leak into other views
		$this->view_builder->shareMultiple(
			array(
				'toc'          => array(),
				'breadcrumbs'  => array(),
				'active_route' => '',
			)
		);

		$kinds = array(
			'namespaces' => 'namespace',
			'classes'    => 'class',
			'interfaces' => 'interface',
			'traits'     => 'trait',
			'functions'  => 'function',
		);

		foreach ($tree as $key => $data) {
			if ($key === 'namespaces') {
				foreach ($data as $namespace => $namespace_data) {
					$namespace_path   = $path;
					$namespace_path[] = $namespace;

					$link                    = Common::prepareLink($namespace_path);
					$namespace_breadcrumbs   = $breadcrumbs;
					$namespace_breadcrumbs[] = array(
						'label' => $namespace,
						'link'  => $link,
					);
					$this->view_builder->shareMultiple(
						array(
							'breadcrumbs'  => $namespace_breadcrumbs,
							'active_route' => $link,
							'page_title'   => "{$namespace} namespace",
						)
					);

					$this->view_builder->buildView(
						implode('/', $path),
						$namespace,
						'namespace-index',
						array('namespace_data' => $this->buildNamespaceData($namespace_data, $namespace_path))
					);

					$this->buildHtml($namespace_data, $namespace_path, $namespace_breadcrumbs);
				}
			}

			if (in_array($key, array('classes', 'traits', 'interfaces'))) {
				foreach ($data as $object_key => $object_data) {
					$object_path   = $path;
					$object_path[] = $object_key;

					$link = Common::prepareLink($object_path);

					$object_breadcrumbs   = $breadcrumbs;
					$object_breadcrumbs[] = array(
						'label' => $object_key,
						'link'  => $link,
					);

					$object_data['link'] = $link;
					$object_data['kind'] = $kinds[$key];

					$toc = $this->buildObjectToc($object_data);
					$this->view_builder->shareMultiple(
						array(
							'toc'          => $toc,
							'breadcrumbs'  => $object_breadcrumbs,
							'active_route' => $link,
							'page_title'   => "{$object_key} {$kinds[$key]}",
						)
					);

					$this->view_builder->buildView(
						implode('/', $path),
						$object_key,
						'object',
						$object_data
					);

					$this->buildHtml($object_data, $path, $breadcrumbs);
				}
			}

			if ($key === 'functions') {
				foreach ($data as $function_key => $function_data) {
					$function_path   = $path;
					$function_path[] = $function_key;

					$link = Common::prepareLink($function_path);

					$function_breadcrumbs   = $breadcrumbs;
					$function_breadcrumbs[] = array(
						'label' => $function_key,
						'link'  => $link,
					);

					$function_data['link'] = $link;
					$function_data['kind'] = $kinds[$key];

					$this->view_builder->shareMultiple(
						array(
							'breadcrumbs'  => $function_breadcrumbs,
							'active_route' => $link,
							'page_title'   => "{$function_key} {$kinds[$key]}",
						)
					);

					$this->view_builder->buildView(
						implode('/', $path),
						$function_key,
						'function',
						array('function_data' => $function_data, 'function_key' => $function_key)
					);
				}
			}
		}
	}

	/**
	 * Get list of errors found.
	 *
	 * @return array List of errors
	 */
	public function getErrors(): array {
		return $this->errors;
	}
}