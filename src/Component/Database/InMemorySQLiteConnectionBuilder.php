<?php

namespace StinWeatherApp\Component\Database;

use PDO;
use PDOException;

class InMemorySQLiteConnectionBuilder extends SQLiteConnectionBuilder {
	public function buildConnection(): void {
		try {
			self::$connection = @new PDO(
				"sqlite::memory:",
				null,
				null,
				self::$settings);
		} catch (PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}
	}
}