<?php

namespace StinWeatherApp\Services\PremiumPaymentRequest\Validation;

use Exception;
use Override;
use StinWeatherApp\Model\User;

/**
 * Class ExistsUserValidationHandler
 *
 * @description Class for user validation handler
 * @package StinWeatherApp\Services\PremiumPaymentRequestDto\Validator
 */
class ExistsUserValidationHandler extends ValidationHandler {

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

		// Set User
		$this->dto->setUser($user);
	}
}