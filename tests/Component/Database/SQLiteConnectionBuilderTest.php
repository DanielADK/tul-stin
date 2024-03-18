<?php

namespace Component\Database;

use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Database\SQLiteConnectionBuilder;
use PDO;
use PDOException;

class SQLiteConnectionBuilderTest extends TestCase {
	public function testBuild(): void {
		$builder = new SQLiteConnectionBuilder();
		$builder->setDatabase('weather');

		$this->assertInstanceOf(
			SQLiteConnectionBuilder::class,
			$builder
		);
		$this->assertEquals('weather', $builder->getDatabase());

	}

	public function testBuildConnection(): void {
		// Create new SQLiteConnectionBuilder instance
		$builder = new SQLiteConnectionBuilder();
		$builder->setDatabase('test');

		// Call buildConnection method
		$builder->buildConnection();

		// Use reflection to access the private property
		$reflection = new \ReflectionClass(SQLiteConnectionBuilder::class);
		$property = $reflection->getProperty('connection');

		// Get the value of the 'connection' property
		$actualPdo = $property->getValue($builder);

		// Assert that the 'connection' property is a PDO instance
		$this->assertInstanceOf(PDO::class, $actualPdo);
	}
}