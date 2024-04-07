<?php

namespace StinWeatherApp\Controller;

use StinWeatherApp\Component\Http\Response;

/**
 * Class DocsController
 *
 * @description This controller is used for displaying help page.
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @package StinWeatherApp\Controller
 */
final class DocsController extends AbstractController {
	/**
	 * Index action
	 */
	public function api(): Response {
		return $this->render("apidocs", []);
	}
}