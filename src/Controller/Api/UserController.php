<?php

namespace StinWeatherApp\Controller\Api;

use StinWeatherApp\Component\Http\Response;
use StinWeatherApp\Component\Router\Router;
use StinWeatherApp\Controller\AbstractController;
use StinWeatherApp\Model\User;

class UserController extends AbstractController {
	public function createUser(): Response {
		$payload = json_decode($this->request->getRawBody(), true);
		if (!is_array($payload)) {
			return new Response("Failed to parse request.", 400);
		}
		if ($payload["username"] === null || strlen($payload["username"]) === 0) {
			return new Response("Username is required.", 400);
		}
		$username = $payload["username"];

		$user = new User(null, $username);
		try {
			$user->persist();
		} catch (\Exception $e) {
			$content = json_encode(["status" => $e->getMessage()]);
			return ($content === false) ? new Response("Failed to encode JSON.", 500) : new Response($content, 500);
		}
		$content = json_encode(["status" => "User created."]);
		return ($content === false) ? new Response("Failed to encode JSON.", 500) : new Response($content, 200);
	}

	/**
	 * @description Delete user by username (TEST ONLY)
	 *
	 * @param string $username
	 *
	 * @return Response
	 */
	public function deleteUser(string $username): Response {
		error_log("Deleting user: " . $username);
		if (!is_string($username)) {
			$content = json_encode(["status" => "Username is required."]);
			return ($content === false) ? new Response("Failed to encode JSON.", 500) : new Response($content, 400);
		}

		$user = User::getUserByUsername($username);
		if ($user === null) {
			$content = json_encode(["status" => "User not found."]);
			return ($content === false) ? new Response("Failed to encode JSON.", 500) : new Response($content, 404);
		}

		try {
			$user->delete();
		} catch (\Exception $e) {
			$content = json_encode(["status" => "Failed to delete user."]);
			return ($content === false) ? new Response("Failed to encode JSON.", 500) : new Response($content, 500);
		}
		$content = json_encode(["status" => "User deleted."]);
		return ($content === false) ? new Response("Failed to encode JSON.", 500) : new Response($content, 200);
	}

	public function options(): Response {
		$response = new Response("", 200);
		/** @var Router $GLOBALS ['router']; */
		$methods = $GLOBALS['router']->getAllowedMethods($this->request->getPath());
		$methods = array_map(fn($method) => $method->value, $methods);
		$response->setHeader("Access-Control-Allow-Methods: " . implode(", ", $methods));
		return $response;
	}
}