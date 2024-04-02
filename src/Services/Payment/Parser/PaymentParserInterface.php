<?php

namespace StinWeatherApp\Services\Payment\Parser;

use Exception;
use StinWeatherApp\Model\Payment;

/**
 * Interface PaymentParserInterface
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Interface for parsing payment data
 * @package StinWeatherApp\Services\Payment\Parser
 */
interface PaymentParserInterface {

	/** @var string[] */
	const array requiredKeys = ['amount', 'currency', 'type'];
	const array allKeys = ['amount', 'currency', 'type', 'datetime', 'status'];

	/**
	 * @description Parses the data and returns a Payment object
	 *
	 * @param string $data
	 *
	 * @return Payment
	 * @throws Exception
	 */
	public function parse(string $data): Payment;
}