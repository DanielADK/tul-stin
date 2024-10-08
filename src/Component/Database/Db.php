<?php
namespace StinWeatherApp\Component\Database;

use DateTime;
use Exception;
use PDO;

abstract class Db {
	protected static PDO $connection;

	/**
	 * @var array<int, mixed>
	 */
	protected static array $settings = array(
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_EMULATE_PREPARES => false,
	);

	public static function connect(ConnectionBuilder $builder): void {
		if (!isset(self::$connection)) {
			$builder->buildConnection();
		}
	}

	/**
	 * Processes parameters to convert them into a database-friendly format.
	 *
	 * @param array<string, int|float|string|DateTime> $params
	 *
	 * @return array<string, int|float|string>
	 * @throws Exception
	 */
	private static function processParameters(array $params = array()): array {
		$new_params = array();
		foreach ($params as $key => $value) {
			if ($value instanceof DateTime) {
				$new_params[$key] = $value->format('Y-m-d H:i:s');
			} else {
				$new_params[$key] = $value;
			}
		}
		return $new_params;
	}
	/**
	 * One row query
	 *
	 * @param string $query
	 * @param array<string, int|float|string> $params
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public static function queryOne(string $query, array $params = array()): mixed {
		$ret = self::$connection->prepare($query);
		$params = self::processParameters($params);
		$ret->execute($params);
		return $ret->fetch();
	}

	/**
	 * All rows query
	 *
	 * @param string               $query
	 * @param array<string, int|float|string> $params
	 *
	 * @return false|array<string, mixed>
	 * @throws Exception
	 */
	public static function queryAll(string $query, array $params = array()): false|array {
		$ret = self::$connection->prepare($query);
		$params = self::processParameters($params);
		$ret->execute($params);
		return $ret->fetchAll();
	}

	/**
	 * One row, one column query
	 *
	 * @param string $query
	 * @param array<string, int|float|string|DateTime> $params
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public static function queryCell(string $query, array $params = array()): mixed {
		$params = self::processParameters($params);
		$ret = self::queryOne($query, $params);
		return (is_array($ret))? $ret[0] : false;
	}

	/**
	 * Execute query and return number of affected rows
	 *
	 * @param string $query
	 * @param array<string, int> $params
	 *
	 * @return int
	 * @throws Exception
	 */
	public static function queryRowCount(string $query, array $params = array()): int {
		$ret = self::$connection->prepare($query);
		$params = self::processParameters($params);
		$ret->execute($params);
		return $ret->rowCount();
	}

	/**
	 * Execute query and return success
	 *
	 * @param string                                   $query
	 * @param array<string, int|float|string|DateTime> $params
	 *
	 * @return bool
	 * @throws Exception
	 */
	public static function execute(string $query, array $params = array()): bool {
		$ret = self::$connection->prepare($query);
		$params = self::processParameters($params);
		return $ret->execute($params);
	}
}
