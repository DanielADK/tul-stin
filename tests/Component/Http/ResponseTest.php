<?php

namespace Component\Http;

use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Http\Method;
use StinWeatherApp\Component\Http\Response;
use StinWeatherApp\Component\Router\Route;
use StinWeatherApp\Component\Router\Router;
use StinWeatherApp\Controller\TestController;

class ResponseTest extends TestCase {
	private Response $response;

	protected function setUp(): void {
		$this->response = new Response();
	}

	public function testSetAndGetContent(): void {
		$this->response->setContent('Test content');
		$this->assertEquals('Test content', $this->response->getContent());
	}

	public function testSetAndGetStatusCode(): void {
		$this->response->setStatusCode(404);
		$this->assertEquals(404, $this->response->getStatusCode());
	}

	public function testSetAndGetHeader(): void {
		$this->response->setHeader('Content-Type: application/json');
		$this->assertContains('Content-Type: application/json', $this->response->getHeader());
	}

	public function testSetHTML(): void {
		$this->response->setHTML();
		$this->assertContains('Content-Type: text/html', $this->response->getHeader());
	}

	public function testSetJSON(): void {
		$this->response->setJSON();
		$this->assertContains('Content-Type: application/json', $this->response->getHeader());
	}

	public function testSetAndGetMultipleHeaders(): void {
		$headers = array('Content-Type: application/json', 'Authorization: Bearer token');

		$response = new Response('', 200, $headers);

		$this->assertContains('Content-Type: application/json', $response->getHeader());
		$this->assertContains('Authorization: Bearer token', $response->getHeader());
	}

	public function testDispatchSetsHeaders(): void {
		$_SERVER['REQUEST_METHOD'] = 'GET';
		// Add a test route that sets headers and returns a Response
		$router = new Router();
		$router->addRoute(new Route('/test', TestController::class, 'methodSetsHeaders', Method::GET));

		// Call dispatch with a request URI and method that match the test route
		$response = $router->dispatch('/test', Method::GET);

		// Check that the response has the expected headers
		$this->assertContains('Content-Type: application/json', $response->getHeader());
		$this->assertContains('Authorization: Bearer token', $response->getHeader());
	}

	public function testSetContentWithFalse(): void {
		$response = new Response();
		$response->setContent(false);
		$this->assertEquals("", $response->getContent());
	}
}