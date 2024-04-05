<?php

namespace StinWeatherApp\Services\PremiumPaymentRequest\Validation;

use Exception;
use StinWeatherApp\Model\Card;
use StinWeatherApp\Services\PremiumPaymentRequest\Parser\PremiumPaymentParserInterface;

class CardValidationHandler extends ValidationHandler {

	/**
	 * @inheritDoc
	 */
	#[\Override] protected function validate(): void {
		$cardKey = PremiumPaymentParserInterface::cardKey;
		if (!is_array($this->data[$cardKey])) {
			throw new Exception('No information about card detected.');
		}
		if (!is_string($this->data[$cardKey]["cardNumber"])) {
			throw new Exception('No information about card number detected.');
		}
		if (!is_string($this->data[$cardKey]["cardExpiration"])) {
			throw new Exception('No information about card expiration date detected.');
		}
		if (!is_string($this->data[$cardKey]["cardCode"])) {
			throw new Exception('No information about card expiration date detected.');
		}
		if (Card::validateNumber($this->data[$cardKey]["cardNumber"]) === false) {
			throw new Exception('Invalid card number.');
		}
		if (Card::validateExpiration($this->data[$cardKey]["cardExpiration"]) === false) {
			throw new Exception('Invalid card expiration date.');
		}
		if (Card::validateCode($this->data[$cardKey]["cardCode"]) === false) {
			throw new Exception('Invalid card code.');
		}
	}
}