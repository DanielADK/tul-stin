<?php

namespace StinWeatherApp\Services\Payment\Parser;

use Exception;
use InvalidArgumentException;
use SimpleXMLElement;
use StinWeatherApp\Model\Builder\PaymentBuilder;
use StinWeatherApp\Model\Payment;
use StinWeatherApp\Model\Types\Currency;
use StinWeatherApp\Model\Types\PaymentType;

class XMLPaymentParser implements PaymentParserInterface {

	/**
	 * @inheritdoc
	 */
	#[\Override]
	public function parse(string $data): Payment {
		try {
			$xml = new SimpleXMLElement($data);
		} catch (Exception $e) {
			throw new InvalidArgumentException('Invalid XML provided', 0, $e);
		}

		foreach (self::requiredKeys as $key) {
			if (!isset($xml->$key)) {
				throw new InvalidArgumentException("Missing required key: {$key}");
			}
		}

		// Check if the currency value is valid
		$currencyValue = (string)$xml->currency;
		if (!Currency::isValid($currencyValue)) {
			throw new InvalidArgumentException("Invalid currency value: {$currencyValue}");
		}

		$pb = new PaymentBuilder();
		$pb->setAmount(isset($xml->amount) ? (float)$xml->amount : 0)
			->setCurrency(Currency::from($currencyValue))
			->setDatetime(new \DateTime((string)$xml->datetime ?: "now"))
			->setType(isset($xml->type) ? PaymentType::from((string)$xml->type) : PaymentType::CASH)
			->setStatus((string)$xml->status ?: "pending");
		return $pb->build();
	}

	/**
	 * @inheritdoc
	 */
	#[\Override]
	public function canParse(string $input): bool {
		$xml = simplexml_load_string($input);
		return $xml !== false;
	}
}