<?php

namespace Services\WeatherFetch\Translators;

use Exception;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Services\WeatherFetch\Translators\OpenMeteoTranslator;

class OpenMeteoTranslatorTest extends TestCase {
	private OpenMeteoTranslator $translator;

	protected function setUp(): void {
		$day = '2024-05-16';
		$longitude = '14.42076';
		$latitude = '50.08804';
		$this->translator = new OpenMeteoTranslator($day, $longitude, $latitude);
	}

	public function testConstructorSetsParameters(): void {
		$params = $this->translator->getParameters();
		$this->assertEquals('2024-05-16', $params['start_date']);
		$this->assertEquals('2024-05-16', $params['end_date']);
		$this->assertEquals('14.42076', $params['longitude']);
		$this->assertEquals('50.08804', $params['latitude']);
		$this->assertEquals('temperature_2m', $params['hourly']);
	}

	public function testConstructorSetsExpectedKeys(): void {
		$expectedKeys = [
			"hourly.time",
			"hourly.temperature_2m",
			"generationtime_ms"
		];
		$this->assertEquals($expectedKeys, $this->translator->getExpectedKeys());
	}

	public function testConstructorSetsTranslationKeys(): void {
		$translationKeys = [
			"hourly.time" => "times",
			"hourly.temperature_2m" => "temperatures",
			"hourly_units.time" => "time_format",
			"hourly_units.temperature_2m" => "temperature_unit"
		];
		$this->assertEquals($translationKeys, $this->translator->getTranslationKeys());
	}

	public function testTranslateReturnsTranslatedData(): void {
		$data = json_encode([
			"hourly" => [
				"time" => ["2024-05-16T00:00:00Z"],
				"temperature_2m" => [15.5]
			],
			"hourly_units" => [
				"time" => "iso8601",
				"temperature_2m" => "°C"
			],
			"generationtime_ms" => 15
		]);

		$expected = [
			"times" => ["2024-05-16T00:00:00Z"],
			"temperatures" => [15.5],
			"time_format" => "iso8601",
			"temperature_unit" => "°C"
		];

		$this->assertEquals($expected, $this->translator->translate($data));
	}

	public function testTranslateThrowsExceptionOnInvalidJson(): void {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage("Failed to decode JSON.");

		$invalidJson = "invalid json";
		$this->translator->translate($invalidJson);
	}

	public function testTranslateThrowsExceptionOnMissingExpectedKeys(): void {
		$data = json_encode([
			"hourly" => [
				"temperature_2m" => [15.5]
			]
		]);

		$this->expectException(Exception::class);
		$this->expectExceptionMessage("Key not found: hourly.time");

		$this->translator->translate($data);
	}
}