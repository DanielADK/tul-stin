<?php

namespace StinWeatherApp\Controller;


use Exception;
use StinWeatherApp\Component\Http\Response;

/**
 * Class AbstractController
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @package StinWeatherApp\Controller
 */
abstract class AbstractController {
	/**
	 * Render page with by set data into view
	 * @param string               $viewName
	 * @param array<string, mixed> $data
	 *
	 * @return Response
	 * @throws Exception
	 */
	protected function render(string $viewName, array $data = array()): Response {
		$viewPath = __DIR__ . "/../View/{$viewName}.phtml";
		if (file_exists($viewPath)) {
			extract($data);
			ob_start();
			include($viewPath);
			$content = ob_get_clean();
			if (is_string($content)) {
				return (new Response($content, 200))->setHTML();
			}

			throw new Exception("Cant render content of view: {$viewName}");

		} else {
			throw new Exception("View {$viewName} not found.");
		}


	}
}