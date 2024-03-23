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
final class HomeController extends AbstractController {
	/**
	 * Index action
	 *
	 * @throws Exception
	 */
	public function index(): Response {
		return $this->render("index");
	}

	/**
	 * @throws Exception
	 */
	public function time(): Response {
		$date = date("d.m.Y");
		$time = date(" H:i:s");
		return $this->render("index", ["date" => $date, "time" => $time]);
	}
}