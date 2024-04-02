<?php

namespace StinWeatherApp\Model\Builder;

use StinWeatherApp\Model\Buyable\Premium;

class PremiumBuilder {
	private $name;
	private $price;
	private $duration;

	public function setName(string $name): self {
		$this->name = $name;
		return $this;
	}

	public function setPrice(float $price): self {
		$this->price = $price;
		return $this;
	}

	public function setDuration(int $duration): self {
		$this->duration = $duration;
		return $this;
	}

	public function build(): Premium {
		return new Premium($this->name, $this->price, $this->duration);
	}

}