<?php

namespace Services\PremiumPaymentRequest\Parser;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Services\PremiumPaymentRequest\Parser\JsonPremiumPaymentParser;

class JsonPremiumPaymentParserTest extends TestCase {
	private JsonPremiumPaymentParser $parser;

	protected function setUp(): void {
		$this->parser = new JsonPremiumPaymentParser();
	}

	public function testParseWithValidInput(): void {
		$json = '{"username":"test","email":"test@example.com","premiumOption":"option1","paymentType":"card","card":{"cardNumber":"1234567890123456","cardExpiration":"12/23","cardCode":"123"}}';
		$expected = [
			'username' => 'test',
			'email' => 'test@example.com',
			'premiumOption' => 'option1',
			'paymentType' => 'card',
			'card' => [
				'cardNumber' => '1234567890123456',
				'cardExpiration' => '12/23',
				'cardCode' => '123'
			]
		];

		$this->assertSame($expected, $this->parser->parse($json));
	}

	public function testParseWithMissingAttributes(): void {
		$this->expectException(InvalidArgumentException::class);

		$json = '{"username":"test","email":"test@example.com","premiumOption":"option1"}';
		$this->parser->parse($json);
	}

	public function testParseWithInvalidInput(): void {
		$this->expectException(InvalidArgumentException::class);

		$json = 'not valid json';
		$this->parser->parse($json);
	}
}