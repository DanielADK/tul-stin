<?php

namespace Services\PremiumPaymentRequest;

use Exception;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Services\PremiumPaymentRequest\PremiumPaymentProcessingHandler;
use StinWeatherApp\Services\PremiumPaymentRequest\PremiumPaymentTransformer;

class PremiumPaymentProcessingHandlerTest extends TestCase {
	/**
	 * @throws \PHPUnit\Framework\MockObject\Exception
	 */
	public function testPpremiumPaymentProcessingHandlerTransformsPayloadSuccessfully(): void {
		$payload = '{"paymentType": "CARD", "card": {"cardNumber": "1234567812345678", "expiryDate": "12/24", "cvv": "123"}}';
		$expectedResult = [
			'paymentType' => 'CARD',
			'card' => [
				'cardNumber' => '1234567812345678',
				'cardExpiration' => '12/24',
				'cardCode' => '123'
			]
		];

		$premiumTransformer = $this->createMock(PremiumPaymentTransformer::class);
		$premiumTransformer->expects($this->once())
			->method('transform')
			->with($payload)
			->willReturn($expectedResult);

		$handler = new PremiumPaymentProcessingHandler($premiumTransformer);
		$result = $handler->getPremiumFromPayload($payload);

		$this->assertEquals($expectedResult, $result);
	}

	public function testPremiumPaymentProcessingHandlerThrowsExceptionWhenPayloadCannotBeTransformed(): void {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Payload could not be transformed.');

		$payload = 'INVALID_PAYLOAD';

		$premiumTransformer = $this->createMock(PremiumPaymentTransformer::class);
		$premiumTransformer->expects($this->once())
			->method('transform')
			->with($payload)
			->willThrowException(new Exception('Payload could not be transformed.'));

		$handler = new PremiumPaymentProcessingHandler($premiumTransformer);
		$handler->getPremiumFromPayload($payload);
	}
}