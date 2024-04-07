<?php

require_once __DIR__ . '/vendor/autoload.php';

use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\SQLiteConnectionBuilder;
use StinWeatherApp\Component\Http\Method;
use StinWeatherApp\Component\Router\Route;
use StinWeatherApp\Component\Router\Router;

// Init router
$router = $GLOBALS['router'] = new Router();

// Connect
try {
	$conn = new SQLiteConnectionBuilder();
	$conn->setDatabase('db/weather.sqlite');
	$conn->buildConnection();
	Db::connect($conn);
} catch (PDOException $e) {
	echo "Database connection failed: " . $e->getMessage();
}

// Load routes
/** @var array<Route> $routes */
$routes = require __DIR__ . '/routes-config.php';

// Add routes to the router
foreach ($routes as $route) {
	$router->addRoute($route->getPath(), $route->getController(), $route->getControllerMethod(), $route->getHttpMethod());
}

$router->dispatch($_SERVER["REQUEST_URI"], Method::from($_SERVER["REQUEST_METHOD"]));