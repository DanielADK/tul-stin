<?php

namespace StinWeatherApp\Component\Http;

/**
 * Class Request
 *
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @description Represents the HTTP request
 * @package StinWeatherApp\Component\Router
 */
class Request {
	private string $method;
	private string $path;
	/** @var array<string, string> $headers */
	private array $headers;
	/** @var array<string, string> $body */
	private array $body;
	/** @var array<string, string> */
	private array $post;
	/** @var array<string, string> */
	private array $get;
	/** @var string $rawBody */
	private string $rawBody;

	public function __construct() {
		$this->method = $_SERVER['REQUEST_METHOD'];
		$parsedPath = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
		$this->path = is_string($parsedPath) ? $parsedPath : '';
		$this->body = $this->method === 'POST' ? $_POST : $_GET;
		$this->post = $_POST;
		$this->get = $_GET;
		$this->rawBody = file_get_contents('php://input');

		// Parse headers
		$this->headers = array();
		foreach($_SERVER as $name => $value) {
			if($name != 'HTTP_MOD_REWRITE' && (str_starts_with($name, 'HTTP_') || $name == 'CONTENT_LENGTH' || $name == 'CONTENT_TYPE')) {
				$name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', str_replace('HTTP_', '', $name)))));
				if($name == 'Content-Type') $name = 'Content-type';
				$this->headers[$name] = $value;
			}
		}
	}

	/**
	 * @return string
	 */
	public function getMethod(): string {
		return $this->method;
	}

	/**
	 * @return string
	 */
	public function getPath(): string {
		return $this->path;
	}

	/**
	 * @param string $name
	 * @return string|null
	 */
	public function getHeader(string $name): ?string {
		return $this->headers[$name] ?? null;
	}

	/**
	 * @return array<string, string>
	 */
	public function getBody(): array {
		return $this->body;
	}

	/**
	 * @description Returns the value of the POST parameter with the given name, or null if the parameter is not set.
	 *
	 * @param string $index
	 *
	 * @return array<string, string>|string|null
	 */
	public function getPost(string $index = "ALL"): array|string|null {
		if ($index === "ALL") {
			return $this->post;
		}
		return $this->post[$index] ?? null;
	}

	/**
	 * @description Returns the value of the GET parameter with the given name, or null if the parameter is not set.
	 *
	 * @param string $index
	 *
	 * @return array<string, string>|string|null
	 */
	public function getGet(string $index = "ALL"): array|string|null {
		if ($index === "ALL") {
			return $this->get;
		}
		return $this->get[$index] ?? null;
	}

	/**
	 * @description Returns the raw body of the request
	 *
	 * @return string
	 */
	public function getRawBody(): string {
		return $this->rawBody;
	}
}