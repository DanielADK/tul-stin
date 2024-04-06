<?php

namespace Component\Parser;

use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Parser\XmlParseable;

class XmlParseableTest extends TestCase {
	private XmlParseable $xmlParseable;

	protected function setUp(): void {
		parent::setUp();

		$this->xmlParseable = new XmlParseable();
		error_reporting(E_ALL);
	}

	/**
	 * Test if the class can be instantiated.
	 */
	public function testCanBeInstantiated(): void {
		$this->assertInstanceOf(XmlParseable::class, $this->xmlParseable);
	}

	/**
	 * Test if the XML can be parsed.
	 */
	public function testCanParse(): void {
		$validXml = '<root><key>value</key></root>';
		$invalidXml = '<root><key>value</key>';
		$this->assertTrue($this->xmlParseable->canParse($validXml));
		@$this->assertFalse($this->xmlParseable->canParse($invalidXml));
	}
}