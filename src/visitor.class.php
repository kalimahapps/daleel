<?php
namespace KalimahApps\Daleel;

use PhpParser\Node\NullableType;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Identifier;
use PhpParser\Node;
use PhpParser\Node\Stmt\{
	Class_,
	Function_,
	Namespace_,
	Interface_,
	Trait_,
	Use_,
	GroupUse,
	ClassConst,
	Property,
	ClassMethod
};
use PhpParser\NodeVisitorAbstract;
use phpDocumentor\Reflection\DocBlockFactory;
use PhpParser\Node\Scalar\{String_, LNumber, DNumber};
use PhpParser\Node\Expr\{ConstFetch, Array_, ClassConstFetch, UnaryMinus};
use PhpParser\Node\Expr\BinaryOp\BitwiseOr;

/**
 * Visitor class for PHPParser to extract data from PHP files.
 */
class Visitor extends NodeVisitorAbstract {
	/**
	 * @var array Array of classes
	 */
	private $classes = [];

	/**
	 * @var array Array of traits
	 */
	private $traits = [];

	/**
	 * @var array Array of interfaces
	 */
	private $interfaces = [];

	/**
	 * @var array Array of uses
	 */
	private $uses = [];

	/**
	 * @var
	 */
	private $functions = [];

	/**
	 * @var array Hierarchy tree of namespaces, classes, methods .. etc
	 */
	private $tree = [];

	/**
	 * @var DocBlockFactory Docblock factory dependency
	 */
	private $docblock_factory;

	/**
	 * @var string Current namespace
	 */
	private $namespace = '';

	/**
	 * @var
	 */
	private $is_namespaced = false;

	/**
	 * Constructor.
	 *
	 * @param DocBlockFactory $docblock_factory Docblock factory dependency
	 */
	public function __construct(DocBlockFactory $docblock_factory) {
		$this->docblock_factory = $docblock_factory;
	}

	/**
	 * Get the list of classes.
	 *
	 * @param Class_ $node Node to process
	 * @return
	 */
	private function getClassNode(Class_ $node) {
		// Ignore anonymous classes
		if (empty($node->name)) {
			return;
		}

		$name = $node->name->toString();

		$this->classes[$name] = [
			'constants'  => $this->processMembers($node->getConstants(), 'constant'),
			'properties' => $this->processMembers($node->getProperties(), 'property'),
			'methods'    => $this->processMethods($node->getMethods()),
			'flags'      => [
				'abstract' => $node->isAbstract(),
				'final'    => $node->isFinal(),
			],
		];

		// Get and parse docblock if exists
		$comment = $node->getDocComment();
		if ($comment) {
			$this->classes[$name]['docblock'] = $this->processDocBlock($comment->getText());
		}

		$extends    = $node->extends;
		$implements = $node->implements;
		$trait_uses = $node->getTraitUses();

		if ($extends) {
			$this->classes[$name]['extends'] = [$extends->toString()];
		}

		if ($implements) {
			$interfaces = [];
			foreach ($implements as $interface) {
				$interfaces[] = $interface->toString();
			}
			$this->classes[$name]['implements'] = $interfaces;
		}

		if ($trait_uses) {
			$traits = [];
			foreach ($trait_uses as $trait_use) {
				foreach ($trait_use->traits as $trait) {
					$traits[] = $trait->toString();
				}
			}

			$this->classes[$name]['traits_uses'] = $traits;
		}
	}

	/**
	 * Handle node on enter.
	 *
	 * @param Node $node Node to process
	 */
	public function leaveNode(Node $node) {
		if ($node instanceof Namespace_) {
			$this->is_namespaced = true;

			$parts = $node->name->getParts();

			$this->namespace = implode('\\', $parts);
		}

		if ($node instanceof Use_) {
			foreach ($node->uses as $use) {
				$this->uses[] = $use->name->toString();
			}
		}

		if ($node instanceof GroupUse) {
			$prefix = $node->prefix->toString();
			foreach ($node->uses as $use) {
				$this->uses[] = $prefix . '\\' . $use->name->toString();
			}
		}

		if ($node instanceof Class_) {
			$this->getClassNode($node);
			return;
		}

		if ($node instanceof Trait_) {
			$name = $node->name->toString();

			$this->traits[$name] = [
				'constants'  => $this->processMembers($node->getConstants(), 'constant'),
				'properties' => $this->processMembers($node->getProperties(), 'property'),
				'methods'    => $this->processMethods($node->getMethods()),
			];
			return;
		}

		if ($node instanceof Interface_) {
			$name = $node->name->toString();

			$this->interfaces[$name] = [
				'constants' => $this->processMembers($node->getConstants(), 'constant'),
				'methods'   => $this->processMethods($node->getMethods()),
			];
			return;
		}

		if ($node instanceof Function_) {
			$name    = $node->name->toString();
			$comment = $node->getDocComment();
			if ($comment) {
				$this->functions[$name]['docblock'] = $this->processDocBlock($comment->getText());
			}
		}
	}

