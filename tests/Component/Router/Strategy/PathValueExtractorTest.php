<?php

namespace Component\Router\Strategy;

use PHPUnit\Framework\TestCase;
use StinWeatherApp\Component\Http\Request;
use StinWeatherApp\Component\Router\Strategy\PathValueExtractor;

class PathValueExtractorTest extends TestCase {
	public function testExtractValue(): void {
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$extractor = new PathValueExtractor();

		// Test 1: Single variable in pattern path
		$_SERVER['REQUEST_URI'] = '/weather/prague';
		$request = new Request();
		$value = $extractor->extractValue('/weather/:city', $request);
		$this->assertEquals(['city' => 'prague'], $value);

		// Test 2: Multiple variables in pattern path
		$_SERVER['REQUEST_URI'] = '/weather/prague/today';
		$request = new Request();
		$value = $extractor->extractValue('/weather/:city/:day', $request);
		$this->assertEquals(['day' => 'today', 'city' => 'prague'], $value);

		// Test 3: Variable at the start of pattern path
		$_SERVER['REQUEST_URI'] = '/prague/weather';
		$request = new Request();
		$value = $extractor->extractValue('/:city/weather', $request);
		$this->assertEquals(['city' => 'prague'], $value);

		// Test 4: Variable at the end of pattern path
		$_SERVER['REQUEST_URI'] = '/weather/prague';
		$request = new Request();
		$value = $extractor->extractValue('/weather/:city', $request);
		$this->assertEquals(['city' => 'prague'], $value);

		// Test 5: Variable in the middle of pattern path
		$_SERVER['REQUEST_URI'] = '/weather/prague/today';
		$request = new Request();
		$value = $extractor->extractValue('/weather/:city/today', $request);
		$this->assertEquals(['city' => 'prague'], $value);

		// Test 6: Pattern path with no variables
		$_SERVER['REQUEST_URI'] = '/weather/prague';
		$request = new Request();
		$value = $extractor->extractValue('/weather/prague', $request);
		$this->assertEquals([], $value);

		// Test 7: Pattern path with variables but real path missing corresponding values
		$_SERVER['REQUEST_URI'] = '/weather';
		$request = new Request();
		$value = $extractor->extractValue('/weather/:city/:day', $request);
		$this->assertEquals([], $value);
	}
}