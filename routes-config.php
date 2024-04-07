<?php

require_once __DIR__ . '/vendor/autoload.php';

use StinWeatherApp\Component\Http\Method;
use StinWeatherApp\Component\Router\Route;
use StinWeatherApp\Component\Router\Strategy\ParamPathStrategy;
use StinWeatherApp\Controller\Api\PaymentController as ApiPaymentController;
use StinWeatherApp\Controller\Api\UserController as ApiUserController;
use StinWeatherApp\Controller\DocsController;
use StinWeatherApp\Controller\HomeController;
use StinWeatherApp\Controller\NotFoundController;
use StinWeatherApp\Controller\PayController;

return array(
	new Route("/", HomeController::class, "index"),
	new Route("/pay", PayController::class, "paymentForm", Method::GET),
	new Route("/time", HomeController::class, "time"),
	new Route("/docs/api", DocsController::class, "api"),
	new Route("/not-found", NotFoundController::class, "index"),

	// Apis
	// payment
	new Route("/api/payment", ApiPaymentController::class, "processPayment", Method::POST),
	new Route("/api/payment", ApiPaymentController::class, "options", Method::OPTIONS),
	// user
	new Route("/api/user", ApiUserController::class, "createUser", Method::POST),
	new Route("/api/user", ApiUserController::class, "options", Method::OPTIONS),
	new Route("/api/user/:username", ApiUserController::class, "deleteUser", Method::DELETE, new ParamPathStrategy()),
);
