<?php

namespace StinWeatherApp\Model\Buyable;

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
			$premiums[] = new Premium((string)$row['name'], (float)$row['price'], (int)$row['duration']);
		}
		return $premiums;
	}

	/**
	 * @inheritDoc
	 */
	#[\Override]
	public static function getById(int|string $id): ?self {
		$data = Db::queryOne('SELECT * FROM premium WHERE name = :id', ['id' => $id]);

		if ($data) {
			return new Premium($data['name'], $data['price'], $data['duration']);
		} else {
			return null;
		}
	}

	public function getDuration(): int {
		return $this->duration;
	}

	/**
	 * @inheritDoc
	 */
	#[\Override] public function persist(): bool {
		$data = [
			'name' => $this->getName(),
			'price' => $this->getPrice(),
			'duration' => $this->duration
		];

		if ($this->getName() === null) {
			Db::queryOne('INSERT INTO premium (name, price, duration) VALUES (:name, :price, :duration)', $data);
		} else {
			Db::queryOne('UPDATE premium SET price = :price, duration = :duration WHERE name = :name', $data);
		}
		return true;
	}
}