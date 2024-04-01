<?php

namespace StinWeatherApp\Controller;

use StinWeatherApp\Component\Http\Response;

class PayController extends AbstractController {

	public function paymentForm(): Response {
		return $this->render("pay");
	}
}