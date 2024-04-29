<?php

namespace StinWeatherApp\Controller\Api;

use StinWeatherApp\Component\Http\Response;
use StinWeatherApp\Controller\AbstractController;
use StinWeatherApp\Services\WeatherFetch\Translators\OpenMeteoTranslator;
use StinWeatherApp\Services\WeatherFetch\WeatherFetchService;

class WeatherController extends AbstractController {
	public function getWeather(): Response {

		$latitude = $this->request->getGet("latitude");
		$longitude = $this->request->getGet("longitude");

		if ($latitude === null || $longitude === null) {
			return new Response(json_encode(["status" => "Latitude and longitude are required."]), 400);
		}

		if (!is_numeric($latitude) || !is_numeric($longitude)) {
			return new Response(json_encode(["status" => "Latitude and longitude must be numeric."]), 400);
		}

		$translator = new OpenMeteoTranslator("1", $longitude, $latitude);
		$weatherService = new WeatherFetchService($translator);
		try {
			$weatherData = $weatherService->fetch();
			$weatherData = $translator->translate($weatherData);
		} catch (\Exception $e) {
			$content = json_encode(["status" => "failed"]);
			error_log($e);
			return ($content === false) ? new Response("Failed to encode JSON.", 500) : new Response($content, 500);
		}

		$content = json_encode(["status" => "success", "data" => $weatherData]);
		return ($content === false) ? new Response("Failed to encode JSON.", 500) : new Response($content, 200);
	}
}