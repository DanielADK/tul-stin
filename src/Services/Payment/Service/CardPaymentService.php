<?php

namespace StinWeatherApp\Services\Payment\Service;

use StinWeatherApp\Model\Payment;
use StinWeatherApp\Model\Types\PaymentType;
use StinWeatherApp\Services\Payment\PaymentServiceProcess;

/**
 * Class CardPaymentService
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Service for card payments
 * @package StinWeatherApp\Services\Payment\Service
 */
class CardPaymentService implements PaymentServiceInterface {
	private PaymentServiceProcess $paymentServiceProcess;

	/**
	 * CardPaymentService constructor.
	 *
	 * @param PaymentServiceProcess $paymentServiceProcess
	 */
	public function __construct(PaymentServiceProcess $paymentServiceProcess) {
		$this->paymentServiceProcess = $paymentServiceProcess;
	}

	/**
	 * @description Processes the payment by card
	 *
	 * @param Payment $payment
	 *
	 * @return bool
	 */
	#[\Override]
	public function processPayment(Payment $payment): bool {
		$payment->setType(PaymentType::CARD);
		if ($this->paymentServiceProcess->pay($payment)) {
			$payment->setStatus("DONE");
			return true;
		} else {
			$payment->setStatus("FAILED");
			return false;
		}
	}
}