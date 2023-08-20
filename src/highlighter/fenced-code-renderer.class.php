<?php

namespace KalimahApps\Daleel\CodeHighlighter;

use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Renderer\Block\FencedCodeRenderer as BaseFencedCodeRenderer;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\{ChildNodeRendererInterface, NodeRendererInterface};
use League\CommonMark\Util\Xml;

/**
 * Render a FencedCode block to HTML.
 */
class FencedCodeRenderer implements NodeRendererInterface {
	/**
	 * @var CodeHighlighter $highlighter The highlighter
	 */
	protected CodeHighlighter $highlighter;

	/**
	 * @var BaseFencedCodeRenderer $base_renderer The base renderer
	 */
	protected $base_renderer;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->highlighter   = new CodeHighlighter();
		$this->base_renderer = new BaseFencedCodeRenderer();
	}

	/**
	 * Render a FencedCode block to HTML.
	 *
	 * @param Node                       $node           The node to render
	 * @param ChildNodeRendererInterface $child_renderer The child renderer to use
	 *
	 * @return string Rendered HTML
	 */
	public function render(Node $node, ChildNodeRendererInterface $child_renderer) {
		$element = $this->base_renderer->render($node, $child_renderer);

		$element->setContents(
			$this->highlighter->highlight(
				$element->getContents(),
				$this->getSpecifiedLanguage($node)
			)
		);

		return $element;
	}

	/**
	 * Get the language specified in the info string.
	 *
	 * @param FencedCode $block The FencedCode block to get the language from
	 *
	 * @return ?string The language specified in the info string or null if none specified
	 */
	protected function getSpecifiedLanguage(FencedCode $block): ?string {
		$info_words = $block->getInfoWords();

		if (empty($info_words) || empty($info_words[0])) {
			return null;
		}

		return Xml::escape($info_words[0], true);
	}
}