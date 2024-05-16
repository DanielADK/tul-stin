<?php

namespace Services\PremiumPaymentRequest\Validation;

use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Dto\PremiumPaymentRequestDto;
use StinWeatherApp\Model\Types\Currency;
use StinWeatherApp\Services\PremiumPaymentRequest\Validation\CurrencyValidationHandler;

class CurrencyValidationHandlerTest extends TestCase {

	public static function validCurrencyDataProvider(): array {
		return [
			'valid currency' => [
				[
					'currency' => 'CZK'
				]
			]
		];
	}

	public static function invalidCurrencyDataProvider(): array {
		return [
			'missing currency information' => [
				[]
			],
			'invalid currency' => [
				[
					'currency' => 'INVALID'
				]
			],
		];
	}

	/**
	 * @throws \PHPUnit\Framework\MockObject\Exception
	 * @throws Exception
	 */
	#[DataProvider('validCurrencyDataProvider')]
	public function testCurrencyValidationPassesWithValidCurrency(array $data): void {
		$dto = $this->createMock(PremiumPaymentRequestDto::class);
		$dto->expects($this->once())->method('setCurrency')->with($this->isInstanceOf(Currency::class));

		$handler = new CurrencyValidationHandler($data, $dto);
		$handler->handle();
	}

	/**
	 * @throws \PHPUnit\Framework\MockObject\Exception
	 */
	#[DataProvider('invalidCurrencyDataProvider')]
	public function testCurrencyValidationFailsWithInvalidCurrency(array $data): void {
		$this->expectException(Exception::class);

		$dto = $this->createMock(PremiumPaymentRequestDto::class);
		$handler = new CurrencyValidationHandler($data, $dto);
		$handler->handle();
	}
}