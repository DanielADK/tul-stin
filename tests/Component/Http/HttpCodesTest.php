<?php

namespace Component\Http;

use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Http\Method;
use StinWeatherApp\Component\Router\Router;
use StinWeatherApp\Controller\TestController;

class HttpCodesTest extends TestCase {
	private Router $router;

	protected function setUp(): void {
		$this->router = new Router();
		$this->router->addRoute('/test-200', TestController::class, 'status200', Method::GET);
		$this->router->addRoute('/test-201', TestController::class, 'status201', Method::GET);
		$this->router->addRoute('/test-400', TestController::class, 'status400', Method::GET);
		$this->router->addRoute('/test-404', TestController::class, 'status404', Method::GET);
		$this->router->addRoute('/test-500', TestController::class, 'status500', Method::GET);
	}

	public function testStatus200(): void {
		$initialLevel = ob_get_level();
		ob_start();
		$response = $this->router->dispatch('/test-200', Method::from("GET"));
		ob_end_clean();
		$this->assertEquals(200, $response->getStatusCode());
		while (ob_get_level() > $initialLevel) {
			ob_end_clean();
		}
	}

	public function testStatus201(): void {
		$initialLevel = ob_get_level();
		ob_start();
		$response = $this->router->dispatch('/test-201', Method::from("GET"));
		ob_end_clean();
		$this->assertEquals(201, $response->getStatusCode());
		while (ob_get_level() > $initialLevel) {
			ob_end_clean();
		}
	}

	public function testStatus400(): void {
		$initialLevel = ob_get_level();
		ob_start();
		$response = $this->router->dispatch('/test-400', Method::from("GET"));
		ob_end_clean();
		$this->assertEquals(400, $response->getStatusCode());
		while (ob_get_level() > $initialLevel) {
			ob_end_clean();
		}
	}

	public function testStatus404(): void {
		$initialLevel = ob_get_level();
		ob_start();
		$response = $this->router->dispatch('/test-404', Method::from("GET"));
		ob_end_clean();
		$this->assertEquals(404, $response->getStatusCode());
		while (ob_get_level() > $initialLevel) {
			ob_end_clean();
		}
	}

	public function testStatus500(): void {
		$initialLevel = ob_get_level();
		ob_start();
		$response = $this->router->dispatch('/test-500', Method::from("GET"));
		ob_end_clean();
		$this->assertEquals(500, $response->getStatusCode());
		while (ob_get_level() > $initialLevel) {
			ob_end_clean();
		}
	}
}