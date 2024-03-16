<?php

namespace StinWeatherApp\Component\Router;

use Exception;
use JetBrains\PhpStorm\NoReturn;
use StinWeatherApp\Component\Http\Response;

/**
 * Class Router
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description General router for the application
 * @package StinWeatherApp\Component\Router
 */
class Router {

	/**
	 * @var array<Route> $routes The routes that the router will handle.
	 */
	private array $routes = array();

	/**
	 * Adds a new route to the router.
	 *
	 * @param string $path The path that the route will handle.
	 * @param string $controller The controller that will handle the route.
	 * @param string $controllerMethod The method of the controller that will be called.
	 * @param string $httpMethod The HTTP method that the route will respond to.
	 */
	public function addRoute(string $path, string $controller, string $controllerMethod = "index", string $httpMethod = "GET"): void {
		if (!$this->isMethodSupported($httpMethod)) {
			throw new \InvalidArgumentException("Unsupported HTTP method: $httpMethod");
		}
		$this->routes[] = new Route($path, $controller, $controllerMethod, $httpMethod);
	}

	/**
	 * Dispatches the request to the appropriate route.
	 *
	 * @param string $requestUri The URI of the request.
	 * @param string $requestMethod The HTTP method of the request.
	 */
	public function dispatch(string $requestUri, string $requestMethod): void {
		try {
			// Iterate through routes -> find the first one that matches the request.
			foreach ($this->routes as $route) {
				if ($route instanceof Route) {
					// If the route matches the request, call the controller method and return the response.
					if ($route->getPath() === $requestUri && $route->getHttpMethod() === $requestMethod) {
						$controller = new ($route->getController());
						$response = $controller->{$route->getControllerMethod()}();

						// If the controller action returns a Response object, send it to the client.
						if ($response instanceof Response) {
							$response->send();
						} else {
							// If the controller action does not return a Response object, throw an exception.
							throw new Exception("Controller action must return an instance of Response");
						}
						return;
					}
				}
			}

			// If no route matches the request, send a 404 response.
			$this->redirect("/404", 404);
		} catch (Exception $e) {
			// If an exception occurs, send a 500 response.
			error_log($e->getMessage());

			$response = new Response("An error occurred: " . $e->getMessage(), 500);
			$response->send();

		}
	}
	/**
	 * Redirects to the given path with optional HTTP status code.
	 *
	 * @param string $path The path or URL to redirect to.
	 * @param int $statusCode (optional) HTTP status code for the redirection, defaults to 302.
	 */
	#[NoReturn]
	public static function redirect(string $path, int $statusCode = 302): void {
		header("Location: $path", true, $statusCode);
		exit();
	}

	/**
	 * Checks if the given HTTP method is supported.
	 *
	 * @param string $method The HTTP method to check.
	 * @return bool True if the method is supported, false otherwise.
	 */
	private function isMethodSupported(string $method): bool {
		return in_array($method, array("GET", "POST", "PUT", "DELETE", "OPTIONS"));
	}

}