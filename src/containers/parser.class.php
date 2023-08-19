<?php
namespace KalimahApps\Daleel\Containers;

use KalimahApps\Daleel\Containers\BlockParser;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;

/**
 * Parse custom container.
 * They are defined by a delimiter of three or more colons.
 *
 * @example
 * ```
 * ::: container-name Title
 * This is a container.
 * :::
 * ```
 */
final class ContainerParser extends AbstractBlockContinueParser {
	/**
	 * @var Division $block Div block to be built
	 */
	private Division $block;

	/**
	 * @var string $delim Delimiter character for this block
	 */
	private string $delim;

	/**
	 * @var string $class_name Class name for this block
	 */
	private string $class_name;

	/**
	 * @var string Title for this block
	 */
	private string $title;

	/**
	 * ContainerParser constructor.
	 *
	 * @param string $class_name Class name for this block
	 * @param string $title      Title for this block
	 * @param string $delim      Delimiter character for this block
	 */
	public function __construct(string $class_name, string $title, string $delim) {
		$this->block      = new Division();
		$this->delim      = $delim;
		$this->title      = $title;
		$this->class_name = $class_name;
	}

	/**
	 * Instantiate a new instance of the BlockStartParserInterface.
	 */
	public static function createBlockStartParser(): BlockStartParserInterface {
		return new BlockParser();
	}

	/**
	 * Parse the given line.
	 *
	 * @param Cursor                       $cursor              Cursor to parse
	 * @param BlockContinueParserInterface $active_block_parser The block parser for the active block
	 * @return BlockContinue                                    Returns `BlockContinue::at($cursor)` if the block is still open,
	 */
	public function tryContinue(Cursor $cursor, BlockContinueParserInterface $active_block_parser): ?BlockContinue {
		$cursor->advanceToNextNonSpaceOrTab();

		if ($this->delim === $cursor->getRemainder()) {
			return BlockContinue::finished();
		}

		return BlockContinue::at($cursor);
	}

	/**
	 * Handle when the container is closed.
	 */
	public function closeBlock(): void {
		$this->block->data->set('class_name', $this->class_name);
		$this->block->data->set('container_title', $this->title);
	}

	/**
	 * Get the block type that this parser understands.
	 */
	public function getBlock(): AbstractBlock {
		return $this->block;
	}

	/**
	 * Set container type to this block.
	 */
	public function isContainer(): bool {
		return true;
	}

	/**
	 * Set the container to be able to contain other blocks.
	 *
	 * @param AbstractBlock $child_block Child block to be contained
	 * @return bool
	 */
	public function canContain(AbstractBlock $child_block): bool {
		return true;
	}
}