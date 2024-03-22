<?php

namespace StinWeatherApp\Component\Router\Strategy;

/**
 * Interface PathStrategyInterface
 * Interface for path strategy
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @package StinWeatherApp\Component\Router\Strategy
 */
interface PathStrategyInterface {
	/**
	 * @description Check if the path matches the strategy
	 * @param string $path
	 * @return bool
	 */
	public function matches(string $path, string $requestPath): bool;
}