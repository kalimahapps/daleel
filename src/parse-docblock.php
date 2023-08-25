<?php
namespace KalimahApps\Daleel;

use League\CommonMark\CommonMarkConverter;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\InvalidTag;
use KalimahApps\Daleel\Common;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;

/**
 * Parse docblock data.
 *
 * Extract summary, description, tags, paramaters .. etc
 */
class ParseDocBlock {
	/**
	 * @var array $this->docblock_data Docblock data
	 */
	private $docblock_data = [];

	/**
	 * Reference to current tag.
	 *
	 * @var object $currentTag Current tag
	 */
	private $current_tag;

	/**
	 * Reference to CommonMarkConverter object.
	 *
	 * @var CommonMarkConverter $converter CommonMarkConverter object
	 */
	private CommonMarkConverter $converter;

	/**
	 * @var array $uses Array of uses in current file
	 */
	private array $uses;

	/**
	 * Initiate docblock parsing.
	 *
	 * @param DocBlock $docblock Docblock object
	 * @param array    $uses     Array of uses in current file
	 */
	public function __construct(DocBlock $docblock, array $uses) {
		$this->converter = new CommonMarkConverter();
		$this->uses      = $uses;

		$summary = $docblock->getSummary();
		$summary = $this->converter->convert($summary);

		$description = $docblock->getDescription()->render();
		$description = $this->converter->convert($description);

		$this->docblock_data = [
			'summary'     => $this->unwrapParagraphs($summary),
			'description' => $this->unwrapParagraphs($description),
			'tags'        => [],
		];

		$tags = $docblock->getTags();
		foreach ($tags as $tag) {
			$this->current_tag = $tag;

			$tag_name = $tag->getName();

			if ($this->current_tag instanceof InvalidTag) {
				continue;
			}

			match($tag_name) {
				'param'                        => $this->processParamTag(),
				'var', 'return', 'throws'      => $this->processVarReturnThrowsTag(),
				'deprecated', 'since'          => $this->processDeprecatedTag(),
				'internal', 'ignore', 'access' => $this->processDescriptionTag(),
				'link'                         => $this->processLinkTag(),
				'see'                          => $this->processSeeTag(),
				'example'                      => $this->processExampleTag(),
				default                        => $this->processDefaultTag(),
			};
		}
	}

	/**
	 * Default tag processor.
	 *
	 * It is empty because all the tags that need to be
	 * parsed are handled in the match expression.
	 */
	private function processDefaultTag() {
		$tag_name = $this->current_tag->getName();
		// Common::debug("Unhandled tag: {$tag_name}");
	}

	/**
	 * Attempt to resolve type from the list of uses.
	 *
	 * @param string $type Type to resolve
	 * @return string      Resolved type if found, otherwise the original type
	 */
	private function resolveType(string $type) {
		if (count($this->uses) === 0) {
			return $type;
		}

		// if type is already resolved (contains `\`) then return it
		if (strpos($type, '\\') === false) {
			return $type;
		}

		// Attempt to resolve type from the list of uses
		foreach ($this->uses as $use) {
			$has_type = str_ends_with($use, $type);
			if ($has_type) {
				return $use;
			}
		}

		return $type;
	}

	/**
	 * Get converted description.
	 */
	private function getDescription() {
		$description = $this->current_tag->getDescription()?->render() ?? '';
		$description = $this->converter->convert($description);
		return $this->unwrapParagraphs($description);
	}

	/**
	 * Remove `<p>` and `</p>` tags from description.
	 *
	 * @param string $description Description
	 * @return
	 */
	private function unwrapParagraphs(string $description) {
		$description = preg_replace('/<p>(.*?)<\/p>/', '$1', $description);
		return $description;
	}

	/**
	 * Process `@param` tag.
	 */
	private function processParamTag() {
		$tag_type = $this->current_tag?->getType()?->__toString();
		if (empty($tag_type)) {
			$tag_type = 'mixed';
		}

		$name  = $this->current_tag->getVariableName();
		$types = explode('|', $tag_type);
		foreach ($types as $key => $type) {
			$types[$key] = $this->resolveType($type);
		}

		$this->docblock_data['tags']['params'][$name] = [
			'types'       => $types,
			'description' => $this->getDescription(),
		];
	}

	/**
	 * Process `@example` tag.
	 */
	private function processExampleTag() {
		$tag_name = $this->current_tag->getName();

		$this->docblock_data['tags'][$tag_name] = [
			'description' => $this->getDescription(),
		];
	}

	/**
	 * Process `@var`, `@return`, `@throws` tags.
	 */
	private function processVarReturnThrowsTag() {
		$tag_name = $this->current_tag->getName();
		$tag_type = $this->current_tag?->getType()?->__toString() ?? 'mixed';

		$types = explode('|', $tag_type);
		foreach ($types as $key => $type) {
			$types[$key] = $this->resolveType($type);
		}
		$this->docblock_data['tags'][$tag_name] = [
			'types'       => $types,
			'description' => $this->getDescription(),
		];
	}

	/**
	 * Process `@deprecated` tag.
	 */
	private function processDeprecatedTag() {
		$tag_name = $this->current_tag->getName();

		$this->docblock_data['tags'][$tag_name] = [
			'version'     => $this->current_tag->getVersion(),
			'description' => $this->getDescription(),
		];
	}

	/**
	 * Process `@internal`, `@ignore`, `@access` tags.
	 */
	private function processDescriptionTag() {
		$tag_name = $this->current_tag->getName();

		$this->docblock_data['tags'][$tag_name] = [
			'description' => $this->getDescription(),
		];
	}

	/**
	 * Process `@link` tag.
	 */
	private function processLinkTag() {
		$tag_name = $this->current_tag->getName();

		$this->docblock_data['tags'][$tag_name] = [
			'link'        => $this->current_tag->getLink(),
			'description' => $this->getDescription(),
		];
	}

	/**
	 * Process `@see` tag.
	 */
	private function processSeeTag() {
		$tag_name = $this->current_tag->getName();

		$reference = $this->current_tag->getReference();
		if ($reference instanceof Url) {
			$reference = $reference->__toString();
		}

		$description = $this->getDescription();
		if (empty($description)) {
			$description = $reference;
		}

		$this->docblock_data['tags'][$tag_name][] = [
			'link'        => $reference,
			'description' => $this->getDescription(),
		];
	}

	/**
	 * Get docblock data after parsing.
	 *
	 * @return array Parsed docblock data
	 */
	public function getParsedDocblockData() {
		return $this->docblock_data;
	}
}