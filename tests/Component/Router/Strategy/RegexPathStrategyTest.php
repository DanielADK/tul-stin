<?php

namespace Component\Router\Strategy;

use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Http\Method;
use StinWeatherApp\Component\Router\Router;
use StinWeatherApp\Component\Router\Strategy\RegexPathStrategy;
use StinWeatherApp\Controller\TestController;

class RegexPathStrategyTest extends TestCase {
	private Router $router;

	protected function setUp(): void {
		$this->router = new Router();
	}

	/**
	 * @test Router with regex path strategy
	 */
	public function testRouterWithRegexPathStrategy(): void {
		// Add routes
		$this->router->addRoute('/article/(\d+)', TestController::class, 'getArticle', Method::GET, new RegexPathStrategy());
		$this->router->addRoute('/user/(\d+)', TestController::class, 'getUser', Method::GET, new RegexPathStrategy());

		// Test matching routes
		$route = $this->router->getRouteByPath('/article/123');
		$this->assertNotNull($route);
		$this->assertEquals(TestController::class, $route->getController());
		$this->assertEquals('getArticle', $route->getControllerMethod());

		$route = $this->router->getRouteByPath('/user/456');
		$this->assertNotNull($route);
		$this->assertEquals(TestController::class, $route->getController());
		$this->assertEquals('getUser', $route->getControllerMethod());

		// Test non-matching route
		$route = $this->router->getRouteByPath('/nonexistent/789');
		$this->assertNull($route);
	}

	/**
	 * @test Invalid path
	 */
	public function testInvalidPath(): void {
		$this->router->addRoute('/invalid/(\d+)', TestController::class, 'getInvalid', Method::GET, new RegexPathStrategy());

		$route = $this->router->getRouteByPath('/invalid/string');
		$this->assertNull($route);
	}

	/**
	 * @test With special characters
	 */
	public function testPathWithSpecialCharacters(): void {
		$this->router->addRoute('/special/(\W+)', TestController::class, 'getSpecial', Method::GET, new RegexPathStrategy());

		$route = $this->router->getRouteByPath('/special/!@#');
		$this->assertNotNull($route);
		$this->assertEquals(TestController::class, $route->getController());
		$this->assertEquals('getSpecial', $route->getControllerMethod());
	}

	/**
	 * @test With multiple regex groups
	 */
	public function testPathWithMultipleRegexGroups(): void {
		$this->router->addRoute('/multi/(\d+)/(\w+)', TestController::class, 'getMulti', Method::GET, new RegexPathStrategy());

		$route = $this->router->getRouteByPath('/multi/123/abc');
		$this->assertNotNull($route);
		$this->assertEquals(TestController::class, $route->getController());
		$this->assertEquals('getMulti', $route->getControllerMethod());
	}

	/**
	 * @test Non-matching route
	 */
	public function testNonMatchingRoute(): void {
		$this->router->addRoute('/nonexistent/(\d+)', TestController::class, 'getNonexistent', Method::GET, new RegexPathStrategy());

		$route = $this->router->getRouteByPath('/nonexistent/abc');
		$this->assertNull($route);
	}

	/**
	 * @test Complex regex edge cases
	 */
	public function testComplexRegexEdgeCases(): void {
		$this->router->addRoute('/complex/(\d+)(\w*)(\W*)', TestController::class, 'getComplex', Method::GET, new RegexPathStrategy());

		// Test a route that matches the complex regular expression.
		$route = $this->router->getRouteByPath('/complex/123abc!@#');
		$this->assertNotNull($route);
		$this->assertEquals(TestController::class, $route->getController());
		$this->assertEquals('getComplex', $route->getControllerMethod());

		// Test a route that does not match the complex regular expression.
		$route = $this->router->getRouteByPath('/complex/abc123');
		$this->assertNull($route);

		// Edge case: path matches the regular expression, but is empty.
		$route = $this->router->getRouteByPath('/complex/');
		$this->assertNull($route);

		// Edge case: path matches the regular expression, but contains only digits.
		$route = $this->router->getRouteByPath('/complex/123');
		$this->assertNotNull($route);
		$this->assertEquals(TestController::class, $route->getController());
		$this->assertEquals('getComplex', $route->getControllerMethod());

		// Edge case: path matches the regular expression, but contains only alphanumeric characters.
		$route = $this->router->getRouteByPath('/complex/abc');
		$this->assertNull($route);

		// Edge case: path matches the regular expression, but contains first ald last regex.
		$route = $this->router->getRouteByPath('/complex/1!');
		$this->assertNotNull($route);
		$this->assertEquals(TestController::class, $route->getController());
		$this->assertEquals('getComplex', $route->getControllerMethod());

		// Edge case: path matches the regular expression, but contains only special characters.
		$route = $this->router->getRouteByPath('/complex/!@#');
		$this->assertNull($route);
	}
}