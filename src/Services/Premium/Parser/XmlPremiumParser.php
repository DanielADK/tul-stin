<?php

namespace StinWeatherApp\Services\Premium\Parser;

use Exception;
use InvalidArgumentException;
use SimpleXMLElement;
use StinWeatherApp\Component\Parser\XmlParseable;
use StinWeatherApp\Model\Builder\PremiumBuilder;
use StinWeatherApp\Model\Buyable\Premium;

class XmlPremiumParser extends XmlParseable implements PremiumParserInterface {

	/**
	 * @inheritdoc
	 */
	#[\Override]
	public function parse(string $data): Premium {
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

		$pb = new PremiumBuilder();
		$pb->setName((string)$xml->name)
			->setPrice((float)$xml->price)
			->setDuration((int)$xml->duration);
		return $pb->build();
	}
}