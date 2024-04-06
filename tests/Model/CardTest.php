<?php

namespace Model;

use PHPUnit\Framework\TestCase;
use StinWeatherApp\Model\Card;

class CardTest extends TestCase {

	private Card $card;

	/**
	 * @throws \Exception
	 */
	public function testSetNumberAndGetNumber(): void {
		$this->card->setNumber('8765432187654321');
		$this->assertSame('8765432187654321', $this->card->getNumber());
	}

	public function testSetNumberInvalid(): void {
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('Invalid card number. Must be 16 digits.');
		$this->card->setNumber('1234');
	}

	public function testSetExpirationAndGetExpiration(): void {
		$this->card->setExpiration('01/25');
		$this->assertSame('01/25', $this->card->getExpiration());
	}

	public function testSetExpirationInvalid(): void {
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('Invalid expiration date. Must be in format MM/YY.');
		$this->card->setExpiration('01/2025');
	}

	public function testSetCodeAndGetCode(): void {
		$this->card->setCode('456');
		$this->assertSame('456', $this->card->getCode());
	}

	public function testSetCodeInvalid(): void {
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('Invalid code. Must be 3 digits.');
		$this->card->setCode('12');
	}

	protected function setUp(): void {
		$this->card = new Card('1234567812345678', '12/24', '123');
	}
}