<?php

namespace StinWeatherApp\Services\Premium\Parser;

use StinWeatherApp\Model\Buyable\Premium;

interface PremiumParserInterface {
	const array requiredKeys = ['username', 'email', 'premiumOption'];

	/**
	 * @description Parses the data and returns a Premium object
	 *
	 * @param string $data
	 *
	 * @return Premium
	 */
	public function parse(string $data): Premium;
}