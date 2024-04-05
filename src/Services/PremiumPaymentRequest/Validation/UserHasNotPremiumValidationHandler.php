<?php

namespace StinWeatherApp\Services\PremiumPaymentRequest\Validation;

use DateTime;
use Exception;
use Override;
use StinWeatherApp\Model\User;

class UserHasNotPremiumValidationHandler extends ValidationHandler {

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
		if ($user->getPremiumUntil() !== null && $user->getPremiumUntil() > new DateTime()) {
			throw new Exception('User does not have premium.');
		}
	}
}