<?php

namespace StinWeatherApp\Model;

use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\PersistableInterface;

class Place implements PersistableInterface {
	private ?string $name;
	private float $latitude;
	private float $longitude;

	public function setName(string $name): Place {
		$this->name = $name;
		return $this;
	}

	public function getName(): ?string {
		return $this->name;
	}

	public function setLatitude(float $latitude): Place {
		$this->latitude = $latitude;
		return $this;
	}

	public function getLatitude(): float {
		return $this->latitude;
	}

	public function setLongitude(float $longitude): Place {
		$this->longitude = $longitude;
		return $this;
	}

	public function getLongitude(): float {
		return $this->longitude;
	}

	/**
	 * @param int|string $id
	 * @return self|null
	 */
	public static function getById(int|string $id): ?self {
		$result = Db::queryOne("SELECT * FROM place WHERE name = ?", array($id));
		return (!$result) ? null : self::parseFromArray($result);
	}

	/**
	 * @description Parses an array into a Place object
	 * @param array<string> $array
	 * @return Place
	 */
	private static function parseFromArray(array $array): Place {
		$keys = array("name", "latitude", "longitude");
		foreach ($keys as $key) {
			if (!array_key_exists($key, $array)) {
				throw new \InvalidArgumentException("Missing key: $key");
			}
		}
		$place = new Place();
		$place->setName($array["name"]);
		$place->setLatitude((float)$array["latitude"]);
		$place->setLongitude((float)$array["longitude"]);
		return $place;
	}

	/**
	 * @return bool
	 */
	public function persist(): bool {
		$data = [
			"name" => $this->name,
			"latitude" => $this->latitude,
			"longitude" => $this->longitude
		];

		if ($this->name === null) {
			return Db::execute("INSERT INTO place (name, latitude, longitude) VALUES (?, ?, ?)", array_values($data));
		} else {
			return Db::execute("UPDATE place SET name = ?, latitude = ?, longitude = ? WHERE name = ?", array_values($data));
		}
	}
}