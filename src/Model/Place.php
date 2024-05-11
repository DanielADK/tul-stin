<?php

namespace StinWeatherApp\Model;

use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\PersistableInterface;

class Place implements PersistableInterface {
	private ?string $name;
	private float $latitude;
	private float $longitude;

	public function __construct(string $name, float $latitude, float $longitude) {
		$this->setName($name);
		$this->setLatitude($latitude);
		$this->setLongitude($longitude);
	}

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
		return new Place(
			name: $array["name"],
			latitude: (float)$array["latitude"],
			longitude: (float)$array["longitude"]
		);
	}

	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function persist(): bool {
		$data = [
			"name" => $this->name,
			"latitude" => $this->latitude,
			"longitude" => $this->longitude
		];

		return Db::execute("INSERT INTO place (name, latitude, longitude)
							        SELECT * FROM (SELECT :name as name, :latitude as latitude, :longitude as longitude) AS tmp
							        WHERE NOT EXISTS (
							            SELECT name FROM place WHERE name = :name
							        ) LIMIT 1", $data);
	}
}