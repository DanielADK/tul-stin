<?php

namespace StinWeatherApp\Component\Router;

use StinWeatherApp\Component\Auth\AuthInterface;
use StinWeatherApp\Component\Http\Method;
use StinWeatherApp\Component\Router\Strategy\DirectPathStrategy;
use StinWeatherApp\Component\Router\Strategy\PathStrategyInterface;

/**
 * Class Route
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Specific route
 * @package StinWeatherApp\Component\Router
 */
final class Route {
	/** @var string $path The path of the route */
	private string $path;

	/** @var string $controller The controller that handles the route */
	private string $controller;

	/** @var string $controllerMethod The method in the controller that handles the route */
	private string $controllerMethod;

	/** @var Method $httpMethod The HTTP method of the route (GET, POST, etc.) */
	private Method $httpMethod;

	/** @var PathStrategyInterface $strategy The strategy for matching the path*/
	private PathStrategyInterface $strategy;
	private ?AuthInterface $auth;

	/**
	 * Route constructor.
	 *
	 * @param string                     $path The path of the route
	 * @param string                     $controller The controller that handles the route
	 * @param string                     $controllerMethod The method in the controller that handles the route
	 * @param Method                     $httpMethod The HTTP method of the route (GET, POST, etc.)
	 * @param PathStrategyInterface|null $pathStrategy The strategy for matching the path
	 * @param AuthInterface|null $auth The authentication service
	 */
	public function __construct(string                 $path,
	                            string                 $controller,
	                            string                 $controllerMethod,
	                            Method                 $httpMethod = Method::GET,
	                            ?PathStrategyInterface $pathStrategy = new DirectPathStrategy(),
	                            ?AuthInterface         $auth = null) {
		if (!class_exists($controller)) {
			throw new \InvalidArgumentException("Controller class does not exist: $controller");
		}

		$this->path = $path;
		$this->controller = $controller;
		$this->controllerMethod = $controllerMethod;
		$this->httpMethod = $httpMethod;
		$this->strategy = $pathStrategy ?? new DirectPathStrategy();
		$this->auth = $auth;
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
	 * @return string The controller that handles the route
	 */
	public function getController(): string {
		return $this->controller;
	}

	/**
	 * Set the method in the controller that handles the route
	 * @param string $controllerMethod The method in the controller that handles the route
	 * @return Route The current route instance
	 */
	public function setControllerMethod(string $controllerMethod): Route {
		$this->controllerMethod = $controllerMethod;
		return $this;
	}

	/**
	 * Get the method in the controller that handles the route
	 * @return string The method in the controller that handles the route
	 */
	public function getControllerMethod(): string {
		return $this->controllerMethod;
	}

	/**
	 * Set the HTTP method of the route (GET, POST, etc.)
	 * @param Method $httpMethod The HTTP method of the route (GET, POST, etc.)
	 * @return Route The current route instance
	 */
	public function setHttpMethod(Method $httpMethod): Route {
		$this->httpMethod = $httpMethod;
		return $this;
	}

	/**
	 * Get the HTTP method of the route (GET, POST, etc.)
	 * @return Method The HTTP method of the route (GET, POST, etc.)
	 */
	public function getHttpMethod(): Method {
		return $this->httpMethod;
	}

	/**
	 * Get the strategy for matching the path
	 * @return PathStrategyInterface The strategy for matching the path
	 */
	public function getStrategy(): PathStrategyInterface {
		return $this->strategy;
	}

	/**
	 * Is path matching the route with the strategy
	 * @param string $requestPath
	 * @return bool
	 */
	public function matches(string $requestPath): bool {
		return $this->strategy->matches($this->path, $requestPath);
	}

	/**
	 * Get authentication service
	 *
	 * @return AuthInterface|null
	 */
	public function getAuth(): ?AuthInterface {
		return $this->auth;
	}
}