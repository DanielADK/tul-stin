<?php

namespace StinWeatherApp\Services\PremiumPaymentRequest\Parser;

use InvalidArgumentException;
use StinWeatherApp\Component\Parser\JsonParseable;
use StinWeatherApp\Model\Types\PaymentType;

class JsonPremiumPaymentParser extends JsonParseable implements PremiumPaymentParserInterface {

	/**
	 * @inheritDoc
	 */
	public static function parse(string $data): array {
		$data = json_decode($data, true);

		// Parsing error
		if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
			throw new InvalidArgumentException('Invalid JSON provided');
		}

		// Required keys
		foreach (self::requiredKeys as $key) {
			if (!array_key_exists($key, $data)) {
				throw new InvalidArgumentException("Missing required key: {$key}");
			}
		}

		// Payment required keys
		// PaymentTypes are uppered
		$paymentType = PaymentType::tryFrom(strtoupper($data["paymentType"]));
		if ($paymentType !== null && array_key_exists(self::cardKey, $data)) {
			foreach (self::cardKeys as $key) {
				if (!array_key_exists($key, $data["card"])) {
					throw new InvalidArgumentException("Missing required key: {$key}");
				}
			}
		} else {
			throw new InvalidArgumentException("Invalid payment type.");
		}

		return $data;
	}
}