<?php

namespace StinWeatherApp\Controller\Api;

use StinWeatherApp\Component\Auth\ApiKeyAuth;
use StinWeatherApp\Component\Http\Response;
use StinWeatherApp\Controller\AbstractController;

class PlacesController extends AbstractController {
	/**
	 * @throws \Exception
	 */
	public function getPlaces(): Response {
		// Response
		$response = new Response();
		$response->setJSON();

		// Auth
		$auth = new ApiKeyAuth();
		$login = $auth->login($this->request);
		if (!$login) {
			$response->setStatusCode(401);
			$response->setContent(json_encode(["status" => "failed", "description" => "Unauthorized"]));
			return $response;
		}
		try {
			$user = $auth->getUser();
		} catch (\Exception $e) {
			$response->setStatusCode(401);
			$response->setContent(json_encode(["status" => "failed", "description" => "Unauthorized"]));
			return $response;
		}

		$placesAsStr = array();
		foreach ($user->getFavouritePlaces() as $place) {
			$placesAsStr[] = array("name" => $place->getName(), "latitude" => $place->getLatitude(), "longitude" => $place->getLongitude());
		}
		$arr_content = ["status" => "success", "places" => $placesAsStr];
		$content = json_encode($arr_content);
		if ($content === false) {
			$response->setStatusCode(500);
			$response->setContent("Failed to encode JSON.");
			return $response;
		}

		$response->setStatusCode(200);
		$response->setContent($content);
		return $response;
	}
}