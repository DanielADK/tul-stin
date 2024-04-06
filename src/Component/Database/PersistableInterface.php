<?php

namespace StinWeatherApp\Component\Database;

/**
 * Interface PersistableInterface
 *
 * @description Interface for persistable objects
 * @package StinWeatherApp\Component\Database
 */
interface PersistableInterface {

	/**
	 * @description Returns the object by id
	 *
	 * @param int|string $id
	 *
	 * @return PersistableInterface|null
	 */
	public static function getById(int|string $id): ?self;

	/**
	 * @description Persists the object
	 * @return bool
	 */
	public function persist(): bool;

}