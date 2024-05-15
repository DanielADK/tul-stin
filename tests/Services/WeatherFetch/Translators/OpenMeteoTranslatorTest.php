<?php

namespace Services\WeatherFetch\Translators;

use Exception;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Services\WeatherFetch\Translators\OpenMeteoTranslator;

class OpenMeteoTranslatorTest extends TestCase {
	public function testOpenMeteoTranslatorTranslatesDataSuccessfully(): void {
		$data = '{"hourly":{"time":["00:00","01:00"],"temperature_2m":["-1.2","-1.3"]},"generationtime_ms":"0.1"}';
		$expectedResult = [
			'times' => ["00:00", "01:00"],
			'temperatures' => ["-1.2", "-1.3"],
			'generationtime_ms' => "0.1"
		];

		$translator = $this->createMock(OpenMeteoTranslator::class);
		$translator->method('translate')->willReturn($expectedResult);

		$result = $translator->translate($data);

		$this->assertEquals($expectedResult, $result);
	}

	public function testOpenMeteoTranslatorThrowsExceptionWhenDataCannotBeDecoded(): void {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Failed to decode JSON.');

		$data = 'INVALID_DATA';

		$translator = $this->createMock(OpenMeteoTranslator::class);
		$translator->method('translate')->willThrowException(new Exception('Failed to decode JSON.'));

		$translator->translate($data);
	}

	public function testOpenMeteoTranslatorThrowsExceptionWhenExpectedKeysAreMissing(): void {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Expected key missing.');

		$data = '{"hourly":{"time":["00:00","01:00"]},"generationtime_ms":"0.1"}'; // temperature_2m key is missing

		$translator = $this->createMock(OpenMeteoTranslator::class);
		$translator->method('translate')->willThrowException(new Exception('Expected key missing.'));

		$translator->translate($data);
	}
}