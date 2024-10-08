<?php

namespace Component\Router;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Auth\AuthInterface;
use StinWeatherApp\Component\Http\Method;
use StinWeatherApp\Component\Http\Response;
use StinWeatherApp\Component\Router\Route;
use StinWeatherApp\Component\Router\Router;
use StinWeatherApp\Controller\NotFoundController;
use StinWeatherApp\Controller\TestController;

class RouterTest extends TestCase {
	private Router $router;
	private int $initialLevel;

	protected function setUp(): void {
		$this->router = new Router();
		$_SERVER['REQUEST_URI'] = '/';
		$_SERVER['REQUEST_METHOD'] = 'GET';

		// Set initial output buffer level
		$this->initialLevel = ob_get_level();
		ob_start();
	}

	protected function tearDown(): void {
		// Clean output buffer
		while (ob_get_level() > $this->initialLevel) {
			ob_end_clean();
		}
	}

	/**
	 * @description Test if the route has an authentication object
	 * @throws Exception
	 */
	public function testGetAuth() {
		$authMock = $this->createMock(AuthInterface::class);
		$routeWithAuth = new Route('/auth-path', TestController::class, 'testMethod', Method::GET, null, $authMock);
		$this->assertSame($authMock, $routeWithAuth->getAuth());

		$routeWithoutAuth = new Route('/auth-path', TestController::class, 'testMethod', Method::GET);
		$this->assertNull($routeWithoutAuth->getAuth());
	}
	public function testAddRoute(): void {
		$path = '/test';
		$this->router->addRoute(new Route($path, TestController::class, 'testMethod', Method::GET));

		$route = $this->router->getRouteByPath($path);
		$this->assertNotNull($route);
		$this->assertEquals($path, $route->getPath());
		$this->assertEquals(TestController::class, $route->getController());
		$this->assertEquals('testMethod', $route->getControllerMethod());
		$this->assertEquals('GET', $route->getHttpMethod()->value);
	}

	public function testSetNotFound(): void {
		$this->router->setNotFound(new Route('/not-found', NotFoundController::class, 'index'));

		$this->assertEquals('/not-found', $this->router->getNotFoundRoute()->getPath());
		$this->assertEquals(NotFoundController::class, $this->router->getNotFoundRoute()->getController());
		$this->assertEquals('index', $this->router->getNotFoundRoute()->getControllerMethod());
	}
	public function testGetRoutes(): void
	{
		$this->router->addRoute(new Route('/test1', TestController::class, 'testMethod', Method::GET));
		$this->router->addRoute(new Route('/test2', TestController::class, 'testMethod', Method::GET));

		$routes = $this->router->getRoutes();
		$count = 0;
		foreach ($routes as $method => $route) {
			$count += count($route);
		}

		$this->assertSame(3, $count); // 2 routes added + 1 notFoundRoute
	}

	public function testGetRouteByPath(): void {
		$this->router->addRoute(new Route('/test', TestController::class, 'testMethod', Method::GET));

		$route = $this->router->getRouteByPath('/test');

		$this->assertNotNull($route);
		$this->assertEquals('/test', $route->getPath());
		$this->assertEquals(TestController::class, $route->getController());
		$this->assertEquals('testMethod', $route->getControllerMethod());
		$this->assertEquals('GET', $route->getHttpMethod()->value);
	}

	public function testGetRouteByPathReturnsNull(): void {
		$route = $this->router->getRouteByPath('/non-existing-route');

		$this->assertNull($route);
	}

	#[Depends('testAddRoute')]
	public function testDispatch(): void {
		$response = $this->router->dispatch('/test', Method::GET);

		$this->assertInstanceOf(Response::class, $response);

		$this->router->addRoute(new Route('/invalidcontrollermethod', TestController::class, 'invalidcontrollermethod', Method::GET));

		$response = $this->router->dispatch('/invalidcontrollermethod', Method::GET);
		$this->assertEquals(500, $response->getStatusCode());
	}

	public function testDispatchControllerActionDoesNotReturnResponse(): void {
		$this->router->addRoute(new Route('/noResponseObjectReturn', TestController::class, 'noResponseObjectReturn', Method::GET));

		// Expect an exception when the controller action does not return a Response

		// Call dispatch with a request URI and method that match a route whose controller action does not return a Response
		$response = $this->router->dispatch('/noResponseObjectReturn', Method::GET);
		$this->assertEquals(500, $response->getStatusCode());
	}

	public function testRedirect(): void {
		$response = Router::redirect('/redirect');

		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals(302, $response->getStatusCode());
	}

	public function testIsMethodSupported(): void {
		$method = Method::GET;
		$this->assertTrue($this->router->isMethodSupported($method));
	}

	public function testSearchRoutesSpeed(): void {
		$numOfRoutes = 50_000;
		// Create $numOfRoutes routes with unique paths and add them to the router
		for ($i = 0; $i < $numOfRoutes; $i++) {
			$this->router->addRoute(new Route("/test$i", TestController::class, 'testMethod', Method::GET));
		}

		// Create an array with all paths in random order
		$paths = range(0, $numOfRoutes-1);
		shuffle($paths);
		$paths = array_map(fn($i) => "/test$i", $paths);

		// Measure the time to search for all routes
		$start = microtime(true);
		foreach ($paths as $path) {
			$route = $this->router->getRouteByPath($path);
			if ($route === null) {
				$this->fail("Route not found for path: $path");
			}
		}
		$end = microtime(true);

		// Print the time to search for all routes
		$time = $end - $start;
		$this->assertLessThanOrEqual(2, $time);
	}
}