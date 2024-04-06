<?php

namespace StinWeatherApp\Services\Payment;

use StinWeatherApp\Model\Payment;
use StinWeatherApp\Model\Types\Currency;

/**
 * Class PaymentServiceProcess
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Service for payment processing
 * @package StinWeatherApp\Services\Payment
 */
class PaymentServiceProcess {

	/**
	 * @description Processes the payment
	 *
	 * @param Payment $payment
	 *
	 * @return bool
	 */
	public function pay(Payment $payment): bool {
		// Payment processing logic
		if ($payment->getAmount() > 1000 && $payment->getCurrency() == Currency::CZK) {
			return true;
		}
		return false;
	}
}