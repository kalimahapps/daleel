<?php

namespace KalimahApps\Daleel\CodeHighlighter;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\{FencedCode, IndentedCode};
use League\CommonMark\Extension\ExtensionInterface;

/**
 * Handle code highlight.
 */
class CodeHighlighterExtension implements ExtensionInterface {
	/**
	 * Register the extension.
	 *
	 * @param EnvironmentBuilderInterface $environment Environment builder
	 * @return void
	 */
	public function register(EnvironmentBuilderInterface $environment): void {
		$environment
		->addRenderer(FencedCode::class, new FencedCodeRenderer(), 10)
		->addRenderer(IndentedCode::class, new IndentedCodeRenderer(), 10);
	}
}