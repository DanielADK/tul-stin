<?php

namespace Component\Http;

use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Http\Request;

class RequestTest extends TestCase {

	private Request $request;

	public function testGetMethod(): void {
		$this->assertEquals($_SERVER['REQUEST_METHOD'], $this->request->getMethod());
	}

	public function testGetHeader(): void {
		$this->assertEquals($_SERVER['HTTP_AUTHORIZATION'] ?? null, $this->request->getHeader('Authorization'));
	}

	public function testGetBody(): void {
		$this->assertEquals($_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET, $this->request->getBody());
	}

	public function testGetPost(): void {
		$this->assertEquals($_POST, $this->request->getPost("ALL"));
	}

	public function testGetGet(): void {
		$this->assertEquals($_GET, $this->request->getGet("ALL"));
	}

	public function testGetRawBody(): void {
		$this->assertEquals(file_get_contents('php://input'), $this->request->getRawBody());
	}

	protected function setUp(): void {
		parent::setUp();

		// Simulate a HTTP request
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_SERVER['HTTP_AUTHORIZATION'] = 'Bearer token';
		$_POST = ['key' => 'value'];
		$_GET = ['key' => 'value'];
		file_put_contents('php://input', json_encode(['key' => 'value']));

		// Create a new Request instance
		$this->request = new Request();
	}
}