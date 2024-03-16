<?php

namespace Component\Database;

use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Database\SQLiteConnectionBuilder;

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
}