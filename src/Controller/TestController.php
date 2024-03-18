<?php

namespace StinWeatherApp\Controller;

use StinWeatherApp\Component\Http\Response;
use StinWeatherApp\Controller\AbstractController;

/**
 * Class TestController - only for testing purposes
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @package StinWeatherApp\Controller
 */
final class TestController extends AbstractController {

	public function get(): Response {
		return new Response("GET method", 200);
	}

	public function post(): Response {
		return new Response("POST method", 201);
	}

	public function put(): Response {
		return new Response("PUT method", 200);
	}

	public function delete(): Response {
		return new Response("DELETE method", 200);
	}

	public function options(): Response {
		return new Response("OPTIONS method", 200);
	}

	public function status200(): Response {
		return new Response("Status 200", 200);
	}

	public function status201(): Response {
		return new Response("Status 201", 201);
	}

	public function status400(): Response {
		return new Response("Status 400", 400);
	}

	public function status404(): Response {
		return new Response("Status 404", 404);
	}

	public function status500(): Response {
		return new Response("Status 500", 500);
	}

	public function testMethod(): Response {
		return new Response("Status 500", 500);
	}

	public function noResponseObjectReturn(): string {
		return "This method should return Response object";
	}

	public function methodSetsHeaders(): Response {
		return new Response("", 200, array("Content-Type: application/json", "Authorization: Bearer token"));
	}
}