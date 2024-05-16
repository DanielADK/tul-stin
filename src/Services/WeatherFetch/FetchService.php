<?php

namespace StinWeatherApp\Services\WeatherFetch;

/**
 * Interface FetchService
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Service for fetching weather data
 * @package StinWeatherApp\Services\WeatherFetch
 */
interface FetchService {
	/**
	 * @description Fetches the weather data
	 * @return string
	 */
	public function fetch(): string;

	/**
	 * @description Processes the weather data
	 * @return array
	 */
	public function processData(): array;
}