	/**
	 * Clear data before traversing.
	 *
	 * @param array $nodes Nodes to process. Not used
	 */
	public function beforeTraverse(array $nodes) {
		$this->is_namespaced = false;
		$this->tree          = [];
		$this->classes       = [];
		$this->traits        = [];
		$this->interfaces    = [];
		$this->uses          = [];
		$this->functions     = [];
	}

	/**
	 * Handle node on leave.
	 *
	 * @param array $nodes Nodes to process
	 */
	public function afterTraverse(array $nodes) {
		// Make sure there is a namespace
		if ($this->is_namespaced === false) {
			$this->namespace = 'Global';
		}

		// Get namespace parts to build the hierarchy tree
		$parts = explode('\\', $this->namespace);

		/*
		* Convert namespace parts to a a nested array
		* e.g. ['Illuminate', 'Auth', 'Access'] becomes
		* [
		* 	'namepaces' => [
		* 		'Illuminate' => [
		* 			'namespaces' => [
		* 				'Auth' => [
		* 					'namespaces' => [
		* 						'Access' => [],
		* 					],
		* 				],
		* 			],
		* 		],
		* 	]
		* ]
		*/
		$namespace_tree = ['namespaces' => []];
		foreach ($parts as $part) {
			if (!isset($parent)) {
				$parent = &$namespace_tree;
			}
			$parent['namespaces'][$part] = [];
			$parent = &$parent['namespaces'][$part];
		}

		// Since $parent is a reference, at this point it is pointing to the last
		// namespace part. We can now add the classes, interfaces and traits to it.
		// Only add non-empty arrays
		$parent = array_filter([
				'classes'    => $this->classes,
				'interfaces' => $this->interfaces,
				'traits'     => $this->traits,
				'functions'  => $this->functions,
		]);

		if (!empty($parent)) {
			$this->tree = $namespace_tree;
		}
	}

	/**
	 * Get visibility of a node.
	 *
	 * @param ClassConst|ClassMethod|Property $node Node to process
	 *
	 * @return string Visibility of the node
	 */
	private function getVisibility(ClassConst|ClassMethod|Property $node): string {
		$visibility = 'public';
		if ($node->isPrivate()) {
			$visibility = 'private';
		} elseif ($node->isProtected()) {
			$visibility = 'protected';
		}
		return $visibility;
	}

	/**
	 * Process $node to get the default value.
	 *
	 * @param Node $node Node to process
	 * @return mixed     Default value
	 */
	private function getDefaultValue($node) {
		if ($node instanceof String_) {
			return "'$node->value'";
		}

		if ($node instanceof LNumber || $node instanceof DNumber) {
			return $node->value;
		}

		if ($node instanceof ConstFetch || $node instanceof ClassConstFetch) {
			return $node->name->toString();
		}

		if ($node instanceof Array_) {
			$items = $node->items;
			return count($items) > 0 ? 'array' : '[]';
		}

		if ($node instanceof BitwiseOr) {
			return 'int';
		}

		if ($node instanceof UnaryMinus) {
			return '-' . $this->getDefaultValue($node->expr);
		}

		if (empty($node)) {
			return 'null';
		}

		return 'unresolved';
	}

