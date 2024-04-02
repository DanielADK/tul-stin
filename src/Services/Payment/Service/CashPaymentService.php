<?php

namespace StinWeatherApp\Services\Payment\Service;

use StinWeatherApp\Model\Payment;
use StinWeatherApp\Model\Types\PaymentType;
use StinWeatherApp\Services\Payment\PaymentServiceProcess;

/**
 * Class CashPaymentService
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Service for cash payments
 * @package StinWeatherApp\Services\Payment\Service
 */
class CashPaymentService implements PaymentServiceInterface {
	private PaymentServiceProcess $paymentServiceProcess;

	/**
	 * CashPaymentService constructor.
	 *
	 * @param PaymentServiceProcess $paymentServiceProcess
	 */
	public function __construct(PaymentServiceProcess $paymentServiceProcess) {
		$this->paymentServiceProcess = $paymentServiceProcess;
	}

	/**
	 * @description Processes the payment by cash
	 *
	 * @param Payment $payment
	 *
	 * @return bool
	 */
	#[\Override]
	public function processPayment(Payment $payment): bool {
		$payment->setType(PaymentType::CASH);
		if ($this->paymentServiceProcess->pay($payment)) {
			$payment->setStatus("DONE");
			return true;
		} else {
			$payment->setStatus("FAILED");
			return false;
		}
	}
}