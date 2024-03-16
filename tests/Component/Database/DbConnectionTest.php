<?php

namespace Component\Database;

use DateTime;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use StinWeatherApp\Component\Database\Db;
use ReflectionMethod;

class DbConnectionTest extends TestCase {
	/**
	 * @throws ReflectionException
	 */
	public function testProcessParametersWithDateTime() {
		$reflectionMethod = new ReflectionMethod(Db::class, 'processParameters');

		$params = [
			'date' => new DateTime('2021-01-01 12:00:00'),
			'name' => 'Test'
		];

		$expected = [
			'date' => '2021-01-01 12:00:00',
			'name' => 'Test'
		];

		$result = $reflectionMethod->invoke(null, $params);

		$this->assertEquals($expected, $result);
	}
}