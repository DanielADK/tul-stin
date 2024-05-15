<?php

namespace Services\PremiumPaymentRequest\Validation;

use DateTime;
use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\SQLiteConnectionBuilder;
use StinWeatherApp\Component\Dto\PremiumPaymentRequestDto;
use StinWeatherApp\Model\User;
use StinWeatherApp\Services\PremiumPaymentRequest\Validation\ExistsUserValidationHandler;

class ExistsUserValidationHandlerTest extends TestCase {

	public static function validUserDataProvider(): array {
		return [
			'valid user' => [
				['username' => 'test']
			]
		];
	}

	public static function invalidUserDataProvider(): array {
		return [
			'missing user information' => [
				[]
			],
			'invalid user' => [
				['username' => 'invalidUser']
			],
		];
	}

	/**
	 * @throws Exception|\PHPUnit\Framework\MockObject\Exception
	 */
	#[DataProvider("validUserDataProvider")]
	public function testUserValidationPassesWithMockedUser(array $data): void {
		$dto = $this->createMock(PremiumPaymentRequestDto::class);
		$dto->expects($this->once())->method('setUser')->with($this->isInstanceOf(User::class));

		$handler = new ExistsUserValidationHandler($data, $dto);
		$handler->handle();
	}

	/**
	 * @throws \PHPUnit\Framework\MockObject\Exception
	 */
	#[DataProvider("invalidUserDataProvider")]
	public function testUserValidationFailsWithInvalidUser(array $data): void {
		$this->expectException(Exception::class);

		$dto = $this->createMock(PremiumPaymentRequestDto::class);
		$handler = new ExistsUserValidationHandler($data, $dto);
		$handler->handle();
	}

	/**
	 * @throws Exception
	 */
	protected function setUp(): void {
		parent::setUp();

		// Make Database in-memory connection
		$conn = new SQLiteConnectionBuilder();
		$conn->setDatabase(':memory:');
		$conn->buildConnection();
		Db::connect($conn);

		// Create the user table
		Db::execute("
            CREATE TABLE user (
                id INTEGER PRIMARY KEY,
                username TEXT,
                api_key TEXT,
                premium_until TEXT
            )
        ");

		// Insert a user
		$result = [
			'id' => '1',
			'username' => 'test',
			'api_key' => 'test_key',
			'premium_until' => (new DateTime("+1 month"))->format("Y-m-d H:i:s"),
		];

		// Insert the user into the database
		Db::execute("INSERT INTO user (id, username, api_key, premium_until) VALUES (?, ?, ?, ?)", array_values($result));
	}
}