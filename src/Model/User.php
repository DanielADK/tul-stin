<?php

namespace StinWeatherApp\Model;

use DateTime;
use Exception;
use Random\RandomException;
use StinWeatherApp\Component\Database\Db;
use StinWeatherApp\Component\Database\PersistableInterface;

class User implements PersistableInterface {
	private ?int $id;
	private string $username;
	private ?string $apiKey = null;
	private ?DateTime $premiumUntil = null;
	/** @var array<Place> */
	private array $favouricePlaces = array();

	public function __construct(?int $id, string $username) {
		$this->id = $id;
		$this->username = $username;
	}

	public function setId(int $id): User {
		$this->id = $id;
		return $this;
	}

	public function getId(): ?int {
		return $this->id;
	}

	public function setUsername(string $username): User {
		$this->username = $username;
		return $this;
	}

	public function getUsername(): string {
		return $this->username;
	}

	public function addFavouritePlace(Place $place): User {
		$this->favouricePlaces[] = $place;
		return $this;
	}

	/**
	 * @description Returns the favourite places
	 * @return array<Place>
	 */
	public function getFavouritePlaces(): array {
		return $this->favouricePlaces;
	}

	/**
	 * @param int $length
	 *
	 * @return string
	 * @throws RandomException
	 */
	public function generateRandomBytes(int $length): string {
		return random_bytes($length);
	}

	public function generateApiKey(): User {
		if ($this->apiKey !== null || $this->hasPremium()) {
			return $this;
		}
		try {
			$this->apiKey = hash("sha256", $this->generateRandomBytes(32));
		} catch (RandomException $e) {
			$this->apiKey = hash("sha256",
				$this->username
				. (new DateTime())->format("Y-m-d H:i:s"));
		}
		return $this;
	}

	public function getApiKey(): string|null {
		return $this->apiKey;
	}

	private function setApiKey(?string $apiKey): User {
		$this->apiKey = $apiKey;
		return $this;
	}

	public function setPremiumUntil(?DateTime $premiumUntil): User {
		$this->premiumUntil = $premiumUntil;
		return $this;
	}

	public function getPremiumUntil(): DateTime|null {
		return $this->premiumUntil;
	}

	public function hasPremium(): bool {
		return $this->getPremiumUntil() !== null && $this->getPremiumUntil() > new DateTime();
	}

	public function validatePremium(bool $persist = true): void {
		if ($this->getPremiumUntil() < new DateTime()) {
			$this->setPremiumUntil(null);
			$this->setApiKey(null);
			if (!$persist) {
				return;
			}
			try {
				$this->persist();
			} catch (Exception $e) {
				error_log($e->getMessage());
			}
		}
	}

	public static function getUserByUsername(string $username): ?User {
		$result = Db::queryOne("SELECT * FROM user WHERE username = ?", [$username]);
		$user = (!$result) ? null : self::parseFromArray($result);
		// If non-null, validate the user's premium status
		$user?->validatePremium();
		return $user;
	}

	/**
	 * @param int|string $id
	 * @return ?User
	 */
	#[\Override]
	public static function getById(int|string $id): ?User {
		$result = Db::queryOne("SELECT * FROM user WHERE id = ?", [$id]);
		$favourites = Db::queryAll('SELECT * FROM favourite_places WHERE user = :user', [':user' => $id]);
		$user = (!$result) ? null : self::parseFromArray($result + ['favourites' => $favourites]);
		// If non-null, validate the user's premium status
		$user?->validatePremium();

		return $user;
	}

	public static function getByApiKey(string $apiKey): ?User {
		$result = Db::queryOne("SELECT * FROM user WHERE api_key = ?", [$apiKey]);
		if (!isset($result['username'])) {
			return null;
		}
		$favourites = Db::queryAll('SELECT * FROM favourite_places WHERE user = :user', [':user' => $result['username']]);
		$user = (!$result) ? null : self::parseFromArray($result + ['favourites' => $favourites]);
		// If non-null, validate the user's premium status
		$user?->validatePremium();
		return $user;
	}

	/**
	 * @param array<string, string|array<string>> $array
	 */
	private static function parseFromArray(array $array): User {
		$user = new User((int)$array['id'], $array['username']);
		if ($array['api_key'] !== null) {
			$user->setApiKey($array['api_key']);
		}
		if ($array['premium_until'] !== null) {
			try {
				$user->setPremiumUntil(new DateTime($array['premium_until']));
			} catch (Exception $e) {
				$user->setPremiumUntil(null);
			}
		}

		// Parse favourites
		if (!array_key_exists('favourites', $array) || !is_array($array['favourites'])) {
			return $user;
		}
		foreach ($array["favourites"] as $favourite) {
			$place = Place::getById($favourite['name']);
			if ($place) {
				$user->addFavouritePlace($place);
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
			$result = Db::execute('UPDATE user 
				SET 
				   username = :username,
				   api_key = :api_key,
				   premium_until = :premium_until
			   WHERE id = :id',
				$data);
		} else {
			// Insert a new record
			$result = Db::execute('INSERT INTO user (username, api_key, premium_until) 
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
		if (!$result) {
			throw new Exception('Failed to save the user.');
		}

		// Persist favourite places
		$favouritePlacesInDb = Db::queryAll('SELECT * FROM favourite_places WHERE user = :user', [':user' => $this->username]);
		if (!is_array($favouritePlacesInDb)) {
			throw new Exception('Failed to get the favourite places.');
		}
		// Convert the favourite places in the object to an associative array
		$favouritePlacesInObject = [];
		foreach ($this->favouricePlaces as $place) {
			$favouritePlacesInObject[$place->getName()] = true;
		}

		// Remove the places that are in the database but not in the object
		foreach ($favouritePlacesInDb as $placeInDb) {
			if (!isset($favouritePlacesInObject[$placeInDb['name']])) {
				$result = Db::execute('DELETE FROM favourite_places WHERE user = :user AND name = :name', [
					':user' => $this->username,
					':name' => $placeInDb['name']
				]);
				if (!$result) {
					throw new Exception('Failed to remove the favourite place.');
				}
			}
		}

		// Persist the favourite places
		foreach ($this->favouricePlaces as $place) {
			if (!isset($favouritePlacesInObject[$place->getName()])) {
				$result = Db::execute('INSERT INTO favourite_places (user, name) VALUES (:user, :name)', [
					':user' => $this->username,
					':name' => $place->getName()
				]);
				if (!$result) {
					throw new Exception('Failed to save the favourite place.');
				}
			}
		}
		return true;
	}

	/**
	 * @description Deletes the user
	 * @throws Exception
	 */
	public function delete(): void {
		if (!$this->id) {
			throw new Exception('Cannot delete user without id.');
		}
		$result = Db::execute('DELETE FROM user WHERE id = :id', [":id" => $this->id]);
		if (!$result) {
			throw new Exception('Failed to delete the user.');
		}
	}

	public function removeFavouritePlace(Place $place): User {
		$this->favouricePlaces = array_filter($this->favouricePlaces, fn($p) => $p->getName() !== $place->getName());
		return $this;
	}
}