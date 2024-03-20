<?php

namespace StinWeatherApp\Component\Http;

class Request {
	private string $method;
	private string $path;
	/** @var array<string, string> $headers */
	private array $headers;
	/** @var array<string, string> $body */
	private array $body;

	public function __construct() {
		$this->method = $_SERVER['REQUEST_METHOD'];
		$parsedPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$this->path = is_string($parsedPath) ? $parsedPath : '';
		$this->body = $this->method === 'POST' ? $_POST : $_GET;
		// Parse headers
		$this->headers = array();
		foreach($_SERVER as $name => $value) {
			if($name != 'HTTP_MOD_REWRITE' && (str_starts_with($name, 'HTTP_') || $name == 'CONTENT_LENGTH' || $name == 'CONTENT_TYPE')) {
				$name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', str_replace('HTTP_', '', $name)))));
				if($name == 'Content-Type') $name = 'Content-type';
				$headers[$name] = $value;
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
}