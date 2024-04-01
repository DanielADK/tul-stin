<?php

namespace StinWeatherApp\Services\Payment;

use Exception;
use StinWeatherApp\Model\Payment;
use StinWeatherApp\Services\Payment\Parser\JsonPaymentParser;
use StinWeatherApp\Services\Payment\Parser\XMLPaymentParser;

/**
 * Class PaymentTransformer
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Transformer for payment from raw format to object
 * @package StinWeatherApp\Services\Payment
 */
class PaymentTransformer {
	private JsonPaymentParser $jsonPaymentParser;
	private XMLPaymentParser $xmlPaymentParser;

	/**
	 * PaymentTransformer constructor.
	 */
	public function __construct() {
		$this->jsonPaymentParser = new JsonPaymentParser();
		$this->xmlPaymentParser = new XMLPaymentParser();
	}

	/**
	 * @description Transforms the payment from raw format
	 *
	 * @param string $input
	 *
	 * @return Payment
	 * @throws Exception
	 */
	public function transform(string $input): Payment {
		if ($this->jsonPaymentParser->canParse($input)) {
			return $this->transformJson($input);
		} elseif ($this->xmlPaymentParser->canParse($input)) {
			return $this->transformXML($input);
		} else {
			throw new Exception("Unsupported format");
		}
	}

	/**
	 * @description Transforms raw the payment from JSON
	 *
	 * @param string $json
	 *
	 * @return Payment
	 * @throws Exception
	 */
	private function transformJson(string $json): Payment {
		return $this->jsonPaymentParser->parse($json);
	}

	/**
	 * @description Transforms raw the payment from XML
	 *
	 * @param string $xml
	 *
	 * @return Payment
	 * @throws Exception
	 */
	private function transformXML(string $xml): Payment {
		return $this->xmlPaymentParser->parse($xml);
	}
}