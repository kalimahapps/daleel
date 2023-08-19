<?php
namespace KalimahApps\Daleel\Commands;

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Extend SymfonyStyle to add custom methods.
 */
class ConsoleStyle extends SymfonyStyle {
	/**
	 * Override success message.
	 *
	 * @param string|array $message Message to display
	 * @param int          $lines   Number of lines to add before message
	 */
	public function success(string|array $message, int $lines = 1) {
		$this->newLine($lines);
		parent::success($message);
	}

	/**
	 * Create a subtitle with a blue bullet.
	 *
	 * @param string $message    Message to display
	 * @param int    $pre_lines  Number of lines to add before message
	 * @param int    $post_lines Number of lines to add after message
	 */
	public function subTitle(string $message, int $pre_lines = 1, int $post_lines = 0) {
		$this->newLine($pre_lines);
		$style = 'fg=blue';
		$this->write(sprintf('<%s>◦ %s</>', $style, $message));
		$this->newLine($post_lines);
	}
}