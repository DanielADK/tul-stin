<?php

require_once 'vendor/autoload.php';

use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\SQLiteConnectionBuilder;
use StinWeatherApp\Component\Router\Router;
use StinWeatherApp\Controller\HomeController;


// Connect
$conn = new SQLiteConnectionBuilder();
$conn->setDatabase('db/weather');
$conn->buildConnection();
Db::connect($conn);

$router = new Router();
$router->addRoute("/", HomeController::class);

$router->dispatch($_SERVER["REQUEST_URI"], $_SERVER["REQUEST_METHOD"]);