<?php
namespace GisConverter\Exception;


class InvalidTextException extends Exception {
	public function __construct($decoder_name, $text = "") {
		$this->message = "invalid text for decoder " . $decoder_name . ($text ? (": " . $text) : "");
	}
}