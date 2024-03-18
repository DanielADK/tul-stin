<?php

namespace Component\Http;

use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Http\Response;

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
		// Set multiple headers
		$this->response->setHeader('Content-Type: application/json');
		$this->response->setHeader('Authorization: Bearer token');

		// Get the headers
		$headers = $this->response->getHeader();

		// Assert that the headers contain the correct values
		$this->assertContains('Content-Type: application/json', $headers);
		$this->assertContains('Authorization: Bearer token', $headers);
	}
}