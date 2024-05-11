<?php

namespace Services\PremiumPaymentRequest\Validation;

use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Dto\PremiumPaymentRequestDto;
use StinWeatherApp\Model\Card;
use StinWeatherApp\Services\PremiumPaymentRequest\Parser\PremiumPaymentParserInterface;
use StinWeatherApp\Services\PremiumPaymentRequest\Validation\CardValidationHandler;


class CardValidationHandlerTest extends TestCase {

	public static function validCardDataProvider(): array {
		return [
			'valid card' => [
				[
					PremiumPaymentParserInterface::cardKey => [
						'cardNumber' => '1234567812345678',
						'cardExpiration' => '12/24',
						'cardCode' => '123'
					]
				]
			]
		];
	}

	public static function invalidCardDataProvider(): array {
		return [
			'missing card information' => [
				[]
			],
			'missing card number' => [
				[
					PremiumPaymentParserInterface::cardKey => [
						'cardExpiration' => '12/24',
						'cardCode' => '123'
					]
				]
			],
		];
	}

	/**
	 * @throws Exception
	 */
	#[DataProvider('validCardDataProvider')]
	public function testCardValidationPassesWithValidCard(array $data): void {
		$dto = $this->createMock(PremiumPaymentRequestDto::class);
		$dto->expects($this->once())->method('setCard')->with($this->isInstanceOf(Card::class));

		$handler = new CardValidationHandler($data, $dto);
		$handler->handle();
	}

	/**
	 * @throws \PHPUnit\Framework\MockObject\Exception
	 */
	#[DataProvider('invalidCardDataProvider')]
	public function testCardValidationFailsWithInvalidCard(array $data): void {
		$this->expectException(Exception::class);

		$dto = $this->createMock(PremiumPaymentRequestDto::class);
		$handler = new CardValidationHandler($data, $dto);
		$handler->handle();
	}
}