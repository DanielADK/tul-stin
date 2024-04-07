<?php

use StinWeatherApp\Component\Http\Method;
use StinWeatherApp\Component\Router\Route;
use StinWeatherApp\Controller\Api\PaymentController as ApiPaymentController;
use StinWeatherApp\Controller\HomeController;
use StinWeatherApp\Controller\NotFoundController;
use StinWeatherApp\Controller\PayController;

return [
	"/" => new Route("/", HomeController::class, "index"),
	"/pay" => new Route("/pay", PayController::class, "paymentForm", Method::GET),
	"/time" => new Route("/time", HomeController::class, "time"),
	"/api/payment" => new Route("/api/payment", ApiPaymentController::class, "processPayment", Method::POST),
	"notFound" => new Route("/not-found", NotFoundController::class, "index"),
];