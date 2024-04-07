<?php

namespace StinWeatherApp\Controller;

use Exception;
use StinWeatherApp\Component\Http\Request;
use StinWeatherApp\Component\Http\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

/**
 * Class AbstractController
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @package StinWeatherApp\Controller
 */
abstract class AbstractController {

	protected Request $request;
	protected Environment $twig;

	public function __construct(Request $request) {
		// Twig init
		$loader = new FilesystemLoader(__DIR__ . "/../View");
		$this->twig = new Environment($loader, [
		]);

		$this->request = $request;
	}

	/**
	 * Render page with by set data into view
	 * @param string               $viewName
	 * @param array<string, mixed> $data
	 *
	 * @return Response
	 */
	protected function render(string $viewName, array $data = array()): Response {
		try {
			$content = $this->twig->render("{$viewName}.twig", $data);
			return (new Response($content, 200))->setHTML();
		} catch (LoaderError $e) {
			return new Response("Template {$viewName} not found", 500);
		} catch (RuntimeError $e) {
			return new Response("Template {$viewName} runtime error {$e}", 500);
		} catch (SyntaxError $e) {
			return new Response("Template {$viewName} syntax error {$e}", 500);
		} catch (Exception $e) {
			return new Response("Template {$viewName} rendering error {$e}", 500);
		}
	}
}