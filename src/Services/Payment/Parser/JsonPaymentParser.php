<?php

namespace StinWeatherApp\Services\Payment\Parser;

use DateTime;
use Exception;
use InvalidArgumentException;
use StinWeatherApp\Model\Builder\PaymentBuilder;
use StinWeatherApp\Model\Payment;
use StinWeatherApp\Model\Types\Currency;
use StinWeatherApp\Model\Types\PaymentType;

/**
 * Class JsonPaymentParser
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Parses the JSON data into a Payment object
 * @package StinWeatherApp\Services\Payment\Parser
 */
class JsonPaymentParser implements PaymentParserInterface {

	/**
	 * @description Parses the data and returns a Payment object
	 *
	 * @param string $data
	 *
	 * @return Payment
	 * @throws Exception
	 */
	#[\Override]
	public function parse(string $data): Payment {
		$paymentData = json_decode($data, true);

		if (json_last_error() !== JSON_ERROR_NONE || !is_array($paymentData)) {
			throw new InvalidArgumentException('Invalid JSON provided');
		}

		$requiredKeys = ['amount', 'currency', 'type'];
		foreach ($requiredKeys as $key) {
			if (!array_key_exists($key, $paymentData)) {
				throw new InvalidArgumentException("Missing required key: {$key}");
			}
		}

		$pb = new PaymentBuilder();
		$pb->setAmount($paymentData['amount'])
			->setCurrency(Currency::from($paymentData['currency']))
			->setDatetime(new DateTime($paymentData['datetime'] ?? "now"))
			->setType(PaymentType::from($paymentData['type']))
			->setStatus($paymentData['status'] ?? "pending");
		return $pb->build();
	}

}