<?php

namespace StinWeatherApp\Component\Auth;

use StinWeatherApp\Component\Http\Request;
use StinWeatherApp\Model\User;

/**
 * Interface AuthInterface
 *
 * @description Interface for authentication classes
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @package StinWeatherApp\Component\Auth
 */
interface AuthInterface {
	/**
	 * @description Logs the user in
	 *
	 * @param Request $request
	 *
	 * @return bool
	 */
	public function login(Request $request): bool;

	/**
	 * @description Returns the user
	 * @return User
	 */
	public function getUser(): User;

}