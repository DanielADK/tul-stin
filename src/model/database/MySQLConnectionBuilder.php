<?php

namespace StinWeatherApp\model\database;

use PDO;
use PDOException;

class MySQLConnectionBuilder extends Db implements ConnectionBuilder {
	private string $host;
	private string $username;
	private string $password;
	private string $database;

	public function setHost(string $host): MySQLConnectionBuilder {
		$this->host = $host;
		return $this;
	}

	public function getHost(): string {
		return $this->host;
	}

	public function setUsername(string $username): MySQLConnectionBuilder {
		$this->username = $username;
		return $this;
	}

	public function getUsername(): string {
		return $this->username;
	}

	public function setPassword(string $password): MySQLConnectionBuilder {
		$this->password = $password;
		return $this;
	}

	public function getPassword(): string {
		return $this->password;
	}

	public function setDatabase(string $database): MySQLConnectionBuilder {
		$this->database = $database;
		return $this;
	}

	public function getDatabase(): string {
		return $this->database;
	}

	public function buildConnection(): void {
		try {
			self::$connection = @new PDO(
				"mysql:host=$this->host;dbname=$this->database",
				$this->username,
				$this->password,
				self::$settings);
				self::$connection->exec("set names utf8");
		} catch (PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}
	}
}