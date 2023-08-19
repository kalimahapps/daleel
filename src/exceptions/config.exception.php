<?php
namespace KalimahApps\Daleel\Exceptions;

/**
 * Custom exception class for config file errors.
 */
class ConfigException extends \Exception {
	/**
	 * Redefine the exception so message isn't optional.
	 *
	 * @param string         $message  Exception message to throw
	 * @param integer        $code     Exception code
	 * @param Throwable|null $previous Previous exception if nested exception
	 */
	public function __construct($message, $code = 0, \Throwable $previous = null) {
		parent::__construct($message, $code, $previous);
	}

	/**
	 * custom string representation of object.
	 */
	public function toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}