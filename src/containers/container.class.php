<?php
namespace KalimahApps\Daleel\Containers;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

/**
 * Container extension for CommonMark.
 * This extension adds support for custom containers.
 * @example
 * ```
 * ::: container-name Title
 * This is a container.
 * :::
 */
final class ContainerExtension implements ConfigurableExtensionInterface {
	/**
	 * Set the configuration for this extension.
	 *
	 * @param ConfigurationBuilderInterface $builder The builder is used to configure the extension
	 * @return void
	 */
	public function configureSchema(ConfigurationBuilderInterface $builder): void {
		$builder->addSchema('container',
			Expect::structure(array(
					'default_titles' => Expect::[],
				))
		);
	}

	/**
	 * Register the extension with the environment.
	 *
	 * @param EnvironmentBuilderInterface $environment The environment is used to register additional functionality
	 * @return void
	 */
	public function register(EnvironmentBuilderInterface $environment): void {
		$environment->addBlockStartParser(ContainerParser::createBlockStartParser())
		->addRenderer(Division::class, new DivisionRenderer());
	}
}