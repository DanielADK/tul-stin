<?php

namespace Model;

use Exception;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\SQLiteConnectionBuilder;
use StinWeatherApp\Model\Place;

class PlaceTest extends TestCase {

	public function testConstructorAndGetters() {
		$place = new Place('Test Place', 50.0874654, 14.4212535);

		$this->assertEquals('Test Place', $place->getName());
		$this->assertEquals(50.0874654, $place->getLatitude());
		$this->assertEquals(14.4212535, $place->getLongitude());
	}

	public function testSetters() {
		$place = new Place('Test Place', 50.0874654, 14.4212535);

		$place->setName('New Test Place');
		$place->setLatitude(51.0874654);
		$place->setLongitude(15.4212535);

		$this->assertEquals('New Test Place', $place->getName());
		$this->assertEquals(51.0874654, $place->getLatitude());
		$this->assertEquals(15.4212535, $place->getLongitude());
	}

	public function testPersistWithNewPlace() {
		$place = new Place('Test Place', 50.0874654, 14.4212535);

		// Assuming that the place does not exist in the database
		$this->assertTrue($place->persist());
	}

	public function testPersistWithExistingPlace() {
		$place = new Place('Test Place', 50.0874654, 14.4212535);
		$this->assertTrue($place->persist());

		// Assuming that the place already exists in the database
		$this->expectException(Exception::class);
		$place->persist();
	}

	public function testPlaceCanBeRetrievedById(): void {
		$place = new Place('Test Place', 50.0874654, 14.4212535);
		$place->persist();

		$retrievedPlace = Place::getById($place->getName());

		$this->assertInstanceOf(Place::class, $retrievedPlace);
		$this->assertEquals($place->getName(), $retrievedPlace->getName());
		$this->assertEquals($place->getLatitude(), $retrievedPlace->getLatitude());
		$this->assertEquals($place->getLongitude(), $retrievedPlace->getLongitude());
	}

	public function testPlaceCannotBeRetrievedByNonExistentId(): void {
		$retrievedPlace = Place::getById('Non Existent Place');

		$this->assertNull($retrievedPlace);
	}

	/**
	 * @throws \Exception
	 */
	protected function setUp(): void {
		parent::setUp();

		// Make Database in-memory connection
		$conn = new SQLiteConnectionBuilder();
		$conn->setDatabase(':memory:');
		$conn->buildConnection();
		Db::connect($conn);

		// Create the place table
		Db::execute("create table place (
			    name      text   not null
			        constraint place_pk
			            primary key,
			    latitude  double not null,
			    longitude double not null
			);
			
			create unique index place_name_uindex
			    on place (name);
		");

	}

}