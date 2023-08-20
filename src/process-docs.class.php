<?php
namespace KalimahApps\Daleel;
use Symfony\Component\Console\Style\SymfonyStyle;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Node\Query;
use League\CommonMark\Node\Block\Document as MarkdownDocument;
use Symfony\Component\Finder\Finder;
use KalimahApps\Daleel\{ViewBuilder, Config};
use KalimahApps\Daleel\Containers\ContainerExtension;
use KalimahApps\Daleel\InternalLinkExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Extension\{
	CommonMark\CommonMarkCoreExtension,
	GithubFlavoredMarkdownExtension
};
use League\CommonMark\Node\StringContainerHelper;
use Spatie\CommonMarkHighlighter\FencedCodeRenderer;
use Spatie\CommonMarkHighlighter\IndentedCodeRenderer;
use Throwable;

/**
 * Process markdown files.
 */
class ProcessDocs {
	/**
	 * @var SymfonyStyle $console input/output interface
	 */
	private SymfonyStyle $console;

	/**
	 * @var array Array of errors
	 */
	private $errors = array();

	/**
	 * @var
	 */
	private Config $config;

	/**
	 * @var
	 */
	private ViewBuilder $view_builder;

	/**
	 * Build link tree and create HTML files.
	 *
	 * @param SymfonyStyle $console input/output interface
	 */
	public function __construct(SymfonyStyle $console) {
		$this->console = $console;

		$this->config       = Config::getInstance();
		$this->view_builder = ViewBuilder::getInstance();
	}

	/**
	 * Start the process.
	 * @return boolean True if successful, false otherwise
	 */
	public function start(): bool {
		$exclude   = $this->config->getConfig('exclude');
		$docs_path = $this->config->getConfig('docs_path');

		if ($docs_path === false) {
			throw new \Exception('docs_path not found');
		}

		if (empty($exclude)) {
			$exclude = array();
		}

		$finder = new Finder();
		try {
			$finder->files()->in($docs_path)->notPath($exclude)->name('*.md');

			if (!$finder->hasResults()) {
				throw new \Exception("No files found in $docs_path");
			}
		} catch (\Exception $error) {
			Common::createError($this->console, 'Error while reading docs folder', $error);
			return false;
		}

		if (!$finder->hasResults()) {
			return false;
		}

		$this->createDocs($finder);
		$this->createIndex();

		return true;
	}

	/**
	 * Create the main index.html file.
	 *
	 * @return bool True if successful, false otherwise
	 */
	public function createIndex(): bool {
		$main = $this->config->getConfig('main');
		if (empty($main)) {
			return false;
		}

		$main['title']       = $this->config->getConfig('title');
		$main['latest_link'] = Common::prepareLink(array('index'), $this->config->getConfig('latest_version'));

		$this->view_builder->share('page_title', '');
		$this->view_builder->buildIndexView(
			array('data' => $main)
		);

		return true;
	}

