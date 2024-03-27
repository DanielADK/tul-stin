<?php

require_once __DIR__ . '/vendor/autoload.php';

use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\SQLiteConnectionBuilder;
use StinWeatherApp\Component\Http\Method;
use StinWeatherApp\Component\Router\Route;
use StinWeatherApp\Component\Router\Router;
use StinWeatherApp\Controller\HomeController;
use StinWeatherApp\Controller\NotFoundController;
use StinWeatherApp\Controller\PaymentController;

// Init router
$router = new Router();

// Connect
try {
	$conn = new SQLiteConnectionBuilder();
	$conn->setDatabase('db/weather.sqlite');
	$conn->buildConnection();
	Db::connect($conn);
} catch (PDOException $e) {
	echo "Database connection failed: " . $e->getMessage();
}

// Routes
$router->addRoute("/", HomeController::class);
$router->setNotFound(new Route("/not-found", NotFoundController::class, "index"));
$router->addRoute("/pay", PaymentController::class, "paymentProcessing", Method::POST);
$router->addRoute("/time", HomeController::class, "time");

$router->dispatch($_SERVER["REQUEST_URI"], Method::from($_SERVER["REQUEST_METHOD"]));