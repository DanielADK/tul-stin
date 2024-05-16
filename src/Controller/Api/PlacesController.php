<?php

namespace StinWeatherApp\Controller\Api;

use StinWeatherApp\Component\Http\Response;
use StinWeatherApp\Component\Router\Router;
use StinWeatherApp\Controller\AbstractController;
use StinWeatherApp\Model\Place;
use StinWeatherApp\Model\User;

class PlacesController extends AbstractController {
	/**
	 * @throws \Exception
	 */
	public function getPlaces(User $user): Response {
		// Response
		$response = new Response();
		$response->setJSON();

		$placesAsStr = array();
		foreach ($user->getFavouritePlaces() as $place) {
			$placesAsStr[] = array("name" => $place->getName(), "latitude" => $place->getLatitude(), "longitude" => $place->getLongitude());
		}
		$arr_content = ["status" => "success", "places" => $placesAsStr];
		$content = json_encode($arr_content);
		if ($content === false) {
			return $response->setStatusCode(500)
				->setContent("Failed to encode JSON.");
		}

		return $response->setStatusCode(200)
			->setContent($content);
	}

	public function addPlace(User $user): Response {
		// Response
		$response = new Response();
		$response->setJSON();

		// Get data and verify
		$data = $this->request->getRawBody();
		$data = json_decode($data, true);
		if (!is_array($data)) {
			return $response->setStatusCode(400)
				->setContent(json_encode(["status" => "error", "message" => "Invalid JSON."]));
		}
		if (!isset($data["name"]) || !isset($data["latitude"]) || !isset($data["longitude"])) {
			return $response->setStatusCode(400)
				->setContent(json_encode(["status" => "error", "message" => "Missing required data."]));
		}
		if (!is_string($data["name"]) || !is_numeric($data["latitude"]) || !is_numeric($data["longitude"])) {
			return $response->setStatusCode(400)
				->setContent(json_encode(["status" => "error", "message" => "Invalid data types."]));
		}

		// Check if the place already exists
		$place = Place::getById($data["name"]);
		if (!$place) {
			// Add place
			$place = new Place(name: $data["name"],
				latitude: (float)$data["latitude"],
				longitude: (float)$data["longitude"]);
			try {
				$place->persist();
			} catch (\Exception $e) {
				return $response->setStatusCode(500)
					->setContent(json_encode(["status" => "error", "message" => "Place already exists."]));
			}
		}

		// Check if the user already has this place in favourite places
		$favouritePlaces = $user->getFavouritePlaces();
		foreach ($favouritePlaces as $favouritePlace) {
			if ($favouritePlace->getName() === $place->getName()) {
				return $response->setStatusCode(400)
					->setContent(json_encode(["status" => "error", "message" => "The place is already in favourite places."]));
			}
		}

		// Add place to user
		$user->addFavouritePlace($place);
		try {
			$user->persist();
		} catch (\Exception $e) {
			error_log($e->getMessage());
			return $response->setStatusCode(500)
				->setContent(json_encode(["status" => "error", "message" => "Failed to add place to favourite places."]));
		}

		return $response->setStatusCode(200)
			->setContent(json_encode(["status" => "success"]));
	}

	public function removePlace(User $user, string $city): Response {
		// Response
		$response = new Response();
		$response->setJSON();

		// Check if the place exists
		$place = Place::getById($city);
		if (!$place) {
			return $response->setStatusCode(400)
				->setContent(json_encode(["status" => "error", "message" => "The place does not exist."]));
		}

		// Check if the user has this place in favourite places
		$favouritePlaces = $user->getFavouritePlaces();
		$matchingPlaces = array_filter($favouritePlaces, fn($p) => $p->getName() === $place->getName());
		$found = !empty($matchingPlaces);

		if (!$found) {
			return $response->setStatusCode(400)
				->setContent(json_encode(["status" => "error", "message" => "The place is not in favourite places."]));
		}

		// Remove place from user
		$user->removeFavouritePlace($place);
		try {
			$user->persist();
		} catch (\Exception $e) {
			return $response->setStatusCode(500)
				->setContent(json_encode(["status" => "error", "message" => "Failed to remove place from favourite places."]));
		}

		return $response->setStatusCode(200)
			->setContent(json_encode(["status" => "success"]));
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