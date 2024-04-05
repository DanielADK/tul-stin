<?php

namespace StinWeatherApp\Component\Router\Strategy;

/**
 * Class DirectPathStrategy
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Strategy for direct path matching
 * @package StinWeatherApp\Component\Router\Strategy
 */
class DirectPathStrategy implements PathStrategyInterface {

	/**
	 * @inheritdoc
	 * @param string $path
	 * @return bool
	 */
	#[\Override]
	public function matches(string $path, string $requestPath): bool {
		return $path === $requestPath;
	}}