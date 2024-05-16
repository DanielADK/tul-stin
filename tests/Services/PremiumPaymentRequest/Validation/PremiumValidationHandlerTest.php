<?php

namespace Services\PremiumPaymentRequest\Validation;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\SQLiteConnectionBuilder;
use StinWeatherApp\Component\Dto\PremiumPaymentRequestDto;
use StinWeatherApp\Model\Buyable\Premium;
use StinWeatherApp\Services\PremiumPaymentRequest\Validation\PremiumValidationHandler;

class PremiumValidationHandlerTest extends TestCase {
	/**
	 * @throws Exception
	 */
	public function testPremiumValidationHandlerThrowsExceptionWhenPremiumOptionIsNotString(): void {
		$data = ['premiumOption' => 123];
		$dto = $this->createMock(PremiumPaymentRequestDto::class);

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('No information about premium detected.');

		$handler = new PremiumValidationHandler($data, $dto);
		$handler->handle();
	}

	/**
	 * @throws Exception
	 */
	public function testPremiumValidationHandlerThrowsExceptionWhenPremiumOptionIsInvalid(): void {
		$data = ['premiumOption' => 'invalidOption'];
		$dto = $this->createMock(PremiumPaymentRequestDto::class);

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('Invalid premium option.');

		$handler = new PremiumValidationHandler($data, $dto);
		$handler->handle();
	}

	public function testPremiumValidationHandlerSetsPremiumOnDtoWhenPremiumOptionIsValid(): void {
		$data = ['premiumOption' => '1'];
		$dto = new PremiumPaymentRequestDto();

		$handler = new PremiumValidationHandler($data, $dto);
		$handler->handle();

		$this->assertInstanceOf(Premium::class, $dto->getPremium());
		$this->assertEquals(1, $dto->getPremium()->getId());
	}

	protected function setUp(): void {
		parent::setUp();

		// Make Database in-memory connection
		$conn = new SQLiteConnectionBuilder();
		$conn->setDatabase(':memory:');
		$conn->buildConnection();
		Db::connect($conn);

		// Create the user table
		Db::execute("
            CREATE TABLE premium (
			    id INTEGER PRIMARY KEY,
			    name TEXT NOT NULL,
			    duration INTEGER NOT NULL,
			    currency TEXT DEFAULT 'CZK' NOT NULL,
			    price FLOAT NOT NULL
			);
        ");

		// Insert a user
		$result = [
			'id' => 1,
			'name' => 'test',
			'duration' => 60,
			'currency' => 'CZK',
			'price' => 1234.56
		];

		// Insert the user into the database
		Db::execute("INSERT INTO premium (id, name, duration, currency, price) VALUES (?, ?, ?, ?, ?)", array_values($result));
	}
}