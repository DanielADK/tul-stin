<?php

namespace StinWeatherApp\Model\Builder;

use DateTime;
use InvalidArgumentException;
use StinWeatherApp\Model\Payment;
use StinWeatherApp\Model\Types\Currency;
use StinWeatherApp\Model\Types\PaymentType;

/**
 * Class PaymentBuilder
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Builder for Payment object
 * @package StinWeatherApp\Model\Builder
 */
class PaymentBuilder {
	private float $amount;
	private Currency $currency;
	private DateTime $datetime;
	private PaymentType $type;
	private string $status;

	/**
	 * Set the amount for the Payment.
	 *
	 * @param float $amount
	 *
	 * @return self
	 */
	public function setAmount(float $amount): self {
		if ($amount < 0) {
			throw new InvalidArgumentException('Invalid amount');
		}
		$this->amount = $amount;
		return $this;
	}

	/**
	 * Set the Currency for the Payment.
	 *
	 * @param Currency $currency
	 *
	 * @return self
	 */
	public function setCurrency(Currency $currency): self {
		$this->currency = $currency;
		return $this;
	}

	/**
	 * Set the datetime for the Payment.
	 *
	 * @param DateTime $datetime
	 *
	 * @return self
	 */
	public function setDatetime(DateTime $datetime): self {
		$this->datetime = $datetime;
		return $this;
	}

	/**
	 * Set the type for the Payment.
	 *
	 * @param PaymentType $type
	 *
	 * @return self
	 */
	public function setType(PaymentType $type): self {
		$this->type = $type;
		return $this;
	}

	/**
	 * Set the status for the Payment.
	 *
	 * @param string $status
	 *
	 * @return self
	 */
	public function setStatus(string $status): self {
		$this->status = $status;
		return $this;
	}

	/**
	 * Build the Payment object.
	 *
	 * @return Payment
	 */
	public function build(): Payment {
		return new Payment(
			$this->amount,
			$this->currency,
			$this->datetime,
			$this->type,
			$this->status
		);
	}
}