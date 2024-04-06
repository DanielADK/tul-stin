<?php

namespace Model\Buyable;

use Exception;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\SQLiteConnectionBuilder;
use StinWeatherApp\Model\Buyable\Premium;
use StinWeatherApp\Model\Types\Currency;

class PremiumTest extends TestCase {

	private Premium $premium;

	public static function setUpBeforeClass(): void {
		// Make Database in-memory connection
		$conn = new SQLiteConnectionBuilder();
		$conn->setDatabase(':memory:');
		$conn->buildConnection();
		Db::connect($conn);

		// Create the premium table
		Db::execute("
	        CREATE TABLE premium (
	            id INTEGER PRIMARY KEY,
	            name TEXT,
	            price REAL,
	            duration INTEGER,
	            currency TEXT
	        )
	    ");

		error_reporting(E_ALL);
	}

	public function testGetSetPrice(): void {
		$this->premium->setPrice(200);
		$this->assertEquals(200, $this->premium->getPrice());
	}

	public function testGetSetName(): void {
		$this->premium->setName('Super Premium Plan');
		$this->assertEquals('Super Premium Plan', $this->premium->getName());
	}

	public function testGetSetDuration(): void {
		$this->premium->setDuration(7200);
		$this->assertEquals(7200, $this->premium->getDuration());
	}

	public function testGetSetId(): void {
		$this->premium->setId(1);
		$this->assertEquals(1, $this->premium->getId());
	}

	public function testGetSetCurrency(): void {
		$this->premium->setCurrency(Currency::EUR);
		$this->assertEquals(Currency::EUR, $this->premium->getCurrency());
		$this->assertEquals("EUR", $this->premium->getCurrencyString());
	}

	/**
	 * @throws Exception
	 */
	public function testPersistAndGetById(): void {
		$this->premium = new Premium('Premium Plan', 100, 3600, Currency::CZK);
		@$result = $this->premium->persist();
		$this->assertTrue($result);

		$this->premium = Premium::getById(1);
		$this->assertInstanceOf(Premium::class, $this->premium);

		// Persist existing
		$newName = 'Super Premium Plan';
		$this->premium->setName($newName);

		@$result = $this->premium->persist();
		$this->assertTrue($result);

		// Check
		$this->premium = Premium::getById(1);
		$this->assertInstanceOf(Premium::class, $this->premium);
		$this->assertSame($newName, $this->premium->getName());
	}

	#[Depends('testPersistAndGetById')]
	public function testGetListOfPremiums(): void {
		$listOfPremiums = Premium::getListOfPremiums();
		$this->assertIsArray($listOfPremiums);
		$this->assertNotEmpty($listOfPremiums);
	}

	public function testNonExisting(): void {
		$this->assertNull(Premium::getById(999999));
	}

	#[Depends('testGetListOfPremiums')]
	#[Depends('testPersistAndGetById')]
	public function testEmptyPremiums(): void {
		Db::execute("DELETE FROM premium");
		$this->assertEmpty(Premium::getListOfPremiums());
		$this->assertNull(Premium::getById(1));
	}

	protected function setUp(): void {
		parent::setUp();
		$this->premium = new Premium('Premium Plan', 100, 3600, Currency::CZK);
	}
}