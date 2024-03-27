<?php

namespace StinWeatherApp\Controller;

use DateTime;
use Exception;
use StinWeatherApp\Component\Http\Response;

/**
 * Class HomeController
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @package StinWeatherApp\Controller
 */
final class HomeController extends AbstractController {
	/**
	 * Index action
	 *
	 * @throws Exception
	 */
	public function index(): Response {
		return $this->render("index", ["helloworld" => "Zdravím světe!!"]);
	}

	/**
	 * @throws Exception
	 */
	public function time(): Response {
		$date = new DateTime();
		return $this->render("time", ["date" => $date]);
	}
}