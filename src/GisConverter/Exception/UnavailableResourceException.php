<?php
namespace GisConverter\Exception;


class UnavailableResourceException extends Exception {
	public function __construct($resource) {
		$this->message = "unavailable resource: $resource";
	}
}