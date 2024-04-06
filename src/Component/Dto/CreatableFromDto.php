<?php

namespace StinWeatherApp\Component\Dto;

/**
 * Interface CreatableFromDto
 *
 * @description Interface for objects that can be created from a DTO
 * @author Daniel Adámek <daniel.adamek@tul.cz>
 * @package StinWeatherApp\Component\Dto
 */
interface CreatableFromDto {
	/**
	 * @description Creates an object from a DTO
	 *
	 * @param DtoInterface $dto
	 *
	 * @return object
	 */
	public static function createFromDTO(DtoInterface $dto): object;
}