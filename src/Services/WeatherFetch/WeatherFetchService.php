<?php

namespace StinWeatherApp\Services\WeatherFetch;

use Exception;
use StinWeatherApp\Services\WeatherFetch\Translators\Translator;

/**
 * Class WeatherFetchService
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Service for fetching weather data
 * @package StinWeatherApp\Services\WeatherFetch
 */
class WeatherFetchService implements FetchService {
	private Translator $translator;

	public function __construct(Translator $translator) {
		$this->translator = $translator;
	}

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public function processData(): string {
		$response = $this->fetch();
		return $this->getTranslator()->translate($response);
	}

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public function fetch(): string {
		// Init cURL
		$ch = curl_init();

		// Set URL
		$url = $this->getTranslator()->getFormattedUrl();
		curl_setopt($ch, CURLOPT_URL, $url);

		// Set the options
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);

		// Execute cURL request
		$response = curl_exec($ch);

		// Check for errors
		if (is_bool($response)) {
			curl_close($ch);
			throw new Exception("cURL error: " . curl_error($ch));
		}

		// Close cURL
		curl_close($ch);

		return $response;
	}

	/**
	 * @description Returns the translator
	 * @return Translator
	 */
	public function getTranslator(): Translator {
		return $this->translator;
	}
}