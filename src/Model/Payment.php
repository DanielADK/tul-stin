<?php

namespace StinWeatherApp\Model;

use Datetime;
use StinWeatherApp\Model\Types\Currency;
use StinWeatherApp\Model\Types\PaymentType;

/**
 * Class Payment
 *
 * @author Daniel Adámek
 * @description Model for payment
 * @package StinWeatherApp\Model
 */
class Payment {
	private float $amount;
	private Currency $currency;
	private DateTime $datetime;
	private PaymentType $type;
	private string $status;

	/**
	 * Payment constructor.
	 *
	 * @param float       $amount
	 * @param Currency    $currency
	 * @param DateTime    $datetime
	 * @param PaymentType $type
	 * @param string      $status
	 */
	public function __construct(float $amount, Currency $currency, DateTime $datetime, PaymentType $type, string $status) {
		$this->amount = $amount;
		$this->currency = $currency;
		$this->datetime = $datetime;
		$this->type = $type;
		$this->status = $status;
	}

	/**
	 * @description Sets the amount
	 *
	 * @param float $amount
	 *
	 * @return Payment
	 */
	public function setAmount(float $amount): Payment {
		$this->amount = $amount;
		return $this;
	}

	/**
	 * @description Gets the amount
	 * @return float
	 */
	public function getAmount(): float {
		return $this->amount;
	}

	/**
	 * @description Sets the currency
	 *
	 * @param Currency $currency
	 *
	 * @return Payment
	 */
	public function setCurrency(Currency $currency): Payment {
		$this->currency = $currency;
		return $this;
	}

	/**
	 * @description Gets the currency
	 * @return Currency
	 */
	public function getCurrency(): Currency {
		return $this->currency;
	}

	/**
	 * @description Sets the datetime
	 *
	 * @param DateTime $datetime
	 *
	 * @return Payment
	 */
	public function setDatetime(Datetime $datetime): Payment {
		$this->datetime = $datetime;
		return $this;
	}

	/**
	 * @description Gets the datetime
	 * @return DateTime
	 */
	public function getDatetime(): Datetime {
		return $this->datetime;
	}

	/**
	 * @description Sets the type
	 *
	 * @param PaymentType $type
	 *
	 * @return Payment
	 */
	public function setType(PaymentType $type): Payment {
		$this->type = $type;
		return $this;
	}

	/**
	 * @description Gets the type
	 * @return PaymentType
	 */
	public function getType(): PaymentType {
		return $this->type;
	}

	/**
	 * @description Sets the status
	 *
	 * @param string $status
	 *
	 * @return Payment
	 */
	public function setStatus(string $status): Payment {
		$this->status = $status;
		return $this;
	}

	/**
	 * @description Gets the status
	 * @return string
	 */
	public function getStatus(): string {
		return $this->status;
	}
}