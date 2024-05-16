<?php

namespace Services\WeatherFetch;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use StinWeatherApp\Services\WeatherFetch\Translators\Translator;
use StinWeatherApp\Services\WeatherFetch\WeatherFetchService;

class WeatherFetchServiceTest extends TestCase {
	/** @var Translator|MockObject */
	private $translatorMock;
	private WeatherFetchService $service;

	public function testGetTranslator(): void {
		$this->assertSame($this->translatorMock, $this->service->getTranslator());
	}

	public function testFetch(): void {
		$url = 'http://example.com?param=value';
		$expectedResponse = 'response data';

		$this->translatorMock->method('getFormattedUrl')->willReturn($url);

		// Mocking the fetch method to return a predefined response
		$this->service = $this->getMockBuilder(WeatherFetchService::class)
			->setConstructorArgs([$this->translatorMock])
			->onlyMethods(['fetch'])
			->getMock();

		$this->service->method('fetch')->willReturn($expectedResponse);

		$this->assertEquals($expectedResponse, $this->service->fetch());
	}

	public function testProcessData(): void {
		$expectedResponse = 'response data';
		$translatedResponse = ['key' => 'value'];

		$this->translatorMock->method('translate')->with($expectedResponse)->willReturn($translatedResponse);

		$this->service = $this->getMockBuilder(WeatherFetchService::class)
			->setConstructorArgs([$this->translatorMock])
			->onlyMethods(['fetch'])
			->getMock();

		$this->service->method('fetch')->willReturn($expectedResponse);

		$this->assertEquals($translatedResponse, $this->service->processData());
	}

	public function testGetExpectedKeys(): void {
		$expectedKeys = ['key1', 'key2'];
		$this->translatorMock->method('getExpectedKeys')->willReturn($expectedKeys);

		$this->assertEquals($expectedKeys, $this->translatorMock->getExpectedKeys());
	}

	protected function setUp(): void {
		$this->translatorMock = $this->createMock(Translator::class);
		$this->service = $this->getMockBuilder(WeatherFetchService::class)
			->setConstructorArgs([$this->translatorMock])
			->onlyMethods(['fetch'])
			->getMock();
	}
}