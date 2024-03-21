<?php

namespace StinWeatherApp\Services\Payment;

use StinWeatherApp\Model\Payment;

interface PaymentServiceInterface {
	public function processPayment(Payment $payment);
}