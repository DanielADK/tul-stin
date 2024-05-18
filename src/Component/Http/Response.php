<?php

namespace StinWeatherApp\Component\Http;

class Response {
	/** @var array<string> */
	protected array $headers = array();
	protected string $content;
	protected int $statusCode = 200;

	/**
	 * Constructor of response
	 *
	 * @param string|false $content
	 * @param int    $statusCode
	 * @param array<string>  $headers
	 */
	public function __construct(string|false $content = "", int $statusCode = 200, array $headers = array()) {
		$this->setContent($content);
		$this->setStatusCode($statusCode);
		foreach ($headers as $header) {
			$this->setHeader($header);
		}
	}

	/**
	 * Sets the response content type to text/html
	 *
	 * @return Response
	 */
	public function setHTML(): Response {
		$this->setHeader("Content-Type: text/html");
		return $this;
	}

	/**
	 * Sets the response content type to application/json
	 * @return Response
	 */
	public function setJSON(): Response {
		$this->setHeader("Content-Type: application/json");
		return $this;
	}

	/**
	 * Sets the response content type to text/plain
	 * @return Response
	 */
	public function setHeader(string $header): void {
		$this->headers[] = $header;
	}

	/**
	 * @return array<string>
	 */
	public function getHeader(): array {
		return $this->headers;
	}

	/**
	 * Sets the response content
	 * @param string|false $content
	 * @return Response
	 */
	public function setContent(string|false $content): Response {
		if ($content === false) {
			$this->content = "";
			return $this;
		}
		$this->content = $content;
		return $this;
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