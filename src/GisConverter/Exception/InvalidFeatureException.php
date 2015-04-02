<?php

namespace GisConverter\Exception;


class InvalidFeatureException extends Exception {
	public function __construct($decoder_name, $text = "") {
		$this->message = "invalid feature for decoder $decoder_name" . ($text ? ": $text" : "");
	}
}