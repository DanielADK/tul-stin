<?php

namespace StinWeatherApp\Services\Premium\Parser;

use InvalidArgumentException;
use StinWeatherApp\Component\Parser\JsonParseable;
use StinWeatherApp\Model\Buyable\Premium;

class JsonPremiumParser extends JsonParseable implements PremiumParserInterface {

	/**
	 * @param string $data
	 *
	 * @return Premium
	 */
	#[\Override]
	public function parse(string $data): Premium {
		$data = json_decode($data, true);

		if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
			throw new InvalidArgumentException('Invalid JSON provided');
		}

		foreach (self::requiredKeys as $key) {
			if (!array_key_exists($key, $data)) {
				throw new InvalidArgumentException("Missing required key: {$key}");
			}
		}

		return new Premium($data['name'], $data['price'], $data['duration']);
	}
}