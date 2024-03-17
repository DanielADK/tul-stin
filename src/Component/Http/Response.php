<?php

namespace StinWeatherApp\Component\Http;

use StinWeatherApp\Component\Router\Router;

class Response {
	/** @var array<string> */
	protected array $headers = array();
	protected string $content;
	protected int $statusCode = 200;

	/**
	 * Constructor of response
	 * @param string $content
	 * @param int    $statusCode
	 * @param array  $headers
	 */
	public function __construct(string $content = "", int $statusCode = 200, array $headers = array()) {
		$this->setContent($content);
		$this->setStatusCode($statusCode);
		foreach ($headers as $header) {
			$this->setHeader($header);
		}
	}

	public function setHTML(): Response {
		$this->setHeader("Content-Type: text/html");
		return $this;
	}

	public function setJSON(): Response {
		$this->setHeader("Content-Type: application/json");
		return $this;
	}

	public function setHeader(string $header): void {
		$this->headers[] = $header;
	}

	/**
	 * @return array<string>
	 */
	public function getHeader(): array {
		return $this->headers;
	}

	public function setContent(string $content): void {
		$this->content = $content;
	}

	public function getContent(): string {
		return $this->content;
	}

	public function setStatusCode(int $statusCode): Response {
		$this->statusCode = $statusCode;
		return $this;
	}

	public function getStatusCode(): int {
		return $this->statusCode;
	}

	public function sendHeaders(): void {
		if (!headers_sent()) {
			foreach ($this->headers as $header) {
				header($header, true, $this->statusCode);
			}
		}
	}

	public function send(): void {
		$this->sendHeaders();
		http_response_code($this->statusCode);
		echo $this->content;
	}

}