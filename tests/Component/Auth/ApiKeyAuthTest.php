<?php

namespace Component\Auth;

use Exception;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Auth\ApiKeyAuth;
use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\SQLiteConnectionBuilder;
use StinWeatherApp\Component\Http\Request;

class ApiKeyAuthTest extends TestCase {

	public function testApiKeyAuthLoginReturnsTrueWithValidBearerToken(): void {
		$_SERVER["HTTP_AUTHORIZATION"] = 'Bearer validApiKey';
		$_SERVER["REQUEST_METHOD"] = 'GET';
		$request = new Request();

		$apiKeyAuth = new ApiKeyAuth();
		$this->assertTrue($apiKeyAuth->login($request));
	}

	public function testApiKeyAuthLoginReturnsFalseWithInvalidBearerToken(): void {
		$_SERVER["HTTP_AUTHORIZATION"] = 'Invalid validApiKey';
		$_SERVER["REQUEST_METHOD"] = 'GET';
		$request = new Request();

		$apiKeyAuth = new ApiKeyAuth();
		$this->assertFalse($apiKeyAuth->login($request));
	}

	public function testApiKeyAuthGetUserThrowsExceptionWithNoApiKey(): void {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('No API key detected');

		$apiKeyAuth = new ApiKeyAuth();
		$apiKeyAuth->getUser();
	}

	/**
	 * @throws Exception
	 */
	public function testApiKeyAuthGetUserThrowsExceptionWithInvalidApiKey(): void {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('API key is invalid');

		$apiKeyAuth = new ApiKeyAuth();
		$apiKeyAuth->apiKey = 'invalidApiKey';
		$apiKeyAuth->getUser();
	}

	public function testApiKeyAuthIsAuthenticatedReturnsFalseWithNoApiKey(): void {
		$apiKeyAuth = new ApiKeyAuth();
		$this->assertFalse($apiKeyAuth->isAuthenticated());
	}

	public function testApiKeyAuthIsAuthenticatedReturnsTrueWithApiKey(): void {
		$apiKeyAuth = new ApiKeyAuth();
		$apiKeyAuth->apiKey = 'validApiKey';
		$this->assertTrue($apiKeyAuth->isAuthenticated());
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
	}
}