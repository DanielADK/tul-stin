<?php

namespace StinWeatherApp\Controller;

use StinWeatherApp\Component\Http\Response;

class PaymentController extends AbstractController {
	/*
	private PaymentProcessingHandler $paymentProcessingHandler;

	public function __construct(PaymentProcessingHandler $paymentProcessingHandler) {
		parent::__construct($this->request);
		$this->paymentProcessingHandler = $paymentProcessingHandler;
	}
	*/

	public function paymentProcessing(): Response {
		return new Response($_POST["name"]);
	}
}