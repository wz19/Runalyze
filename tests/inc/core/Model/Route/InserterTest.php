<?php

namespace Runalyze\Model\Route;

use PDO;

class InvalidInserterObjectForRoute_MockTester extends \Runalyze\Model\Object {
	public function properties() {
		return array('foo');
	}
}

/**
 * Generated by hand
 */
class InserterTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \PDO
	 */
	protected $PDO;

	protected function setUp() {
		$this->PDO = new PDO('sqlite::memory:');
		$this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->PDO->exec('CREATE TABLE IF NOT EXISTS `'.PREFIX.'route` (
			`id` INTEGER PRIMARY KEY AUTOINCREMENT,
			`accountid` INT NOT NULL,
			`name` VARCHAR( 255 ) NOT NULL,
			`cities` VARCHAR( 255 ) NOT NULL,
			`distance` DECIMAL( 6, 2 ) NOT NULL,
			`elevation` SMALLINT NOT NULL,
			`elevation_up` SMALLINT NOT NULL,
			`elevation_down` SMALLINT NOT NULL,
			`lats` LONGTEXT NOT NULL,
			`lngs` LONGTEXT NOT NULL,
			`elevations_original` LONGTEXT NOT NULL,
			`elevations_corrected` LONGTEXT NOT NULL,
			`elevations_source` VARCHAR( 255 ) NOT NULL,
			`startpoint_lat` FLOAT( 8, 5 ) NOT NULL,
			`startpoint_lng` FLOAT( 8,  5 ) NOT NULL,
			`endpoint_lat` FLOAT( 8, 5 ) NOT NULL,
			`endpoint_lng` FLOAT( 8, 5 ) NOT NULL,
			`min_lat` FLOAT( 8, 5 ) NOT NULL,
			`min_lng` FLOAT( 8, 5 ) NOT NULL,
			`max_lat` FLOAT( 8, 5 ) NOT NULL,
			`max_lng` FLOAT( 8, 5 ) NOT NULL,
			`in_routenet` TINYINT( 1 ) NOT NULL
			);
		');
	}

	protected function tearDown() {
		$this->PDO->exec('DROP TABLE `'.PREFIX.'route`');
	}

	/**
	 * @expectedException \PHPUnit_Framework_Error
	 */
	public function testWrongObject() {
		new Inserter($this->PDO, new InvalidInserterObjectForRoute_MockTester);
	}

	public function testSimpleInsert() {
		$R = new Object(array(
			Object::NAME => 'Test route',
			Object::DISTANCE => 3.14,
			Object::LATITUDES => array(47.7, 47.8),
			Object::LONGITUDES => array(7.8, 7.7)
		));

		$I = new Inserter($this->PDO, $R);
		$I->setAccountID(1);
		$I->insert();

		$data = $this->PDO->query('SELECT * FROM `'.PREFIX.'route` WHERE `accountid`=1')->fetch(PDO::FETCH_ASSOC);
		$N = new Object($data);

		$this->assertEquals(1, $data[Inserter::ACCOUNTID]);
		$this->assertEquals('Test route', $N->name());
		$this->assertTrue($N->hasID());
		$this->assertTrue($N->hasPositionData());
		$this->assertEquals(47.7, $N->get(Object::MIN_LATITUDE));
	}

	public function testElevationCalculation() {
		$R = new Object(array(
			Object::ELEVATIONS_CORRECTED => array(100, 120, 110)
		));

		$I = new Inserter($this->PDO, $R);
		$I->setAccountID(1);
		$I->insert();

		$data = $this->PDO->query('SELECT * FROM `'.PREFIX.'route` WHERE `accountid`=1')->fetch(PDO::FETCH_ASSOC);
		$N = new Object($data);

		$this->assertGreaterThan(0, $N->elevation());
		$this->assertGreaterThan(0, $N->elevationUp());
		$this->assertGreaterThan(0, $N->elevationDown());
	}

}
