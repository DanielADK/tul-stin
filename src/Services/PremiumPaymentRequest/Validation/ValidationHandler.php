<?php

namespace StinWeatherApp\Services\PremiumPaymentRequest\Validation;

use Exception;
use StinWeatherApp\Services\PremiumPaymentRequest\Creator\PremiumPaymentRequestDTO;

/**
 * Abstract class ValidationHandler
 *
 * @description Abstract class for validation handlers
 * @package StinWeatherApp\Services\PremiumPaymentRequestDTO\Validator
 */
abstract class ValidationHandler {
	protected ?ValidationHandler $nextHandler = null;
	/** @var array<string, string|array<string, string>> */
	protected array $data;
	protected PremiumPaymentRequestDTO $premiumPaymentRequest;

	/**
	 * ValidationHandler constructor.
	 *
	 * @param array<string, string|array<string,string>> $data
	 */
	public function __construct(array $data) {
		$this->data = $data;
	}

	/**
	 * @description Sets the next handler
	 *
	 * @param ValidationHandler $handler
	 *
	 * @return ValidationHandler
	 */
	public function setNext(ValidationHandler $handler): ValidationHandler {
		$this->nextHandler = $handler;
		return $handler;
	}

	/**
	 * @description Handles the validation
	 * @return bool
	 * @throws Exception
	 */
	public function handle(): bool {
		$this->validate();
		if ($this->nextHandler) {
			return $this->nextHandler->handle();
		}

		return true;
	}

	/**
	 * @description Validates the data
	 * @return void
	 * @throws Exception
	 */
	abstract protected function validate(): void;
}