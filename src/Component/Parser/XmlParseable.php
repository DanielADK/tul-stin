<?php

namespace StinWeatherApp\Component\Parser;

abstract class XmlParseable implements ParseableInterface {

	/**
	 * @inheritDoc
	 */
	#[\Override]
	public function canParse(string $input): bool {
		$xml = simplexml_load_string($input);
		return $xml !== false;
	}
}