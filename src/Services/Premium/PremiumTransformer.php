<?php

namespace StinWeatherApp\Services\Premium;

use Exception;
use StinWeatherApp\Model\Buyable\Premium;
use StinWeatherApp\Services\Premium\Parser\JsonPremiumParser;
use StinWeatherApp\Services\Premium\Parser\XmlPremiumParser;

/**
 * Class PremiumTransformer
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Transformer for premium data
 * @package StinWeatherApp\Services\Premium
 */
class PremiumTransformer {
	private JsonPremiumParser $jsonParser;
	private XMLPremiumParser $xmlParser;

	/**
	 * PremiumTransformer constructor.
	 */
	public function __construct() {
		$this->jsonParser = new JsonPremiumParser();
		$this->xmlParser = new XMLPremiumParser();
	}

	/**
	 * @description Transforms the premium data from raw format
	 *
	 * @param string $input
	 *
	 * @return Premium
	 * @throws Exception
	 */
	public function transform(string $input): Premium {
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
	 * @return Premium
	 * @throws Exception
	 */
	private function transformJson(string $json): Premium {
		return $this->jsonParser->parse($json);
	}

	/**
	 * @description Transforms raw the premium from XML
	 *
	 * @param string $xml
	 *
	 * @return Premium
	 * @throws Exception
	 */
	private function transformXML(string $xml): Premium {
		return $this->xmlParser->parse($xml);
	}
}