<?php
namespace KalimahApps\Daleel\CodeHighlighter;

use League\CommonMark\Extension\CommonMark\Renderer\Block\IndentedCodeRenderer as BaseIndentedCodeRenderer;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\{ChildNodeRendererInterface, NodeRendererInterface};

/**
 * Render an IndentedCode block to HTML.
 */
class IndentedCodeRenderer implements NodeRendererInterface {
	/**
	 * @var CodeHighlighter $highlighter The highlighter
	 */
	protected CodeHighlighter $highlighter;

	/**
	 * @var BaseIndentedCodeRenderer $base_renderer Base renderer instance
	 */
	protected BaseIndentedCodeRenderer $base_renderer;

	/**
	 * Constructor.
	 *
	 * @param array $autodetect_languages Languages to autodetect
	 */
	public function __construct(array $autodetect_languages = array()) {
		$this->highlighter   = new CodeHighlighter($autodetect_languages);
		$this->base_renderer = new BaseIndentedCodeRenderer();
	}

	/**
	 * Render an IndentedCode block to HTML.
	 *
	 * @param Node                       $node           The node to render
	 * @param ChildNodeRendererInterface $child_renderer The child renderer to use
	 *
	 * @return string Rendered HTML
	 */
	public function render(Node $node, ChildNodeRendererInterface $child_renderer) {
		$element = $this->base_renderer->render($node, $child_renderer);

		$element->setContents(
			$this->highlighter->highlight($element->getContents())
		);

		return $element;
	}
}