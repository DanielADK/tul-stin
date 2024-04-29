<?php

namespace StinWeatherApp\Services\WeatherFetch\Translators;

use Exception;

/**
 * Class Translator
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Translator for weather data
 * @package StinWeatherApp\Services\WeatherGrabber\Translators
 */
abstract class Translator {
	/** @var array<string, string> */
	private array $parameters;
	/** @var array<string> */
	private array $expectedKeys;
	/** @var array<string, string> */
	private array $translationKeys;
	private string $url;

	/**
	 * Translator constructor.
	 *
	 * @param string $url
	 */
	public function __construct(string $url) {
		$this->url = $url;
		$this->parameters = array();
		$this->expectedKeys = array();
		$this->translationKeys = array();
	}

	/**
	 * @description Prepares the URL for fetching the weather data
	 * @return string
	 */
	public function getFormattedUrl(): string {
		return $this->url . '?' . http_build_query($this->parameters);
	}

	/**
	 * @description Returns the parameters
	 * @return array<string, string>
	 */
	public function getParameters(): array {
		return $this->parameters;
	}

	/**
	 * @description Sets the parameter
	 *
	 * @param string $index
	 * @param string $value
	 */
	public function setParameter(string $index, string $value): void {
		$this->parameters[$index] = $value;
	}

	/**
	 * @description Returns the URL
	 * @return string
	 */
	public function getRawUrl(): string {
		return $this->url;
	}

	/**
	 * @description Sets the URL
	 *
	 * @param string $url
	 */
	public function setUrl(string $url): void {
		$this->url = $url;
	}

	/**
	 * @description Returns the arguments to return
	 * @return array<string>
	 */
	public function getExpectedKeys(): array {
		return $this->expectedKeys;
	}

	/**
	 * @description Sets the arguments to return
	 *
	 * @param array<string> $expectedKeys
	 */
	public function setExpectedKeys(array $expectedKeys): void {
		$this->expectedKeys = $expectedKeys;
	}

	/**
	 * @description Returns the translation keys
	 * @return array<string, string>
	 */
	public function getTranslationKeys(): array {
		return $this->translationKeys;
	}

	/**
	 * @description Sets the translation keys
	 *
	 * @param array<string, string> $translationKeys
	 */
	public function setTranslationKeys(array $translationKeys): void {
		$this->translationKeys = $translationKeys;
	}

	/**
	 * @description Translates the weather data from raw format
	 *
	 * @param string $data
	 *
	 * @return array<string, string>
	 */
	abstract public function translate(string $data): array;

	/**
	 * @description Returns the keys to return. Inner keys parsed by a dot
	 *
	 * @param array<string> $input array of data
	 *
	 * @return bool
	 * @throws Exception
	 */
	protected function validateExpectedKeys(array &$input): bool {
		// Does not contain the required keys (in $this->expectedKeys)
		// Inner keys are separated by a dot
		foreach ($this->expectedKeys as $key) {
			$keys = explode(".", $key);
			$cursor = &$input;
			foreach ($keys as $k) {
				if (!is_array($cursor)) {
					throw new Exception("Key not found: " . $key);
				}
				if (!array_key_exists($k, $cursor)) {
					throw new Exception("Key not found: " . $key);
				}
				$cursor = &$cursor[$k];
			}
		}
		return true;
	}

	/**
	 * @description Translates the keys from the raw data to the desired format
	 * @description Supports inner keys separated by a dot. Returns only the keys specified in translationKeys
	 *
	 * @param array<string, string> $input
	 *
	 * @return array<string, string>
	 */
	protected function translateKeys(array $input): array {
		$translated = array();
		foreach ($this->translationKeys as $oldKey => $newKey) {
			$oldKeys = explode(".", $oldKey);
			$newKeys = explode(".", $newKey);

			$cursor = &$input;
			foreach ($oldKeys as $k) {
				if (!isset($cursor[$k])) {
					continue 2; // Skip to the next translation key if the current key is not found
				}
				$cursor = &$cursor[$k];
			}

			// $cursor is the value we want to translate
			$value = $cursor;

			// insert the value into the translated array at the new key
			$cursor = &$translated;
			foreach ($newKeys as $k) {
				if (!isset($cursor[$k])) {
					$cursor[$k] = array();
				}
				$cursor = &$cursor[$k];
			}

			// $cursor is the location where we want to insert the value
			$cursor = $value;
		}
		return $translated;
	}
}