	/**
	 * Process object members (properties, constants).
	 *
	 * Extract properties and constants data such as visibility and docblock.
	 *
	 * @param Property[]|ClassConst[] $nodes Array of nodes
	 * @param string                  $type  Type of node (property or constant)
	 * @return array                         Array of properties
	 */
	private function processMembers($nodes, $type) {
		$members = [];
		foreach ($nodes as $node) {
			// Get name and default value
			if ($type === 'property') {
				$name                    = $node->props[0]->name->toString();
				$default_value           = $this->getDefaultValue($node->props[0]->default);
				$members[$name]['value'] = $default_value;
			} else {
				$name                    = $node->consts[0]->name->toString();
				$default_value           = $this->getDefaultValue($node->consts[0]->value);
				$members[$name]['value'] = $default_value;
			}

			// Get docblock data
			$members[$name]['docblock'] = [];

			$comment = $node->getDocComment();
			if ($comment) {
				$dockblock = $this->processDocBlock($comment->getText());

				// Aggregate description from summary, description and var tag
				$description = [];
				if (!empty($dockblock['summary'])) {
					$description[] = $dockblock['summary'];
				}

				if (!empty($dockblock['description'])) {
					$description[] = $dockblock['description'];
				}

				if (!empty($dockblock['tags']['var']['description'])) {
					$description[] = $dockblock['tags']['var']['description'];
				}

				$members[$name]['docblock'] = $dockblock;

				$members[$name]['docblock']['description'] = $description;
			}

			$members[$name]['flags'] = [
				'visibility' => $this->getVisibility($node),
			];

			if ($type === 'property') {
				$members[$name]['flags']['static']   = $node->isStatic();
				$members[$name]['flags']['readonly'] = $node->isReadonly();
			}

			// if docblock has deprecated tag, set deprecated flag to true
			if (isset($members[$name]['docblock']['tags']['deprecated'])) {
				// Add deprecated to the top of flags array
				$members[$name]['flags'] = array_merge(
					['deprecated' => true],
					$members[$name]['flags']
				);
			}
		}

		return $members;
	}

	/**
	 * Process object methods.
	 *
	 * Extract methods data such as visibility and docblock.
	 *
	 * @param ClassMethod[] $nodes Array of nodes
	 * @return array               Array of methods with extracted data
	 */
	private function processMethods(array $nodes) {
		$methods = [];
		foreach ($nodes as $node) {
			$name = $node->name->toString();

			// Get docblock data
			$methods[$name]['docblock'] = [];

			$comment = $node->getDocComment();
			if ($comment) {
				$methods[$name]['docblock'] = $this->processDocBlock($comment->getText());
			}

			$methods[$name]['flags'] = [
				'visibility' => $this->getVisibility($node),
				'static'     => $node->isStatic(),
				'abstract'   => $node->isAbstract(),
				'final'      => $node->isFinal(),
			];

			// if docblock has deprecated tag, set deprecated flag to true
			if (isset($methods[$name]['docblock']['tags']['deprecated'])) {
				// Add deprecated to the top of flags array
				$methods[$name]['flags'] = array_merge(
					['deprecated' => true],
					$methods[$name]['flags']
				);
			}

			// Extract params
			$params = $node->getParams();
			foreach ($params as $param) {
				$param_name = $param->var->name;

				$types      = '';
				$param_type = $param->type?->type ?? $param->type;

				if ($param_type instanceof FullyQualified) {
					$types = implode('\\', $param_type->parts);
				} else if ($param_type instanceof Identifier) {
					$types = $param_type->name;
				}

				// if (empty($types)) {
				// 	$types = $param->type->name ?? '';
				// 	$types = explode('|', $types);
				// }
				$methods[$name]['params'][$param_name] = [
					'types'    => explode('|', $types),
					'byRef'    => $param->byRef,
					'variadic' => $param->variadic,
					'nullable' => $param->type instanceof NullableType,
				];
			}
		}

		return $methods;
	}

	/**
	 * Process docblock.
	 *
	 * Extract docblock data such as summary, description and tags.
	 *
	 * @param string $docblock string Docblock to process
	 * @return array           Array of docblock data
	 */
	private function processDocBlock(string $docblock) {
		$docblock = $this->docblock_factory->create($docblock);

		$parse_docblock = new ParseDocBlock($docblock, $this->uses);
		return $parse_docblock->getParsedDocblockData();
	}

	/**
	 * Get the hierarchy tree.
	 */
	public function getTree() {
		return $this->tree;
	}

	/**
	 * Get the list of namespaces.
	 *
	 * This will return a list of objects (classes, traits, interfaces) with their
	 * full namespace.
	 */
	public function getNamespaces() {
		$classes    = array_keys($this->classes);
		$traits     = array_keys($this->traits);
		$interfaces = array_keys($this->interfaces);

		$objects = array_merge($classes, $traits, $interfaces);

		$list = [];
		foreach ($objects as $object) {
			$list[] = $this->namespace . '\\' . $object;
		}

		return $list;
	}
}