	/**
	 * Loop through files and create a hierarchical tree.
	 *
	 * @param Finder $files Files to loop through
	 * @return array        Tree of all files
	 */
	private function createDocs(Finder $files) {
		$files = iterator_to_array($files);

		// creates a new progress bar
		$progress_bar = $this->console->createProgressBar(count($files));
		$progress_bar->setFormat(Common::getProgressBarFormat('Building views', $this->console));

		$final_tree = array();

		$markdown_config = array(
			'heading_permalink' => array(
				'apply_id_to_heading' => true,
				'id_prefix'           => '',
				'fragment_prefix'     => '',
				'symbol'              => '#',
				'heading_class'       => 'scroll-mt-20 relative group',
				'html_class'          => 'absolute -translate-x-full pr-2 opacity-0 font-normal group-hover:opacity-100 transition-opacity duration-200 ease-in-out',
			),
			'highlighter'       => array(
				'add_default_class' => true,
			),
			'container'         => array(
				'default_titles' => array(
					'info'    => 'INFO',
					'tip'     => 'TIP',
					'warning' => 'WARNING',
					'danger'  => 'DANGER',
				),
			),
			'table'             => array(
				'wrap' => array(
					'enabled'    => true,
					'tag'        => 'div',
					'attributes' => array('class' => 'table-wrapper'),
				),
			),
			'external_link'     => array(
				'open_in_new_window' => true,
				'html_class'         => 'external-link',
				'noopener'           => 'external',
				'noreferrer'         => 'external',
			),
		);

		$environment = new Environment($markdown_config);
		$environment->addExtension(new InternalLinkExtension());
		$environment->addExtension(new ImagePathExtension());
		$environment->addExtension(new CommonMarkCoreExtension());
		$environment->addExtension(new FrontMatterExtension());
		$environment->addExtension(new GithubFlavoredMarkdownExtension());
		$environment->addExtension(new HeadingPermalinkExtension());
		$environment->addExtension(new ContainerExtension());
		$environment->addExtension(new ExternalLinkExtension());
		$environment->addRenderer(FencedCode::class, new FencedCodeRenderer(array('php')));
		$environment->addRenderer(IndentedCode::class, new IndentedCodeRenderer(array('php')));

		$converter = new MarkdownConverter($environment);

		foreach ($files as $file) {
			$absolute_file_path = $file->getRealPath();
			$file_name          = $file->getFilename();

			$file_name_without_extension = pathinfo($file_name, PATHINFO_FILENAME);

			$posix_file_path = Common::getPosixPath($absolute_file_path);

			try {
				// remove docs_path from path
				$relative_file_path = str_replace($this->config->getConfig('docs_path'), '', $posix_file_path);

				$path_without_filename = str_replace($file_name, '', $relative_file_path);
				$path_without_filename = ltrim($path_without_filename, '/');
				$path_without_filename = rtrim($path_without_filename, '/');

				// Read file content
				$file_data = file_get_contents($absolute_file_path);

				$data = $converter->convert($file_data);

				// Create a nested TOC from level 2 and 3 headings
				$document = $data->getDocument();

				$title = $this->getTitle($document);
				$toc   = $this->buildToc($document);

				// Since blade template is rendered through base template
				// and base template is rendered with every view
				// we need to share data globally to avoid passing it from one view to another
				$this->view_builder->shareMultiple(
					array(
						'toc'          => $toc,
						'page_title'   => $title,
						'active_route' => array(),
						'file_path'    => "{$path_without_filename}/{$file_name}",
					)
				);

				$link = array($file_name_without_extension);
				if (!empty($path_without_filename)) {
					array_unshift($link, $path_without_filename);
				}

				$link = Common::prepareLink($link);
				$this->view_builder->share('active_route', $link);

				$this->view_builder->buildView(
					$path_without_filename,
					$file_name_without_extension,
					'single',
					array(
						'content' => $data,
					)
				);

				$progress_bar->advance();
			} catch (Throwable $error) {
				$this->errors[] = $error;
			}
		}

		$progress_bar->finish();
		return $final_tree;
	}

	/**
	 * Get the first H1 heading from the document.
	 *
	 * @param MarkdownDocument $document Markdown document
	 * @return string                    Title of the document
	 */
	private function getTitle(MarkdownDocument $document): string {
		// first check if front matter has a title
		$front_matter = $document->data->get('front_matter');
		if (!empty($front_matter['title'])) {
			return $front_matter['title'];
		}

		// Find all headings
		$matching_nodes = (new Query())
			->where(Query::type(Heading::class))
			->findAll($document);

		// Loop through all headings and create a nested array
		foreach ($matching_nodes as $node) {
			$heading_content = StringContainerHelper::getChildText($node, array(RawMarkupContainerInterface::class));

			$level = $node->getLevel();

			if ($level === 1) {
				return $heading_content;
			}
		}

		return '';
	}

	/**
	 * Build table of contents from headings.
	 *
	 * @param MarkdownDocument $document Markdown document
	 * @return
	 */
	private function buildToc(MarkdownDocument $document) {
		// Find all headings
		$matching_nodes = (new Query())
			->where(Query::type(Heading::class))
			->findAll($document);

		$toc = array();

		// Hold last h2 id to add h3s to it
		$last_h2_id = '';

		// Loop through all headings and create a nested array
		foreach ($matching_nodes as $node) {
			$heading_content = StringContainerHelper::getChildText($node, array(RawMarkupContainerInterface::class));

			$heading_id = $node->data->get('attributes/id');
			$level      = $node->getLevel();

			if ($level === 2) {
				$toc[$heading_id] = array(
					'label'    => $heading_content,
					'children' => array(),
				);
				$last_h2_id = $heading_id;
			} else if ($level === 3) {
				$toc[$last_h2_id]['children'][$heading_id] = array(
					'label'    => $heading_content,
					'children' => array(),
				);
			}
		}

		return $toc;
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