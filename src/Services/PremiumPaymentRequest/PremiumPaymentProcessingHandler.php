<?php

namespace StinWeatherApp\Services\PremiumPaymentRequest;

use Exception;

/**
 * Class PaymentProcessingHandler
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Handler for payment processing
 * @package StinWeatherApp\Services\Payment
 */
class PremiumPaymentProcessingHandler {
	private PremiumPaymentTransformer $premiumTransformer;

	/**
	 * PaymentProcessingHandler constructor.
	 *
	 * @param PremiumPaymentTransformer $premiumTransformer
	 */
	public function __construct(PremiumPaymentTransformer $premiumTransformer) {
		$this->premiumTransformer = $premiumTransformer;
	}

	/**
	 * @return array<string, string>
	 * @throws Exception
	 */
	public function getPremiumFromPayload(string $payload): array {
		return $this->premiumTransformer->transform($payload);
	}
}