<?php

namespace StinWeatherApp\Services\Payment;

use StinWeatherApp\Model\Payment;

class CashPaymentServiceInterface implements PaymentServiceInterface {
	private PaymentServiceProcess $paymentServiceProcess;

	public function __construct(PaymentServiceProcess $paymentServiceProcess) {
		$this->paymentServiceProcess = $paymentServiceProcess;
	}

	/**
	 * @param Payment $payment
	 *
	 * @return void
	 */
	#[\Override]
	public function processPayment(Payment $payment): void {

	}
}