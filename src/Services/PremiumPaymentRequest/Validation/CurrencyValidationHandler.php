<?php

namespace StinWeatherApp\Services\PremiumPaymentRequest\Validation;

use Exception;
use Override;
use StinWeatherApp\Model\Types\Currency;

class CurrencyValidationHandler extends ValidationHandler {

	/**
	 * @inheritDoc
	 */
	#[Override]
	protected function validate(): void {
		if (!is_string($this->data["currency"])) {
			throw new Exception('No information about currency detected.');
		}
		$currency = Currency::fromString($this->data["currency"]);
		if ($currency === null) {
			throw new Exception('Invalid currency.');
		}

		// Set Currency
		$this->dto->setCurrency($currency);
	}
}