<?php

namespace StinWeatherApp\Component\Router;

/**
 * Class Route
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Specific route
 * @package StinWeatherApp\Component\Router
 */
final class Route {
	/**
	 * @var string $path The path of the route
	 */
	private string $path;

	/**
	 * @var string $controller The controller that handles the route
	 */
	private string $controller;

	/**
	 * @var string $controllerMethod The method in the controller that handles the route
	 */
	private string $controllerMethod;

	/**
	 * @var string $httpMethod The HTTP method of the route (GET, POST, etc.)
	 */
	private string $httpMethod;

	/**
	 * Route constructor.
	 *
	 * @param string $path The path of the route
	 * @param string $controller The controller that handles the route
	 * @param string $controllerMethod The method in the controller that handles the route
	 * @param string $httpMethod The HTTP method of the route (GET, POST, etc.)
	 */
	public function __construct(string $path, string $controller, string $controllerMethod, string $httpMethod) {
		if (!class_exists($controller)) {
			throw new \InvalidArgumentException("Controller class does not exist: $controller");
		}

		$this->path = $path;
		$this->controller = $controller;
		$this->controllerMethod = $controllerMethod;
		$this->httpMethod = $httpMethod;
	}

	/**
	 * Set the path of the route
	 *
	 * @param string $path The path of the route
	 * @return Route The current route instance
	 */
	public function setPath(string $path): Route {
		$this->path = $path;
		return $this;
	}

	/**
	 * Get the path of the route
	 *
	 * @return string The path of the route
	 */
	public function getPath(): string {
		return $this->path;
	}

	/**
	 * Set the controller that handles the route
	 *
	 * @param string $controller The controller that handles the route
	 * @return Route The current route instance
	 */
	public function setController(string $controller): Route {
		$this->controller = $controller;
		return $this;
	}

	/**
	 * Get the controller that handles the route
	 *
	 * @return string The controller that handles the route
	 */
	public function getController(): string {
		return $this->controller;
	}

	/**
	 * Set the method in the controller that handles the route
	 *
	 * @param string $controllerMethod The method in the controller that handles the route
	 * @return Route The current route instance
	 */
	public function setControllerMethod(string $controllerMethod): Route {
		$this->controllerMethod = $controllerMethod;
		return $this;
	}

	/**
	 * Get the method in the controller that handles the route
	 *
	 * @return string The method in the controller that handles the route
	 */
	public function getControllerMethod(): string {
		return $this->controllerMethod;
	}

	/**
	 * Set the HTTP method of the route (GET, POST, etc.)
	 *
	 * @param string $httpMethod The HTTP method of the route (GET, POST, etc.)
	 * @return Route The current route instance
	 */
	public function setHttpMethod(string $httpMethod): Route {
		$this->httpMethod = $httpMethod;
		return $this;
	}

	/**
	 * Get the HTTP method of the route (GET, POST, etc.)
	 *
	 * @return string The HTTP method of the route (GET, POST, etc.)
	 */
	public function getHttpMethod(): string {
		return $this->httpMethod;
	}

}