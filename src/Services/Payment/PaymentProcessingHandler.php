<?php

namespace StinWeatherApp\Services\Payment;

use Exception;
use StinWeatherApp\Services\Payment\Service\CardPaymentService;
use StinWeatherApp\Services\Payment\Service\CashPaymentService;
use StinWeatherApp\Services\Payment\Service\PaymentServiceInterface;

/**
 * Class PaymentProcessingHandler
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Handler for payment processing
 * @package StinWeatherApp\Services\Payment
 */
class PaymentProcessingHandler {
	/** @var array<string, PaymentServiceInterface> */
	private array $paymentServices;
	private PaymentTransformer $paymentTransformer;

	/**
	 * PaymentProcessingHandler constructor.
	 *
	 * @param CardPaymentService $cardPaymentService
	 * @param CashPaymentService $cashPaymentService
	 * @param PaymentTransformer $paymentTransformer
	 */
	public function __construct(CardPaymentService $cardPaymentService,
	                            CashPaymentService $cashPaymentService,
	                            PaymentTransformer $paymentTransformer) {
		$this->paymentTransformer = $paymentTransformer;
		$this->paymentServices = [
			"CARD" => $cardPaymentService,
			"CASH" => $cashPaymentService
		];
	}

	/**
	 * @description Processes the payment
	 *
	 * @param string $payload
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function processPayment(string $payload): bool {
		$payment = $this->paymentTransformer->transform($payload);
		$paymentType = $payment->getType()->value;
		if (!array_key_exists($paymentType, $this->paymentServices)) {
			return false;
		}
		return $this->paymentServices[$paymentType]->processPayment($payment);

	}
}