<?php

namespace StinWeatherApp\Model\Types;

enum Currency: string {
	case CZK = "CZK";
	case EUR = "EUR";

	/**
	 * @description Returns if the value is valid
	 *
	 * @param string $value
	 *
	 * @return boolean
	 */
	public static function isValid(string $value): bool {
		$values = array_flip(array_map(fn($case) => $case->name, self::cases()));
		return isset($values[$value]);
	}

	/**
	 * @description Returns the value from string
	 * @param string $value
	 * @return PaymentType|null
	 */
	public static function fromString(string $value): ?Currency {
		$value = strtoupper($value);
		return (self::isValid($value) ? self::from($value) : null);
	}
}
