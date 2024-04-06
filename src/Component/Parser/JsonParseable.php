<?php

namespace StinWeatherApp\Component\Parser;

class JsonParseable implements ParseableInterface {

	/**
	 * @inheritdoc
	 */
	#[\Override]
	public function canParse(string $input): bool {
		return json_validate($input);
	}
}