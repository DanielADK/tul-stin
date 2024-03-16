<?php

namespace StinWeatherApp\Controller;

use Exception;
use StinWeatherApp\Component\Http\Response;

/**
 * Class HomeController
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @package StinWeatherApp\Controller
 */
class HomeController extends AbstractController {
	/**
	 * Index action
	 *
	 * @throws Exception
	 */
	public function index(): Response {
		return $this->render("index");
	}
}