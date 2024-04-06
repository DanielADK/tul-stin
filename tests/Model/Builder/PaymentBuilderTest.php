<?php

namespace Model\Builder;

use DateTime;
use Exception;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Model\Builder\PaymentBuilder;
use StinWeatherApp\Model\Card;
use StinWeatherApp\Model\Types\Currency;
use StinWeatherApp\Model\Types\PaymentType;

class PaymentBuilderTest extends TestCase {

	private PaymentBuilder $builder;
	private DateTime $dateTime;

	/**
	 * @throws \Exception
	 */
	public function testBuildWithoutCard(): void {

		$this->builder
			->setAmount(100.0)
			->setCurrency(Currency::CZK)
			->setDatetime($this->dateTime)
			->setType(PaymentType::CASH)
			->setStatus('PROCESSING');

		$payment = $this->builder->build();

		$this->assertEquals(100.0, $payment->getAmount());
		$this->assertEquals(Currency::CZK, $payment->getCurrency());
		$this->assertEquals($this->dateTime, $payment->getDatetime());
		$this->assertEquals(PaymentType::CASH, $payment->getType());
		$this->assertEquals('PROCESSING', $payment->getStatus());
	}

	/**
	 * @throws Exception
	 */
	#[Depends('testBuildWithoutCard')]
	public function testBuildAsCardWithoutCard(): void {
		$this->builder
			->setAmount(100.0)
			->setCurrency(Currency::CZK)
			->setDatetime($this->dateTime)
			->setStatus('PROCESSING')
			->setType(PaymentType::CARD);

		$this->expectException(Exception::class);
		$payment = $this->builder->build();
	}

	/**
	 * @throws Exception
	 */
	#[Depends('testBuildAsCardWithoutCard')]
	public function testBuildWithCard(): void {
		$card = new Card('1234567812345678', '12/24', '123');

		$this->builder
			->setAmount(100.0)
			->setCurrency(Currency::CZK)
			->setDatetime($this->dateTime)
			->setStatus('PROCESSING')
			->setType(PaymentType::CARD)
			->setCard($card);

		$payment = $this->builder->build();
		$this->assertEquals(100.0, $payment->getAmount());
		$this->assertEquals(Currency::CZK, $payment->getCurrency());
		$this->assertEquals($this->dateTime, $payment->getDatetime());
		$this->assertEquals(PaymentType::CARD, $payment->getType());
		$this->assertEquals('PROCESSING', $payment->getStatus());
		$this->assertEquals($card, $payment->getCard());
	}

	protected function setUp(): void {
		$this->builder = new PaymentBuilder();
		$this->dateTime = new DateTime();
	}
}