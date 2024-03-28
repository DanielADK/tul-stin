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

	protected function setUp(): void {
		$this->parser = new JsonPaymentParser();
	}

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
		$this->assertTrue(is_string($data));

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
		$this->assertTrue(is_string($data));

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
		$this->assertTrue(is_string($data));

		$this->parser->parse($data);
	}

	/**
	 * @throws Exception
	 */
	public function testParseWithInvalidValues(): void {
		$this->expectException(InvalidArgumentException::class);

		$data = json_encode([
			'amount' => -100.0, // Invalid value: amount cannot be negative
			'currency' => 'EUR',
			'type' => 'CASH'
		]);
		$this->assertTrue(is_string($data));

		$this->parser->parse($data);
	}

	/**
	 * Test parsing with unknown keys.
	 *
	 * @throws Exception
	 */
	public function testParseWithUnknownKeys(): void {
		$data = json_encode([
			'amount' => 100.0,
			'currency' => 'EUR',
			'type' => 'CARD',
			'unknownKey' => 'unknownValue'
		]);
		$this->assertTrue(is_string($data));

		$payment = $this->parser->parse($data);

		$this->assertInstanceOf(Payment::class, $payment);
	}

	/**
	 * Test parsing with empty strings.
	 *
	 * @throws Exception
	 */
	public function testParseWithEmptyStrings(): void {
		$this->expectException(\TypeError::class);

		$data = json_encode([
			'amount' => '',
			'currency' => '',
			'type' => ''
		]);
		$this->assertTrue(is_string($data));

		$this->parser->parse($data);
	}

	/**
	 * Test parsing with null values.
	 *
	 * @throws Exception
	 */
	public function testParseWithNullValues(): void {
		$this->expectException(\TypeError::class);

		$data = json_encode([
			'amount' => null,
			'currency' => null,
			'type' => null
		]);
		$this->assertTrue(is_string($data));

		$this->parser->parse($data);
	}

	/**
	 * Test parsing with invalid data types.
	 *
	 * @throws Exception
	 */
	public function testParseWithInvalidDataTypes(): void {
		$this->expectException(ValueError::class);

		$data = json_encode([
			'amount' => '100',
			'currency' => 100,
			'type' => 100
		]);
		$this->assertTrue(is_string($data));

		$this->parser->parse($data);
	}

	/**
	 * Test parsing with various date and time formats.
	 *
	 * @throws Exception
	 */
	public function testParseWithVariousDatetimeFormats(): void {
		$data = json_encode([
			'amount' => 100.0,
			'currency' => 'EUR',
			'type' => 'CASH',
			'datetime' => '01/01/2022'
		]);
		$this->assertTrue(is_string($data));

		$payment = $this->parser->parse($data);

		$this->assertInstanceOf(Payment::class, $payment);

		$data = json_encode([
			'amount' => 100.0,
			'currency' => 'EUR',
			'type' => 'CARD',
			'datetime' => '2022-01-01T00:00:00Z'
		]);
		$this->assertTrue(is_string($data));

		$payment = $this->parser->parse($data);

		$this->assertInstanceOf(Payment::class, $payment);
	}

	/**
	 * Test parsing with invalid currency and payment type values.
	 *
	 * @throws Exception
	 */
	public function testParseWithInvalidCurrencyAndPaymentTypeValues(): void {
		$this->expectException(ValueError::class);

		$data = json_encode([
			'amount' => 100.0,
			'currency' => 'INVALID_CURRENCY',
			'type' => 'INVALID_TYPE'
		]);
		$this->assertTrue(is_string($data));

		$this->parser->parse($data);
	}
}