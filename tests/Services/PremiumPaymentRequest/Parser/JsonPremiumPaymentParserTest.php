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
		$json = '{"username":"test", "premiumOption":"option1","paymentType":"card","card":{"cardNumber":"1234567890123456","cardExpiration":"12/23","cardCode":"123"}}';
		$expected = [
			'username' => 'test',
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

		$json = '{"username":"test","premiumOption":"option1"}';
		$this->parser->parse($json);
	}

	public function testParseWithInvalidInput(): void {
		$this->expectException(InvalidArgumentException::class);

		$json = 'not valid json';
		$this->parser->parse($json);
	}

	public function testParseThrowsExceptionInvalidCardData(): void {
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid card data.');

		$json = '{"username":"test","premiumOption":"option1","paymentType":"card"}';
		$this->parser->parse($json);
	}

	public function testParseThrowsExceptionMissingRequiredKey(): void {

		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Missing required key');
		// Missing cardVerification
		$json = '{"username":"test", "premiumOption":"option1","paymentType":"card","card":{"cardNumber":"1234567890123456","cardExpiration":"12/23"}}';


		$this->parser->parse($json);
	}

}