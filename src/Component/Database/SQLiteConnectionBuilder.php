<?php

namespace StinWeatherApp\Component\Database;

use PDO;
use PDOException;

class SQLiteConnectionBuilder extends Db implements ConnectionBuilder {
	private string $database;

	public function setDatabase(string $database): SQLiteConnectionBuilder {
		$this->database = $database;
		return $this;
	}

	public function getDatabase(): string {
		return $this->database;
	}

	/**
	 * Build the connection to SQLite database
	 *
	 * @return void
	 * @throws PDOException
	 */
	public function buildConnection(): void {
		self::$connection = new PDO(
			"sqlite:".$this->database,
			null,
			null,
			self::$settings);
	}
}