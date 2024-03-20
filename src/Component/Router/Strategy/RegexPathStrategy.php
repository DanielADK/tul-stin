<?php

namespace StinWeatherApp\Component\Router\Strategy;

use StinWeatherApp\Component\Router\Strategy\PathStrategyInterface;

/**
 * Class RegexPathStrategy
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Strategy for regex path matching
 * @package StinWeatherApp\Component\Router\Strategy
 */
class RegexPathStrategy implements PathStrategyInterface {

	/**
	 * @inheritdoc
	 * @param string $path
	 * @return bool
	 */
	#[\Override]
	public function matches(string $path, string $requestPath): bool {
		return preg_match("~^{$path}$~", $requestPath) === 1;
	}
}