<?php

namespace StinWeatherApp\Model;

use Exception;

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
	 *
	 * @throws Exception
	 */
	public function __construct(string $number, string $expiration, string $code) {
		$this->setNumber($number);
		$this->setExpiration($expiration);
		$this->setCode($code);
	}

	public function getNumber(): string {
		return $this->number;
	}

	/**
	 * @throws Exception
	 */
	public function setNumber(string $number): Card {
		if (!self::validateNumber($number)) {
			throw new Exception('Invalid card number. Must be 16 digits.');
		}
		$this->number = $number;
		return $this;
	}

	public function getExpiration(): string {
		return $this->expiration;
	}

	/**
	 * @throws Exception
	 */
	public function setExpiration(string $expiration): Card {
		if (!self::validateExpiration($expiration)) {
			throw new Exception('Invalid expiration date. Must be in format MM/YY.');
		}
		$this->expiration = $expiration;
		return $this;
	}

	public function getCode(): string {
		return $this->code;
	}

	/**
	 * @throws Exception
	 */
	public function setCode(string $code): Card {
		if (!self::validateCode($code)) {
			throw new Exception('Invalid code. Must be 3 digits.');
		}
		$this->code = $code;
		return $this;
	}

	/**
	 * @description Validates the card number
	 *
	 * @param string $number
	 *
	 * @return bool
	 */
	public static function validateNumber(string $number): bool {
		$result = preg_match('/^\d{16}$/', $number);
		return $result !== false && $result !== 0;
	}

	/**
	 * @description Validates the expiration
	 *
	 * @param string $expiration
	 *
	 * @return bool
	 */
	public static function validateExpiration(string $expiration): bool {
		$result = preg_match('/^\d{2}\/\d{2}$/', $expiration);
		return $result !== false && $result !== 0;
	}

	/**
	 * @description Validates the expiration date
	 *
	 * @param string $expiration
	 *
	 * @return bool
	 */
	public static function validateExpirationDate(string $expiration): bool {
		if (!self::validateExpiration($expiration)) {
			return false;
		}

		[$month, $year] = explode('/', $expiration);

		// Check if the month is between 1 and 12
		if ($month < 1 || $month > 12) {
			return false;
		}

		// Get the current year and month
		$currentYear = date('y');
		$currentMonth = date('m');

		// Check if the year is the same or higher than the current year
		// and if it's the same year, the month should be the same or higher than the current month
		if ($year < $currentYear || ($year == $currentYear && $month < $currentMonth)) {
			return false;
		}

		return true;
	}

	/**
	 * @description Validates the code
	 *
	 * @param string $code
	 *
	 * @return bool
	 */
	public static function validateCode(string $code): bool {
		$result = preg_match('/^\d{3}$/', $code);
		return $result !== false && $result !== 0;
	}

}