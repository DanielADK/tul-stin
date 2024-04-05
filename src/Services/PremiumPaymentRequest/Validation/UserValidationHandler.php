<?php

namespace StinWeatherApp\Services\PremiumPaymentRequest\Validation;

use Exception;
use Override;
use StinWeatherApp\Model\User;

/**
 * Class UserValidationHandler
 *
 * @description Class for user validation handler
 * @package StinWeatherApp\Services\PremiumPaymentRequestDTO\Validator
 */
class UserValidationHandler extends ValidationHandler {

	/**
	 * @inheritDoc
	 */
	#[Override]
	protected function validate(): void {
		if (!is_string($this->data["username"])) {
			throw new Exception('No information about user detected.');
		}
		$user = User::getUserByUsername($this->data["username"]);
		if ($user === null) {
			throw new Exception('Invalid user.');
		}
	}
}