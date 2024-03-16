<?php

namespace Component\Database;

use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Database\MySQLConnectionBuilder;

class MySQLConnectionBuilderTest extends TestCase {
	public function testBuild(): void {
		$builder = new MySQLConnectionBuilder();
		$builder->setHost('localhost')
			->setUsername('root')
			->setPassword('password')
			->setDatabase('weather');

		$this->assertInstanceOf(
			MySQLConnectionBuilder::class,
			$builder
		);
		$this->assertEquals('localhost', $builder->getHost());
		$this->assertEquals('root', $builder->getUsername());
		$this->assertEquals('password', $builder->getPassword());
		$this->assertEquals('weather', $builder->getDatabase());

	}
}