<?php

namespace StinWeatherApp\Component\Auth;

use Exception;
use StinWeatherApp\Component\Http\Request;
use StinWeatherApp\Model\User;

class ApiKeyAuth implements AuthInterface {
	private string $apiKey;

	/**
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
		$user = User::getByApiKey($this->apiKey);
		if ($user === null) {
			throw new Exception("User not found");
		}
		return $user;
	}
}