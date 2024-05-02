<?php

namespace StinWeatherApp\Controller\Api;

use StinWeatherApp\Component\Http\Response;
use StinWeatherApp\Component\Router\Router;
use StinWeatherApp\Controller\AbstractController;
use StinWeatherApp\Services\WeatherFetch\Translators\OpenMeteoTranslator;
use StinWeatherApp\Services\WeatherFetch\WeatherFetchService;

class WeatherController extends AbstractController {
	public function getWeather(): Response {
		$response = new Response();
		$response->setJSON();

		$latitude = $this->request->getGet("latitude");
		$longitude = $this->request->getGet("longitude");

		if ($latitude === null || $longitude === null) {
			$response->setStatusCode(400);
			$response->setContent(json_encode(["status" => "Latitude and longitude are required."]));
			return $response;
		}

		if (!is_numeric($latitude) || !is_numeric($longitude)) {
			$response->setStatusCode(400);
			$response->setContent(json_encode(["status" => "Latitude and longitude must be numeric."]));
			return $response;
		}

		$translator = new OpenMeteoTranslator("1", $longitude, $latitude);
		$weatherService = new WeatherFetchService($translator);
		try {
			$fetched_data = $weatherService->fetch();
			$weatherData = $translator->translate($fetched_data);
		} catch (\Exception $e) {
			$content = json_encode(["status" => "failed"]);
			error_log($e);
			if ($content === false) {
				$response->setStatusCode(500);
				$response->setContent("Failed to encode JSON.");
				return $response;
			}
			$response->setStatusCode(200);
			$response->setContent($content);
			return $response;
		}

		$content = json_encode(["status" => "success", "data" => $weatherData]);
		if ($content === false) {
			$response->setStatusCode(500);
			$response->setContent("Failed to encode JSON.");
			return $response;
		}
		$response->setStatusCode(200);
		$response->setContent($content);
		return $response;
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