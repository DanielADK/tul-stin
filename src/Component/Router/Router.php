<?php

namespace StinWeatherApp\Component\Router;

use Exception;
use StinWeatherApp\Component\Http\Method;
use StinWeatherApp\Component\Http\Request;
use StinWeatherApp\Component\Http\Response;
use StinWeatherApp\Component\Router\Strategy\DirectPathStrategy;
use StinWeatherApp\Component\Router\Strategy\PathStrategyInterface;
use StinWeatherApp\Component\Router\Strategy\PathValueExtractor;
use StinWeatherApp\Controller\NotFoundController;

/**
 * Class Router
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description General router for the application
 * @package StinWeatherApp\Component\Router
 */
class Router {

	/** @var array<string, array<string, Route>> $routes Array of Methods -> array path-route */
	private array $routes = array();
	/** * @var Route $notFoundRoute The route that will be called when no other route matches the request. */
	private Route $notFoundRoute;

	/**
	 * Router constructor.
	 */
	public function __construct() {
		// Set the default not found route.
		$this->notFoundRoute = new Route("/not-found", NotFoundController::class, "index", Method::GET);
		$this->addRoute($this->notFoundRoute->getPath(), $this->notFoundRoute->getController(), $this->notFoundRoute->getControllerMethod(), $this->notFoundRoute->getHttpMethod());
	}

	/**
	 * Adds a new route to the router.
	 *
	 * @param string $path The path that the route will handle.
	 * @param string $controller The controller that will handle the route.
	 * @param string $controllerMethod The method of the controller that will be called.
	 * @param Method $httpMethod The HTTP method that the route will respond to.
	 * @param PathStrategyInterface $strategy The strategy that the route will use to match the path.
	 *
	 * @return Router
	 */
	public function addRoute(string $path, string $controller, string $controllerMethod = "index", Method $httpMethod = Method::GET, PathStrategyInterface $strategy = new DirectPathStrategy()): Router {
		$this->routes[$httpMethod->value][$path] = new Route($path, $controller, $controllerMethod, $httpMethod, $strategy);

		return $this;
	}

	/**
	 * Returns the route that matches the given path.
	 *
	 * @param string $path The path to match.
	 * @return Route|null The matching route, or null if no route matches the path.
	 */
	public function getRouteByPath(string $path, Method $method = Method::GET): ?Route {
		// Optimization: O(1) search for the route that matches the exact path.
		if (isset($this->routes[$method->value][$path]) && $this->routes[$method->value][$path] instanceof Route) {
			return $this->routes[$method->value][$path];
		}
		// Generic search
		foreach ($this->routes as $methods) {
			foreach ($methods as $route) {
				if ($route->matches($path)) {
					return $route;
				}
			}
		}
		return null;
	}

	/**
	 * Returns array of all routes
	 *
	 * @return array<string, array<string, Route>>
	 */
	public function getRoutes(): array {
		return $this->routes;
	}

	/**
	 * Sets the route that will be called when no other route matches the request.
	 *
	 * @param Route $route The not found route.
	 */
	public function setNotFound(Route $route): void {
		$this->notFoundRoute = $route;
		$this->routes[$this->notFoundRoute->getHttpMethod()->value][$this->notFoundRoute->getPath()] = $this->notFoundRoute;
	}

	/**
	 * Returns the route that will be called when no other route matches the request.
	 *
	 * @return Route The not found route.
	 */
	public function getNotFoundRoute(): Route {
		return $this->notFoundRoute;
	}

	/**
	 * Dispatches the request to the appropriate route.
	 *
	 * @param string $requestUri The URI of the request.
	 * @param Method $requestMethod The HTTP method of the request.
	 */
	public function dispatch(string $requestUri, Method $requestMethod): Response {
		try {
			// Create a new request object
			$request = new Request();

			// Find by index
			$route = $this->getRouteByPath($requestUri, $requestMethod);
			if ($route instanceof Route) {
				// If the route matches the request, call the controller method and return the response.
				if ($route->matches($requestUri) && $route->getHttpMethod() === $requestMethod) {
					$controller = new ($route->getController())($request);
					if (!method_exists($controller, $route->getControllerMethod())) {
						throw new Exception("Method {$route->getControllerMethod()} in controller {$route->getController()} does not exist!");
					}
					$params = PathValueExtractor::extractValue($route->getPath(), $request);
					/** @var callable $callable */
					$callable = [$controller, $route->getControllerMethod()];
					$response = call_user_func_array($callable, $params);

					// If the controller action returns a Response object, send it to the client.
					if ($response instanceof Response) {
						$response->send();
					} else {
						// If the controller action does not return a Response object, throw an exception.
						throw new Exception("Controller action must return an instance of Response");
					}
					return $response;
				}
			}
			// If no route matches the request, send a 404 response.
			return $this->redirect(
				path: $this->notFoundRoute->getPath());
		} catch (Exception $e) {
			// If an exception occurs, send a 500 response.
			error_log($e->getMessage());

			$response = new Response(statusCode: 500);
			$response->send();

			return $response;
		}
	}
	/**
	 * Redirects to the given path with optional HTTP status code.
	 *
	 * @param string $path The path or URL to redirect to.
	 * @param int $statusCode (optional) HTTP status code for the redirection, defaults to 302.
	 */
	public static function redirect(string $path, int $statusCode = 302): Response {
		header("Location: $path", true, $statusCode);
		return new Response(statusCode: $statusCode);
	}

	/**
	 * Checks if the given HTTP method is supported.
	 *
	 * @param Method $method The HTTP method to check.
	 *
	 * @return bool True if the method is supported, false otherwise.
	 */
	public function isMethodSupported(Method $method): bool {
		return in_array($method, Method::cases());
	}

	/**
	 * Get all allowed methods for a given path.
	 *
	 * @param string $path The path to check.
	 *
	 * @return array<Method> An array of allowed methods.
	 */
	public function getAllowedMethods(string $path): array {
		$allowedMethods = [];

		foreach ($this->routes as $methods) {
			foreach ($methods as $route) {
				if ($route->matches($path)) {
					$allowedMethods[] = $route->getHttpMethod();
				}
			}
		}

		return $allowedMethods;
	}

}