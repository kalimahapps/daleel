<?php
namespace KalimahApps\Daleel\Containers;

use KalimahApps\Daleel\Containers\Division;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Xml\XmlNodeRendererInterface;
use League\Config\{ConfigurationInterface, ConfigurationAwareInterface};

/**
 * Render container block to HTML.
 */
final class DivisionRenderer implements NodeRendererInterface, XmlNodeRendererInterface, ConfigurationAwareInterface {
	/**
	 * @var
	 */
	private ConfigurationInterface $config;

	/**
	 * @var
	 */
	private string $class_name = '';

	/**
	 * Render a Division node to HTML.
	 *
	 * @param Node                       $node           The node to render
	 * @param ChildNodeRendererInterface $child_renderer The renderer object for child nodes
	 */
	public function render(Node $node, ChildNodeRendererInterface $child_renderer) {
		Division::assertInstanceOf($node);

		// Get list of default classes and titles
		$defaults = $this->config->get('container/default_titles') ?? [];

		// Get class name from attributes
		$this->class_name = $node->data->get('class_name', '');
		$default_title    = $defaults[$this->class_name] ?? '';

		$attrs          = $node->data->get('attributes');
		$attrs['class'] = 'custom-container ';
		if ($this->class_name !== '') {
			$attrs['class'] = $attrs['class'] . $this->class_name;
		}

		// Create a div for the title
		$title = $node->data->get('container_title');
		$title = $title === '' ? $default_title : $title;

		$title_div = '';
		if ($title !== '') {
			$title_div = new HtmlElement('div', array('class' => 'custom-container-title'), $title);
		}

		// Create a div for the content
		$content_div = new HtmlElement(
			'div',
			array('class' => 'custom-container-content'),
			$child_renderer->renderNodes($node->children())
			);

		// Create a div for the container
		$container_div = new HtmlElement('div', $attrs, "{$title_div}{$content_div}");

		return $container_div;
	}

	/**
	 * Get the XML tag name for this node.
	 *
	 * @param Node $node The node to get the XML tag name for
	 */
	public function getXmlTagName(Node $node): string {
		return 'div';
	}

	/**
	 * Get an array of XML attributes for this node.
	 *
	 * @param Node $node The node to get XML attributes for
	 */
	public function getXmlAttributes(Node $node): array {
		return '' !== $this->class_name ? array('class' => $this->class_name) : [];
	}

	/**
	 * Set the configuration object.
	 *
	 * @param ConfigurationInterface $configuration The configuration object
	 */
	public function setConfiguration(ConfigurationInterface $configuration): void {
		$this->config = $configuration;
	}
}