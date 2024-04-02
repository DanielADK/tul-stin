<?php

namespace StinWeatherApp\Services\Premium\Parser;

use StinWeatherApp\Model\Buyable\Premium;

interface PremiumParserInterface {
	const array requiredKeys = ['username', 'email', 'premiumOption'];

	public function parse(string $data): Premium;
}