<?php

namespace StinWeatherApp\Model;

use DateTime;
use Exception;
use Random\RandomException;
use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\PersistableInterface;

class User implements PersistableInterface {
	private int $id;
	private string $username;
	private string|null $apiKey = null;
	private DateTime|null $premiumUntil = null;

	public function __construct(int $id, string $username) {
		$this->id = $id;
		$this->username = $username;
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

	public function generateApiKey(): User {
		try {
			$this->apiKey = hash("sha256", random_bytes(64));
		} catch (RandomException $e) {
			$this->apiKey = hash("sha256",
				$this->id
				. $this->username
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

	/**
	 * @return bool
	 * @throws Exception
	 */
	#[\Override]
	public function persist(): bool {
		$data = [
			'username' => $this->username,
			'api_key' => $this->apiKey,
			'premium_until' => $this->premiumUntil,
		];

		if ($this->id) {
			$data = array_merge($data, ['id' => $this->id]);
			// Update the existing record
			$result = Db::queryOne('UPDATE user 
				SET 
				   username = :username,
				   api_key = :api_key,
				   premium_until = :premium_until
			   WHERE id = :id',
				$data);
		} else {
			// Insert a new record
			$result = Db::queryOne('INSERT INTO user (username, api_key, premium_until) 
											VALUES (:username, :api_key, :premium_until)', $data);
			if ($result) {
				// Get the last insert id
				$id = Db::queryCell('SELECT last_insert_rowid()');;
				if (is_int($id)) {
					$this->setId($id);
				} else {
					throw new Exception('Failed to get the last insert id.');
				}
			}
		}

		// Check if the operation was successful
		if ($result) {
			return true;
		} else {
			throw new Exception('Failed to save the user.');
		}

	}

	/**
	 * @param int|string $id
	 *
	 * @return User
	 */
	#[\Override]
	public static function getById(int|string $id): User {
		return self::getUserByUsername($id);
	}

}