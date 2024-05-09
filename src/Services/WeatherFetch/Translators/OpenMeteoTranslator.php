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
	 * @param string $day
	 * @param string        $longitude
	 * @param string        $latitude
	 * @param array<string> $expectedKeys
	 * @param array<string> $translationKeys
	 */
	public function __construct(string $day,
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
		$this->setParameter('start_date', $day);
		$this->setParameter('end_date', $day);
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
		$json = json_decode($data, true);
		error_log($data);
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