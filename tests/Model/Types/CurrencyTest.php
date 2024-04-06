<?php

namespace Model\Types;

use PHPUnit\Framework\TestCase;
use StinWeatherApp\Model\Types\Currency;

class CurrencyTest extends TestCase {

	public function testValidCurrencies(): void {
		$this->assertFalse(Currency::isValid('USD'));
		$this->assertTrue(Currency::isValid('EUR'));
		$this->assertTrue(Currency::isValid('CZK'));
	}

	public function testInvalidCurrency(): void {
		$this->assertFalse(Currency::isValid('INVALID'));
	}

	public function testFromStringValid(): void {
		$this->assertEquals(Currency::EUR, Currency::fromString('EUR'));
		$this->assertEquals(Currency::CZK, Currency::fromString('CZK'));
	}

	public function testFromStringInvalid(): void {
		$this->assertNull(Currency::fromString('INVALID'));
		$this->assertNull(Currency::fromString('USD'));
	}
}