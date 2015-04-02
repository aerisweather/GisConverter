<?php
namespace GisConverter\Exception;


class UnimplementedException extends Exception {
	public function __construct($message) {
		$this->message = "unimplemented $message";
	}
}