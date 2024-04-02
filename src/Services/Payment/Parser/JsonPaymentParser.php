<?php

namespace StinWeatherApp\Services\Payment\Parser;

use DateTime;
use InvalidArgumentException;
use StinWeatherApp\Component\Parser\JsonParseable;
use StinWeatherApp\Model\Builder\PaymentBuilder;
use StinWeatherApp\Model\Card;
use StinWeatherApp\Model\Payment;
use StinWeatherApp\Model\Types\Currency;
use StinWeatherApp\Model\Types\PaymentType;

/**
 * Class JsonPremiumParser
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Parses the JSON data into a Payment object
 * @package StinWeatherApp\Services\Payment\Parser
 */
class JsonPaymentParser extends JsonParseable implements PaymentParserInterface {

	/**
	 * @inheritdoc
	 */
	#[\Override]
	public function parse(string $data): Payment {
		$paymentData = json_decode($data, true);

		if (json_last_error() !== JSON_ERROR_NONE || !is_array($paymentData)) {
			throw new InvalidArgumentException('Invalid JSON provided');
		}

		foreach (self::requiredKeys as $key) {
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

		if (PaymentType::from($paymentData['type']) == PaymentType::CARD) {
			foreach (Card::cardKeys as $key) {
				if (!array_key_exists($key, $paymentData)) {
					throw new InvalidArgumentException("Missing required key: {$key}");
				}
			}
			$card = new Card($paymentData["cardNumber"], $paymentData["expiryDate"], $paymentData["code"]);
			$pb->setCard($card);
		}

		return $pb->build();
	}
}