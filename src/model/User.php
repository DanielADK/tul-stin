<?php

class User {
	private int $id;
	private string $username;
	private string $email;
	private string $apiKey;

	public function __construct(int $id, string $username, string $email) {
		$this->id = $id;
		$this->apiKey = hash('sha256', $username . $email);
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

	public function setApiKey(string $apiKey): User {
		$this->apiKey = $apiKey;
		return $this;
	}

	public function getApiKey(): string {
		return $this->apiKey;
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

}