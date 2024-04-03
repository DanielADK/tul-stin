<?php

namespace StinWeatherApp\Services\Payment;

use Exception;
use StinWeatherApp\Model\Payment;
use StinWeatherApp\Services\Payment\Service\CardPaymentService;
use StinWeatherApp\Services\Payment\Service\CashPaymentService;
use StinWeatherApp\Services\Payment\Service\PaymentServiceInterface;
use StinWeatherApp\Services\PremiumPaymentRequest\PremiumPaymentTransformer;

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
	private PremiumPaymentTransformer $premiumTransformer;

	/**
	 * PaymentProcessingHandler constructor.
	 *
	 * @param CardPaymentService        $cardPaymentService
	 * @param CashPaymentService        $cashPaymentService
	 * @param PaymentTransformer        $paymentTransformer
	 * @param PremiumPaymentTransformer $premiumTransformer
	 */
	public function __construct(CardPaymentService $cardPaymentService,
	                            CashPaymentService $cashPaymentService,
	                            PaymentTransformer        $paymentTransformer,
	                            PremiumPaymentTransformer $premiumTransformer
	) {
		$this->paymentTransformer = $paymentTransformer;
		$this->premiumTransformer = $premiumTransformer;
		$this->paymentServices = [
			"CARD" => $cardPaymentService,
			"CASH" => $cashPaymentService
		];
	}

	/**
	 * @description Processes the payment
	 *
	 * @param Payment $payment
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function processPayment(Payment $payment): bool {
		$payment->setStatus("PREPROCESSING");
		$paymentType = $payment->getType()->value;
		if (!array_key_exists($paymentType, $this->paymentServices)) {
			throw new Exception("Unsupported payment type");
		}

		$payment->setStatus("PAYMENT");
		$payment->persist();
		$result = $this->paymentServices[$paymentType]->processPayment($payment);

		if ($result) {
			$payment->setStatus("DONE");
		} else {
			$payment->setStatus("FAILED");
		}
		$payment->persist();
		return $result;
	}
}