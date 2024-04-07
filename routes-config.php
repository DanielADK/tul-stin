<?php

use StinWeatherApp\Component\Http\Method;
use StinWeatherApp\Component\Router\Route;
use StinWeatherApp\Controller\Api\PaymentController as ApiPaymentController;
use StinWeatherApp\Controller\DocsController;
use StinWeatherApp\Controller\HomeController;
use StinWeatherApp\Controller\NotFoundController;
use StinWeatherApp\Controller\PayController;

return [
	new Route("/", HomeController::class, "index"),
	new Route("/pay", PayController::class, "paymentForm", Method::GET),
	new Route("/time", HomeController::class, "time"),
	new Route("/api/payment", ApiPaymentController::class, "processPayment", Method::POST),
	new Route("/api/payment", ApiPaymentController::class, "options", Method::OPTIONS),
	new Route("/docs/api", DocsController::class, "api"),
	new Route("/not-found", NotFoundController::class, "index"),
];