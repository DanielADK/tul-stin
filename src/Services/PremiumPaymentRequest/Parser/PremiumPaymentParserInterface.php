<?php

namespace StinWeatherApp\Services\PremiumPaymentRequest\Parser;

interface PremiumPaymentParserInterface {
	const array requiredKeys = ['username', 'email', 'premiumOption', "paymentType"];
	const string cardKey = "card";
	const array cardKeys = ['cardNumber', 'cardExpiration', 'cardCode'];

	/**
	 * @description Parses the data and returns an associative array
	 *
	 * @param string $data
	 *
	 * @return array<string, string>
	 */
	public static function parse(string $data): array;
}