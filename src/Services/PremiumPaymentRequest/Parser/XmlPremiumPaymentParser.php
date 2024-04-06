<?php

namespace StinWeatherApp\Services\PremiumPaymentRequest\Parser;

use Exception;
use InvalidArgumentException;
use SimpleXMLElement;
use StinWeatherApp\Component\Parser\XmlParseable;
use StinWeatherApp\Services\Payment\Parser\JsonPaymentParser;

class XmlPremiumPaymentParser extends XmlParseable implements PremiumPaymentParserInterface {

	/**
	 * @inheritdoc
	 */
	public static function parse(string $data): array {
		try {
			$xml = new SimpleXMLElement($data);
		} catch (Exception $e) {
			throw new InvalidArgumentException('Invalid XML provided', 0, $e);
		}

		// XML to associative array with json parsing
		$json = json_encode($xml);
		if ($json === false) {
			throw new InvalidArgumentException('Invalid XML provided');
		}

		return JsonPremiumPaymentParser::parse($json);

	}
}