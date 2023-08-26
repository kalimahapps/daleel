<?php

namespace KalimahApps\Daleel\CodeHighlighter;

use DomainException;
use Highlight\Highlighter;
use function HighlightUtilities\splitCodeIntoArray;

/**
 * Handle code highlighting.
 */
class CodeHighlighter {
	/**
	 * Highlighter instance.
	 *
	 * @var Highlighter $highlighter Highlighter instance
	 */
	protected Highlighter $highlighter;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->highlighter = new Highlighter();

		$this->highlighter->setAutodetectLanguages(['php']);
	}

	/**
	 * Highlight a code string.
	 *
	 * @param string $code_block Code block to highlight
	 * @param string $info_line  Language to use for highlighting
	 * @return string            Highlighted code block
	 */
	public function highlight(string $code_block, ?string $info_line = null) {
		$code_block_without_tags = strip_tags($code_block);
		$contents                = htmlspecialchars_decode($code_block_without_tags);

		$definition = $this->parseLangAndLines($info_line);
		$language   = $definition['lang'];

		try {
			$result = $language
				? $this->highlighter->highlight($language, $contents)
				: $this->highlighter->highlightAuto($contents);

			$code = $result->value;

			if (count($definition['lines']) > 0) {
				$loc = splitCodeIntoArray($code);

				foreach ($loc as $index => $line) {
					$loc[$index] = vsprintf('<span class="line%s">%s</span>', [
						isset($definition['lines'][$index + 1]) ? ' highlighted' : '',
						$line,
					]);
				}

				$code = implode('', $loc);
			}

			return vsprintf('<code class="%s hljs %s" data-lang="%s">%s</code>', [
				'language-' . ($language ? $language : $result->language),
				$result->language,
				$language ? $language : $result->language,
				$code,
			]);
		} catch (DomainException $e) {
			return vsprintf('<code class="%s hljs %s" data-lang="%s">%s</code>', [
				"language-{$language}",
				$language,
				$language,
				$code_block,
			]);
		}
	}

	/**
	 * Parse the language and line numbers from the info string.
	 *
	 * @param string $language Info string
	 * @return array           Parsed language and line numbers
	 */
	private function parseLangAndLines(?string $language) {
		$parsed = [
			'lang'  => $language,
			'lines' => [],
		];

		if ($language === null) {
			return $parsed;
		}

		$brace_pos = strpos($language, '{');

		if ($brace_pos === false) {
			return $parsed;
		}

		$parsed['lang'] = substr($language, 0, $brace_pos);
		$line_def       = substr($language, $brace_pos + 1, -1);
		$line_numbers   = explode(',', $line_def);

		foreach ($line_numbers as $line_num) {
			if (strpos($line_num, '-') === false) {
				$parsed['lines'][intval($line_num) ] = true;

				continue;
			}

			$extremes = explode('-', $line_num);

			if (count($extremes) !== 2) {
				continue;
			}

			$start = intval($extremes[0]);
			$end   = intval($extremes[1]);

			for ($index = $start; $index <= $end; $index++) {
				$parsed['lines'][$index] = true;
			}
		}

		return $parsed;
	}
}