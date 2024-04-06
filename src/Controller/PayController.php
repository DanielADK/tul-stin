<?php

namespace StinWeatherApp\Controller;

use StinWeatherApp\Component\Http\Response;
use StinWeatherApp\Model\Buyable\Premium;

class PayController extends AbstractController {

	public function paymentForm(): Response {
		$premiumList = Premium::getListOfPremiums();

		return $this->render("pay", [
			"premiumList" => $premiumList
		]);
	}
}