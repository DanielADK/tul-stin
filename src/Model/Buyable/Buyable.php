<?php

namespace StinWeatherApp\Model\Buyable;

/**
 * Abstract Class Buyable
 *
 * @author Daniel Adámek
 * @description Model for buyable items/products/services
 * @package StinWeatherApp\Model\Buyable
 */
abstract class Buyable {
	private string $name;
	private float $price;

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


}