<?php

namespace Component\Database;

use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\InMemorySQLiteConnectionBuilder;
use StinWeatherApp\Component\Database\SQLiteConnectionBuilder;

#[CoversClass(Db::class)]
class DbQueryTest extends TestCase {
	private static PDO $db;

	public static function setUpBeforeClass(): void  {
		// Create new PDO instance
		$cb = new SQLiteConnectionBuilder();
		$cb->setDatabase(":memory:");
		$cb->buildConnection();

		// Connect Db class to the PDO instance
		Db::connect($cb);

		// Create table and insert sample data
		Db::queryRowCount("
        CREATE TABLE users (
            id INTEGER PRIMARY KEY,
            name TEXT,
            email TEXT
        )
    ");

		Db::queryRowCount("
        INSERT INTO users (name, email) VALUES
        ('John Doe', 'john@example.com'),
        ('Jane Doe', 'jane@example.com')
    ");
	}

	public function testConnect(): void {
		// Use reflection to access the private property
		$reflection = new \ReflectionClass(Db::class);
		$property = $reflection->getProperty('connection');

		// Get the value of the 'connection' property
		$actualPdo = $property->getValue();

		// Assert that the 'connection' property is a PDO instance
		$this->assertInstanceOf(PDO::class, $actualPdo);
	}

	public static function tearDownAfterClass(): void {
		// Drop table after tests
		self::$db->exec("DROP TABLE users");
	}

	#[Depends('testConnect')]
	public function testSelect(): void {
		$result = Db::queryAll('SELECT * FROM users');
		$this->assertCount(2, $result);
	}

	#[Depends('testSelect')]
	public function testInsert(): void {
		Db::queryRowCount("INSERT INTO users (name, email) VALUES ('Test User', 'test@example.com')");
		$result = Db::queryAll('SELECT * FROM users');
		$this->assertCount(3, $result);
	}

	#[Depends('testInsert')]
	public function testUpdate(): void {
		Db::queryRowCount("UPDATE users SET name = 'Updated User' WHERE name = 'Test User'");
		$result = Db::queryOne("SELECT * FROM users WHERE name = 'Updated User'");
		$this->assertNotNull($result);
	}

	#[Depends('testUpdate')]
	public function testDelete(): void {
		Db::queryRowCount("DELETE FROM users WHERE name = 'Updated User'");
		$result = Db::queryAll('SELECT * FROM users');
		$this->assertCount(2, $result);
	}

	#[Depends('testInsert')]
	public function testQueryCell(): void {
		$result = Db::queryCell("SELECT name FROM users WHERE email = 'john@example.com'");
		$this->assertEquals('John Doe', $result);
	}

	#[Depends('testDelete')]
	public function testExecute(): void {
		$sql = "INSERT INTO users (name, email) VALUES (:name, :email)";
		$params = ['name' => 'Execute Test', 'email' => 'execute@test.com'];

		$result = Db::execute($sql, $params);

		$this->assertTrue($result);

		$user = Db::queryOne("SELECT * FROM users WHERE name = :name", ['name' => 'Execute Test']);
		$this->assertNotNull($user);
		$this->assertEquals('Execute Test', $user['name']);
		$this->assertEquals('execute@test.com', $user['email']);
	}
}