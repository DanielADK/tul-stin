<?php

namespace StinWeatherApp\Services\WeatherFetch\Translators;

use Exception;
use Override;

/**
 * Class OpenMeteoTranslator
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description OpenMeteo translator
 * @package StinWeatherApp\Services\WeatherGrabber\Translators
 */
class OpenMeteoTranslator extends Translator {

	/**
	 * @param string        $forecast_days
	 * @param string        $longitude
	 * @param string        $latitude
	 * @param array<string> $expectedKeys
	 * @param array<string> $translationKeys
	 */
	public function __construct(string $forecast_days,
	                            string $longitude,
	                            string $latitude,
	                            array  $expectedKeys = array(
		                            "hourly.time",
		                            "hourly.temperature_2m",
		                            "generationtime_ms"
	                            ),
	                            array  $translationKeys = array(
		                            "hourly.time" => "times",
		                            "hourly.temperature_2m" => "temperatures",
		                            "hourly_units.time" => "time_format",
		                            "hourly_units.temperature_2m" => "temperature_unit",
	                            )) {
		parent::__construct('https://api.open-meteo.com/v1/forecast');
		$this->setParameter('forecast_days', $forecast_days);
		$this->setParameter('longitude', $longitude);
		$this->setParameter('latitude', $latitude);
		$this->setParameter('hourly', "temperature_2m");
		$this->setExpectedKeys($expectedKeys);
		$this->setTranslationKeys($translationKeys);
	}

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	#[Override]
	public function translate(string $data): array {
		error_log($data);
		$json = json_decode($data, true);
		// Is not an array
		if (!is_array($json)) {
			throw new Exception("Failed to decode JSON.");
		}

		// Validate
		$this->validateExpectedKeys($json);

		// Translate keys
		return $this->translateKeys($json);
	}
}