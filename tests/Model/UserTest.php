<?php

namespace Model;

use DateTime;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\SQLiteConnectionBuilder;
use StinWeatherApp\Model\User;

class UserTest extends TestCase {

	private User $user;

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
}