<?php
namespace KalimahApps\Daleel\Containers;

use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\MarkdownParserStateInterface;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;

/**
 * This class is the return for `createBlockStartParser` method in `ContainerParser` class.
 */
class BlockParser implements BlockStartParserInterface {
	/**
	 * Check whether we should handle the block at the current position.
	 *
	 * @param Cursor                       $cursor       A cloned copy of the cursor at the current parsing location
	 * @param MarkdownParserStateInterface $parser_state Additional information about the state of the Markdown parser
	 * @return BlockStart                                The BlockStart that has been identified, or null if the block doesn't match here
	 */
	public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parser_state): ?BlockStart {
		if ($cursor->getNextNonSpaceCharacter() !== ':') {
			return BlockStart::none();
		}

		$match = $cursor->match('/^[\s\t]*:{3}\s*/u');

		if (null === $match) {
			return BlockStart::none();
		}

		$class_name = '';
		$title      = '';
		if (false === $cursor->isAtEnd()) {
			$reminder = $cursor->getRemainder();

			// Extract class and title from the reminder
			$match_string = preg_match('/^(\w+)(.*)/u', $reminder, $matches);
			if ($match_string) {
				$class_name = $matches[1];
				$title      = trim($matches[2]);
			}
			$cursor->advanceToEnd();
		}

		$container_parser = new ContainerParser($class_name, $title, $match);
		return BlockStart::of($container_parser)->at($cursor);
	}
}