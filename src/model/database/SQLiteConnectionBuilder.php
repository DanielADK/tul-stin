<?php

namespace StinWeatherApp\model\database;

use PDO;
use PDOException;

class SQLiteConnectionBuilder extends Db implements ConnectionBuilder {
	private string $database;

	public function setDatabase(string $database): SQLiteConnectionBuilder {
		$this->database = $database;
		return $this;
	}

	public function getDatabase(string $database): string {
		return $this->database;
	}

	public function buildConnection(): void {
		try {
			self::$connection = @new PDO(
				"sqlite:".$this->database.".sqlite",
				null,
				null,
				self::$settings);
		} catch (PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}
	}
}