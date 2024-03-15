<?php

use StinWeatherApp\model\database\SQLiteConnectionBuilder;
use PHPUnit\Framework\TestCase;

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