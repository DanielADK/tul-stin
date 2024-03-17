<?php

namespace StinWeatherApp\Component\Http;

enum Method: string {
	case GET = "GET";
	case POST = "POST";
	case PUT = "PUT";
	case DELETE = "DELETE";
	case OPTIONS = "OPTIONS";
}