<?php

namespace Services\WeatherFetch\Translators;

use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use StinWeatherApp\Services\WeatherFetch\Translators\Translator;

class DummyTranslator extends Translator {
	public function translate(string $data): array {
		return json_decode($data, true);
	}
}

class TranslatorTest extends TestCase {
	private DummyTranslator $translator;

	public function testGetFormattedUrl(): void {
		$this->translator->setParameter('key', 'value');
		$this->assertEquals('http://example.com?key=value', $this->translator->getFormattedUrl());
	}

	public function testGetParameters(): void {
		$this->translator->setParameter('key1', 'value1');
		$this->translator->setParameter('key2', 'value2');
		$expected = ['key1' => 'value1', 'key2' => 'value2'];
		$this->assertEquals($expected, $this->translator->getParameters());
	}

	public function testGetAndSetUrl(): void {
		$this->translator->setUrl('http://newurl.com');
		$this->assertEquals('http://newurl.com', $this->translator->getRawUrl());
	}

	public function testGetAndSetExpectedKeys(): void {
		$keys = ['key1', 'key2'];
		$this->translator->setExpectedKeys($keys);
		$this->assertEquals($keys, $this->translator->getExpectedKeys());
	}

	public function testGetAndSetTranslationKeys(): void {
		$translationKeys = ['oldKey1' => 'newKey1', 'oldKey2' => 'newKey2'];
		$this->translator->setTranslationKeys($translationKeys);
		$this->assertEquals($translationKeys, $this->translator->getTranslationKeys());
	}

	public function testValidateExpectedKeys(): void {
		$this->translator->setExpectedKeys(['key1', 'nested.key2']);
		$input = [
			'key1' => 'value1',
			'nested' => [
				'key2' => 'value2'
			]
		];

		$reflection = new ReflectionClass($this->translator);
		$method = $reflection->getMethod('validateExpectedKeys');

		$this->assertTrue($method->invokeArgs($this->translator, [&$input]));
	}

	public function testValidateExpectedKeysThrowsException(): void {
		$this->translator->setExpectedKeys(['key1', 'nested.key2']);
		$input = [
			'key1' => 'value1',
			'nested' => []
		];

		$reflection = new ReflectionClass($this->translator);
		$method = $reflection->getMethod('validateExpectedKeys');

		$this->expectException(Exception::class);
		$method->invokeArgs($this->translator, [&$input]);
	}

	public function testTranslateKeys(): void {
		$this->translator->setTranslationKeys(['key1' => 'newKey1', 'nested.key2' => 'newNested.newKey2']);
		$input = [
			'key1' => 'value1',
			'nested' => [
				'key2' => 'value2'
			]
		];

		$reflection = new ReflectionClass($this->translator);
		$method = $reflection->getMethod('translateKeys');

		$expected = [
			'newKey1' => 'value1',
			'newNested' => [
				'newKey2' => 'value2'
			]
		];
		$this->assertEquals($expected, $method->invokeArgs($this->translator, [$input]));
	}

	protected function setUp(): void {
		$this->translator = new DummyTranslator('http://example.com');
	}
}
