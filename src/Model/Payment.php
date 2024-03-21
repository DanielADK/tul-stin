<?php

namespace StinWeatherApp\Model;

use Datetime;
use StinWeatherApp\Model\Types\Currency;
use StinWeatherApp\Model\Types\PaymentType;

class Payment {
	private float $amount;
	private Currency $currency;
	private DateTime $datetime;
	private PaymentType $type;
	private string $status;

	public function __construct(float $amount, Currency $currency, DateTime $datetime, PaymentType $type, string $status) {
		$this->amount = $amount;
		$this->currency = $currency;
		$this->datetime = $datetime;
		$this->type = $type;
		$this->status = $status;
	}
}