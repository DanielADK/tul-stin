<?php

namespace StinWeatherApp\Model;

use DateTime;
use Exception;
use Random\RandomException;

class User {
	private int $id;
	private string $username;
	private string $email;
	private string|null $apiKey = null;
	private DateTime|null $premiumUntil = null;

	public function __construct(int $id, string $username, string $email) {
		$this->id = $id;
		$this->username = $username;
		$this->email = $email;
	}

	public function setId(int $id): User {
		$this->id = $id;
		return $this;
	}

	public function getId(): int {
		return $this->id;
	}

	public function setUsername(string $username): User {
		$this->username = $username;
		return $this;
	}

	public function getUsername(): string {
		return $this->username;
	}

	public function setEmail(string $email): User {
		$this->email = $email;
		return $this;
	}

	public function getEmail(): string {
		return $this->email;
	}

	public function generateApiKey(): User {
		try {
			$this->apiKey = hash("sha256", random_bytes(64));
		} catch (RandomException $e) {
			$this->apiKey = hash("sha256",
				$this->id
				. $this->username
				. $this->email
				. (new DateTime())->format("Y-m-d H:i:s"));
		}
		return $this;
	}

	public function getApiKey(): string|null {
		return $this->apiKey;
	}

	public function setPremiumUntil(DateTime $premiumUntil): User {
		$this->premiumUntil = $premiumUntil;
		return $this;
	}

	public function getPremiumUntil(): DateTime|null {
		return $this->premiumUntil;
	}

	public static function getUserByUsername(string $username): User {
		$result = Db::queryOne("SELECT * FROM user WHERE username = ?", [$username]);
		return self::parseFromArray($result);
	}

	/**
	 */
	private static function parseFromArray(array $row): User {
		$user = new User($row['id'], $row['username'], $row['email']);
		if ($row['api_key'] !== null) {
			$user->apiKey = $row['api_key'];
		}
		if ($row['premium_until'] !== null) {
			try {
				$user->premiumUntil = new DateTime($row['premium_until']);
			} catch (Exception $e) {
				$user->premiumUntil = null;
			}
		}
		return $user;
	}
}