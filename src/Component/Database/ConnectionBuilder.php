<?php

namespace StinWeatherApp\Component\Database;

interface ConnectionBuilder {
	public function buildConnection(): void;
}