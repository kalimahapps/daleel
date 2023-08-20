<?php

namespace KalimahApps\Daleel;

use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;

/**
 * Make sure images paths are valid.
 */
final class ImagePathExtension  implements ExtensionInterface {
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
		foreach ($event->getDocument()->iterator() as $image) {
			$is_image_node = $image instanceof Image;
			if (!$is_image_node) {
				continue;
			}

			$url = $image->getUrl();

			// ignore if does not start with /
			if (strpos($url, '/') !== 0) {
				continue;
			}

			// Remove leading slash
			$url = ltrim($url, '/');

			// Add version and other required path segments
			$url = Common::prepareLink(explode('/', $url), null, false);

			$image->setUrl($url);
		}
	}
}