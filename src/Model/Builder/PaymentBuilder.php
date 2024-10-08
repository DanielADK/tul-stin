<?php

namespace StinWeatherApp\Model\Builder;

use DateTime;
use Exception;
use InvalidArgumentException;
use StinWeatherApp\Model\Card;
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

	private Card|null $card = null;

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
	 * Set the card for the Payment.
	 *
	 * @param Card $card
	 *
	 * @return self
	 */
	public function setCard(Card $card): self {
		$this->card = $card;
		return $this;
	}

	/**
	 * Build the Payment object.
	 *
	 * @return Payment
	 * @throws Exception
	 */
	public function build(): Payment {
		$payment = new Payment(
			$this->amount,
			$this->currency,
			$this->datetime,
			$this->type,
			$this->status
		);

		if ($this->card !== null && $this->type === PaymentType::CARD) {
			$payment->setCard($this->card);
		} elseif ($this->card === null && $this->type === PaymentType::CARD) {
			throw new Exception('Card can be set only for CARD');
		}
		return $payment;
	}
}