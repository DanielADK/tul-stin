<?php

namespace StinWeatherApp\Services\PremiumPaymentRequest\Validation;

use Exception;
use Override;
use StinWeatherApp\Model\Buyable\Premium;

class PremiumValidationHandler extends ValidationHandler {

	/**
	 * @inheritDoc
	 */
	#[Override]
	protected function validate(): void {
		if (!is_string($this->data["premiumOption"])) {
			throw new Exception('No information about premium detected.');
		}
		if (Premium::getById($this->data["premiumOption"]) === null) {
			throw new Exception('Invalid premium option.');
		}
	}
}