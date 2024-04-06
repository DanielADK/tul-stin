<?php

namespace Component\Dto;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Dto\PremiumPaymentRequestDto;
use StinWeatherApp\Model\Buyable\Premium;
use StinWeatherApp\Model\Card;
use StinWeatherApp\Model\Types\Currency;
use StinWeatherApp\Model\Types\PaymentType;
use StinWeatherApp\Model\User;

class PremiumPaymentRequestDtoTest extends TestCase {
	private PremiumPaymentRequestDto $dto;

	/**
	 * Test if the DTO can be instantiated.
	 */
	public function testCanBeInstantiated(): void {
		$this->assertInstanceOf(PremiumPaymentRequestDto::class, $this->dto);
	}

	/**
	 * Test if the DTO can set and get a Premium.
	 *
	 * @throws Exception
	 */
	public function testCanSetAndGetPremium(): void {
		$premium = $this->createMock(Premium::class);
		$this->dto->setPremium($premium);

		$this->assertSame($premium, $this->dto->getPremium());
	}

	/**
	 * Test if the DTO can set and get a PaymentType.
	 */
	public function testCanSetAndGetPaymentType(): void {
		foreach (PaymentType::cases() as $paymentType) {
			$this->dto->setPaymentType($paymentType);
			$this->assertSame($paymentType, $this->dto->getPaymentType());
		}
	}

	/**
	 * Test if the DTO can set and get a Card.
	 *
	 * @throws Exception
	 */
	public function testCanSetAndGetCard(): void {
		$card = $this->createMock(Card::class);
		$this->dto->setCard($card);

		$this->assertSame($card, $this->dto->getCard());
	}

	/**
	 * Test if the DTO can set and get a User.
	 *
	 * @throws Exception
	 */
	public function testCanSetAndGetUser(): void {
		$user = $this->createMock(User::class);
		$this->dto->setUser($user);

		$this->assertSame($user, $this->dto->getUser());
	}

	/**
	 * Test if the DTO can set and get a Currency.
	 */
	public function testCanSetAndGetCurrency(): void {
		foreach (Currency::cases() as $currency) {
			$this->dto->setCurrency($currency);
			$this->assertSame($currency, $this->dto->getCurrency());
		}
	}

	protected function setUp(): void {
		parent::setUp();

		$this->dto = new PremiumPaymentRequestDto();
	}
}