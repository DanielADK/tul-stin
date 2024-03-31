<?php

namespace Model;

use DateTime;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Model\Payment;
use StinWeatherApp\Model\Types\Currency;
use StinWeatherApp\Model\Types\PaymentType;

class PaymentTest extends TestCase {

	private Payment $payment;

	public function testGetAndSetAmount(): void {
		$this->payment->setAmount(200.0);
		$this->assertSame(200.0, $this->payment->getAmount());
	}

	public function testGetAndSetCurrency(): void {
		$this->payment->setCurrency(Currency::EUR);
		$this->assertSame(Currency::EUR, $this->payment->getCurrency());
	}

	public function testGetAndSetDatetime(): void {
		$datetime = new DateTime('2022-01-01');
		$this->payment->setDatetime($datetime);
		$this->assertSame($datetime, $this->payment->getDatetime());
	}

	public function testGetAndSetType(): void {
		$this->payment->setType(PaymentType::CARD);
		$this->assertSame(PaymentType::CARD, $this->payment->getType());
	}

	public function testGetAndSetStatus(): void {
		$this->payment->setStatus('completed');
		$this->assertSame('completed', $this->payment->getStatus());
	}

	protected function setUp(): void {
		$this->payment = new Payment(100.0, Currency::CZK, new DateTime(), PaymentType::CASH, 'pending');
	}
}