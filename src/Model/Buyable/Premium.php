<?php

namespace StinWeatherApp\Model\Buyable;

use Exception;
use Override;
use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\PersistableInterface;

/**
 * Class Premium
 *
 * @author Daniel Adámek
 * @description Model for premium services
 * @package StinWeatherApp\Model
 */
class Premium extends Buyable implements PersistableInterface {
	/** @var int $duration duration in seconds */
	private int $duration;

	/**
	 * @description
	 *
	 * @param string $name
	 * @param float  $price
	 * @param int    $duration
	 */
	public function __construct(string $name, float $price, int $duration) {
		$this->setName($name);
		$this->setPrice($price);
		$this->duration = $duration;
	}

	/**
	 * @description Returns list of all premiums
	 * @return Premium[]
	 */
	public static function getListOfPremiums(): array {
		$result = Db::queryAll("SELECT * FROM premium");
		if (!is_array($result) || count($result) === 0) {
			return array();
		}

		$premiums = array();
		foreach ($result as $row) {
			if (is_array($row)) {
				$premiums[] = new Premium($row['name'], $row['price'], $row['duration']);
			}
		}
		return $premiums;
	}

	/**
	 * @inheritDoc
	 */
	#[\Override]
	public static function getById(int|string $id): ?self {
		$data = Db::queryOne('SELECT * FROM premium WHERE id = :id', ['id' => $id]);

		if (is_array($data)) {
			$premium = new Premium($data['name'], $data['price'], $data['duration']);
			$premium->setId($data["id"]);
			return $premium;
		} else {
			return null;
		}
	}

	public function getDuration(): int {
		return $this->duration;
	}

	/**
	 * @inheritDoc
	 * @throws \Exception
	 */
	#[Override]
	public function persist(): bool {
		$data = [
			'name' => $this->getName(),
			'price' => $this->getPrice(),
			'duration' => $this->duration,
			'currency' => $this->getCurrency()->value
		];

		if ($this->getId()) {
			$data = array_merge($data, ['id' => $this->getId()]);
			Db::queryOne('UPDATE premium SET name = :name, price = :price, duration = :duration, currency = :currency WHERE id = :id', $data);
		} else {
			$result = Db::queryOne('INSERT INTO premium (name, price, duration, currency) VALUES (:name, :price, :duration, :currency)', $data);
			if ($result) {
				$id = Db::queryCell('SELECT last_insert_rowid()');
				if (is_int($id)) {
					$this->setId($id);
				} else {
					throw new Exception('Failed to get the last insert id.');
				}
			}
		}
		return true;
	}
}