<?php

require_once 'vendor/autoload.php';

use StinWeatherApp\model\database\SQLiteConnectionBuilder;
use StinWeatherApp\model\database\Db;



// Connect
$conn = new SQLiteConnectionBuilder();
$conn->setDatabase('db/weather');
$conn->buildConnection();
Db::connect($conn);