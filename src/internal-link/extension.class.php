<?php

namespace KalimahApps\Daleel;

use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;

/**
 * Make sure internal links are valid.
 */
final class InternalLinkExtension  implements ExtensionInterface {
	/**
	 * Register extension.
	 *
	 * @param EnvironmentBuilderInterface $environment Environment builder
	 */
	public function register(EnvironmentBuilderInterface $environment): void {
		$environment->addEventListener(DocumentParsedEvent::class, array($this, 'documentCallback'), 0);
	}

	/**
	 * Callback for DocumentParsedEvent.
	 *
	 * @param DocumentParsedEvent $event Event
	 */
	public function documentCallback(DocumentParsedEvent $event): void {
		foreach ($event->getDocument()->iterator() as $link) {
			$is_link_node = $link instanceof Link;
			if (!$is_link_node) {
				continue;
			}

			$url = $link->getUrl();

			// ignore if does not start with /
			if (strpos($url, '/') !== 0) {
				continue;
			}

			$matches = array();
			preg_match('/#(.*)$/', $url, $matches);

			if (count($matches) > 0) {
				$anchor = $matches[1];
			}

			// Remove anchor
			$url = preg_replace('/#(.*)$/', '', $url);

			// Remove .md, .html extensions if present
			$url = preg_replace('/\.(md|html)$/', '', $url);

			// Remove leading slash
			$url = ltrim($url, '/');

			// Add version and other required path segments
			$url = Common::prepareLink(explode('/', $url));

			if (isset($anchor)) {
				$url .= "#{$anchor}";
			}

			$link->setUrl($url);
		}
	}
}