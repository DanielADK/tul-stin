<?php

require_once __DIR__ . '/vendor/autoload.php';

use StinWeatherApp\Component\Auth\ApiKeyAuth;
use StinWeatherApp\Component\Http\Method;
use StinWeatherApp\Component\Router\Route;
use StinWeatherApp\Component\Router\Strategy\ParamPathStrategy;
use StinWeatherApp\Controller\Api\PaymentController as ApiPaymentController;
use StinWeatherApp\Controller\Api\PlacesController as ApiPlacesController;
use StinWeatherApp\Controller\Api\UserController as ApiUserController;
use StinWeatherApp\Controller\Api\WeatherController as ApiWeatherController;
use StinWeatherApp\Controller\DocsController;
use StinWeatherApp\Controller\HomeController;
use StinWeatherApp\Controller\NotFoundController;
use StinWeatherApp\Controller\PayController;

return array(
	new Route("", HomeController::class, "index"),
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
	// places
	new Route(path: "/api/places", controller: ApiPlacesController::class, controllerMethod: "getPlaces", httpMethod: Method::GET, auth: new ApiKeyAuth()),
	new Route(path: "/api/places", controller: ApiPlacesController::class, controllerMethod: "addPlace", httpMethod: Method::POST, auth: new ApiKeyAuth()),
	new Route(path: "/api/places", controller: ApiPlacesController::class, controllerMethod: "options", httpMethod: Method::OPTIONS),
	new Route(path: "/api/places/:city", controller: ApiPlacesController::class, controllerMethod: "removePlace", httpMethod: Method::DELETE, pathStrategy: new ParamPathStrategy(), auth: new ApiKeyAuth()),
	// forecast
	new Route("/api/weather", ApiWeatherController::class, "getWeather", Method::GET),
	new Route("/api/weather", ApiWeatherController::class, "options", Method::OPTIONS),
);
