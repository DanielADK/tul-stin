<?php

namespace StinWeatherApp\Model\Types;

enum PaymentType: string {
	case CASH = "CASH";
	case CARD = "CARD";


	public static function fromString(string $value): ?PaymentType {
		$value = strtoupper($value);
		foreach (self::cases() as $case) {
			if ($value === $case->value) {
				return $case;
			}
		}
		return null;
	}
}