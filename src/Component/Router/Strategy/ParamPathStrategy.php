<?php

namespace StinWeatherApp\Component\Router\Strategy;

use StinWeatherApp\Component\Router\Strategy\PathStrategyInterface;

/**
 * Class ParamPathStrategy
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Strategy for regex path matching
 * @package StinWeatherApp\Component\Router\Strategy
 */
class ParamPathStrategy implements PathStrategyInterface {

	/**
	 * @inheritdoc
	 * @param string $path
	 * @return bool
	 */
	#[\Override]
	public function matches(string $path, string $requestPath): bool {
		$pathAsRegex = "@^" . preg_replace('/:\w+/', '(\w+)', $path) . "$@D";
		return preg_match($pathAsRegex, $requestPath) === 1;
	}
}