<?php

namespace StinWeatherApp\Services\PremiumPaymentRequest\Validation;

use Exception;
use StinWeatherApp\Model\Types\PaymentType;

class PaymentTypeValidationHandler extends ValidationHandler {

	/**
	 * @inheritDoc
	 */
	#[\Override]
	protected function validate(): void {
		if (!is_string($this->data["paymentType"])) {
			throw new Exception('No information about payment type detected.');
		}
		$type = PaymentType::fromString($this->data["paymentType"]);
		if ($type === null) {
			throw new Exception('Invalid payment type.');
		}

		// If Payment is by card, then we need to validate the card details
		if ($type === PaymentType::CARD) {
			$moveNext = $this->nextHandler;
			$cardHandler = new CardValidationHandler($this->data, $this->dto);
			$this->nextHandler = $cardHandler;
			if ($moveNext !== null) {
				$this->nextHandler->setNext($moveNext);
			}
		}

		// Set PaymentType
		$this->dto->setPaymentType($type);
	}
}