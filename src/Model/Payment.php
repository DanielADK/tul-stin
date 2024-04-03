<?php

namespace StinWeatherApp\Model;

use Datetime;
use Exception;
use Override;
use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\PersistableInterface;
use StinWeatherApp\Model\Types\Currency;
use StinWeatherApp\Model\Types\PaymentType;

/**
 * Class Payment
 *
 * @author Daniel Adámek
 * @description Model for payment
 * @package StinWeatherApp\Model
 */
class Payment implements PersistableInterface {
	private int $id;
	private float $amount;
	private Currency $currency;
	private DateTime $datetime;
	private PaymentType $type;
	/** @var string NONE,PREPROCESSING,PAYMENT,DONE,FAILED */
	private string $status;
	/** @var Card|null $card NOT PERSISTING */
	private Card|null $card = null;

	/**
	 * Payment constructor.
	 *
	 * @param float       $amount
	 * @param Currency    $currency
	 * @param DateTime    $datetime
	 * @param PaymentType $type
	 * @param string      $status
	 */
	public function __construct(float $amount, Currency $currency, DateTime $datetime, PaymentType $type, string $status) {
		$this->amount = $amount;
		$this->currency = $currency;
		$this->datetime = $datetime;
		$this->type = $type;
		$this->status = $status;
	}

	/**
	 * @description Sets the id
	 *
	 * @param int $id
	 *
	 * @return Payment
	 */
	public function setId(int $id): Payment {
		$this->id = $id;
		return $this;
	}

	/**
	 * @description Gets the id
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * @description Sets the amount
	 *
	 * @param float $amount
	 *
	 * @return Payment
	 */
	public function setAmount(float $amount): Payment {
		$this->amount = $amount;
		return $this;
	}

	/**
	 * @description Gets the amount
	 * @return float
	 */
	public function getAmount(): float {
		return $this->amount;
	}

	/**
	 * @description Sets the currency
	 *
	 * @param Currency $currency
	 *
	 * @return Payment
	 */
	public function setCurrency(Currency $currency): Payment {
		$this->currency = $currency;
		return $this;
	}

	/**
	 * @description Gets the currency
	 * @return Currency
	 */
	public function getCurrency(): Currency {
		return $this->currency;
	}

	/**
	 * @description Sets the datetime
	 *
	 * @param DateTime $datetime
	 *
	 * @return Payment
	 */
	public function setDatetime(Datetime $datetime): Payment {
		$this->datetime = $datetime;
		return $this;
	}

	/**
	 * @description Gets the datetime
	 * @return DateTime
	 */
	public function getDatetime(): Datetime {
		return $this->datetime;
	}

	/**
	 * @description Sets the type
	 *
	 * @param PaymentType $type
	 *
	 * @return Payment
	 */
	public function setType(PaymentType $type): Payment {
		$this->type = $type;
		return $this;
	}

	/**
	 * @description Gets the type
	 * @return PaymentType
	 */
	public function getType(): PaymentType {
		return $this->type;
	}

	/**
	 * @description Sets the status
	 *
	 * @param string $status
	 *
	 * @return Payment
	 */
	public function setStatus(string $status): Payment {
		$this->status = $status;
		return $this;
	}

	/**
	 * @description Gets the status
	 * @return string
	 */
	public function getStatus(): string {
		return $this->status;
	}

	public function setCard(?Card $card): Payment {
		$this->card = $card;
		return $this;
	}

	public function getCard(): ?Card {
		return $this->card;
	}

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	#[Override]
	public function persist(): bool {    // Prepare the data for insertion or update
		$data = [
			'amount' => $this->getAmount(),
			'currency' => $this->getCurrency()->value,
			'datetime' => $this->getDatetime()->format('Y-m-d H:i:s'),
			'type' => $this->getType()->value,
			'status' => $this->getStatus(),
		];

		if ($this->id) {
			$data = array_merge($data, ['id' => $this->id]);
			// Update the existing record
			$result = Db::queryOne('UPDATE payment 
				SET 
                   amount = :amount,
                   currency = :currency,
                   datetime = :datetime,
                   type = :type,
                   status = :status
               WHERE id = :id',
				$data);
		} else {
			// Insert a new record
			$result = Db::queryOne('INSERT INTO payment (amount, currency, datetime, type, status) 
											VALUES (:amount, :currency, :datetime, :type, :status)', $data);
			if ($result) {
				// Get the last insert id
				$id = Db::queryCell('SELECT last_insert_rowid()');;
				if (is_int($id)) {
					$this->setId($id);
				} else {
					throw new Exception('Failed to get the last insert id.');
				}
			}
		}

		// Check if the operation was successful
		if ($result) {
			return true;
		} else {
			throw new Exception('Failed to save the payment.');
		}
	}

	/**
	 * @description Gets the payment by id
	 *
	 * @param int|string $id
	 *
	 * @return Payment|null
	 * @throws Exception
	 */
	public static function getById(int|string $id): ?self {
		$data = Db::queryOne('SELECT * FROM payment WHERE id = :id', ['id' => $id]);

		if ($data) {
			$payment = new Payment(
				$data['amount'],
				Currency::from($data['currency']),
				new DateTime($data['datetime']),
				PaymentType::from($data['type']),
				$data['status']
			);
			$payment->setId($data['id']);
			return $payment;
		} else {
			return null;
		}
	}
}