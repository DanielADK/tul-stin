<?php

namespace Model;

use DateTime;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Random\RandomException;
use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\SQLiteConnectionBuilder;
use StinWeatherApp\Model\User;

class UserTest extends TestCase {
	use PHPMock;

	private User $user;

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

		$this->user = new User(1, 'TestUser');
	}

	public function testSetAndGetUsername(): void {
		$this->user->setUsername('NewTestUser');
		$this->assertEquals('NewTestUser', $this->user->getUsername());
	}

	public function testGenerateApiKey(): void {
		$this->user->generateApiKey();
		$this->assertNotNull($this->user->getApiKey());
	}

	public function testPersistAndGetById(): void {
		$this->user = new User(null, 'TestUser');
		try {
			// Persist the user
			$this->user->persist();
		} catch (\Exception $e) {
			$this->fail('Failed to persist the user: ' . $e->getMessage());
		}
		$this->assertNotNull($this->user->getId());
		// Get the user by id
		$persistedUser = User::getById($this->user->getId());

		// Assert the persisted user is not null and its properties match the original user
		$this->assertNotNull($persistedUser);
		$this->assertEquals($this->user->getUsername(), $persistedUser->getUsername());
		$this->assertEquals($this->user->getApiKey(), $persistedUser->getApiKey());

		// Get the user by username
		$persistedUser = User::getUserByUsername($this->user->getUsername());

		// Assert the persisted user is not null and its properties match the original user
		$this->assertNotNull($persistedUser);
		$this->assertEquals($this->user->getUsername(), $persistedUser->getUsername());
		$this->assertEquals($this->user->getApiKey(), $persistedUser->getApiKey());
	}

	public function testHasPremium(): void {
		$this->user->setPremiumUntil(new DateTime('+1 month'));
		$this->assertTrue($this->user->hasPremium());

		$this->user->setPremiumUntil(new DateTime('-1 month'));
		$this->assertFalse($this->user->hasPremium());
	}

	public function testValidatePremium(): void {
		$this->user->setPremiumUntil(new DateTime('+1 month'));
		$this->user->generateApiKey();
		$apiKey = $this->user->getApiKey();

		$this->user->validatePremium(false);
		$this->assertEquals($apiKey, $this->user->getApiKey());
		$this->assertNotNull($this->user->getPremiumUntil());

		$this->user->setPremiumUntil(new DateTime('-1 month'));
		$this->user->validatePremium(false);
		$this->assertNull($this->user->getApiKey());
		$this->assertNull($this->user->getPremiumUntil());
	}

	/**
	 * @throws Exception
	 */
	public function testGenerateApiKeyThrowsException(): void {
		// Create a mock of the User class
		$userMock = $this->createPartialMock(User::class, ['generateRandomBytes']);
		$userMock->setUsername("TestUser");

		// Define the exception
		$exception = new RandomException('Random bytes generation failed.');

		// Configure the stub
		$userMock->method('generateRandomBytes')->will($this->throwException($exception));

		// Call the method
		$userMock->generateApiKey();

		// Assert that apiKey is set to the expected value
		$expectedApiKey = hash("sha256", $userMock->getUsername() . (new DateTime())->format("Y-m-d H:i:s"));
		$this->assertEquals($expectedApiKey, $userMock->getApiKey());
	}

	/**
	 * @throws Exception
	 */
	public function testValidatePremiumThrowsException(): void {
		// Create a mock of the User class
		$userMock = $this->createPartialMock(User::class, ['persist']);

		// Define the exception
		$exception = new \Exception('Persist failed.');

		// Configure the stub
		$userMock->method('persist')->will($this->throwException($exception));

		// Set premiumUntil to a past date to trigger the if condition
		$userMock->setPremiumUntil((new DateTime())->modify('-1 day'));

		// Call the method
		$userMock->validatePremium();

		// Assert that apiKey and premiumUntil are null
		$this->assertNull($userMock->getApiKey());
		$this->assertNull($userMock->getPremiumUntil());
	}

	/**
	 * @throws Exception
	 */
	public function testParseFromArrayThrowsException(): void {
		// Define the result array with an invalid date
		$result = [
			'id' => '1',
			'username' => 'test',
			'api_key' => 'test_key',
			'premium_until' => 'invalid_date'
		];

		// Insert the user into the database
		Db::execute("INSERT INTO user (id, username, api_key, premium_until) VALUES (?, ?, ?, ?)", array_values($result));

		// Call the method
		$user = User::getById(1);

		// Assert that premiumUntil is null
		$this->assertNull($user->getPremiumUntil());
	}
}