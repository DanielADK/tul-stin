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
		$cardArr = $this->data[$cardKey];
		if (!is_array($cardArr)) {
			throw new Exception('No information about card detected.');
		}
		if (!is_string($cardArr["cardNumber"])) {
			throw new Exception('No information about card number detected.');
		}
		if (!is_string($cardArr["cardExpiration"])) {
			throw new Exception('No information about card expiration date detected.');
		}
		if (!is_string($cardArr["cardCode"])) {
			throw new Exception('No information about card expiration date detected.');
		}
		if (Card::validateNumber($cardArr["cardNumber"]) === false) {
			throw new Exception('Invalid card number.');
		}
		if (Card::validateExpiration($cardArr["cardExpiration"]) === false) {
			throw new Exception('Invalid card expiration date.');
		}
		if (Card::validateExpirationDate($cardArr["cardExpiration"]) === false) {
			throw new Exception('Card has expired or expiration date is invalid.');
		}
		if (Card::validateCode($cardArr["cardCode"]) === false) {
			throw new Exception('Invalid card code.');
		}

		$card = new Card($cardArr["cardNumber"], $cardArr["cardExpiration"], $cardArr["cardCode"]);

		// Set Card
		$this->dto->setCard($card);
	}
}