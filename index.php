<?php

require_once 'vendor/autoload.php';

use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\SQLiteConnectionBuilder;
 use StinWeatherApp\Component\Http\Method;
use StinWeatherApp\Component\Router\Router;
use StinWeatherApp\Controller\HomeController;
use StinWeatherApp\Controller\NotFoundController;


// Connect
$conn = new SQLiteConnectionBuilder();
$conn->setDatabase('db/weather');
$conn->buildConnection();
Db::connect($conn);

$router = new Router();
$router->addRoute("/", HomeController::class);
$router->setNotFound("/not-found", NotFoundController::class);
$router->addRoute("/test", HomeController::class, "AAA");

$router->dispatch($_SERVER["REQUEST_URI"], Method::from($_SERVER["REQUEST_METHOD"]));