<?php
namespace KalimahApps\Daleel;
/**
 *
 */
abstract class Testing {
	readonly public string $test;
	abstract public function test();
}

trait TestTrait{
	public function testTrait(){
		return 'test';
	}
}

interface TestInterface{
	public function testInterfaceMethod();
}

/**
 * Tester class to extend
 */
final class Tester extends Testing implements TestInterface {
	use TestTrait;

	public function test() {
		return 'test';
	}

	public function testInterfaceMethod(){
		return 'test';
	}

	/**
	 * Undocumented function
	 *
	 * @deprecated 1.0.0 Use test() instead
	 * @excpetion Exception Test exception
	 * @return void
	 */
	private function testDeprecated(){

	}
}