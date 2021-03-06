<?php

namespace Runalyze\Parameter;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2014-09-15 at 18:54:39.
 */
class ParameterIntTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \Runalyze\Parameter\Int
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = new Int(0);
	}

	public function testValues() {
		$this->assertEquals(0, $this->object->value());
		$this->assertEquals('0', $this->object->valueAsString());

		$this->object->set(5);
		$this->assertEquals(5, $this->object->value());
		$this->assertEquals('5', $this->object->valueAsString());

		$this->object->setFromString('17');
		$this->assertEquals(17, $this->object->value() );
	}

}
