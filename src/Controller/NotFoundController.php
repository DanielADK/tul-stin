<?php

namespace StinWeatherApp\Controller;

use StinWeatherApp\Component\Http\Response;

/**
 * Class NotFoundController
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @package StinWeatherApp\Controller
 */
final class NotFoundController extends AbstractController {
	public function index(): Response {
		return new Response("Page not found", 404);
	}
}