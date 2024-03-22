<?php

namespace Component\Http;

use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Http\Method;
use StinWeatherApp\Component\Router\Router;
use StinWeatherApp\Controller\TestController;

class HttpMethodsTest extends TestCase {
 private Router $router;

 protected function setUp(): void {
  $this->router = new Router();
  $this->router->addRoute('/test-get', TestController::class, 'get', Method::GET);
  $this->router->addRoute('/test-post', TestController::class, 'post', Method::POST);
  $this->router->addRoute('/test-put', TestController::class, 'put', Method::PUT);
  $this->router->addRoute('/test-delete', TestController::class, 'delete', Method::DELETE);
  $this->router->addRoute('/test-options', TestController::class, 'options', METHOD::OPTIONS);
 }

 public function testGet(): void {
	 $_SERVER['REQUEST_METHOD'] = 'GET';
  $initialLevel = ob_get_level();
  ob_start();
  $this->router->dispatch('/test-get', Method::from("GET"));
  $output = ob_get_clean();
  $this->assertEquals("GET method", $output);
  while (ob_get_level() > $initialLevel) {
   ob_end_clean();
  }
 }

 public function testPost(): void {
	 $_SERVER['REQUEST_METHOD'] = 'POST';
  $initialLevel = ob_get_level();
  ob_start();
  $this->router->dispatch('/test-post', Method::from("POST"));
  $output = ob_get_clean();
  $this->assertEquals("POST method", $output);
  while (ob_get_level() > $initialLevel) {
   ob_end_clean();
  }
 }

 public function testPut(): void {
	 $_SERVER['REQUEST_METHOD'] = 'PUT';
  $initialLevel = ob_get_level();
  ob_start();
  $this->router->dispatch('/test-put', Method::from("PUT"));
  $output = ob_get_clean();
  $this->assertEquals("PUT method", $output);
  while (ob_get_level() > $initialLevel) {
   ob_end_clean();
  }
 }

 public function testDelete(): void {
	 $_SERVER['REQUEST_METHOD'] = 'DELETE';
  $initialLevel = ob_get_level();
  ob_start();
  $this->router->dispatch('/test-delete', Method::from("DELETE"));
  $output = ob_get_clean();
  $this->assertEquals("DELETE method", $output);
  while (ob_get_level() > $initialLevel) {
   ob_end_clean();
  }
 }

 public function testOptions(): void {
	 $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
  $initialLevel = ob_get_level();
  ob_start();
  $this->router->dispatch('/test-options', Method::from("OPTIONS"));
  $output = ob_get_clean();
  $this->assertEquals("OPTIONS method", $output);
  while (ob_get_level() > $initialLevel) {
   ob_end_clean();
  }
 }
}