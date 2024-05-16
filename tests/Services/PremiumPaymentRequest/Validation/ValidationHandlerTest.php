<?php

namespace Services\PremiumPaymentRequest\Validation;

use Exception;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Dto\PremiumPaymentRequestDto;
use StinWeatherApp\Services\PremiumPaymentRequest\Validation\ValidationHandler;

class ValidationHandlerTest extends TestCase {
	public function testValidationHandlerProcessesDataSuccessfully(): void {
		$data = ['key' => 'value'];
		$dto = new PremiumPaymentRequestDto();

		$validationHandler = $this->getMockBuilder(ValidationHandler::class)
			->setConstructorArgs([$data, $dto])
			->getMockForAbstractClass();

		$validationHandler->expects($this->once())
			->method('validate');

		$this->assertTrue($validationHandler->handle());
	}

	public function testValidationHandlerPassesToNextHandler(): void {
		$data = ['key' => 'value'];
		$dto = new PremiumPaymentRequestDto();

		$validationHandler1 = $this->getMockBuilder(ValidationHandler::class)
			->setConstructorArgs([$data, $dto])
			->getMockForAbstractClass();

		$validationHandler2 = $this->getMockBuilder(ValidationHandler::class)
			->setConstructorArgs([$data, $dto])
			->getMockForAbstractClass();

		$validationHandler1->setNext($validationHandler2);

		$validationHandler1->expects($this->once())
			->method('validate');

		$validationHandler2->expects($this->once())
			->method('validate');

		$validationHandler1->handle();
	}

	public function testValidationHandlerThrowsExceptionOnInvalidData(): void {
		$this->expectException(Exception::class);

		$data = ['key' => 'invalid value'];
		$dto = new PremiumPaymentRequestDto();

		$validationHandler = $this->getMockBuilder(ValidationHandler::class)
			->setConstructorArgs([$data, $dto])
			->getMockForAbstractClass();

		$validationHandler->expects($this->once())
			->method('validate')
			->will($this->throwException(new Exception()));

		$validationHandler->handle();
	}
}