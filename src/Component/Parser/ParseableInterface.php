<?php

namespace StinWeatherApp\Component\Parser;

/**
 * Interface ParseableInterface
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Interface for parseable objects from formats
 * @package StinWeatherApp\Component\Parser
 */
interface ParseableInterface {

	/**
	 * @description Checks if the parser can parse the input
	 *
	 * @param string $input
	 *
	 * @return bool
	 */
	public function canParse(string $input): bool;
}