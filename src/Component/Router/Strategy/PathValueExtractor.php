<?php

namespace StinWeatherApp\Component\Router\Strategy;

use StinWeatherApp\Component\Http\Request;

class PathValueExtractor {
	/**
	 * Extracts the values of variables from the real path based on the pattern path
	 *
	 * @param string $patternPath The pattern path
	 * @param Request $request The request
	 * @return array<string, string> The values of the variables
	 */
	public function extractValue(string $patternPath, Request $request): array {
		$realPath = $request->getPath();
		$patternParts = explode('/', $patternPath);
		$realPathParts = explode('/', $realPath);

		$variables = array();
		foreach ($patternParts as $index => $part) {
			if (str_starts_with($part, ':')) { // variable
				$variableName = substr($part, 1); // remove the ':' prefix
				if (isset($realPathParts[$index])) {
					$variables[$variableName] = $realPathParts[$index];
				}
			}
		}

		return $variables;
	}
}