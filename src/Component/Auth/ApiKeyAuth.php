<?php

namespace StinWeatherApp\Component\Auth;

use Exception;
use StinWeatherApp\Component\Http\Request;
use StinWeatherApp\Model\User;

class ApiKeyAuth implements AuthInterface {
	public ?string $apiKey = null;

	/**
	 * @description Login user by API key
	 *
	 * @param Request $request
	 *
	 * @return bool
	 */
	public function login(Request $request): bool {
		$authHeader = $request->getHttpAuthorization();
		$bearerToken = explode(" ", $authHeader);
		if (count($bearerToken) == 2 && $bearerToken[0] == "Bearer") {
			$this->apiKey = $bearerToken[1];
			return true;
		}
		return false;

	}

	/**
	 * @return User
	 * @throws Exception
	 */
	public function getUser(): User {
		if (!isset($this->apiKey)) {
			throw new Exception("No API key detected");
		}
		$user = User::getByApiKey($this->apiKey);
		if (!isset($user) || !($user instanceof User)) {
			throw new Exception("API key is invalid");
		}
		return $user;
	}

	/**
	 * @inheritDoc
	 */
	public function isAuthenticated(): bool {
		return isset($this->apiKey);
	}
}