<?php

namespace StinWeatherApp\Services\PremiumPaymentRequest;

use Exception;
use StinWeatherApp\Services\PremiumPaymentRequest\Parser\JsonPremiumPaymentParser;
use StinWeatherApp\Services\PremiumPaymentRequest\Parser\XmlPremiumPaymentParser;

/**
 * Class PremiumPaymentTransformer
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Transformer for premium data
 * @package StinWeatherApp\Services\Premium
 */
class PremiumPaymentTransformer {
	private JsonPremiumPaymentParser $jsonParser;
	private XmlPremiumPaymentParser $xmlParser;

	/**
	 * PremiumPaymentTransformer constructor.
	 */
	public function __construct() {
		$this->jsonParser = new JsonPremiumPaymentParser();
		$this->xmlParser = new XmlPremiumPaymentParser();
	}

	/**
	 * @description Transforms the premium data from raw format
	 *
	 * @param string $input
	 *
	 * @return array<string, string>
	 * @throws Exception
	 */
	public function transform(string $input): array {
		if ($this->jsonParser->canParse($input)) {
			return $this->transformJson($input);
		} elseif ($this->xmlParser->canParse($input)) {
			return $this->transformXML($input);
		} else {
			throw new Exception("Unsupported format");
		}
	}


	/**
	 * @description Transforms raw the premium from JSON
	 *
	 * @param string $json
	 *
	 * @return array<string, string>
	 * @throws Exception
	 */
	private function transformJson(string $json): array {
		return $this->jsonParser->parse($json);
	}

	/**
	 * @description Transforms raw the premium from XML
	 *
	 * @param string $xml
	 *
	 * @return array<string, string>
	 * @throws Exception
	 */
	private function transformXML(string $xml): array {
		return $this->xmlParser->parse($xml);
	}
}