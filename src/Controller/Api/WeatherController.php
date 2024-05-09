<?php

namespace StinWeatherApp\Controller\Api;

use DateTime;
use Exception;
use StinWeatherApp\Component\Auth\ApiKeyAuth;
use StinWeatherApp\Component\Http\Response;
use StinWeatherApp\Component\Router\Router;
use StinWeatherApp\Controller\AbstractController;
use StinWeatherApp\Services\WeatherFetch\Translators\OpenMeteoTranslator;
use StinWeatherApp\Services\WeatherFetch\WeatherFetchService;

class WeatherController extends AbstractController {
	/**
	 * @throws \Exception
	 */
	private function getDateByAuth(): DateTime {
		$auth = new ApiKeyAuth();
		$now = new DateTime();

		if (!$auth->login($this->request)) {
			return $now;
		}
		// if getUser throws an exception, user not found -> not authenticated
		$auth->getUser();

		$day = $this->request->getGet("date");
		if (!is_string($day)) {
			return $now;
		}
		$now = DateTime::createFromFormat("Y-m-d", $day);
		if (!$now) {
			throw new Exception("Invalid date format. Use YYYY-MM-DD.");
		}
		return $now;
	}
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

		// get date by auth
		try {
			$date = $this->getDateByAuth();
		} catch (Exception $e) {
			// if getUser throws an exception, user not found -> not authenticated
			// if apiKey not set -> now
			$response->setStatusCode(401);
			$response->setContent(json_encode(["status" => "error", "message" => $e->getMessage()]));
			return $response;
		}

		$translator = new OpenMeteoTranslator($date->format("Y-m-d"), $longitude, $latitude);
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