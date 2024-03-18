<?php

namespace Component\Router;

use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Http\Method;
use StinWeatherApp\Component\Router\Route;
use StinWeatherApp\Controller\TestController;

class RouteTest extends TestCase {
	private Route $route;

	protected function setUp(): void {
		$this->route = new Route('/test', TestController::class, 'testMethod', Method::GET);
	}

	public function testSetAndGetPath(): void {
		$this->route->setPath('/new-test');
		$this->assertEquals('/new-test', $this->route->getPath());
	}

	public function testSetAndGetController(): void {
		$this->route->setController('NewTestController');
		$this->assertEquals('NewTestController', $this->route->getController());
	}

	public function testSetAndGetControllerMethod(): void {
		$this->route->setControllerMethod('newTestMethod');
		$this->assertEquals('newTestMethod', $this->route->getControllerMethod());
	}

	public function testSetAndGetHttpMethod(): void {
		$this->route->setHttpMethod(Method::POST);
		$this->assertEquals('POST', $this->route->getHttpMethod()->value);
	}
}