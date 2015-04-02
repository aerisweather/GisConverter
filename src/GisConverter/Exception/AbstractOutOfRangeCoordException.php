<?php
namespace GisConverter\Exception;


abstract class AbstractOutOfRangeCoordException extends Exception {
	public $type;

	public function __construct($coord) {
		$this->message = "invalid {$this->type}: $coord";
	}
}