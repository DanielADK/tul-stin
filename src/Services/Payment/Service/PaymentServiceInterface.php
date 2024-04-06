<?php

namespace StinWeatherApp\Services\Payment\Service;

use StinWeatherApp\Model\Payment;

/**
 * Interface PaymentServiceInterface
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Interface for payment services
 * @package StinWeatherApp\Services\Payment\Service
 */
interface PaymentServiceInterface {
	/**
	 * @description Processes the payment
	 *
	 * @param Payment $payment
	 *
	 * @return bool
	 */
	public function processPayment(Payment $payment): bool;
}