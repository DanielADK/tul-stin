<?php

namespace Services\PremiumPaymentRequest\Validation;

use Exception;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Dto\PremiumPaymentRequestDto;
use StinWeatherApp\Model\Types\PaymentType;
use StinWeatherApp\Services\PremiumPaymentRequest\Validation\PaymentTypeValidationHandler;

class PaymentTypeValidationHandlerTest extends TestCase {
	public function testPaymentTypeValidationHandlerWithInvalidPaymentTypeThrowsException(): void {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid payment type.');

		$data = ['paymentType' => 'INVALID'];
		$dto = new PremiumPaymentRequestDto();

		$handler = new PaymentTypeValidationHandler($data, $dto);
		$handler->handle();
	}

	public function testPaymentTypeValidationHandlerWithNoPaymentTypeThrowsException(): void {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('No information about payment type detected.');

		$data = [];
		$dto = new PremiumPaymentRequestDto();

		$handler = new PaymentTypeValidationHandler($data, $dto);
		$handler->handle();
	}

	public function testPaymentTypeValidationHandlerWithValidPaymentTypeSetsPaymentTypeOnDto(): void {
		$data = ['paymentType' => "CASH"];
		$dto = new PremiumPaymentRequestDto();

		$handler = new PaymentTypeValidationHandler($data, $dto);
		$handler->handle();

		$this->assertEquals(PaymentType::CASH, $dto->getPaymentType());
	}

	public function testPaymentTypeValidationHandlerWithCardPaymentTypeThrowsExceptionIfCardDataIsInvalid(): void {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('No information about card detected.');

		$data = [
			'paymentType' => "CARD",
			'cardData' => [
				'cardNumber' => 'INVALID',
				'cardExpiration' => 'INVALID',
				'cardCode' => 'INVALID'
			]
		];
		$dto = new PremiumPaymentRequestDto();

		$handler = new PaymentTypeValidationHandler($data, $dto);
		$handler->handle();
	}

	public function testPaymentTypeValidationHandlerWithCardPaymentTypeReturnsDtoWithCardDataIfValid(): void {
		$data = [
			'paymentType' => "CARD",
			'card' => [
				'cardNumber' => '1234567812345678',
				'cardExpiration' => '12/24',
				'cardCode' => '123'
			]
		];
		$dto = new PremiumPaymentRequestDto();

		$handler = new PaymentTypeValidationHandler($data, $dto);
		$handler->handle();

		$this->assertEquals($data['card']["cardNumber"], $dto->getCard()->getNumber());
		$this->assertEquals($data['card']["cardExpiration"], $dto->getCard()->getExpiration());
		$this->assertEquals($data['card']["cardCode"], $dto->getCard()->getCode());
	}
}