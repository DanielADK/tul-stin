<?php

namespace StinWeatherApp\Controller\Api;

use StinWeatherApp\Component\Http\Response;
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
			$place->persist();
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
			return $response->setStatusCode(500)
				->setContent(json_encode(["status" => "error", "message" => "Failed to add place to favourite places."]));
		}

		return $response->setStatusCode(200)
			->setContent(json_encode(["status" => "success"]));
	}
}