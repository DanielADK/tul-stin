<?php

namespace Component\Database;

use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\SQLiteConnectionBuilder;

class DbEmptyConnectionTest extends TestCase {
	public function testEmptyConnection(): void {
		$reflection = new \ReflectionClass(Db::class);

		// Make Database in-memory connection
		$conn = new SQLiteConnectionBuilder();
		$conn->setDatabase(':memory:');
		$conn->buildConnection();
		Db::connect($conn);

		$this->assertNotNull($reflection->getStaticPropertyValue("connection"));
	}
}