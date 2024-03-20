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

	public function testInvalidPath(): void {
		$this->router->addRoute('/invalid/(\d+)', TestController::class, 'getInvalid', Method::GET, new RegexPathStrategy());

		$route = $this->router->getRouteByPath('/invalid/string');
		$this->assertNull($route);
	}

	public function testPathWithSpecialCharacters(): void {
		$this->router->addRoute('/special/(\W+)', TestController::class, 'getSpecial', Method::GET, new RegexPathStrategy());

		$route = $this->router->getRouteByPath('/special/!@#');
		$this->assertNotNull($route);
		$this->assertEquals(TestController::class, $route->getController());
		$this->assertEquals('getSpecial', $route->getControllerMethod());
	}

	public function testPathWithMultipleRegexGroups(): void {
		$this->router->addRoute('/multi/(\d+)/(\w+)', TestController::class, 'getMulti', Method::GET, new RegexPathStrategy());

		$route = $this->router->getRouteByPath('/multi/123/abc');
		$this->assertNotNull($route);
		$this->assertEquals(TestController::class, $route->getController());
		$this->assertEquals('getMulti', $route->getControllerMethod());
	}

	public function testNonMatchingRoute(): void {
		$this->router->addRoute('/nonexistent/(\d+)', TestController::class, 'getNonexistent', Method::GET, new RegexPathStrategy());

		$route = $this->router->getRouteByPath('/nonexistent/abc');
		$this->assertNull($route);
	}

}