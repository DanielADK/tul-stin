<?php

namespace StinWeatherApp\Services\Payment\Parser;

use StinWeatherApp\Model\Payment;

/**
 * Interface PaymentParserInterface
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Interface for parsing payment data
 * @package StinWeatherApp\Services\Payment\Parser
 */
interface PaymentParserInterface {
	/**
	 * @description Parses the data and returns a Payment object
	 *
	 * @param string $data
	 *
	 * @return Payment
	 */
	public function parse(string $data): Payment;
}