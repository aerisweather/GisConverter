<?php
namespace GisConverter\Exception;

class UnimplementedMethodException extends UnimplementedException {
	public function __construct($method, $class) {
		$this->message = "method $class::$method";
	}
}