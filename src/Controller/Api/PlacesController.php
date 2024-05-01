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

		// Add place
		$place = new Place(name: $data["name"],
			latitude: $data["latitude"],
			longitude: $data["longitude"]);
		$place->persist();

		// Add place to user
		$user->addFavouritePlace($place);
		$user->persist();
	}
}