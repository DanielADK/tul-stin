<?php

namespace Component\Router\Strategy;

use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Http\Method;
use StinWeatherApp\Component\Router\Router;
use StinWeatherApp\Component\Router\Strategy\ParamPathStrategy;
use StinWeatherApp\Controller\TestController;

class ParamPathStrategyTest extends TestCase {
	public function testMatches(): void {
		$strategy = new ParamPathStrategy();

		// Test 1: Single variable in pattern path
		$this->assertTrue($strategy->matches('/weather/:city', '/weather/prague'));

		// Test 2: Multiple variables in pattern path
		$this->assertTrue($strategy->matches('/weather/:city/:day', '/weather/prague/today'));

		// Test 3: Variable at the start of pattern path
		$this->assertTrue($strategy->matches('/:city/weather', '/prague/weather'));

		// Test 4: Variable at the end of pattern path
		$this->assertTrue($strategy->matches('/weather/:city', '/weather/prague'));

		// Test 5: Variable in the middle of pattern path
		$this->assertTrue($strategy->matches('/weather/:city/today', '/weather/prague/today'));

		// Test 6: Pattern path with no variables
		$this->assertTrue($strategy->matches('/weather/prague', '/weather/prague'));

		// Test 7: Pattern path with variables but real path missing corresponding values
		$this->assertFalse($strategy->matches('/weather/:city/:day', '/weather'));
	}

	public function testDispatchWithParamPathStrategy(): void {
		// Start buffering the output
		ob_start();
		$_SERVER['REQUEST_METHOD'] = 'GET';
		// Create a new router
		$router = new Router();

		// Add routes with ParamPathStrategy
		$router->addRoute('/weather/:city', TestController::class, 'weather', Method::GET, new ParamPathStrategy());
		$router->addRoute('/weather/:city/:day', TestController::class, 'weatherday', Method::GET, new ParamPathStrategy());

		// Test dispatch with single variable in path
		$requestUri = '/weather/prague';
		$_SERVER['REQUEST_URI'] = $requestUri;
		$response = $router->dispatch($requestUri, Method::GET);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals("prague", $response->getContent());
		$this->assertEmpty($response->getHeader());

		// Test dispatch with multiple variables in path
		$requestUri = '/weather/prague/today';
		$_SERVER['REQUEST_URI'] = $requestUri;
		$response = $router->dispatch($requestUri, Method::GET);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals("praguetoday", $response->getContent());
		$this->assertEmpty($response->getHeader());

		// Test dispatch with path that does not match any route -> redirect to not-found page
		$requestUri = '/weather';
		$_SERVER['REQUEST_URI'] = $requestUri;
		$response = $router->dispatch($requestUri, Method::GET);
		$this->assertEquals(302, $response->getStatusCode());
		$this->assertEmpty($response->getHeader());

		// End buffering the output and clean the buffer
		ob_end_clean();
	}
}