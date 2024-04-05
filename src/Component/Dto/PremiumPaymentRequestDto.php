<?php

namespace StinWeatherApp\Component\Dto;

use StinWeatherApp\Model\Buyable\Premium;
use StinWeatherApp\Model\Card;
use StinWeatherApp\Model\Types\Currency;
use StinWeatherApp\Model\Types\PaymentType;
use StinWeatherApp\Model\User;

class PremiumPaymentRequestDto implements DtoInterface {
	private ?Premium $premium = null;
	private ?PaymentType $paymentType = null;
	private ?Card $card = null;
	private ?User $user = null;
	private ?Currency $currency = null;

	public function getPremium(): ?Premium {
		return $this->premium;
	}

	public function setPremium(?Premium $premium): PremiumPaymentRequestDto {
		$this->premium = $premium;
		return $this;
	}

	public function getPaymentType(): ?PaymentType {
		return $this->paymentType;
	}

	public function setPaymentType(?PaymentType $paymentType): PremiumPaymentRequestDto {
		$this->paymentType = $paymentType;
		return $this;
	}

	public function getCard(): ?Card {
		return $this->card;
	}

	public function setCard(?Card $card): PremiumPaymentRequestDto {
		$this->card = $card;
		return $this;
	}

	public function getUser(): ?User {
		return $this->user;
	}

	public function setUser(?User $user): PremiumPaymentRequestDto {
		$this->user = $user;
		return $this;
	}

	public function getCurrency(): ?Currency {
		return $this->currency;
	}

	public function setCurrency(?Currency $currency): PremiumPaymentRequestDto {
		$this->currency = $currency;
		return $this;
	}
}