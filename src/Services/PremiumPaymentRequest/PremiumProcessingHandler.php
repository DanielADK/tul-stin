<?php

namespace StinWeatherApp\Services\PremiumPaymentRequest;

use StinWeatherApp\Model\Buyable\Premium;

/**
 * Class PaymentProcessingHandler
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Handler for payment processing
 * @package StinWeatherApp\Services\Payment
 */
class PremiumProcessingHandler {
	private PremiumTransformer $premiumTransformer;

	/**
	 * PaymentProcessingHandler constructor.
	 *
	 * @param PremiumTransformer $premiumTransformer
	 */
	public function __construct(PremiumTransformer $premiumTransformer) {
		$this->premiumTransformer = $premiumTransformer;
	}

	/**
	 * @throws \Exception
	 */
	public function getPremiumFromPayload(string $payload): Premium {
		return $this->premiumTransformer->transform($payload);
	}
}