<?php

namespace Services\Payment\Parser;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Model\Payment;
use StinWeatherApp\Model\Types\Currency;
use StinWeatherApp\Model\Types\PaymentType;
use StinWeatherApp\Services\Payment\Parser\JsonPaymentParser;
use ValueError;

class JsonPaymentParserTest extends TestCase {

	private JsonPaymentParser $parser;

	/**
	 * @throws \Exception
	 */
	public function testParseWithValidData(): void {
		$data = json_encode([
			'amount' => 100.0,
			'currency' => 'CZK',
			'type' => 'CARD',
			'datetime' => '2022-01-01 00:00:00',
			'status' => 'completed'
		]);

		$payment = $this->parser->parse($data);

		$this->assertInstanceOf(Payment::class, $payment);
		$this->assertEquals(100.0, $payment->getAmount());
		$this->assertEquals(Currency::CZK, $payment->getCurrency());
		$this->assertEquals(PaymentType::CARD, $payment->getType());
		$this->assertEquals('completed', $payment->getStatus());
	}

	/**
	 * @throws \Exception
	 */
	public function testParseWithInvalidData(): void {
		$this->expectException(InvalidArgumentException::class);

		$data = 'invalid json';
		$this->parser->parse($data);
	}

	/**
	 * @throws \Exception
	 */
	public function testParseWithMissingKeys(): void {
		$data = json_encode([
			'amount' => 100.0,
			'currency' => 'EUR',
			'type' => 'CASH'
		]);

		$payment = $this->parser->parse($data);

		$this->assertInstanceOf(Payment::class, $payment);
		$this->assertEquals(100.0, $payment->getAmount());
		$this->assertEquals(Currency::EUR, $payment->getCurrency());
		$this->assertEquals(PaymentType::CASH, $payment->getType());
		$this->assertEquals('pending', $payment->getStatus());
	}

	/**
	 * @throws Exception
	 */
	public function testParseWithMissingRequiredKeys(): void {
		$this->expectException(InvalidArgumentException::class);

		$data = json_encode([
			'amount' => 100.0,
			'currency' => 'EUR'
			// 'type' key is missing
		]);

		$this->parser->parse($data);
	}

	/**
	 * @throws Exception
	 */
	public function testParseWithInvalidValues(): void {
		$this->expectException(ValueError::class);

		$data = json_encode([
			'amount' => -100.0, // Invalid value: amount cannot be negative
			'currency' => 'EUR',
			'type' => 'CASH'
		]);

		$this->parser->parse($data);
	}

	protected function setUp(): void {
		$this->parser = new JsonPaymentParser();
	}
}