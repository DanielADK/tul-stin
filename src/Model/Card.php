<?php

namespace StinWeatherApp\Model;

/**
 * Class Card
 *
 * @author Daniel Adámek
 * @description Model for card
 * @package StinWeatherApp\Model
 */
class Card {

	public const array cardKeys = ['cardNumber', 'expiryDate', 'code'];
	private string $number;
	private string $expiration;
	private string $code;

	/**
	 * Card constructor.
	 *
	 * @param string $number
	 * @param string $expiration
	 * @param string $code
	 */
	public function __construct(string $number, string $expiration, string $code) {
		$this->number = $number;
		$this->expiration = $expiration;
		$this->code = $code;
	}

	public function getNumber(): string {
		return $this->number;
	}

	public function setNumber(string $number): Card {
		$this->number = $number;
		return $this;
	}

	public function getExpiration(): string {
		return $this->expiration;
	}

	public function setExpiration(string $expiration): Card {
		$this->expiration = $expiration;
		return $this;
	}

	public function getCode(): string {
		return $this->code;
	}

	public function setCode(string $code): Card {
		$this->code = $code;
		return $this;
	}

}