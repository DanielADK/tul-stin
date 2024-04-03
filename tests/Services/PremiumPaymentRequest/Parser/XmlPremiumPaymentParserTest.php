<?php

namespace Services\PremiumPaymentRequest\Parser;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Services\Payment\Parser\XmlPaymentParser;
use StinWeatherApp\Services\PremiumPaymentRequest\Parser\XmlPremiumPaymentParser;

class XmlPremiumPaymentParserTest extends TestCase {

	private XmlPremiumPaymentParser $parser;

	public function testParseWithValidInput(): void {
		$xml = '<root><username>test</username><email>test@example.com</email><premiumOption>option1</premiumOption><paymentType>card</paymentType><card><cardNumber>1234567890123456</cardNumber><cardExpiration>12/23</cardExpiration><cardCode>123</cardCode></card></root>';
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

		$this->assertSame($expected, $this->parser->parse($xml));
	}

	public function testParseWithMissingAttributes(): void {
		$this->expectException(InvalidArgumentException::class);

		$xml = '<root><username>test</username><email>test@example.com</email><premiumOption>option1</premiumOption></root>';
		$this->parser->parse($xml);
	}

	public function testParseWithInvalidInput(): void {
		$this->expectException(InvalidArgumentException::class);

		$xml = 'not valid xml';
		$this->parser->parse($xml);
	}

	protected function setUp(): void {
		$this->parser = new XmlPremiumPaymentParser();
		error_reporting(E_ALL);
	}

}