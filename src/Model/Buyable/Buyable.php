<?php

namespace StinWeatherApp\Model\Buyable;

use StinWeatherApp\Model\Types\Currency;

/**
 * Abstract Class Buyable
 *
 * @author Daniel Adámek
 * @description Model for buyable items/products/services
 * @package StinWeatherApp\Model\Buyable
 */
abstract class Buyable {
	private ?int $id = null;
	private string $name;
	private float $price;
	private Currency $currency;

	public function getId(): ?int {
		return $this->id;
	}

	public function setId(int $id): Buyable {
		$this->id = $id;
		return $this;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setName(string $name): Buyable {
		$this->name = $name;
		return $this;
	}

	public function getPrice(): float {
		return $this->price;
	}

	public function setPrice(float $price): Buyable {
		$this->price = $price;
		return $this;
	}

	public function getCurrency(): Currency {
		return $this->currency;
	}

	public function getCurrencyString(): string {
		return $this->currency->value;
	}

	public function setCurrency(Currency $currency): Buyable {
		$this->currency = $currency;
		return $this;
	}
}