<?php

namespace Model\Types;

use PHPUnit\Framework\TestCase;
use StinWeatherApp\Model\Types\PaymentType;

class PaymentTypeTest extends TestCase {

	public function testValidPaymentTypes(): void {
		$this->assertTrue(PaymentType::isValid('CASH'));
		$this->assertTrue(PaymentType::isValid('CARD'));
	}

	public function testInvalidPaymentType(): void {
		$this->assertFalse(PaymentType::isValid('INVALID'));
	}

	public function testFromStringValid(): void {
		$this->assertEquals(PaymentType::CASH, PaymentType::fromString('CASH'));
		$this->assertEquals(PaymentType::CARD, PaymentType::fromString('CARD'));
	}

	public function testFromStringInvalid(): void {
		$this->assertNull(PaymentType::fromString('INVALID'));
		$this->assertNull(PaymentType::fromString('CHECK'));
	}
}