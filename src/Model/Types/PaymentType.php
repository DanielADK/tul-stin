<?php

namespace StinWeatherApp\Model\Types;

enum PaymentType: string {
	case CASH = "CASH";
	case CARD = "CARD";
}