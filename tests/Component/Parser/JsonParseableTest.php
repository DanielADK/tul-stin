<?php

namespace Component\Parser;

use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Parser\JsonParseable;

class JsonParseableTest extends TestCase {
	private JsonParseable $jsonParseable;

	/**
	 * Test if the class can be instantiated.
	 */
	public function testCanBeInstantiated(): void {
		$this->assertInstanceOf(JsonParseable::class, $this->jsonParseable);
	}

	/**
	 * Test if the JSON can be parsed.
	 */
	public function testCanParse(): void {
		$validJson = '{"key": "value"}';
		$invalidJson = '{key: value}';

		$this->assertTrue($this->jsonParseable->canParse($validJson));
		$this->assertFalse($this->jsonParseable->canParse($invalidJson));
	}

	protected function setUp(): void {
		parent::setUp();

		$this->jsonParseable = new JsonParseable();
	}
}