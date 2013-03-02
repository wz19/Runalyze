<?php

require_once dirname(__FILE__) . '/../../../inc/import/class.ParserPWX.php';

/**
 * Test class for ParserPWX.
 * Generated by PHPUnit on 2013-01-10 at 23:42:48.
 */
class ParserPWXTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ParserPWX
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {}

	/**
	 * Test: incorrect xml-file 
	 */
	public function test_empty() {
		$Parser = new ParserPWX('');
		$Parser->parseTraining();

		$this->assertFalse( $Parser->worked() );
		$this->assertNotEmpty( $Parser->getErrors() );
	}

	/**
	 * Test: incorrect xml-file 
	 */
	public function test_notPWX() {
		$XML = '<any><xml><file></file></xml></any>';

		$Parser = new ParserPWX($XML);
		$Parser->parseTraining();

		$this->assertFalse( $Parser->worked() );
		$this->assertNotEmpty( $Parser->getErrors() );
	}

	/**
	 * Test: standard file
	 * Filename: "without-dist.pwx" 
	 */
	public function test_withoutDist() {
		$XML    = file_get_contents(FRONTEND_PATH.'../tests/testfiles/pwx/without-dist.pwx');
		$Parser = new ParserPWX($XML);
		$Parser->parseTraining();

		$this->assertTrue( $Parser->worked() );
		$this->assertEquals('10.02.2009', $Parser->get('datum'));
		$this->assertEquals('06:15', $Parser->get('zeit'));
		$this->assertEquals('Stuart', $Parser->get('comment'));
		$this->assertEquals("Apple, iPhone (SERIAL_NUMBER)", $Parser->get('creator_details'));
		$this->assertTrue( Validator::isClose($Parser->get('s'), 1646) );
		$this->assertTrue( Validator::isClose($Parser->get('distance'), 4.891) );
	}

	/**
	 * Test: standard file
	 * Filename: "with-dist.pwx" 
	 */
	public function test_withDist() {
		$XML    = file_get_contents(FRONTEND_PATH.'../tests/testfiles/pwx/with-dist.pwx');
		$Parser = new ParserPWX($XML);
		$Parser->parseTraining();

		$this->assertTrue( $Parser->worked() );
		$this->assertEquals('16.11.2008', $Parser->get('datum'));
		$this->assertEquals('11:40', $Parser->get('zeit'));
		$this->assertEquals('Blue Sky trail with Dan and Ian', $Parser->get('comment'));
		$this->assertEquals("Garmin, Edge 205/305 (EDGE305 Software Version 3.20)", $Parser->get('creator_details'));
		$this->assertTrue( Validator::isClose($Parser->get('s'), 6978) );
		$this->assertTrue( Validator::isClose($Parser->get('distance'), 16.049) );
	}

	/**
	 * Test: standard file
	 * Filename: "with-dist-and-hr.pwx" 
	 */
	public function test_withDistAndHr() {
		$XML    = file_get_contents(FRONTEND_PATH.'../tests/testfiles/pwx/with-dist-and-hr.pwx');
		$Parser = new ParserPWX($XML);
		$Parser->parseTraining();

		$this->assertTrue( $Parser->worked() );
		$this->assertEquals('02.11.2008', $Parser->get('datum'));
		$this->assertEquals('09:08', $Parser->get('zeit'));
		$this->assertEquals('Loveland-Estes-Loveland.. ok, not quite all the way to Estes, but made it to the top of the hardest climb', $Parser->get('comment'));
		$this->assertEquals("Garmin, Edge 205/305 (EDGE305 Software Version 3.20)", $Parser->get('creator_details'));
		$this->assertTrue( Validator::isClose($Parser->get('s'), 13539) );
		$this->assertTrue( Validator::isClose($Parser->get('distance'), 89.535) );
		$this->assertTrue( Validator::isClose($Parser->get('pulse_avg'), 146) );
		$this->assertTrue( Validator::isClose($Parser->get('pulse_max'), 174) );
	}

}

?>