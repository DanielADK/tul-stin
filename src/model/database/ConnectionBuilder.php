<?php

namespace StinWeatherApp\model\database;

interface ConnectionBuilder {
	public function buildConnection(): void;
}