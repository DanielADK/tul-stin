<?php

namespace StinWeatherApp\Model\Types;

enum Currency: string {
	case CZK = "CZK";
	case EUR = "EUR";

	public static function isValid(string $value): bool {
		foreach (self::cases() as $case) {
			if ($value === $case->value) {
				return true;
			}
		}

		return false;
	}

	public static function fromString(string $value): ?Currency {
		$value = strtoupper($value);
		foreach (self::cases() as $case) {
			if ($value === $case->value) {
				return $case;
			}
		}
		return null;
	}
}
