<?php

namespace Services\Payment\Parser;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Services\Payment\Parser\XmlPaymentParser;

class XMLPaymentParserTest extends TestCase {

	private XmlPaymentParser $parser;

	protected function setUp(): void {
		$this->parser = new XmlPaymentParser();
		error_reporting(E_ALL);
	}

	/**
	 * Test that the parse method correctly processes valid XML data and returns the expected Payment object.
	 *
	 * @throws Exception
	 */
	public function testParseValidXML(): void {
		$xml = '<payment><amount>100</amount><currency>CZK</currency><type>CASH</type><datetime>2022-01-01T00:00:00</datetime><status>pending</status></payment>';

		$payment = $this->parser->parse($xml);

		$this->assertSame(100.0, $payment->getAmount());
		$this->assertSame('CZK', $payment->getCurrency()->value);
		$this->assertSame('CASH', $payment->getType()->value);
		$this->assertSame('2022-01-01T00:00:00', $payment->getDatetime()->format('Y-m-d\TH:i:s'));
		$this->assertSame('pending', $payment->getStatus());
	}

	/**
	 * Test that the parse method throws an InvalidArgumentException if invalid XML data is provided.
	 *
	 * @throws Exception
	 */
	public function testParseInvalidXML(): void {
		$this->expectException(InvalidArgumentException::class);

		$xml = 'invalid xml';
		// Generates warning -> want to suppress it
		@$this->parser->parse($xml);
	}

	/**
	 * Test that the parse method throws an InvalidArgumentException if any of the required keys are missing in the XML
	 * data.
	 *
	 * @throws Exception
	 */
	public function testParseMissingRequiredKeys(): void {
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Missing required key: amount');

		$xml = '<payment><currency>CZK</currency><type>CASH</type><datetime>2022-01-01T00:00:00</datetime><status>pending</status></payment>';
		$this->parser->parse($xml);
	}

	/**
	 * Test that the parse method throws an InvalidArgumentException if an invalid currency value is provided in the
	 * XML data.
	 *
	 * @throws Exception
	 */
	public function testParseInvalidCurrency(): void {
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid currency value: INVALID');

		$xml = '<payment><amount>100</amount><currency>INVALID</currency><type>CASH</type><datetime>2022-01-01T00:00:00</datetime><status>pending</status></payment>';
		$this->parser->parse($xml);
	}
}